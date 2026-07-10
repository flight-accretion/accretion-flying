<?php

namespace FlyingCalculation\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;

use FlyingCalculation\Http\Requests;
use FlyingCalculation\Http\Controllers\Controller;
use FlyingCalculation\Classes\UploadHandler;

use Auth;
use Session;
use Validator;
use DB;

use FlyingCalculation\Country;
use FlyingCalculation\State;
use FlyingCalculation\Airport;
use FlyingCalculation\City;
use FlyingCalculation\HandlingCharge;

use FlyingCalculation\Services\AirportService;

class AirportController extends Controller
{
  public function __construct( Guard $auth, AirportService $airport_service){
		$this->auth = $auth;
		$this->airport_service = $airport_service;
	}

  //View all Airports
  // public function getIndex() 
	// {
	// 	if(Auth::check() && Auth::User()->user_type == 0)
  //   {
  //     $cities = City::orderBy('created_at', 'desc')->get()->keyby('id');
  //     $airports = Airport::where('status',1)->orderBy('created_at', 'desc')->get();
  //     $charges = HandlingCharge::orderBy('created_at', 'desc')->pluck('charges','airport_id');
      
	// 		return view('admin.view_airports')
	// 		->with('cities', $cities)
	// 		->with('airports', $airports)
	// 		->with('charges', $charges)
	// 		->with('menu', 'airports')
  //     ->with('sub_menu', 'view_airports');
  //   }
  //   else
  //   {
  //     return redirect()->to('/');
  //   }
	// }

  public function getIndex() 
{
    if(Auth::check() && Auth::User()->user_type == 0)
    {
        $cities = City::orderBy('created_at', 'desc')->get()->keyBy('id');
        $airports = Airport::orderBy('created_at', 'desc')->get(); // include all, not just status=1
        $charges = HandlingCharge::orderBy('created_at', 'desc')->pluck('charges','airport_id');

        return view('admin.view_airports')
            ->with('cities', $cities)
            ->with('airports', $airports)
            ->with('charges', $charges)
            ->with('menu', 'airports')
            ->with('sub_menu', 'view_airports');
    }
    else
    {
        return redirect()->to('/');
    }
}
	
  //get add airport
 public function getAdd()
  {
    if(Auth::check())
    {
      $states = State::pluck('name', 'id');
      $cities = City::orderBy('created_at', 'desc')->get()->keyBy('id');
      return view('admin.add_airport')
          ->with('states', $states)
          ->with('cities', $cities)
          ->with('menu', 'airport')
          ->with('sub_menu', 'add_airport');
    }
    else
    {
      return redirect()->to('/');
    }
  } 
  
  //Add airport 
  public function postAdd(Request $request)
  {
    $request_data = $request->all();
		$validator = $this->airport_service->add_airport_rules($request_data);
		
		if($validator->fails())
		{ 
			return redirect()->back()->withErrors($validator)->withInput();
		}
		else
		{
			$this->airport_service->addAirport($request_data);  

			return redirect()->back()->with('success', 'Airport added successfully');
		}
  }
  
  //Get edit airport 
   public function getEdit(Request $request)
  {
    if(Auth::check() && Auth::User()->user_type == 0)
    {
      Session::forget('menu');
      $request_data = $request->all();
      $states = State::orderBy('created_at', 'desc')->pluck('name','id');
      $airport = Airport::find($request_data['airport-id']);
      $cities = City::orderBy('created_at', 'desc')->get()->keyBy('id');
      $obj_charges = HandlingCharge::where('airport_id', $request_data['airport-id'] )->first();
      $general_charges = HandlingCharge::where('airport_id', 0)->first();
      if(!empty($obj_charges)){
        $charges= $obj_charges->charges;
      }
      else{
        $charges =0;
      }
      if(!empty($general_charges) && $general_charges->charges > $charges){
        $charges = $general_charges->charges;
      }
      $crew_handling = ($airport && $airport->crew_handling !== null) ? $airport->crew_handling : 25000;
      
      return view('admin.edit_airport')
      ->with('states', $states)
      ->with('cities', $cities)
      ->with('airport', $airport)
      ->with('charges', $charges)
      ->with('crew_handling', $crew_handling)
      ->with('menu', 'airport')
      ->with('sub_menu', 'edit_airport');
    }
    else
    {
      return redirect()->to('/');
    }
  } 
  
  //Get view
   public function getView(Request $request)
  {
    if(Auth::check() && Auth::User()->user_type == 0)
    {
      Session::forget('menu');
      $request_data = $request->all();
      $states = State::orderBy('created_at', 'desc')->pluck('name','id');
      $airport = Airport::find($request_data['airport-id']);
      $cities = City::orderBy('created_at', 'desc')->get()->keyBy('id');
      $obj_charges = HandlingCharge::where('airport_id', $request_data['airport-id'] )->first();
      $general_charges = HandlingCharge::where('airport_id', 0)->first();
      if(!empty($obj_charges)){
        $charges= $obj_charges->charges;
      }
      else{
        $charges =0;
      }
      if(!empty($general_charges) && $general_charges->charges > $charges){
        $charges = $general_charges->charges;
      }
      $crew_handling = ($airport && $airport->crew_handling !== null) ? $airport->crew_handling : 25000;
      
      return view('admin.view_airport')
      ->with('states', $states)
      ->with('cities', $cities)
      ->with('airport', $airport)
      ->with('charges', $charges)
      ->with('crew_handling', $crew_handling)
      ->with('menu', 'airport')
      ->with('sub_menu', 'view_airport');
    }
    else
    {
      return redirect()->to('/');
    }
  } 
  
  //Edit property 
  public function postEdit(Request $request)
  {
    $request_data = $request->all();
    $validator = $this->airport_service->edit_airport_rules($request_data);
    
    if($validator->fails())
    {
      return redirect()->back()->withErrors($validator)->withInput();
    }
    else
    {
      $airport = Airport::whereRaw("LOWER(name) = '".strtolower($request_data['airport'])."'")->where('id', '!=', $request_data['airport-id'])->get();
      if(count($airport) > 0) {
        $error['airport'] = 'Airport name already exists.';
        return redirect()->back()->withErrors($error)->withInput();
      }
      $this->airport_service->editAirport($request_data);  
      
      return redirect()->back()->with('success', 'Airport updated successfully');    
    } 
    
  }  

  //Delete property
  public function postDelete(Request $request)
  {
    $request_data = $request->all();
    $airport_id = $request_data['airport-id']; 
    $property = DB::table('airport')->where('id', $airport_id)->delete();
    return redirect()->to('airport')->with('success', 'Airport deleted successfully');
  }
}
