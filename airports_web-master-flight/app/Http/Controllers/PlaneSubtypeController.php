<?php
namespace FlyingCalculation\Http\Controllers;
use Illuminate\Contracts\Auth\Guard;

use Auth;
use DB;
use Session;
use Validator;
use Illuminate\Http\Request;
use FlyingCalculation\Services\PlaneSubtypeService;

use FlyingCalculation\Http\Requests;
use FlyingCalculation\Http\Controllers\Controller;
use FlyingCalculation\PlaneType;
use FlyingCalculation\PlaneSubType;


class PlaneSubtypeController extends Controller
{
    public function __construct( Guard $auth, PlaneSubtypeService $plane_subtype_service){
		$this->auth = $auth;
		$this->plane_subtype_service = $plane_subtype_service;
	}
    
  //View all plane-subtype
  public function getIndex() 
	{
		if(Auth::check() && Auth::User()->user_type == 0)
    {
        $types = DB::Table('plane_type')->pluck('name','id');
        $subtypes = DB::Table('plane_subtypes')->get();
      
			return view('admin.view_planes_subtypes')
			->with('types', $types)
			->with('subtypes', $subtypes)
			->with('menu', 'planes_subtype')
            ->with('sub_menu', 'view_planes_subtype');
    }
    else
    {
      return redirect()->to('/');
    }
	
	}

     //get add plane
  public function getAdd()
  {
    $plane_types = PlaneType::orderBy('created_at')->get();
    
    if(Auth::check())
    {
      Session::forget('menu');
      return view('admin.add_plane_subtype')
      ->with('plane_types', $plane_types)
      ->with('menu', 'planes_subtype')
      ->with('sub_menu', 'add_plane_subtype');
    }
    else
    {
      return redirect()->to('/');
    }
  } 
  
  //Add Plane 
  public function postAdd(Request $request)
  { 
    $request_data = $request->all(); 
    $validator = $this->plane_subtype_service->add_plane_subtype_rules($request_data);
		
		if($validator->fails())
		{ 
			return redirect()->back()->withErrors($validator)->withInput();
		}
		else
		{
      $this->plane_subtype_service->addPlaneSubtype($request_data);  
      return redirect()->back()->with('success', 'Plane Subtype added successfully');
		}
  }

  public function getEdit(Request $request)
  {
    // dd($request->all());
    if(Auth::check())
    {
      Session::forget('menu');
      $request_data = $request->all();
      $plane_types = DB::Table('plane_type')->pluck('name','id');
      $plane = DB::Table('plane_subtypes')->where('id', $request_data['subtype-id'])->first();
      
      return view('admin.edit_plane_subtype')
      ->with('plane', $plane)
      ->with('plane_types', $plane_types)
      ->with('menu', 'planes_subtype')
      ->with('sub_menu', 'edit_plane_subtype');
    }
    else
    {
      return redirect()->to('/');
    }
  } 
  
  // Edit plane 
  public function postEdit(Request $request)
  {
    $request_data = $request->all();
		$validator = $this->plane_subtype_service->edit_plane_subtype_rules($request_data);
		
		if($validator->fails())
		{ 
			return redirect()->back()->withErrors($validator)->withInput();
		}
		else
		{
      $this->plane_subtype_service->editPlaneSubtype($request_data);  
      return redirect()->back()->with('success', 'Plane SubType updated successfully');
		}
  }  
	
    //Delete plane
  public function postDelete(Request $request)
  {
    $request_data = $request->all();
    $plane_subtype_id = $request_data['plane-id']; 
    $plane = DB::table('plane_subtypes')->where('id', $plane_subtype_id)->delete();
    return redirect()->to('subtype')->with('success', 'Plane Subtype deleted successfully');
  }

}
