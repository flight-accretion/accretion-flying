<?php

namespace FlyingCalculation\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;

use FlyingCalculation\Http\Requests;
use FlyingCalculation\Http\Controllers\Controller;

use Auth;
use Session;
use Validator;
use DB;

use FlyingCalculation\Setting;

use FlyingCalculation\Services\SettingService;

class SettingController extends Controller
{
  public function __construct( Guard $auth, SettingService $setting_service){
		$this->auth = $auth;
		$this->setting_service = $setting_service;
	}

  //View all setting
  public function getIndex() 
	{
		if(Auth::check() && Auth::User()->user_type == 0)
    {
      $settings = Setting::orderBy('created_at')->get();
			return view('admin.view_settings')
			->with('settings', $settings)
			->with('menu', 'settings')
      ->with('sub_menu', 'view_settings');
    }
    else
    {
      return redirect()->to('/');
    }
	}
	
  //get add setting
  public function getAdd()
  {
    if(Auth::check())
    {
      Session::forget('menu');
      return view('admin.add_setting')
      ->with('menu', 'settings')
      ->with('sub_menu', 'add_setting');
    }
    else
    {
      return redirect()->to('/');
    }
  } 
  
  //Add Setting 
  public function postAdd(Request $request)
  {
    $request_data = $request->all();
		$validator = $this->setting_service->add_setting_rules($request_data);
		
    $type = isset($request_data['type']) ? $request_data['type'] : null;
    
		if($validator->fails())
		{ 
			return redirect()->back()->withErrors($validator)->withInput();
		}
		else
		{
      $setting = 0;
      $setting += Setting::whereDate('from_date', '<=', date('Y-m-d', strtotime($request_data['from-date'])))
        ->where('setting_type', $type)
        ->whereDate('to_date', '>=', date('Y-m-d', strtotime($request_data['from-date'])))
        ->count();
      $setting += Setting::whereDate('from_date', '<=', date('Y-m-d', strtotime($request_data['to-date'])))
        ->where('setting_type', $type)
        ->whereDate('to_date', '>=', date('Y-m-d', strtotime($request_data['to-date'])))
        ->count();
      if($setting == 0){
        $this->setting_service->addSetting($request_data); 
        return redirect()->back()->with('success', 'Setting added successfully');
      }
      else{
        return redirect()->back()
          ->withInput()
          ->withErrors([
            'to-date' 			=> 'A Settings record already exists for this date range',
            'from-date' 		=> 'A Settings record already exists for this date range',
          ]);
      }
       
		}
  }
  
  //Get edit setting 
  public function getEdit(Request $request)
  {
    if(Auth::check())
    {
      Session::forget('menu');
      $request_data = $request->all();
      $setting = Setting::where('id', $request_data['setting-id'])->first();
      return view('admin.edit_setting')
      ->with('setting', $setting)
      ->with('menu', 'settings');
    }
    else
    {
      return redirect()->to('/');
    }
  } 
  
  //Get edit setting 
  public function getView(Request $request)
  {
    if(Auth::check())
    {
      Session::forget('menu');
      $request_data = $request->all();
      $setting = Setting::where('id', $request_data['setting-id'])->first();
      return view('admin.view_setting')
      ->with('setting', $setting)
      ->with('menu', 'settings');
    }
    else
    {
      return redirect()->to('/');
    }
  } 
  
  //Edit setting 
  public function postEdit(Request $request)
  {
    $request_data = $request->all();
		$validator = $this->setting_service->add_setting_rules($request_data);
    
    $type = isset($request_data['type']) ? $request_data['type'] : null;
		
		if($validator->fails())
		{ 
			return redirect()->back()->withErrors($validator)->withInput();
		}
		else
		{
      $this->setting_service->editSetting($request_data);  
      return redirect()->back()->with('success', 'Setting updated successfully');
		}
  }  
	
  //Delete setting
  public function postDelete(Request $request)
  {
    $request_data = $request->all();
    $setting_id = $request_data['setting-id']; 
    $setting = DB::table('setting')->where('id', $setting_id)->delete();
    return redirect()->to('setting')->with('success', 'Setting deleted successfully');
  }
}
