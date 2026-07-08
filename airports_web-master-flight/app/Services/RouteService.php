<?php namespace FlyingCalculation\Services;

use Validator;

use FlyingCalculation\Route;
use Illuminate\Support\Facades\DB;

class RouteService 
{
	//add Route rules
	public function add_route_rules(array $data){
		$messages = [
			'location_1.required' => 'Please select from location.',
			'location_1.exists' => 'Please select a valid from location.',
			'location_2.required' => 'Please select to location.',
			'location_2.different' => 'Please select different location.',
			'location_2.exists' => 'Please select a valid to location.',
			'plane.required' => 'Please select machine.',
			'plane.exists' => 'Please select a valid machine.',
			'time.required' => 'Please enter time.',
			'time.integer' => 'Please enter valid time.',
			'distance.required' => 'Please enter distance.',
			'distance.numeric' => 'Please enter valid distance.',
		];

		$validator = Validator::make($data, [
			'location_1' => 'required|exists:airport,id',
			'location_2' => 'required|different:location_1|exists:airport,id',
			'plane' => 'required|exists:plane,id',
			'time' => 'required|integer|min:0',
			'distance' => 'required|numeric|min:0',
		], $messages);
    
		return $validator;
	}
  
	// add Route
	public function addRoute(array $request_data) {
    DB::transaction(function () use ($request_data): void {
      $location_1 = (int) $request_data['location_1'];
      $location_2 = (int) $request_data['location_2'];
      $plane_id = (int) $request_data['plane'];

      $this->saveRouteRow($location_1, $location_2, $plane_id, $request_data['time'], $request_data['distance']);
      $this->saveRouteRow($location_2, $location_1, $plane_id, $request_data['time'], $request_data['distance']);
    });
	}
  
  // edit Route rules
	public function edit_route_rules(array $data){
		$messages = [
			'route-id.required' => 'Route not found.',
			'route-id.exists' => 'Route not found.',
			'location_1.required' => 'Please select from location.',
			'location_1.exists' => 'Please select a valid from location.',
			'location_2.required' => 'Please select to location.',
			'location_2.different' => 'Please select different location.',
			'location_2.exists' => 'Please select a valid to location.',
			'plane.required' => 'Please select machine.',
			'plane.exists' => 'Please select a valid machine.',
			'time.required' => 'Please enter time.',
			'time.integer' => 'Please enter valid time.',
			'distance.required' => 'Please enter distance.',
			'distance.numeric' => 'Please enter valid distance.',
		];

		$validator = Validator::make($data, [
			'route-id' => 'required|exists:route,id',
			'location_1' => 'required|exists:airport,id',
			'location_2' => 'required|different:location_1|exists:airport,id',
			'plane' => 'required|exists:plane,id',
			'time' => 'required|integer|min:0',
			'distance' => 'required|numeric|min:0',
		], $messages);
    
		return $validator;
	}
  
	// edit Route
	public function editRoute(array $request_data) {
    DB::transaction(function () use ($request_data): void {
      $location_1 = (int) $request_data['location_1'];
      $location_2 = (int) $request_data['location_2'];
      $plane_id = (int) $request_data['plane'];

      $this->saveRouteRow($location_1, $location_2, $plane_id, $request_data['time'], $request_data['distance'], (int) $request_data['route-id']);
      $this->saveRouteRow($location_2, $location_1, $plane_id, $request_data['time'], $request_data['distance']);
    });
  }

  private function saveRouteRow($location_1, $location_2, $plane_id, $time, $distance, $route_id = null)
  {
    $route = $route_id ? Route::find($route_id) : null;

    if(!$route){
      $route = Route::where('location_1', $location_1)
        ->where('location_2', $location_2)
        ->where('plane_id', $plane_id)
        ->first();
    }

    if(!$route){
      $route = new Route;
    }

    $route->location_1 = $location_1;
    $route->location_2 = $location_2;
    $route->time = (int) $time;
    $route->distance = (float) $distance;
    $route->plane_id = $plane_id;
    $route->price = 0;
    $route->save();

    return $route;
  }
}
