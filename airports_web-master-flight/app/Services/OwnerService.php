<?php namespace FlyingCalculation\Services;

use Validator;

use FlyingCalculation\Owner;
use FlyingCalculation\SecondaryContact;

class OwnerService 
{
	//add Owner rules
	public function add_owner_rules(array $data){
		$messages = [
			'owner.required' => 'Please enter owner name.',
			'contact-1.required' => 'Please enter first contact',
			'email-1.required' => 'Please enter first email',
		];

		$validator = Validator::make($data, [
			'owner' => 'required|min:2|max:50',
			'contact-1' => 'required',
      'email-1' => 'required|email'
		], $messages);
    
		return $validator;
	}
  
	// add Owner
	public function addOwner(array $request_data) {
    $obj_owner= new Owner;
    $obj_owner->name = $request_data['owner'];
    //$obj_owner->other = $request_data['other'];
    $obj_owner->contact_number_1 = $request_data['contact-1'];
   // $obj_owner->contact_number_2 = $request_data['contact-2'];
    $obj_owner->email_1 = $request_data['email-1']; 
   // $obj_owner->email_2 = $request_data['email-2']; 
    $obj_owner->save();
    
    if(isset($request_data['names']) && isset($request_data['contacts']) && isset($request_data['emails'])){
			$names = $request_data['names'];
			$contacts = $request_data['contacts'];
			$emails = $request_data['emails'];
      
      $i = 0;
      foreach ($names as $name) {
        if($name != ''){
          $secondary_contact = new SecondaryContact;
          $secondary_contact->owner_id = $obj_owner->id;
					$secondary_contact->name = $name;
          $secondary_contact->email = isset($emails[$i]) ? $emails[$i] : '';
          $secondary_contact->contact = isset($contacts[$i]) ? $contacts[$i] : '';
					$secondary_contact->save(); 
        }
        $i++;
			}
		}
    
	}
  
  // edit Owner rules
	public function edit_owner_rules(array $data){
		$messages = [
			'owner.required' => 'Please enter owner name.',
			'contact-1.required' => 'Please enter first contact',
			'email-1.required' => 'Please enter first email',
		];

		$validator = Validator::make($data, [
			'owner' => 'required|min:2|max:50',
			'contact-1' => 'required',
      'email-1' => 'required|email'
		], $messages);
    
		return $validator;
	}
  
	// edit Owner
	public function editOwner(array $request_data) {
    $obj_owner= Owner::find($request_data['owner-id']);
    $obj_owner->name = $request_data['owner'];
    //$obj_owner->other = $request_data['other'];
    $obj_owner->contact_number_1 = $request_data['contact-1'];
    //$obj_owner->contact_number_2 = $request_data['contact-2'];
    $obj_owner->email_1 = $request_data['email-1']; 
    //$obj_owner->email_2 = $request_data['email-2']; 
    $obj_owner->save();
    
    $secondary_contact = SecondaryContact::where('owner_id', $obj_owner->id)->delete();
    
    if(isset($request_data['names']) && isset($request_data['contacts']) && isset($request_data['emails'])){
			$names = $request_data['names'];
			$contacts = $request_data['contacts'];
			$emails = $request_data['emails'];
      
      $i = 0;
      foreach ($names as $name) {
        if($name != ''){
          $secondary_contact = new SecondaryContact;
          $secondary_contact->owner_id = $obj_owner->id;
					$secondary_contact->name = $name;
          $secondary_contact->email = isset($emails[$i]) ? $emails[$i] : '';
          $secondary_contact->contact = isset($contacts[$i]) ? $contacts[$i] : '';
					$secondary_contact->save(); 
        }
        $i++;
			}
		}
  }
}
