<?php namespace FlyingCalculation\Services;

use Validator;
use DB;
use Illuminate\Support\Facades\Http;

use FlyingCalculation\City;
use FlyingCalculation\State;

class CityService 
{
  protected $cityStateLookupCache = [];

		//add City rules
	public function add_city_rules(array $data){
		$messages = [
			'city.required' => 'Please enter city name.',
			'city.unique' => 'City name should be unique.',
			'state_id.required' => 'Please select state.',
			'state_id.exists' => 'Please select a valid state.'
		];

		$validator = Validator::make($data, [
			'city' => 'required|min:2|max:50|unique:city,name',
			'state_id' => 'required|exists:state,id'
		], $messages);
    
		return $validator;
	}
  
	// add City
	public function addCity(array $request_data) {	
      $obj_city= new City;
      $obj_city->state_id = $request_data['state_id'];
      $obj_city->name = $request_data['city'];
      $obj_city->save();  
		
	}
  
  // edit City rules
	public function edit_city_rules(array $data){
		$messages = [
			'city.required' => 'Please enter city name.',
			'state.required' => 'Please select state.',
			'state.exists' => 'Please select a valid state.'
		];
    
		$validator = Validator::make($data, [
			'city' => 'required|min:2|max:50',
			'state' => 'required|exists:state,id'
		], $messages);
    
		return $validator;
	}
  
	// edit City
	public function editCity(array $request_data) {
		$obj_city = City::find($request_data['city-id']);
    $obj_city->state_id = $request_data['state'];
		$obj_city->name = $request_data['city'];	
    $obj_city->save();  
    
	}

  public function getCountryNameForLookup($countryName, $countryCode)
  {
    $countryName = trim($countryName);
    $countryCode = trim($countryCode);

    if(strtoupper($countryName) == 'IN' || ($countryName == '' && strtoupper($countryCode) == 'IN')){
      return 'India';
    }

    return $countryName != '' ? $countryName : $countryCode;
  }

  public function getOrCreateCountryId($countryName, $countryCode)
  {
    $countryName = trim($countryName);
    $countryCode = trim($countryCode);

    if(strtoupper($countryName) == 'IN' || ($countryName == '' && strtoupper($countryCode) == 'IN')){
      $countryName = 'India';
    }

    if($countryName == ''){
      $countryName = ($countryCode != '' ? $countryCode : 'Unknown');
    }

    $country_id = DB::table('country')
      ->whereRaw('LOWER(name) = ?', [strtolower($countryName)])
      ->value('id');

    if(!empty($country_id)){
      return $country_id;
    }

    return DB::table('country')->insertGetId([
      'name' => $countryName,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s')
    ]);
  }

  private function extractStateNameFromNominatimResults($results)
  {
    if(!is_array($results)){
      return '';
    }

    foreach($results as $result){
      $address = isset($result['address']) && is_array($result['address']) ? $result['address'] : [];

      foreach(['state', 'province', 'region', 'state_district', 'county'] as $addressKey){
        if(!empty($address[$addressKey])){
          return trim($address[$addressKey]);
        }
      }
    }

    return '';
  }

  private function searchStateNameFromOpenStreetMap($params)
  {
    try {
      $response = Http::withHeaders([
        'User-Agent' => 'FlyingCalculation/1.0',
        'Accept' => 'application/json',
      ])->timeout(8)->get('https://nominatim.openstreetmap.org/search', $params);

      if(!$response->successful()){
        return '';
      }

      return $this->extractStateNameFromNominatimResults($response->json());
    } catch (\Exception $e) {
      return '';
    }
  }

  public function getStateNameFromOpenStreetMap($cityName, $countryName, $countryCode)
  {
    $cityName = trim($cityName);
    $lookupCountry = $this->getCountryNameForLookup($countryName, $countryCode);

    if($cityName == ''){
      return '';
    }

    $cacheKey = strtolower($cityName.'|'.$lookupCountry);
    if(array_key_exists($cacheKey, $this->cityStateLookupCache)){
      return $this->cityStateLookupCache[$cacheKey];
    }

    $commonParams = [
      'format' => 'json',
      'addressdetails' => 1,
      'limit' => 5,
    ];

    $stateName = $this->searchStateNameFromOpenStreetMap(array_merge($commonParams, [
      'city' => $cityName,
      'country' => $lookupCountry,
    ]));

    if($stateName == ''){
      $stateName = $this->searchStateNameFromOpenStreetMap(array_merge($commonParams, [
        'county' => $cityName,
        'country' => $lookupCountry,
      ]));
    }

    if($stateName == ''){
      $stateName = $this->searchStateNameFromOpenStreetMap(array_merge($commonParams, [
        'q' => $cityName.', '.$lookupCountry,
      ]));
    }

    $this->cityStateLookupCache[$cacheKey] = $stateName;

    return $stateName;
  }

  public function resolveStateNameForCity($cityName, $stateName = '', $countryName = '', $countryCode = 'IN')
  {
    $stateName = trim($stateName);

    if($stateName != '' && strtolower($stateName) != 'unknown'){
      return $stateName;
    }

    $resolvedStateName = $this->getStateNameFromOpenStreetMap($cityName, $countryName, $countryCode);

    return $resolvedStateName != '' ? $resolvedStateName : $stateName;
  }

  public function getOrCreateStateId($stateName, $countryName = '', $countryCode = 'IN')
  {
    $stateName = trim($stateName);

    if($stateName == ''){
      $stateName = 'Unknown';
    }

    $state_id = State::whereRaw('LOWER(name) = ?', [strtolower($stateName)])->value('id');

    if(!empty($state_id)){
      return $state_id;
    }

    $state = new State;
    $state->name = $stateName;
    $state->country_id = $this->getOrCreateCountryId($countryName, $countryCode);
    $state->save();

    return $state->id;
  }

  public function getOrCreateCityId($cityName, $stateName = '', $countryName = '', $countryCode = 'IN')
  {
    $cityName = trim($cityName);
    $stateName = $this->resolveStateNameForCity($cityName, $stateName, $countryName, $countryCode);

    if($cityName == ''){
      return 0;
    }

    $city = City::whereRaw('LOWER(name) = ?', [strtolower($cityName)])->first();

    if(!empty($city)){
      $currentState = State::find($city->state_id);
      if($stateName != '' && (empty($currentState) || strtolower($currentState->name) == 'unknown')){
        $city->state_id = $this->getOrCreateStateId($stateName, $countryName, $countryCode);
        $city->save();
      }

      return $city->id;
    }

    $city = new City;
    $city->name = $cityName;
    $city->state_id = $this->getOrCreateStateId($stateName, $countryName, $countryCode);
    $city->save();

    return $city->id;
  }

  public function backfillUnknownCityStates($countryName = 'India', $countryCode = 'IN', $limit = 25)
  {
    $unknownStateIds = State::whereRaw('LOWER(name) = ?', ['unknown'])->pluck('id')->toArray();

    $cities = City::select('city.*')
      ->leftJoin('state', 'city.state_id', '=', 'state.id')
      ->where(function($query) use ($unknownStateIds) {
        $query->whereNull('state.id');
        if(!empty($unknownStateIds)){
          $query->orWhereIn('city.state_id', $unknownStateIds);
        }
      })
      ->orderBy('city.updated_at', 'asc')
      ->limit($limit)
      ->get();

    $updatedCount = 0;

    foreach($cities as $city){
      $stateName = $this->resolveStateNameForCity($city->name, '', $countryName, $countryCode);

      if($stateName != '' && strtolower($stateName) != 'unknown'){
        $city->state_id = $this->getOrCreateStateId($stateName, $countryName, $countryCode);
        $city->save();
        $updatedCount++;
      }
    }

    return $updatedCount;
  }
}
