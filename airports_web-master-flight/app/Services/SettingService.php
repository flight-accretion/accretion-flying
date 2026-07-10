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
			'from-date.date_format' => 'Please enter from date in dd-mm-yyyy format',
			'to-date.date_format' => 'Please enter to date in dd-mm-yyyy format',
		];

		$validator = Validator::make($data, [
			'type' => 'required|in:0,1',
      'from-date' => 'required|date_format:d-m-Y',
      'to-date' => 'required|date_format:d-m-Y',
      'amount' => 'required_if:type,1|nullable|numeric|min:0',
      'gst-rate' => 'required_if:type,0|nullable|numeric|min:0',
      'igst-rate' => 'required_with:igst-check'
		], $messages);

    $validator->after(function($validator) use ($data) {
      $from_date = $this->parseSettingDate(isset($data['from-date']) ? $data['from-date'] : null);
      $to_date = $this->parseSettingDate(isset($data['to-date']) ? $data['to-date'] : null);

      if($from_date && $to_date && $to_date <= $from_date){
        $validator->errors()->add('to-date', 'To date must be after from date.');
      }
    });
    
		return $validator;
	}

  public function databaseDate($value)
  {
    $date = $this->parseSettingDate($value);

    if($date){
      return $date->format('Y-m-d');
    }

    return date('Y-m-d', strtotime($value));
  }
  
	// add Setting
	public function addSetting(array $request_data) {
    $obj_setting= new Setting;
    $obj_setting->setting_type = $request_data['type'];
    
    $obj_setting->to_date = $this->databaseDate($request_data['to-date']);
    $obj_setting->from_date = $this->databaseDate($request_data['from-date']);
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
    $obj_setting->to_date = $this->databaseDate($request_data['to-date']);
    $obj_setting->from_date = $this->databaseDate($request_data['from-date']);
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

  private function parseSettingDate($value)
  {
    if(!$value){
      return null;
    }

    $date = \DateTime::createFromFormat('d-m-Y', $value);
    $errors = \DateTime::getLastErrors();
    $has_errors = is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0);

    if($date && !$has_errors){
      $date->setTime(0, 0, 0);
      return $date;
    }

    return null;
  }
}
