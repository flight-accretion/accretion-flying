<?php

namespace FlyingCalculation\Http\Controllers;

use Illuminate\Http\Request;

use FlyingCalculation\Http\Requests;
use FlyingCalculation\Http\Controllers\Controller;

use Auth;
use Session;
use Validator;
use DB;

use FlyingCalculation\Plane;
use FlyingCalculation\PlaneType;
use FlyingCalculation\City;
use FlyingCalculation\Owner;
use FlyingCalculation\PlaneImage;

class AdminController extends Controller
{
  public function getDashboard()
  {
     if (!Auth::check() || Auth::user()->user_type != 0) {
    return redirect('/');
  }

    $cities = DB::Table('city')->pluck('name','id');
    $types = DB::Table('plane_type')->pluck('name','id');
    $plane_subtypes = DB::Table('plane_subtypes')->pluck('sub_type','id');
    $owners = DB::Table('owner')->pluck('name','id');
    $owner_contact = DB::Table('owner')->pluck('contact_number_1','id');
    $planes = Plane::orderBy('created_at')->get();
    
    return view('admin.dashboard')
    ->with('planes', $planes)
    ->with('types', $types)
    ->with('plane_subtypes', $plane_subtypes)
    ->with('cities', $cities)
    ->with('owners', $owners)
    ->with('owner_contact', $owner_contact)
    ->with('menu', 'planes')
    ->with('sub_menu', 'view_planes');
  }
}
