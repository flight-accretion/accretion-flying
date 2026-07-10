<?php namespace FlyingCalculation\Services;

use Validator;

use FlyingCalculation\Setting;

class SettingService 
{
	//add Setting rules
	public function add_setting_rules(array $data){
		$messages = [
			'type.required' => 'Please select setting type.',
			'to-date.required' => 'Please enter to date.',
			'from-date.required' => 'Please enter from date',
			'amount.required' => 'Please enter amount',
			'amount.numeric' => 'Please enter a valid amount',
			'amount.min' => 'Amount cannot be negative',
			'gst-rate.required_if' => 'Please enter GST rate',
			'gst-rate.numeric' => 'Please enter a valid GST rate',
			'gst-rate.min' => 'GST rate cannot be negative',
		];

		$validator = Validator::make($data, [
			'type' => 'required|in:0,1',
      'from-date' => 'required',
      'to-date' => 'required|date|after:from-date',
      'amount' => 'required_if:type,1|nullable|numeric|min:0',
      'gst-rate' => 'required_if:type,0|nullable|numeric|min:0',
      'igst-rate' => 'required_with:igst-check'
		], $messages);
    
		return $validator;
	}
  
	// add Setting
	public function addSetting(array $request_data) {
    $obj_setting= new Setting;
    $obj_setting->setting_type = $request_data['type'];
    
    $obj_setting->to_date = date('Y-m-d', strtotime($request_data['to-date']));
    $obj_setting->from_date = date('Y-m-d', strtotime($request_data['from-date']));
    if($request_data['type'] == 0) {
      $gst = isset($request_data['gst-rate']) && $request_data['gst-rate'] !== '' ? $request_data['gst-rate'] : 0;
      $obj_setting->cgst = isset($request_data['cgst']) && $request_data['cgst'] !== '' ? $request_data['cgst'] : ($gst / 2);
      $obj_setting->sgst = isset($request_data['sgst']) && $request_data['sgst'] !== '' ? $request_data['sgst'] : ($gst / 2);
      $obj_setting->gst = $gst;
      $obj_setting->igst = isset($request_data['igst-rate']) && $request_data['igst-rate'] !== '' ? $request_data['igst-rate'] : 0;
      $obj_setting->amount = 0;
      $obj_setting->is_percent = 1; 
    }
    if($request_data['type'] == 1) {
      $obj_setting->amount = $request_data['amount'];
      $obj_setting->cgst = 0;
      $obj_setting->sgst = 0;
      $obj_setting->gst = 0;
      $obj_setting->igst = 0;
      $obj_setting->is_percent = 0; 
    }
    $obj_setting->status = 1; 
    $obj_setting->save();
	}
  
 
	// edit Setting
	public function editSetting(array $request_data) {
    $obj_setting= Setting::find($request_data['setting-id']);
    $obj_setting->setting_type = $request_data['type'];
    $obj_setting->to_date = date('Y-m-d', strtotime($request_data['to-date']));
    $obj_setting->from_date = date('Y-m-d', strtotime($request_data['from-date']));
    $obj_setting->amount = isset($request_data['amount']) && $request_data['amount'] !== '' ? $request_data['amount'] : 0;
    if($request_data['type'] == 0) {
      $gst = isset($request_data['gst-rate']) && $request_data['gst-rate'] !== '' ? $request_data['gst-rate'] : 0;
      $obj_setting->cgst = isset($request_data['cgst']) && $request_data['cgst'] !== '' ? $request_data['cgst'] : ($gst / 2);
      $obj_setting->sgst = isset($request_data['sgst']) && $request_data['sgst'] !== '' ? $request_data['sgst'] : ($gst / 2);
      $obj_setting->gst = $gst;
      $obj_setting->igst = isset($request_data['igst-rate']) && $request_data['igst-rate'] !== '' ? $request_data['igst-rate'] : 0;
      $obj_setting->amount = 0;
      $obj_setting->is_percent = 1; 
    }
    if($request_data['type'] == 1) {
      $obj_setting->amount = $request_data['amount'];
      $obj_setting->cgst = 0;
      $obj_setting->sgst = 0;
      $obj_setting->gst = 0;
      $obj_setting->igst = 0;
      $obj_setting->is_percent = 0; 
    }
    $obj_setting->status = $request_data['status']; 
    $obj_setting->save();
  }
}
