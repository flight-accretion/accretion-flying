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
use FlyingCalculation\City;
use FlyingCalculation\Airport;
use FlyingCalculation\Plane;
use FlyingCalculation\HandlingCharge;

use FlyingCalculation\Services\CityService;

class CityController extends Controller
{
  public function __construct( Guard $auth, CityService $city_service){
		$this->auth = $auth;
		$this->city_service = $city_service;
	}

  //View all States
  public function getIndex() 
	{
		if(Auth::check() && Auth::User()->user_type == 0)
    {
      $this->city_service->backfillUnknownCityStates('India', 'IN', 50);
      $cities = City::orderBy('created_at', 'desc')->get();
      $states = State::orderBy('created_at', 'desc')->pluck('name','id');
      
			return view('admin.view_cities')
			->with('cities', $cities)
			->with('states', $states)
			->with('menu', 'city')
      ->with('sub_menu', 'view_cities');
    }
    else
    {
      return redirect()->to('/');
    }
	}
	
  public function getHandlingCharge()
  { 
    if(Auth::check())
    {
      $obj_charges = HandlingCharge::where('airport_id', 0)->first();
      if(empty($obj_charges)){
        $obj_charges = HandlingCharge::saveGeneralCharge(0);
      }
      
      return view('admin.general_handling_charge')
          ->with('obj_charges', $obj_charges)
          ->with('menu', 'handling_charge')
          ->with('sub_menu', 'view_handling_charge');
    }
    else
    {
      return redirect()->to('/');
    }
  } 
  
  public function postHandlingCharge(Request $request)
  {
    if($request->isMethod('POST')){
        $request_data = $request->all();
      
        $messages = [
        'charges.required' => 'Please enter Charges.',
      ];

      $validator = Validator::make($request_data, [
        'charges' => 'required',
      ], $messages);
    
    	if($validator->fails())
      {
        return redirect()->back()->withErrors($validator)->withInput();
      }
      else
      {
        HandlingCharge::applyGeneralChargeToAirports($request_data['charges']);
      
        return redirect()->back()->with('success', 'Charges updated successfully');
      }
    }
  } 
  
  //get add city
 public function getAdd()
  {
    if(Auth::check())
    {
      $states = State::pluck('name', 'id');
      return view('admin.add_city')
          ->with('states', $states)
          ->with('menu', 'city')
          ->with('sub_menu', 'add_city');
    }
    else
    {
      return redirect()->to('/');
    }
  } 
  
  //Add city 
  public function postAdd(Request $request)
  {
    $request_data = $request->all();
		$validator = $this->city_service->add_city_rules($request_data);
		
		if($validator->fails())
		{ 
			return redirect()->back()->withErrors($validator)->withInput();
		}
		else
		{
			$this->city_service->addCity($request_data);  

			return redirect()->back()->with('success', 'City added successfully');
		}
  }
  
  //Get edit city 
   public function getEdit(Request $request)
  {
    if(Auth::check() && Auth::User()->user_type == 0)
    {
      Session::forget('menu');
      $request_data = $request->all();
      $states = State::orderBy('created_at', 'desc')->pluck('name','id');
      $city = City::find($request_data['city-id']);
      
      $cities = City::orderBy('created_at', 'desc')->get()->keyBy('id');
      
      return view('admin.edit_city')
      ->with('states', $states)
      ->with('cities', $cities)
      ->with('city', $city)
      ->with('menu', 'city')
      ->with('sub_menu', 'edit_city');
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
      $city = City::find($request_data['city-id']);
      $cities = City::orderBy('created_at', 'desc')->get()->keyBy('id');
      
      return view('admin.view_city')
      ->with('states', $states)
      ->with('cities', $cities)
      ->with('city', $city)
      ->with('menu', 'city')
      ->with('sub_menu', 'view_city');
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
    $validator = $this->city_service->edit_city_rules($request_data);
    
    if($validator->fails())
    {
      return redirect()->back()->withErrors($validator)->withInput();
    }
    else
    {  
      	$this->city_service->editCity($request_data);  
        
        return redirect()->back()->with('success', 'City updated successfully');    
    } 
    
  }  

  //Delete property
  public function postDelete(Request $request)
  {
    $request_data = $request->all();
    $city_id = $request_data['city-id']; 
    $airports = Airport::where('status',1)->where('city_id', $city_id)->get();
    $planes = Plane::where('city_id', $city_id)->get();
    //dd(count($planes), count($airports));
    if(count($airports) > 0 || count($planes) > 0) {
      return redirect()->to('city')->with('error', 'This city can not be deleted as it is used either in airports or in planes.');
    } else {
      $property = DB::table('city')->where('id', $city_id)->delete();
      return redirect()->to('city')->with('success', 'City deleted successfully');
    }
  }
}
