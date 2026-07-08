<?php namespace FlyingCalculation\Services;

use Validator;

use FlyingCalculation\Airport;
use FlyingCalculation\HandlingCharge;

class AirportService 
{
		//add Airport rules
	public function add_airport_rules(array $data){
		$messages = [
			'airport.required' => 'Please enter airport name.',
			'airport.unique' => 'Airport name should be unique.',
			'city.required' => 'Please select city.',
			'latitude.required' => 'Please enter latitude.',
			'longitude.required' => 'Please enter longitude.',
			'gt.required' => 'Please enter ground time.',
			'gt.integer' => 'Ground time should be in minutes.',
			'gt.min' => 'Ground time cannot be negative.'
		];

		$validator = Validator::make($data, [
			'airport' => 'required|min:2|max:50|unique:airport,name',
			'city' => 'required',
			'latitude' => 'required',
			'longitude' => 'required',
			'gt' => 'required|integer|min:0'
		], $messages);
    
		return $validator;
	}
  
	// add Airport
	public function addAirport(array $request_data) {	
    $obj_airport = new Airport;
    $obj_airport->city_id = $request_data['city'];
    $obj_airport->name = $request_data['airport'];
    $obj_airport->iata = $request_data['iata'];	
    $obj_airport->icao = $request_data['icao'];	
    $obj_airport->latitude = $request_data['latitude'];
    $obj_airport->longitude = $request_data['longitude'];
    $obj_airport->open_time = date('0000-00-00 H:i', strtotime($request_data['open-time']));	
    $obj_airport->close_time = date('0000-00-00 H:i', strtotime($request_data['close-time'])); 
    $obj_airport->gt = isset($request_data['gt']) && $request_data['gt'] !== '' ? $request_data['gt'] : 10;
    $obj_airport->status = $request_data['status'];
    $obj_airport->save();
    HandlingCharge::syncAirportCharge($obj_airport, (isset($request_data['charges']) ? $request_data['charges'] : null));
	}
  
  // edit Airport rules
	public function edit_airport_rules(array $data){
		$messages = [
			'airport.required' => 'Please enter airport name.',
			'city.required' => 'Please select airport city.',
			'latitude.required' => 'Please select enter latitude.',
			'longitude.required' => 'Please select enter longitude.',
			'gt.required' => 'Please enter ground time.',
			'gt.integer' => 'Ground time should be in minutes.',
			'gt.min' => 'Ground time cannot be negative.'
		];
    
		$validator = Validator::make($data, [
			'airport' => 'required|min:2|max:50',
			'city' => 'required',
			'latitude' => 'required',
			'longitude' => 'required',
			'gt' => 'required|integer|min:0',
		], $messages);
    
		return $validator;
	}
  
	// edit Airport
	public function editAirport(array $request_data) {
		$obj_airport = Airport::find($request_data['airport-id']);
    $obj_airport->city_id = $request_data['city'];
		$obj_airport->name = $request_data['airport'];	
		$obj_airport->iata = $request_data['iata'];	
		$obj_airport->icao = $request_data['icao'];	
		$obj_airport->latitude = $request_data['latitude'];	
		$obj_airport->longitude = $request_data['longitude'];	
		$obj_airport->open_time = date('0000-00-00 H:i', strtotime($request_data['open-time']));	
		$obj_airport->close_time = date('0000-00-00 H:i', strtotime($request_data['close-time']));	
		$obj_airport->gt = isset($request_data['gt']) && $request_data['gt'] !== '' ? $request_data['gt'] : 10;
		$obj_airport->status = $request_data['status'];
    $obj_airport->save();  
    HandlingCharge::syncAirportCharge($obj_airport, (isset($request_data['charges']) ? $request_data['charges'] : null));
	}
}
