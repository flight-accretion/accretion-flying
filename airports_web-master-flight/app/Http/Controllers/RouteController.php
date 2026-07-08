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

use FlyingCalculation\Route;
use FlyingCalculation\Airport;
use FlyingCalculation\Plane;

use FlyingCalculation\Services\RouteService;

class RouteController extends Controller
{
  public function __construct( Guard $auth, RouteService $route_service){
		$this->auth = $auth;
		$this->route_service = $route_service;
	}

  //View all route
  public function getIndex() 
	{
		if(Auth::check() && Auth::User()->user_type == 0)
    {
      $routes = Route::orderBy('created_at')->get();
      $airports = Airport::where('status',1)->orderBy('created_at')->pluck('name','id');
      $planes = Plane::orderBy('created_at')->pluck('name','id');
			return view('admin.view_routes')
			->with('routes', $routes)
			->with('airports', $airports)
			->with('planes', $planes)
			->with('menu', 'routes')
      ->with('sub_menu', 'view_routes');
    }
    else
    {
      return redirect()->to('/');
    }
	}
	
  //get add route
  public function getAdd()
  {
    $airports = Airport::where('status',1)->orderby('created_at')->get(); 
    $planes = Plane::orderby('created_at')->get(); 
    
    if(Auth::check())
    {
      Session::forget('menu');
      return view('admin.add_route')
			->with('airports', $airports)
			->with('planes', $planes)
      ->with('menu', 'routes')
      ->with('sub_menu', 'add_route');
    }
    else
    {
      return redirect()->to('/');
    }
  } 
  
  //Add Route 
  public function postAdd(Request $request)
  {
    $request_data = $request->all();
		$validator = $this->route_service->add_route_rules($request_data);
		
		if($validator->fails())
		{ 
			return redirect()->back()->withErrors($validator)->withInput();
		}
		else
		{
      $this->route_service->addRoute($request_data);  
      return redirect()->back()->with('success', 'Route added successfully');
		}
  }
  
  //Get edit route 
  public function getEdit(Request $request)
  {
    if(Auth::check())
    {
      Session::forget('menu');
      $request_data = $request->all();
      $airports = Airport::where('status',1)->orderBy('created_at')->pluck('name','id');
      $planes = Plane::orderBy('created_at')->pluck('name','id'); 
      $route = Route::where('id', $request_data['route-id'])->first();
      return view('admin.edit_route')
      ->with('airports', $airports)
      ->with('planes', $planes)
      ->with('route', $route)
      ->with('menu', 'routes');
    }
    else
    {
      return redirect()->to('/');
    }
  } 
  
  //Get edit route 
  public function getView(Request $request)
  {
    if(Auth::check())
    {
      Session::forget('menu');
      $request_data = $request->all();
      $route = Route::where('id', $request_data['route-id'])->first();
      $airports = Airport::where('status',1)->orderBy('created_at')->pluck('name','id');
      $planes = Plane::orderBy('created_at')->pluck('name','id'); 
      return view('admin.view_route')
      ->with('airports', $airports)
      ->with('planes', $planes)
      ->with('route', $route)
      ->with('menu', 'routes');
    }
    else
    {
      return redirect()->to('/');
    }
  } 
  
  //Edit route 
  public function postEdit(Request $request)
  {
    $request_data = $request->all();
		$validator = $this->route_service->edit_route_rules($request_data);
		
		if($validator->fails())
		{ 
			return redirect()->back()->withErrors($validator)->withInput();
		}
		else
		{
      $this->route_service->editRoute($request_data);  
      return redirect()->back()->with('success', 'Route updated successfully');
		}
  }  
	
  //Delete route
  public function postDelete(Request $request)
  {
    $request_data = $request->all();
    $route_id = $request_data['route-id']; 
    //$plane = DB::table('plane')->where('route_id', $route_id)->delete();
    $route = DB::table('route')->where('id', $route_id)->delete();
    return redirect()->to('route')->with('success', 'Route deleted successfully');
  }

}
