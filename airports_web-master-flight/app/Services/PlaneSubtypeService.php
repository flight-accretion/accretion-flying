<?php namespace FlyingCalculation\Services;

use Validator;

use FlyingCalculation\PlaneSubtype;

class PlaneSubtypeService 
{
	//add Plane Subtype rules
	public function add_plane_subtype_rules(array $data){
		$messages = [
      'type.required' => 'Please select plane type',
			'sub_type.required' => 'Please enter plane subtype name.',
		];

		$validator = Validator::make($data, [
      'type' => 'required',
			'sub_type' => 'required|min:2|max:50',
		], $messages);
    
		return $validator;
	}
  
	// add Plane Subtype
	public function addPlaneSubtype(array $request_data) {
    $obj_plane= new PlaneSubtype;
    $obj_plane->plane_type = $request_data['type'];
    $obj_plane->sub_type = $request_data['sub_type'];
    $obj_plane->status = $request_data['status'];
    // dd($obj_plane);
    $obj_plane->save();
    
	}
  
  // edit Plane rules
	public function edit_plane_subtype_rules(array $data){
		$messages = [
			'sub_type.required' => 'Please enter plane subtype name.',
		];
    
		$validator = Validator::make($data, [
      'sub_type' => 'required|min:2|max:50',
		], $messages);
    
		return $validator;
	}
  
	// edit Plane
	public function editPlaneSubtype(array $request_data) {
    $obj_plane = PlaneSubtype::find($request_data['subtype-id']);
    $obj_plane->plane_type = $request_data['plane_type'];
    $obj_plane->sub_type = $request_data['sub_type'];
    $obj_plane->status = $request_data['status'];
    // dd($obj_plane);
    $obj_plane->save();
    
  }
  
}