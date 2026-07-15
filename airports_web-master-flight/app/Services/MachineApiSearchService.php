<?php

namespace FlyingCalculation\Services;

use DB;
use FlyingCalculation\Airport;
use FlyingCalculation\City;
use FlyingCalculation\Route as FlightRoute;

class MachineApiSearchService
{
  private $service_types = [
    'private-jet' => 1,
    'private-jet-charter' => 1,
    'private_jat' => 1,
    'private-jat' => 1,
    'plane' => 1,
    'jet' => 1,
    'private-charter-helicopter' => 2,
    'private-helicopter' => 2,
    'helicopter' => 2,
    'air-ambulance' => 3,
    'air_ambulance' => 3,
    'air ambulance' => 3,
  ];

  public function search(array $request_data)
  {
    $plane_type = $this->resolvePlaneType($request_data);
    $trip_type = $this->resolveTripType($request_data);
    $adults = max(1, (int) $this->inputValue($request_data, ['adults', 'total_adults', 'total-adults'], 1));
    $sort = $this->resolveSort($request_data);
    $date = $this->normalizeDate($this->inputValue($request_data, ['date'], date('Y-m-d')));
    $subtype_ids = $this->subtypeFilterIds($this->inputValue($request_data, ['subtype_ids', 'subtypes_filter_id', 'subtypes-filter-id'], ''));

    $airports = Airport::where('status', 1)->get()->keyBy('id');
    $cities = City::pluck('name', 'id');
    $plane_types = DB::table('plane_type')->pluck('name', 'id');
    $plane_subtypes = DB::table('plane_subtypes')->pluck('sub_type', 'id');
    $handling_charges = DB::table('handling_charges')->pluck('charges', 'airport_id');
    $tax_details = DB::table('setting')
      ->where('setting_type', 0)
      ->where('status', 1)
      ->whereDate('from_date', '<=', date('Y-m-d'))
      ->whereDate('to_date', '>', date('Y-m-d'))
      ->first();

    $departure = $this->resolvePoint($request_data, $airports, 'departure');
    $arrival = $this->resolvePoint($request_data, $airports, 'arrival');
    $has_quote_input = $this->hasPoint($departure) && $this->hasPoint($arrival);

    $query = DB::table('plane')
      ->select(DB::raw('plane.*, city.name as city_name'))
      ->leftJoin('city', 'city.id', '=', 'plane.city_id')
      ->where('plane.seats', '>=', $adults)
      ->orderBy('plane.price_per_hour', $sort);

    if($plane_type){
      $query->where('plane.type_id', $plane_type);
    }

    if(count($subtype_ids) > 0){
      $query->whereIn('plane.subtype', $subtype_ids);
    }

    $planes = $query->get();
    $data = [];

    foreach($planes as $plane){
      $base = $this->basePoint($plane, $airports, $cities, $date);
      $quote = null;

      if($has_quote_input){
        if($trip_type === 2){
          $quote = $this->multiQuote($plane, $base, $request_data, $airports, $cities, $handling_charges, $tax_details);
        } else {
          $quote = $this->singleRoundQuote($plane, $base, $departure, $arrival, $trip_type, $handling_charges, $tax_details, $request_data);
        }
      }

      $data[] = $this->formatMachine($plane, $base, $quote, $plane_types, $plane_subtypes);
    }

    return [
      'success' => true,
      'meta' => [
        'count' => count($data),
        'service' => $this->serviceKeyFromType($plane_type),
        'plane_type' => $plane_type,
        'trip_type' => $this->tripTypeName($trip_type),
        'adults' => $adults,
        'sort' => $sort === 'asc' ? 'price_asc' : 'price_desc',
        'quote_applied' => $has_quote_input,
      ],
      'data' => $data,
    ];
  }

  private function formatMachine($plane, array $base, $quote, $plane_types, $plane_subtypes)
  {
    $image = trim((string) $plane->display_image);

    return [
      'id' => (int) $plane->id,
      'name' => $plane->name,
      'type_id' => (int) $plane->type_id,
      'type' => isset($plane_types[$plane->type_id]) ? $plane_types[$plane->type_id] : '',
      'subtype_id' => isset($plane->subtype) ? (int) $plane->subtype : 0,
      'subtype' => isset($plane_subtypes[$plane->subtype]) ? $plane_subtypes[$plane->subtype] : '',
      'base' => $base,
      'seats' => (int) $plane->seats,
      'speed' => (float) $plane->speed,
      'speed_coefficient' => $this->speedCoefficient($plane),
      'lavatory' => (int) $plane->lavatory,
      'price_per_hour' => (float) $plane->price_per_hour,
      'display_image' => $image,
      'image_url' => $image !== '' ? url('uploads/' . rawurlencode($image)) : null,
      'quote' => $quote,
    ];
  }

  private function singleRoundQuote($plane, array $base, array $departure, array $arrival, $trip_type, $handling_charges, $tax_details, array $request_data)
  {
    $segments = [];
    $speed = max(1, (float) $plane->speed);
    $coefficient = $this->speedCoefficient($plane);
    $gt1 = $plane->type_id == 2 ? 0 : $this->groundTime($departure);
    $gt2 = $plane->type_id == 2 ? 0 : $this->groundTime($arrival);

    if(!$this->samePoint($base, $departure)){
      $segments[] = $this->segment($plane, $base, $departure, $speed, $coefficient, $gt1, $gt2);
    }

    $segments[] = $this->segment($plane, $departure, $arrival, $speed, $coefficient, $gt1, $gt2);

    if($trip_type === 1){
      $segments[] = $this->segment($plane, $arrival, $departure, $speed, $coefficient, $gt1, $gt2);

      if(!$this->samePoint($base, $departure)){
        $segments[] = $this->segment($plane, $departure, $base, $speed, $coefficient, $gt1, $gt2);
      }
    } else if(!$this->samePoint($base, $arrival)){
      $segments[] = $this->segment($plane, $arrival, $base, $speed, $coefficient, $gt1, $gt2);
    }

    return $this->pricedQuote($plane, $segments, $trip_type, $departure, $arrival, $base, $handling_charges, $tax_details, $request_data);
  }

  private function multiQuote($plane, array $base, array $request_data, $airports, $cities, $handling_charges, $tax_details)
  {
    $departures = $this->arrayInput($this->inputValue($request_data, ['multi_departure', 'multi-departure', 'departure'], []));
    $arrivals = $this->arrayInput($this->inputValue($request_data, ['multi_arrival', 'multi-arrival', 'arrival'], []));
    $dep_lats = $this->arrayInput($this->inputValue($request_data, ['dep_multi_latitude', 'dep-multi-latitude'], []));
    $dep_lngs = $this->arrayInput($this->inputValue($request_data, ['dep_multi_longitude', 'dep-multi-longitude'], []));
    $arr_lats = $this->arrayInput($this->inputValue($request_data, ['arr_multi_latitude', 'arr-multi-latitude'], []));
    $arr_lngs = $this->arrayInput($this->inputValue($request_data, ['arr_multi_longitude', 'arr-multi-longitude'], []));
    $heli_deps = $this->arrayInput($this->inputValue($request_data, ['helicopter_multi_departure', 'helicopter-multi-departure'], []));
    $heli_arrs = $this->arrayInput($this->inputValue($request_data, ['helicopter_multi_arrival', 'helicopter-multi-arrival'], []));

    if(count($departures) === 0 && count($arrivals) === 0){
      return null;
    }

    $legs = [];
    $max = max(count($departures), count($arrivals));

    for($i = 0; $i < $max; $i++){
      $dep = $this->resolvePointFromParts(
        isset($departures[$i]) ? $departures[$i] : null,
        isset($dep_lats[$i]) ? $dep_lats[$i] : null,
        isset($dep_lngs[$i]) ? $dep_lngs[$i] : null,
        isset($heli_deps[$i]) ? $heli_deps[$i] : null,
        $airports,
        $cities
      );

      $arr = $this->resolvePointFromParts(
        isset($arrivals[$i]) ? $arrivals[$i] : null,
        isset($arr_lats[$i]) ? $arr_lats[$i] : null,
        isset($arr_lngs[$i]) ? $arr_lngs[$i] : null,
        isset($heli_arrs[$i]) ? $heli_arrs[$i] : null,
        $airports,
        $cities
      );

      if($this->hasPoint($dep) && $this->hasPoint($arr)){
        $legs[] = [$dep, $arr];
      }
    }

    if(count($legs) === 0){
      return null;
    }

    $segments = [];
    $speed = max(1, (float) $plane->speed);
    $coefficient = $this->speedCoefficient($plane);
    $first_departure = $legs[0][0];
    $last_arrival = $legs[count($legs) - 1][1];
    $gt1 = $plane->type_id == 2 ? 0 : $this->groundTime($first_departure);
    $gt2 = $plane->type_id == 2 ? 0 : $this->groundTime($last_arrival);

    if(!$this->samePoint($base, $first_departure)){
      $segments[] = $this->segment($plane, $base, $first_departure, $speed, $coefficient, $gt1, $gt2);
    }

    foreach($legs as $leg){
      $segments[] = $this->segment($plane, $leg[0], $leg[1], $speed, $coefficient, $gt1, $gt2);
    }

    if(!$this->samePoint($base, $last_arrival)){
      $segments[] = $this->segment($plane, $last_arrival, $base, $speed, $coefficient, $gt1, $gt2);
    }

    return $this->pricedQuote($plane, $segments, 2, $first_departure, $last_arrival, $base, $handling_charges, $tax_details, $request_data);
  }

  private function pricedQuote($plane, array $segments, $trip_type, array $departure, array $arrival, array $base, $handling_charges, $tax_details, array $request_data)
  {
    $total_distance = 0;
    $total_minutes = 0;

    foreach($segments as $segment){
      $total_distance += $segment['distance_nm'];
      $total_minutes += $segment['time_minutes'];
    }

    $minimum_minutes = $trip_type === 1 ? 240 : 120;
    $billable_minutes = max($minimum_minutes, (int) round($total_minutes));
    $flying_cost = ($billable_minutes / 60) * (float) $plane->price_per_hour;
    $handling = $this->handlingCharges($plane, $departure, $arrival, $base, $trip_type, $handling_charges);
    $crew_handling_days = $trip_type === 1 ? $this->crewHandlingDays($request_data, $total_minutes) : 0;
    $crew_handling = $trip_type === 1 ? $this->crewHandlingAmount($arrival) * $crew_handling_days : 0;
    $medical_cost = $this->medicalCostAmount($plane->type_id);
    $gst_rate = $this->gstRateAmount($tax_details);
    $sub_total = $flying_cost + $handling + $crew_handling;
    $gst_amount = (int) $plane->type_id === 3 ? 0 : ($gst_rate / 100) * $sub_total;
    $grand_total = (int) $plane->type_id === 3 ? ($sub_total + $medical_cost) : ($sub_total + $gst_amount);

    $route = implode(' > ', $this->routeNames($segments));
    $distance_nm = round($total_distance, 2);
    $flight_time = $this->timeLabel($total_minutes);
    $flight_time_minutes = (int) round($total_minutes);
    $billable_time = $this->timeLabel($billable_minutes);
    $flying_cost_amount = round($flying_cost);
    $handling_amount = round($handling);
    $crew_handling_amount = round($crew_handling);
    $medical_cost_amount = round($medical_cost);
    $gst_amount = round($gst_amount);
    $sub_total_amount = round($sub_total);
    $grand_total_amount = round($grand_total);

    $summary = $this->quoteSummary(
      $base,
      $route,
      $distance_nm,
      $billable_time,
      $flying_cost_amount,
      $handling_amount,
      $crew_handling_amount,
      $medical_cost_amount,
      $gst_rate,
      $gst_amount,
      $sub_total_amount,
      $grand_total_amount
    );

    return [
      'route' => $route,
      'trip_type' => $this->tripTypeName($trip_type),
      'distance_nm' => $distance_nm,
      'flight_time' => $flight_time,
      'flight_time_minutes' => $flight_time_minutes,
      'billable_time' => $billable_time,
      'billable_time_minutes' => $billable_minutes,
      'flying_cost' => $flying_cost_amount,
      'handling_charges' => $handling_amount,
      'crew_handling' => $crew_handling_amount,
      'crew_handling_days' => $crew_handling_days,
      'medical_cost' => $medical_cost_amount,
      'gst_rate' => $gst_rate,
      'gst_amount' => $gst_amount,
      'sub_total' => $sub_total_amount,
      'grand_total' => $grand_total_amount,
      'summary' => $summary,
      'cost_estimate' => $this->quoteCostEstimate($summary),
      'display_rows' => $this->quoteDisplayRows($summary, (int) $plane->type_id === 3),
      'segments' => $segments,
    ];
  }

  private function quoteSummary(array $base, $route, $distance_nm, $billable_time, $flying_cost, $handling, $crew_handling, $medical_cost, $gst_rate, $gst_amount, $sub_total, $grand_total)
  {
    return [
      'base' => [
        'label' => 'Base',
        'value' => $base['name'],
        'airport_id' => isset($base['airport_id']) ? (int) $base['airport_id'] : 0,
        'city_id' => isset($base['city_id']) ? (int) $base['city_id'] : 0,
      ],
      'route' => [
        'label' => 'Route',
        'value' => $route,
      ],
      'flying_cost' => [
        'label' => 'Flying Cost',
        'amount' => $flying_cost,
        'formatted' => $this->moneyLabel($flying_cost),
        'time' => $billable_time,
        'value' => $this->moneyLabel($flying_cost) . ' (For ' . $billable_time . '.)',
      ],
      'distance' => [
        'label' => 'Distance',
        'amount' => $distance_nm,
        'unit' => 'NM',
        'value' => $distance_nm . ' NM',
      ],
      'airport_handling_charges' => [
        'label' => 'Airport Handling Charges',
        'amount' => $handling,
        'formatted' => $this->moneyLabel($handling),
        'value' => $this->moneyLabel($handling),
      ],
      'crew_handling_charges' => [
        'label' => 'Crew Handling Charges',
        'amount' => $crew_handling,
        'formatted' => $this->moneyLabel($crew_handling),
        'value' => $this->moneyLabel($crew_handling),
      ],
      'medical_cost' => [
        'label' => 'Fixed Medical Team Cost',
        'amount' => $medical_cost,
        'formatted' => $this->moneyLabel($medical_cost),
        'value' => $this->moneyLabel($medical_cost),
      ],
      'sub_total' => [
        'label' => 'Sub Total',
        'amount' => $sub_total,
        'formatted' => $this->moneyLabel($sub_total),
        'value' => $this->moneyLabel($sub_total),
      ],
      'gst' => [
        'label' => 'GST (' . $this->numberLabel($gst_rate) . '%)',
        'rate' => $gst_rate,
        'amount' => $gst_amount,
        'formatted' => $this->moneyLabel($gst_amount),
        'value' => $this->moneyLabel($gst_amount),
      ],
      'grand_total' => [
        'label' => 'Grand Total',
        'amount' => $grand_total,
        'formatted' => $this->moneyLabel($grand_total),
        'value' => $this->moneyLabel($grand_total),
      ],
    ];
  }

  private function quoteCostEstimate(array $summary)
  {
    return [
      'base' => $summary['base']['value'],
      'route' => $summary['route']['value'],
      'flying_cost' => $summary['flying_cost']['amount'],
      'flying_cost_text' => $summary['flying_cost']['value'],
      'distance' => $summary['distance']['amount'],
      'distance_unit' => $summary['distance']['unit'],
      'distance_text' => $summary['distance']['value'],
      'airport_handling_charges' => $summary['airport_handling_charges']['amount'],
      'airport_handling_charges_text' => $summary['airport_handling_charges']['value'],
      'crew_handling_charges' => $summary['crew_handling_charges']['amount'],
      'crew_handling_charges_text' => $summary['crew_handling_charges']['value'],
      'medical_cost' => $summary['medical_cost']['amount'],
      'medical_cost_text' => $summary['medical_cost']['value'],
      'sub_total' => $summary['sub_total']['amount'],
      'sub_total_text' => $summary['sub_total']['value'],
      'gst_rate' => $summary['gst']['rate'],
      'gst_amount' => $summary['gst']['amount'],
      'gst_text' => $summary['gst']['value'],
      'grand_total' => $summary['grand_total']['amount'],
      'grand_total_text' => $summary['grand_total']['value'],
    ];
  }

  private function quoteDisplayRows(array $summary, $is_air_ambulance = false)
  {
    $rows = [
      $this->displayRow('base', $summary['base']),
      $this->displayRow('route', $summary['route']),
      $this->displayRow('flying_cost', $summary['flying_cost']),
      $this->displayRow('distance', $summary['distance']),
      $this->displayRow('airport_handling_charges', $summary['airport_handling_charges']),
    ];

    if($summary['crew_handling_charges']['amount'] > 0){
      $rows[] = $this->displayRow('crew_handling_charges', $summary['crew_handling_charges']);
    }

    $rows[] = $this->displayRow('sub_total', $summary['sub_total']);

    if($is_air_ambulance){
      if($summary['medical_cost']['amount'] > 0){
        $rows[] = $this->displayRow('medical_cost', $summary['medical_cost']);
      }
    } else {
      $rows[] = $this->displayRow('gst', $summary['gst']);
    }

    $rows[] = $this->displayRow('grand_total', $summary['grand_total']);

    return $rows;
  }

  private function displayRow($key, array $item)
  {
    return [
      'key' => $key,
      'label' => $item['label'],
      'value' => $item['value'],
      'amount' => isset($item['amount']) ? $item['amount'] : null,
    ];
  }

  private function moneyLabel($amount)
  {
    return html_entity_decode('&#8377;', ENT_QUOTES, 'UTF-8') . $this->numberLabel($amount);
  }

  private function numberLabel($number)
  {
    if(!is_numeric($number)){
      return (string) $number;
    }

    $number = (float) $number;

    if(floor($number) == $number){
      return (string) (int) $number;
    }

    return rtrim(rtrim(number_format($number, 2, '.', ''), '0'), '.');
  }

  private function segment($plane, array $from, array $to, $speed, $coefficient, $gt1, $gt2)
  {
    $manual_route = null;

    if((int) $plane->type_id !== 2 && !empty($from['airport_id']) && !empty($to['airport_id'])){
      $manual_route = FlightRoute::where('plane_id', $plane->id)
        ->where('location_1', $from['airport_id'])
        ->where('location_2', $to['airport_id'])
        ->first();
    }

    $distance = $this->distanceNm($from['latitude'], $from['longitude'], $to['latitude'], $to['longitude']);
    $time = $this->minutesForDistance($distance, $speed, $coefficient, $gt1, $gt2, $plane->type_id);
    $source = 'geometry';

    if($manual_route){
      if(is_numeric($manual_route->distance) && (float) $manual_route->distance > 0){
        $distance = (float) $manual_route->distance;
      }

      if(is_numeric($manual_route->time) && (float) $manual_route->time > 0){
        $time = (float) $manual_route->time;
        $source = 'manual_route';
      }
    }

    return [
      'from' => $from['name'],
      'to' => $to['name'],
      'from_airport_id' => $from['airport_id'],
      'to_airport_id' => $to['airport_id'],
      'distance_nm' => round($distance, 2),
      'time_minutes' => (int) round($time),
      'time' => $this->timeLabel($time),
      'source' => $source,
    ];
  }

  private function resolvePoint(array $input, $airports, $type)
  {
    if($type === 'departure'){
      $id = $this->inputValue($input, ['departure_airport_id', 'departure', 'old-departure', 'old_departure']);
      $lat = $this->inputValue($input, ['dep_latitude', 'dep-latitude', 'departure_latitude', 'old-dep-latitude']);
      $lng = $this->inputValue($input, ['dep_longitude', 'dep-longitude', 'departure_longitude', 'old-dep-longitude']);
      $name = $this->inputValue($input, ['departure_name', 'helicopter_departure', 'helicopter-departure']);
    } else {
      $id = $this->inputValue($input, ['arrival_airport_id', 'arrival', 'old-arrival', 'old_arrival']);
      $lat = $this->inputValue($input, ['arr_latitude', 'arr-latitude', 'arrival_latitude', 'old-arr-latitude']);
      $lng = $this->inputValue($input, ['arr_longitude', 'arr-longitude', 'arrival_longitude', 'old-arr-longitude']);
      $name = $this->inputValue($input, ['arrival_name', 'helicopter_arrival', 'helicopter-arrival']);
    }

    return $this->resolvePointFromParts($id, $lat, $lng, $name, $airports, null);
  }

  private function resolvePointFromParts($airport_id, $lat, $lng, $name, $airports, $cities)
  {
    $airport = null;
    $airport_id = is_numeric($airport_id) ? (int) $airport_id : 0;

    if($airport_id && isset($airports[$airport_id])){
      $airport = $airports[$airport_id];
    }

    if($airport){
      $lat = $this->numericOrDefault($lat, $airport->latitude);
      $lng = $this->numericOrDefault($lng, $airport->longitude);
      $name = $this->cleanName($name);

      if($name === ''){
        $name = $airport->city_name ?: $airport->name;
      }

      return [
        'airport_id' => (int) $airport->id,
        'city_id' => (int) $airport->city_id,
        'name' => $name,
        'airport_name' => $airport->name,
        'latitude' => (float) $lat,
        'longitude' => (float) $lng,
        'gt' => isset($airport->gt) ? (float) $airport->gt : 0,
        'crew_handling' => isset($airport->crew_handling) ? (float) $airport->crew_handling : 25000,
      ];
    }

    return [
      'airport_id' => 0,
      'city_id' => 0,
      'name' => $this->cleanName($name) ?: 'Selected Location',
      'airport_name' => '',
      'latitude' => is_numeric($lat) ? (float) $lat : null,
      'longitude' => is_numeric($lng) ? (float) $lng : null,
      'gt' => 0,
      'crew_handling' => 25000,
    ];
  }

  private function basePoint($plane, $airports, $cities, $date)
  {
    $city_id = (int) $plane->city_id;
    $airport_id = isset($plane->airport_id) ? (int) $plane->airport_id : 0;
    $lat = $plane->latitude;
    $lng = $plane->longitude;

    if((int) $plane->type_id === 2 && $this->isTemporaryBaseActive($plane, $date)){
      $city_id = isset($plane->temporary_city_id) ? (int) $plane->temporary_city_id : $city_id;
      $airport_id = isset($plane->temporary_airport_id) ? (int) $plane->temporary_airport_id : $airport_id;
      $lat = $plane->temp_latitude !== '' ? $plane->temp_latitude : $lat;
      $lng = $plane->temp_longitude !== '' ? $plane->temp_longitude : $lng;
    }

    $airport = $airport_id && isset($airports[$airport_id]) ? $airports[$airport_id] : null;
    $name = isset($cities[$city_id]) ? $cities[$city_id] : (isset($plane->city_name) ? $plane->city_name : '');

    return [
      'airport_id' => $airport_id,
      'city_id' => $city_id,
      'name' => $name,
      'airport_name' => $airport ? $airport->name : '',
      'latitude' => is_numeric($lat) ? (float) $lat : null,
      'longitude' => is_numeric($lng) ? (float) $lng : null,
      'gt' => $airport && isset($airport->gt) ? (float) $airport->gt : 0,
      'crew_handling' => $airport && isset($airport->crew_handling) ? (float) $airport->crew_handling : 25000,
    ];
  }

  private function isTemporaryBaseActive($plane, $date)
  {
    if(empty($plane->from_date) || empty($plane->to_date)){
      return false;
    }

    $from = strtotime($plane->from_date);
    $to = strtotime($plane->to_date);
    $current = strtotime($date);

    return $from && $to && $current && $from <= $current && $to >= $current && !empty($plane->temporary_city_id);
  }

  private function handlingCharges($plane, array $departure, array $arrival, array $base, $trip_type, $handling_charges)
  {
    if((int) $plane->type_id === 2){
      return 0;
    }

    $general = isset($handling_charges[0]) && is_numeric($handling_charges[0]) ? (float) $handling_charges[0] : 0;
    $amount = $this->handlingChargeFor($departure['airport_id'], $handling_charges, $general);
    $amount += $this->handlingChargeFor($arrival['airport_id'], $handling_charges, $general);

    if($trip_type === 1){
      $amount += $this->handlingChargeFor($departure['airport_id'], $handling_charges, $general);
    }

    if(!empty($base['airport_id']) && !$this->samePoint($base, $departure) && !$this->samePoint($base, $arrival)){
      $amount += $this->handlingChargeFor($base['airport_id'], $handling_charges, $general);
    }

    return $amount;
  }

  private function handlingChargeFor($airport_id, $handling_charges, $general)
  {
    if($airport_id && isset($handling_charges[$airport_id]) && is_numeric($handling_charges[$airport_id])){
      return (float) $handling_charges[$airport_id];
    }

    return $general;
  }

  private function routeNames(array $segments)
  {
    $names = [];

    foreach($segments as $segment){
      if(count($names) === 0){
        $names[] = $segment['from'];
      }

      $names[] = $segment['to'];
    }

    return $names;
  }

  private function resolvePlaneType(array $input)
  {
    $plane_type = $this->inputValue($input, ['plane_type', 'plane-type', 'planes', 'type_id']);

    if(is_numeric($plane_type)){
      return (int) $plane_type;
    }

    $service = strtolower(trim((string) $this->inputValue($input, ['service', 'service_type', 'service-type'], '')));
    $service = str_replace('_', '-', $service);

    return isset($this->service_types[$service]) ? $this->service_types[$service] : null;
  }

  private function serviceKeyFromType($type)
  {
    if((int) $type === 1){
      return 'private-jet';
    }

    if((int) $type === 2){
      return 'private-charter-helicopter';
    }

    if((int) $type === 3){
      return 'air-ambulance';
    }

    return 'all';
  }

  private function resolveTripType(array $input)
  {
    $trip = strtolower(trim((string) $this->inputValue($input, ['trip_type', 'trip-type', 'trips'], 0)));

    if($trip === 'round'){
      return 1;
    }

    if($trip === 'multi'){
      return 2;
    }

    return is_numeric($trip) ? (int) $trip : 0;
  }

  private function tripTypeName($trip_type)
  {
    if((int) $trip_type === 1){
      return 'round';
    }

    if((int) $trip_type === 2){
      return 'multi';
    }

    return 'single';
  }

  private function resolveSort(array $input)
  {
    $sort = strtolower(trim((string) $this->inputValue($input, ['sort'], '')));
    $filter_id = $this->inputValue($input, ['filter_id', 'filter-id'], null);

    if($sort === 'price_asc' || $sort === 'asc' || (string) $filter_id === '0'){
      return 'asc';
    }

    return 'desc';
  }

  private function normalizeDate($date)
  {
    $timestamp = strtotime((string) $date);
    return $timestamp ? date('Y-m-d', $timestamp) : date('Y-m-d');
  }

  private function inputValue(array $input, array $keys, $default = null)
  {
    foreach($keys as $key){
      if(array_key_exists($key, $input) && $input[$key] !== null && $input[$key] !== ''){
        return $input[$key];
      }
    }

    return $default;
  }

  private function arrayInput($value)
  {
    if(is_array($value)){
      return array_values($value);
    }

    if($value === null || $value === ''){
      return [];
    }

    $decoded = json_decode((string) $value, true);
    if(is_array($decoded)){
      return array_values($decoded);
    }

    return array_values(array_filter(array_map('trim', explode(',', (string) $value)), function($item) {
      return $item !== '';
    }));
  }

  private function subtypeFilterIds($value)
  {
    return array_values(array_filter($this->arrayInput($value), function($id) {
      return is_numeric($id) && (int) $id > 0;
    }));
  }

  private function speedCoefficient($plane)
  {
    if(isset($plane->speed_coefficient) && is_numeric($plane->speed_coefficient) && (float) $plane->speed_coefficient > 0){
      return (float) $plane->speed_coefficient;
    }

    return 1;
  }

  private function hasPoint(array $point)
  {
    return is_numeric($point['latitude']) && is_numeric($point['longitude']);
  }

  private function samePoint(array $first, array $second)
  {
    if(!empty($first['airport_id']) && !empty($second['airport_id']) && (int) $first['airport_id'] === (int) $second['airport_id']){
      return true;
    }

    if(!$this->hasPoint($first) || !$this->hasPoint($second)){
      return false;
    }

    return $this->distanceNm($first['latitude'], $first['longitude'], $second['latitude'], $second['longitude']) < 1;
  }

  private function distanceNm($lat1, $lon1, $lat2, $lon2)
  {
    if(!is_numeric($lat1) || !is_numeric($lon1) || !is_numeric($lat2) || !is_numeric($lon2)){
      return 0;
    }

    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = min(1, max(-1, $dist));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;

    return $miles * 0.8684;
  }

  private function minutesForDistance($distance, $speed, $coefficient, $gt1, $gt2, $plane_type = null)
  {
    if($distance <= 0 || $speed <= 0){
      return 0;
    }

    if($coefficient <= 0){
      $coefficient = 1;
    }

    $is_helicopter = (int) $plane_type === 2;
    $effective_speed = $speed * $coefficient;
    $slow_distance = $is_helicopter ? 0 : min($distance, 200);
    $cruise_distance = $is_helicopter ? $distance : max($distance - 200, 0);
    $slow_minutes = $effective_speed > 0 ? $slow_distance / ($effective_speed / 60) : 0;
    $cruise_minutes = $cruise_distance > 0 ? $cruise_distance / ($speed / 60) : 0;

    return $gt1 + $gt2 + $slow_minutes + $cruise_minutes;
  }

  private function groundTime(array $point)
  {
    return isset($point['gt']) && is_numeric($point['gt']) ? (float) $point['gt'] : 0;
  }

  private function medicalCostAmount($plane_type, $default = 40000)
  {
    if((int) $plane_type !== 3){
      return 0;
    }

    $setting = DB::table('setting')
      ->where('setting_type', 1)
      ->where('status', 1)
      ->whereDate('from_date', '<=', date('Y-m-d'))
      ->whereDate('to_date', '>', date('Y-m-d'))
      ->orderBy('created_at', 'desc')
      ->first();

    if($setting && is_numeric($setting->amount)){
      return (float) $setting->amount;
    }

    return (float) $default;
  }

  private function gstRateAmount($tax_details = null, $default = 18)
  {
    if($tax_details && isset($tax_details->gst) && is_numeric($tax_details->gst) && (float) $tax_details->gst > 0){
      return (float) $tax_details->gst;
    }

    $setting = DB::table('setting')
      ->where('setting_type', 0)
      ->where('status', 1)
      ->orderBy('updated_at', 'desc')
      ->first();

    if($setting && isset($setting->gst) && is_numeric($setting->gst) && (float) $setting->gst > 0){
      return (float) $setting->gst;
    }

    return (float) $default;
  }

  private function crewHandlingAmount(array $airport, $default = 25000)
  {
    if(isset($airport['crew_handling']) && is_numeric($airport['crew_handling'])){
      return (float) $airport['crew_handling'];
    }

    return (float) $default;
  }

  private function crewHandlingDays(array $request_data, $total_minutes)
  {
    $date = $this->inputValue($request_data, ['date'], null);
    $round_date = $this->inputValue($request_data, ['round_date', 'round-date'], null);

    if(!$date || !$round_date){
      return 1;
    }

    $start = strtotime($date);
    $end = strtotime($round_date);

    if(!$start || !$end){
      return 1;
    }

    $arrival_day = strtotime(date('Y-m-d', $start + ((int) round($total_minutes / 2) * 60)));
    $round_day = strtotime(date('Y-m-d', $end));
    $days = (int) floor(($round_day - $arrival_day) / 86400);

    return max(1, $days);
  }

  private function timeLabel($minutes)
  {
    $minutes = max(0, (int) round($minutes));
    $hours = (int) floor($minutes / 60);
    $mins = $minutes % 60;

    return $hours . ' Hrs ' . $mins . ' min';
  }

  private function numericOrDefault($value, $default)
  {
    return is_numeric($value) ? $value : $default;
  }

  private function cleanName($value)
  {
    $name = trim((string) $value);

    if(strtolower($name) === 'select from map'){
      return '';
    }

    return $name;
  }
}
