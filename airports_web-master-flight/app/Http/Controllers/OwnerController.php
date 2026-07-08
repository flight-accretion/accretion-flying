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

use FlyingCalculation\Owner;
use FlyingCalculation\SecondaryContact;

use FlyingCalculation\Services\OwnerService;

class OwnerController extends Controller
{
  public function __construct( Guard $auth, OwnerService $owner_service){
		$this->auth = $auth;
		$this->owner_service = $owner_service;
	}

  //View all owner
  public function getIndex() 
	{
		if(Auth::check() && Auth::User()->user_type == 0)
    {
      $owners = Owner::orderBy('created_at')->get();
			return view('admin.view_owners')
			->with('owners', $owners)
			->with('menu', 'owners')
      ->with('sub_menu', 'view_owners');
    }
    else
    {
      return redirect()->to('/');
    }
	}
	
  //get add owner
  public function getAdd()
  {
    if(Auth::check())
    {
      Session::forget('menu');
      return view('admin.add_owner')
      ->with('menu', 'owners')
      ->with('sub_menu', 'add_owner');
    }
    else
    {
      return redirect()->to('/');
    }
  } 
  
  //Add Owner 
  public function postAdd(Request $request)
  {
    $request_data = $request->all();
		$validator = $this->owner_service->add_owner_rules($request_data);
		
		if($validator->fails())
		{ 
			return redirect()->back()->withErrors($validator)->withInput();
		}
		else
		{
      $this->owner_service->addOwner($request_data);  
      return redirect()->back()->with('success', 'Owner added successfully');
		}
  }
  
  //Get edit owner 
  public function getEdit(Request $request)
  {
    if(Auth::check())
    {
      Session::forget('menu');
      $request_data = $request->all();
      $owner = Owner::where('id', $request_data['owner-id'])->first();
      $secondary_contacts = SecondaryContact::where('owner_id', $request_data['owner-id'])->get();
      return view('admin.edit_owner')
      ->with('owner', $owner)
      ->with('secondary_contacts', $secondary_contacts)
      ->with('menu', 'owners');
    }
    else
    {
      return redirect()->to('/');
    }
  } 
  
  //Get edit owner 
  public function getView(Request $request)
  {
    if(Auth::check())
    {
      Session::forget('menu');
      $request_data = $request->all();
      $owner = Owner::where('id', $request_data['owner-id'])->first();
      $secondary_contacts = SecondaryContact::where('owner_id', $request_data['owner-id'])->get();
      return view('admin.view_owner')
      ->with('owner', $owner)
      ->with('secondary_contacts', $secondary_contacts)
      ->with('menu', 'owners');
    }
    else
    {
      return redirect()->to('/');
    }
  } 
  
  //Edit owner 
  public function postEdit(Request $request)
  {
    $request_data = $request->all();
		$validator = $this->owner_service->edit_owner_rules($request_data);
		
		if($validator->fails())
		{ 
			return redirect()->back()->withErrors($validator)->withInput();
		}
		else
		{
      $this->owner_service->editOwner($request_data);  
      return redirect()->back()->with('success', 'Owner updated successfully');
		}
  }  
	
  //Delete owner
  public function postDelete(Request $request)
  {
    $request_data = $request->all();
    $owner_id = $request_data['owner-id']; 
    $plane = DB::table('plane')->where('owner_id', $owner_id)->delete();
    $owner = DB::table('owner')->where('id', $owner_id)->delete();
    return redirect()->to('owner')->with('success', 'Owner deleted successfully');
  }
  
  //Get all owners
  public function getAllOwners(Request $request){
    $request_data = $request->all();
    $data = DB::Table('owner')->where('name', 'like', '%'.$request_data['name_start_with'].'%')->pluck('name','id');
    return $data;
  }
}
