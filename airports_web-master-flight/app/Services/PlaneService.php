<?php namespace FlyingCalculation\Services;

use Validator;

use FlyingCalculation\Plane;
use FlyingCalculation\Owner;
use FlyingCalculation\PlaneImage;
use FlyingCalculation\SecondaryContact;

class PlaneService 
{
	//add Plane rules
	public function add_plane_rules(array $data){
		$messages = [
			'plane.required' => 'Please enter plane name.',
			'type.required' => 'Please select plane type',
			'type.not_in' => 'Please select plane type',
			'subtype.required' => 'Please select plane subtype',
			'subtype.not_in' => 'Please select plane subtype',
			'speed_coefficient.required' => 'Please enter speed coefficient.',
			'price.required' => 'Please enter price per hour.',
			'seats.required' => 'Please enter seats.',
			'speed.required' => 'Please enter speed per hour.',
			'lavatory.required' => 'Please select lavatory.',
			'owner.required' => 'Please select owner',
			'contact.required' => 'Please enter owner contact.',
			'city.required' => 'City not found. Please <a target="_blank" href="/city/add" >add city</a> or select another city',
			'city.not_in' => 'City not found. Please <a target="_blank" href="/city/add" >add city</a> or select another city',
			'lat.required_without' => 'Please select latitude from map.',
			'latitude.required_without' => 'Please select latitude from map.',
			'long.required_without' => 'Please select longitude from map.',
			'longitude.required_without' => 'Please select longitude from map.',
		];

		$validator = Validator::make($data, [
			'plane' => 'required|min:2|max:100',
			'type' => 'required|not_in:0|exists:plane_type,id',
			'subtype' => 'required|not_in:0|exists:plane_subtypes,id',
			'speed_coefficient' => 'required|numeric|min:0',
			'price' => 'required|numeric|min:0',
			'seats' => 'required|integer|min:1',
			'speed' => 'required|numeric|min:0',
			'lavatory' => 'required|in:0,1',
			'owner' => 'required|min:2|max:100',
			'contact' => 'required|max:30',
			'email' => 'nullable|email|max:100',
			'name' => 'nullable|max:100',
			'number' => 'nullable|max:30',
			'email-id' => 'nullable|email|max:100',
      'city' => 'required|not_in:0|exists:city,id',
			'lat' => 'required_without:latitude|nullable|numeric',
			'long' => 'required_without:longitude|nullable|numeric',
			'latitude' => 'required_without:lat|nullable|numeric',
			'longitude' => 'required_without:long|nullable|numeric',
			'gt' => 'nullable|integer|min:0',
			'from-date' => 'nullable|date_format:d-m-Y',
			'to-date' => 'nullable|date_format:d-m-Y',
		], $messages);
    
		return $validator;
	}
  
  // add Plane
	public function addPlane(array $request_data) {
    $obj_plane= new Plane;
    $obj_owner = new Owner;
    $obj_plane->name = trim($this->inputValue($request_data, ['plane']));
    $obj_plane->Call_Sign = $this->inputValue($request_data, ['Call_Sign']);
    $obj_plane->type_id = $this->intValue($request_data, ['type']);
    $obj_plane->subtype = $this->intValue($request_data, ['subtype']);
    $obj_plane->speed_coefficient = $this->numericValue($request_data, ['speed_coefficient'], 0.65);
    $obj_plane->gt = $this->intValue($request_data, ['gt'], 0);
    $obj_plane->temp_latitude = $this->inputValue($request_data, ['lat-hel', 'latitude-hel']);
    $obj_plane->temp_longitude = $this->inputValue($request_data, ['long-hel', 'longitude-hel']);
		$obj_plane->temporary_city_id = $this->intValue($request_data, ['city-hel'], 0);
		$obj_plane->temporary_airport_id = $this->intValue($request_data, ['airport-hel'], 0);
    $obj_plane->from_date = $this->dateValue($request_data, 'from-date');
    $obj_plane->to_date = $this->dateValue($request_data, 'to-date');
    $obj_plane->city_id = $this->intValue($request_data, ['city']);
		$obj_plane->airport_id = $this->intValue($request_data, ['airport'], 0);
    $obj_plane->price_per_hour = $this->numericValue($request_data, ['price']);
    $obj_plane->seats = $this->intValue($request_data, ['seats']);
    $obj_plane->lavatory = $this->intValue($request_data, ['lavatory'], 1);
    if($this->inputValue($request_data, ['owner-id']) != "") {
      $obj_plane->owner_id = $this->intValue($request_data, ['owner-id']);
    } else {
      $owner_id = $this->addOwner($request_data); //dd($request_data);
      $obj_plane->owner_id = $owner_id;
    }
    $obj_plane->speed = $this->numericValue($request_data, ['speed']);
    $obj_plane->latitude = $this->inputValue($request_data, ['lat', 'latitude']);
    $obj_plane->longitude = $this->inputValue($request_data, ['long', 'longitude']);
    $obj_plane->note = $this->inputValue($request_data, ['note']);
    $obj_plane->display_image = $this->inputValue($request_data, ['display-image']);
    if(isset($request_data['flower-shower'])){
      $obj_plane->flower_shower = 1;
    } else {
      $obj_plane->flower_shower = 0;
    } 
    //dd($obj_plane);
    $obj_plane->save();
    
    $this->savePlaneImages($obj_plane->id, $request_data['images'] ?? [], false);
	}
  
  // edit Plane rules
	public function edit_plane_rules(array $data){
		$messages = [
			'plane.required' => 'Please enter plane name.',
			'type.required' => 'Please select plane type',
			'type.not_in' => 'Please select plane type',
			'subtype.required' => 'Please select plane subtype',
			'subtype.not_in' => 'Please select plane subtype',
			'speed_coefficient.required' => 'Please enter speed coefficient.',
			'price.required' => 'Please enter price per hour.',
			'seats.required' => 'Please enter seats.',
			'speed.required' => 'Please enter speed per hour.',
			'lavatory.required' => 'Please select lavatory.',
			'owner.required' => 'Please select owner',
			'contact.required' => 'Please enter owner contact.',
			'city.required' => 'City not found. Please <a target="_blank" href="/city/add" >add city</a> or select another city',
			'city.not_in' => 'City not found. Please <a target="_blank" href="/city/add" >add city</a> or select another city',
			'lat.required_without' => 'Please select latitude from map.',
			'latitude.required_without' => 'Please select latitude from map.',
			'long.required_without' => 'Please select longitude from map.',
			'longitude.required_without' => 'Please select longitude from map.',
		];

		$validator = Validator::make($data, [
			'plane-id' => 'required|exists:plane,id',
			'plane' => 'required|min:2|max:100',
			'type' => 'required|not_in:0|exists:plane_type,id',
			'subtype' => 'required|not_in:0|exists:plane_subtypes,id',
			'speed_coefficient' => 'required|numeric|min:0',
			'price' => 'required|numeric|min:0',
			'seats' => 'required|integer|min:1',
			'speed' => 'required|numeric|min:0',
			'lavatory' => 'required|in:0,1',
      'owner' => 'required',
			'contact' => 'required|max:30',
			'email' => 'nullable|email|max:100',
			'name' => 'nullable|max:100',
			'number' => 'nullable|max:30',
			'email-id' => 'nullable|email|max:100',
      'city' => 'required|not_in:0|exists:city,id',
			'lat' => 'required_without:latitude|nullable|numeric',
			'long' => 'required_without:longitude|nullable|numeric',
			'latitude' => 'required_without:lat|nullable|numeric',
			'longitude' => 'required_without:long|nullable|numeric',
			'gt' => 'nullable|integer|min:0',
			'from-date' => 'nullable|date_format:d-m-Y',
			'to-date' => 'nullable|date_format:d-m-Y',
		], $messages);
    
		return $validator;
	}
  
	// edit Plane
	public function editPlane(array $request_data) {
    $obj_plane= Plane::find($request_data['plane-id']);
    $obj_plane->name = trim($this->inputValue($request_data, ['plane']));
    $obj_plane->Call_Sign = $this->inputValue($request_data, ['Call_Sign']);
    $obj_plane->type_id = $this->intValue($request_data, ['type']);
    $obj_plane->lavatory = $this->intValue($request_data, ['lavatory'], 1);
    $obj_plane->subtype = $this->intValue($request_data, ['subtype']);
    $obj_plane->speed_coefficient = $this->numericValue($request_data, ['speed_coefficient'], 0.65);
    $obj_plane->gt = $this->intValue($request_data, ['gt'], 0);
    $obj_plane->temp_latitude = $this->inputValue($request_data, ['lat-hel', 'latitude-hel']);
    $obj_plane->temp_longitude = $this->inputValue($request_data, ['long-hel', 'longitude-hel']);
		$obj_plane->temporary_city_id = $this->intValue($request_data, ['city-hel'], 0);
		$obj_plane->temporary_airport_id = $this->intValue($request_data, ['airport-hel'], 0);
		$obj_plane->from_date = $this->dateValue($request_data, 'from-date');
		$obj_plane->to_date = $this->dateValue($request_data, 'to-date');
    $obj_plane->city_id = $this->intValue($request_data, ['city']);
		$obj_plane->airport_id = $this->intValue($request_data, ['airport'], 0);
    $obj_plane->price_per_hour = $this->numericValue($request_data, ['price']);
    $obj_plane->seats = $this->intValue($request_data, ['seats']);
    $owner_id = $this->editOwner($request_data); //dd($request_data);
    $obj_plane->owner_id = $owner_id;
    $obj_plane->speed = $this->numericValue($request_data, ['speed']);
    $obj_plane->latitude = $this->inputValue($request_data, ['lat', 'latitude']);
    $obj_plane->longitude = $this->inputValue($request_data, ['long', 'longitude']);
    $obj_plane->note = $this->inputValue($request_data, ['note']);
    if(array_key_exists('display-image', $request_data)){
      $obj_plane->display_image = $this->inputValue($request_data, ['display-image']);
    }
    if(isset($request_data['flower-shower'])){
      $obj_plane->flower_shower = 1;
    } else {
      $obj_plane->flower_shower = 0;
    }
    $obj_plane->save();
      
      if(array_key_exists('images', $request_data)){
        $this->savePlaneImages($obj_plane->id, $request_data['images'], true);
      }
  }
  
  
  // edit Plane rules
	public function search_rules(array $data){
		$messages = [
			'planes.required' => 'Select machine type.',
			'planes.not_in' => 'Select machine type.',
			'trips.required' => 'Select trip.',
			'adults.required' => 'Please enter no. of adults.',
			'departure.required' => 'Select departure airport.',
			'departure.not_in' => 'Select departure airport.',
			'arrival.required' => 'Select arrival airport.',
			'arrival.not_in' => 'Select arrival airport.'
		];
    

		$validator = Validator::make($data, [
			'planes' => 'required|not_in:0',
			'trips' => 'required',
			'adults' => 'required',
			'departure' => 'required_if:planes,1|required_if:planes,3',
			'arrival' => 'required_if:planes,1|required_if:planes,3'
		], $messages);
		return $validator;
	}
  
  public function addOwner(array $request_data){
    $obj_owner= new Owner; 
    $obj_owner->name = $this->inputValue($request_data, ['owner']);
    $obj_owner->contact_number_1 = $this->inputValue($request_data, ['contact']);
    $obj_owner->email_1 = $this->inputValue($request_data, ['email']); 
    $obj_owner->save();
    
    $secondary_contact = SecondaryContact::where('owner_id', $obj_owner->id)->delete();
      //dd($request_data);
    if($this->inputValue($request_data, ['name']) != '' || $this->inputValue($request_data, ['number']) != '' || $this->inputValue($request_data, ['email-id']) != ''){
      $secondary_contact = new SecondaryContact;
      $secondary_contact->owner_id = $obj_owner->id;
      $secondary_contact->name = $this->inputValue($request_data, ['name']);
      $secondary_contact->email = $this->inputValue($request_data, ['email-id']);
      $secondary_contact->contact = $this->inputValue($request_data, ['number']);
      $secondary_contact->save(); 
    }
    return $obj_owner->id;
  }
  
  public function editOwner(array $request_data){ //dd($request_data);
    $obj_owner= Owner::find($request_data['owner-id']);
		// if(isset($obj_owner)){
		// 	$obj_owner= new Owner; 
		// }
		$obj_owner= new Owner; 
    $obj_owner->name = $this->inputValue($request_data, ['owner']);
    $obj_owner->contact_number_1 = $this->inputValue($request_data, ['contact']);
    $obj_owner->email_1 = $this->inputValue($request_data, ['email']); 
    $obj_owner->save();
    
    $secondary_contact = SecondaryContact::where('owner_id', $obj_owner->id)->delete();
      
    if($this->inputValue($request_data, ['name']) != '' || $this->inputValue($request_data, ['number']) != '' || $this->inputValue($request_data, ['email-id']) != ''){
      $secondary_contact = new SecondaryContact;
      $secondary_contact->owner_id = $obj_owner->id;
      $secondary_contact->name = $this->inputValue($request_data, ['name']);
      $secondary_contact->email = $this->inputValue($request_data, ['email-id']);
      $secondary_contact->contact = $this->inputValue($request_data, ['number']);
      $secondary_contact->save(); 
    }
    return $obj_owner->id;
  }

  private function inputValue(array $data, array $keys, $default = '')
  {
    foreach($keys as $key){
      if(array_key_exists($key, $data) && $data[$key] !== null){
        return $data[$key];
      }
    }

    return $default;
  }

  private function intValue(array $data, array $keys, $default = 0)
  {
    $value = $this->inputValue($data, $keys, $default);
    return $value === '' ? $default : (int) $value;
  }

  private function numericValue(array $data, array $keys, $default = 0)
  {
    $value = $this->inputValue($data, $keys, $default);
    return $value === '' ? $default : (float) $value;
  }

  private function dateValue(array $data, $key)
  {
    $value = $this->inputValue($data, [$key]);

    if($value != '' && $value != '01-01-1970') {
      return date('Y-m-d', strtotime($value));
    }

    return '0000-00-00 00:00:00';
  }

  private function savePlaneImages($plane_id, $images, $replace = true)
  {
    if($replace){
      PlaneImage::where('plane_id', $plane_id)->delete();
    }

    if(!is_array($images)){
      return;
    }

    foreach($images as $image){
      $image = trim((string) $image);

      if($image == ''){
        continue;
      }

      $obj_plane_image = new PlaneImage();
      $obj_plane_image->plane_id = $plane_id;
      $obj_plane_image->images = $image;
      $obj_plane_image->save();
    }
  }
}
