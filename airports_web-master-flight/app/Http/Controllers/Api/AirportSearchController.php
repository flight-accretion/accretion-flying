<?php

namespace FlyingCalculation\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use FlyingCalculation\Airport;
use FlyingCalculation\Http\Controllers\Controller;

class AirportSearchController extends Controller
{
  public function index(Request $request)
  {
    if(!$this->hasValidApiKey($request)){
      return $this->apiResponse([
        'success' => false,
        'message' => 'Invalid API key.',
      ], 401, $request);
    }

    try {
      $columns = Schema::getColumnListing('airport');
      $has_airport_city_name = in_array('city_name', $columns, true);
      $has_city_table = !$has_airport_city_name && Schema::hasTable('city');
      $query = Airport::from('airport')
        ->where('airport.status', 1)
        ->select('airport.*');
      $search = trim((string) $request->query('q', $request->query('search', '')));
      $limit = (int) $request->query('limit', 500);
      $limit = max(1, min($limit, 1000));

      $lat = $request->query('latitude', $request->query('lat', null));
      $lng = $request->query('longitude', $request->query('lng', null));
      $nearest = (string) $request->query('nearest', '') === '1';

      if($has_city_table){
        $query
          ->leftJoin('city', 'city.id', '=', 'airport.city_id')
          ->addSelect('city.name as city_name');
      }

      $search_fields = ['airport.name'];

      if($has_airport_city_name){
        $search_fields[] = 'airport.city_name';
      } else if($has_city_table){
        $search_fields[] = 'city.name';
      }

      foreach(['iata', 'icao'] as $column){
        if(in_array($column, $columns, true)){
          $search_fields[] = 'airport.'.$column;
        }
      }

      if($search !== ''){
        $terms = array_filter(preg_split('/\s+/', $search));

        $query->where(function($search_query) use ($terms, $search_fields) {
          foreach($terms as $term){
            $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $term).'%';

            $search_query->where(function($term_query) use ($search_fields, $like) {
              foreach($search_fields as $index => $column){
                if($index === 0){
                  $term_query->where($column, 'like', $like);
                } else {
                  $term_query->orWhere($column, 'like', $like);
                }
              }
            });
          }
        });
      }

      if($nearest && is_numeric($lat) && is_numeric($lng)){
        $lat = (float) $lat;
        $lng = (float) $lng;
        $limit = 1;

        $query
          ->whereNotNull('airport.latitude')
          ->whereNotNull('airport.longitude')
          ->selectRaw(
            '(6371 * acos(least(1, greatest(-1, cos(radians(?)) * cos(radians(airport.latitude)) * cos(radians(airport.longitude) - radians(?)) + sin(radians(?)) * sin(radians(airport.latitude)))))) as distance_km',
            [$lat, $lng, $lat]
          )
          ->orderBy('distance_km');
      } else {
        if($has_airport_city_name){
          $query->orderBy('airport.city_name');
        } else if($has_city_table){
          $query->orderBy('city.name');
        }
      }

      $airports = $query
        ->orderBy('airport.name')
        ->limit($limit)
        ->get()
        ->map(function($airport) {
          return [
            'id' => (int) $airport->id,
            'name' => $airport->name,
            'city_id' => (int) $airport->city_id,
            'city_name' => isset($airport->city_name) ? $airport->city_name : '',
            'state_name' => isset($airport->state_name) ? $airport->state_name : '',
            'country_name' => isset($airport->country_name) ? $airport->country_name : '',
            'iata' => isset($airport->iata) ? $airport->iata : '',
            'icao' => isset($airport->icao) ? $airport->icao : '',
            'latitude' => is_numeric($airport->latitude) ? (float) $airport->latitude : null,
            'longitude' => is_numeric($airport->longitude) ? (float) $airport->longitude : null,
            'distance_km' => isset($airport->distance_km) ? round((float) $airport->distance_km, 2) : null,
            'gt' => isset($airport->gt) && is_numeric($airport->gt) ? (float) $airport->gt : 0,
          ];
        })
        ->values();

      return $this->apiResponse([
        'success' => true,
        'meta' => [
          'count' => $airports->count(),
          'search' => $search,
          'search_fields' => ['name', 'city', 'iata', 'icao'],
          'nearest' => $nearest,
          'latitude' => is_numeric($lat) ? (float) $lat : null,
          'longitude' => is_numeric($lng) ? (float) $lng : null,
        ],
        'data' => $airports,
      ], 200, $request);
    } catch(Exception $exception) {
      return $this->apiResponse([
        'success' => false,
        'message' => 'Unable to fetch airport data.',
        'error' => config('app.debug') ? $exception->getMessage() : null,
      ], 500, $request);
    }
  }

  public function options(Request $request)
  {
    return $this->apiResponse(null, 204, $request);
  }

  private function hasValidApiKey(Request $request)
  {
    $api_key = env('MACHINE_API_KEY', '');

    if($api_key === ''){
      return true;
    }

    $given_key = $request->header('X-API-Key', $request->query('api_key', ''));

    return hash_equals($api_key, (string) $given_key);
  }

  private function apiResponse($data, $status, Request $request)
  {
    $response = $data === null ? response('', $status) : response()->json($data, $status);

    $origin = $request->headers->get('Origin', '*');
    $allowed_origins = array_filter(array_map('trim', explode(',', env('MACHINE_API_ALLOWED_ORIGINS', '*'))));
    $allow_origin = '*';

    if(!in_array('*', $allowed_origins, true)){
      $allow_origin = in_array($origin, $allowed_origins, true) ? $origin : reset($allowed_origins);
    }

    $response->headers->set('Access-Control-Allow-Origin', $allow_origin ?: '*');
    $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
    $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, X-API-Key');
    $response->headers->set('Access-Control-Max-Age', '86400');

    return $response;
  }
}
