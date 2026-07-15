<?php

namespace FlyingCalculation\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;

use FlyingCalculation\Http\Requests;
use FlyingCalculation\Http\Controllers\Controller;
use FlyingCalculation\Classes\UploadHandler;
use FlyingCalculation\Exports\MachineDetailsExport;

use Auth;
use Mail;
use Session;
use Validator;
use DB;
use DateTime;
use Excel;
use Log;

use FlyingCalculation\BookingSetting;
use FlyingCalculation\User;
use FlyingCalculation\Booking;
use FlyingCalculation\Plane;
use FlyingCalculation\PlaneType;
use FlyingCalculation\PlaneSubtype;
use FlyingCalculation\City;
use FlyingCalculation\Route;
use FlyingCalculation\Owner;
use FlyingCalculation\PlaneImage;
use FlyingCalculation\SecondaryContact;
use FlyingCalculation\Airport;
use FlyingCalculation\MailContent;

use FlyingCalculation\Services\PlaneService;

class PlaneController extends Controller
{
  	public function __construct( Guard $auth, PlaneService $plane_service){
		$this->auth = $auth;
		$this->plane_service = $plane_service;
	}
  
  //View all plane
  public function getIndex() 
	{
		if(Auth::check() && Auth::User()->user_type == 0)
    {
      $cities = DB::Table('city')->pluck('name','id');
      $types = DB::Table('plane_type')->pluck('name','id');
      $plane_subtypes = DB::Table('plane_subtypes')->pluck('sub_type','id');
      $owners = DB::Table('owner')->pluck('name','id'); 
      $owner_contact = DB::Table('owner')->pluck('contact_number_1','id');
      $planes = Plane::orderBy('created_at')->get();
      
			return view('admin.view_planes')
			->with('planes', $planes)
			->with('types', $types)
			->with('plane_subtypes', $plane_subtypes)
			->with('cities', $cities)
			->with('owners', $owners)
			->with('owner_contact', $owner_contact)
			->with('menu', 'planes')
      ->with('sub_menu', 'view_planes');
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
    // $call_sign = PlaneType::orderBy('created_at')->get();
    $plane_subtypes = PlaneSubtype::orderBy('created_at')->get();
    $cities = City::orderBy('created_at')->get();
    $owners = Owner::select('*','owner.id as own_id','owner.name as name','secondary_contact.name as sec_name')->leftJoin('secondary_contact','owner.id','=','secondary_contact.owner_id')->get()->keyby('own_id');
    $owner_first = Owner::orderBy('created_at')->first();
    if($owner_first){
      $owner_secondary_contacts = SecondaryContact::Where('owner_id',$owner_first->id)->get();
    }
    else{
      $owner_secondary_contacts = collect();
    }
    
    $airports = Airport::where('status',1)->orderBy('created_at')->get()->keyby('id'); 
    
    if(Auth::check())
    {
      Session::forget('menu');
      return view('admin.add_plane')
      ->with('plane_types', $plane_types)
      ->with('plane_subtypes', $plane_subtypes)
      ->with('cities', $cities)
      ->with('airports', $airports)
      ->with('owners', $owners)
      ->with('owner_secondary_contacts', $owner_secondary_contacts)
      ->with('menu', 'planes')
      ->with('sub_menu', 'add_plane');
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
		$validator = $this->plane_service->add_plane_rules($request_data);
		
		if($validator->fails())
		{ 
			return redirect()->back()->withErrors($validator)->withInput();
		}
		else
		{
      $this->plane_service->addPlane($request_data);  
      return redirect()->back()->with('success', 'Plane added successfully');
		}
  }
  
  //Get edit plane 
  public function getEdit(Request $request)
  {
    if(Auth::check())
    {
      Session::forget('menu');
      $request_data = $request->all();
      $cities = DB::Table('city')->pluck('name','id');
      $plane_types = DB::Table('plane_type')->pluck('name','id');
	  $plane_subtypes = PlaneSubtype::orderBy('created_at')->get();
      $owners = Owner::select('*','owner.id as own_id','owner.name as name','secondary_contact.name as sec_name')->leftJoin('secondary_contact','owner.id','=','secondary_contact.owner_id')->get()->keyby('own_id');
      $plane_images = DB::Table('plane_image')->where('plane_id', $request_data['plane-id'])->pluck('images','id');
      $all_owners = Owner::orderBy('created_at')->get();
      $owner_id = null;
      
       if($owners->isNotEmpty()){
        $owner_id = Plane::where('id', '=', $request_data['plane-id'])
                    ->value('owner_id');
        $owner_secondary_contacts = SecondaryContact::Where('owner_id',$owner_id)->get();
      }
      else{
        $owner_secondary_contacts = collect();
      }
      $airports = Airport::where('status',1)->orderBy('created_at')->get()->keyby('id'); 
      $plane = Plane::where('id', $request_data['plane-id'])->first();
      
      return view('admin.edit_plane')
      ->with('plane', $plane)
      ->with('plane_types', $plane_types)
      ->with('plane_subtypes', $plane_subtypes)
      ->with('plane_images', $plane_images)
			->with('cities', $cities)
			->with('airports', $airports)
			->with('owners', $owners)
      ->with('owner_secondary_contacts', $owner_secondary_contacts)
			->with('all_owners', $all_owners)
			->with('owner_id', $owner_id)
      ->with('menu', 'planes')
      ->with('sub_menu', 'edit_plane');
    }
    else
    {
      return redirect()->to('/');
    }
  } 
  
  //Edit plane 
  public function postEdit(Request $request)
  {
    $request_data = $request->all();
		$validator = $this->plane_service->edit_plane_rules($request_data);
		
		if($validator->fails())
		{ 
			return redirect()->back()->withErrors($validator)->withInput();
		}
		else
		{
      $this->plane_service->editPlane($request_data);  
      return redirect()->back()->with('success', 'Plane updated successfully');
		}
  }  
	
  //Delete plane
  public function postDelete(Request $request)
  {
    $request_data = $request->all();
    $plane_id = $request_data['plane-id']; 
    $plane = DB::table('plane')->where('id', $plane_id)->delete();
    return redirect()->to('plane')->with('success', 'Plane deleted successfully');
  }
  
   //Upload image
	public function anyUpload(Request $request)
	{
		$upload_dir = public_path('uploads');

		if($request->isMethod('delete')) {
			$file_name = basename((string) $request->query('file'));

			if($file_name != '') {
				$file_path = $upload_dir . DIRECTORY_SEPARATOR . $file_name;
				$thumbnail_path = $upload_dir . DIRECTORY_SEPARATOR . 'thumbnail' . DIRECTORY_SEPARATOR . $file_name;

				if(is_file($file_path)) {
					@unlink($file_path);
				}

				if(is_file($thumbnail_path)) {
					@unlink($thumbnail_path);
				}
			}

			return response()->json(true);
		}

		if(!$request->hasFile('files')) {
			return response()->json(['files' => [['error' => 'No file was uploaded.']]], 422);
		}

		if(!is_dir($upload_dir)) {
			mkdir($upload_dir, 0755, true);
		}

		$uploaded_files = $request->file('files');
		$uploaded_files = is_array($uploaded_files) ? $uploaded_files : [$uploaded_files];
		$files = [];

		foreach($uploaded_files as $uploaded_file) {
			if(!$uploaded_file || !$uploaded_file->isValid()) {
				$files[] = ['error' => 'Image upload failed.'];
				continue;
			}

			$mime_type = $uploaded_file->getMimeType();
			$extension = strtolower($uploaded_file->getClientOriginalExtension() ?: $uploaded_file->guessExtension());
			$allowed_extensions = ['jpg', 'jpeg', 'png'];

			if(!in_array($extension, $allowed_extensions) || !@getimagesize($uploaded_file->getRealPath())) {
				$files[] = ['error' => 'Only JPG, JPEG and PNG images are allowed.'];
				continue;
			}

			if($uploaded_file->getSize() > (5 * 1024 * 1024)) {
				$files[] = ['error' => 'Image size should be within 5MB.'];
				continue;
			}

			$file_name = \Illuminate\Support\Str::random(16) . '.' . $extension;

			while(file_exists($upload_dir . DIRECTORY_SEPARATOR . $file_name)) {
				$file_name = \Illuminate\Support\Str::random(16) . '.' . $extension;
			}

			$uploaded_file->move($upload_dir, $file_name);
			$file_path = $upload_dir . DIRECTORY_SEPARATOR . $file_name;

			$files[] = [
				'name' => $file_name,
				'size' => filesize($file_path),
				'type' => $mime_type,
				'url' => '/uploads/' . $file_name,
				'deleteUrl' => '/plane/upload?file=' . rawurlencode($file_name),
				'deleteType' => 'DELETE',
			];
		}

		return response()->json(['files' => $files]);
	}
  
  public function getView(Request $request)
  {
    $request_data = $request->all();
    $cities = DB::Table('city')->pluck('name','id');
    $plane_types = DB::Table('plane_type')->pluck('name','id');
    $owners = DB::Table('owner')->pluck('name','id');
    $plane_images = DB::Table('plane_image')->where('plane_id', $request_data['plane-id'])->pluck('images','id');
    
    $owner_id = Plane::where('id', '=', $request_data['plane-id'])
                ->value('owner_id');
    $owner = Owner::where('id',$owner_id)->first();
    $owner_secondary_contacts = SecondaryContact::Where('owner_id',$owner_id)->get();
    $airports = Airport::where('status',1)->orderBy('created_at')->get()->keyby('id');   
    
    if(Auth::check())
    {
      Session::forget('menu');
      $request_data = $request->all();
      $plane = Plane::where('id', $request_data['plane-id'])->first();
      
      return view('admin.view_plane')
      ->with('plane', $plane)
      ->with('plane_types', $plane_types)
      ->with('plane_images', $plane_images)
			->with('cities', $cities)
			->with('owners', $owners)
			->with('airports', $airports)
      ->with('owner_secondary_contacts', $owner_secondary_contacts)
			->with('owner_id', $owner_id)
			->with('owner', $owner)
      ->with('menu', 'plane')
      ->with('sub_menu', 'edit_plane');
    }
    else
    {
      return redirect()->to('/');
    }
  } 
  
  
  //Ownerwise contacts
  public function getOwnerwiseContacts(Request $request)
  {
    if(Auth::check() && Auth::User()->user_type == 0)
    {
      Session::forget('menu');
      $request_data = $request->all();

      $owner_id = isset($request_data['id']) ? $request_data['id'] : 0;
      $contacts = DB::table('owner')
        ->where('owner.id', $owner_id)
        ->leftJoin('secondary_contact','owner.id','=','secondary_contact.owner_id')
        ->select('owner.*', 'secondary_contact.name as sec_name', 'secondary_contact.email as sec_email', 'secondary_contact.contact as sec_contact')
        ->get();

      return response()->json($contacts);
    }
    else
    {
      return response()->json([], 401);
    }
  }
  
  //Plane List
  public function getSearch(Request $request)
  {  
    $request_data = array_merge([
			'departure' => 0,
			'arrival' => 0,
			'dep-latitude' => '',
			'dep-longitude' => '',
			'arr-latitude' => '',
			'arr-longitude' => '',
			'adults' => '',
			'round-adults' => '',
			'date' => '',
			'round-date' => '',
			'helicopter-departure' => '',
			'helicopter-arrival' => '',
			'multi-departure' => array(),
			'multi-arrival' => array(),
			'dep-multi-latitude' => array(),
			'dep-multi-longitude' => array(),
			'arr-multi-latitude' => array(),
			'arr-multi-longitude' => array(),
			'helicopter-multi-departure' => array(),
			'helicopter-multi-arrival' => array(),
			'multi-adults' => array(),
			'multi-date' => array(),
		], $request->all()); 

		if(isset($request_data['planes']) && (string)$request_data['planes'] === '3'){
			$request_data['trips'] = 0;
		}

    if(isset($request_data['flower-shower']) && $request_data['flower-shower'] == 1){
      //$from_date = $request_data['from-date'];
      //$to_date = $request_data['to-date'];
      $lat = $request_data['lat-flower-shower'];
      $long = $request_data['long-flower-shower'];
      $location_name = isset($request_data['location-flower-shower']) && trim($request_data['location-flower-shower']) !== '' ? $request_data['location-flower-shower'] : 'Selected Location';
      $flower_shower_time = isset($request_data['flower-shower-time']) ? (int) $request_data['flower-shower-time'] : 0;
      Session::put('flower_shower_time', $flower_shower_time);
      $flower_shower = $request_data['flower-shower'];
      
      $cities = City::get()->keyBy('id');
      $airports = Airport::where('status',1)->orderBy('updated_at')->get()->keyBy('id');
      $plane_types = DB::Table('plane_type')->pluck('name','id');
      $owners = Owner::orderBy('created_at')->get()->keyBy('id');
      return view('plane_list_by_flower_shower')
      ->with('lat', $lat)
      ->with('long', $long)
      ->with('location_name', $location_name)
      ->with('flower_shower_time', $flower_shower_time)
      ->with('airports', $airports)
      ->with('owners', $owners)
      ->with('flower_shower', $flower_shower)
      ->with('plane_types', $plane_types);
    } else { 
		
			$validator = $this->plane_service->search_rules($request_data);
      
      if($validator->fails())
      {
        return redirect()->back()->withErrors($validator)->withInput();
      } else {
				
				$gt1 = 0; 
				$gt2 = 0;
				$gt = array();
				$crew_handling = $this->airportCrewHandlingAmount($request_data['arrival']);
				$crew_handlings = array();
				foreach(Airport::pluck('crew_handling', 'id') as $airport_id => $amount){
					$crew_handlings[$airport_id] = $this->normalizeCrewHandlingAmount($amount);
				}
				if($request_data['planes'] != 2){
					$gt1 = (float) Airport::where('id',$request_data['departure'])->value('gt'); 
					$gt2 = (float) Airport::where('id',$request_data['arrival'])->value('gt');
					$gt = Airport::pluck('gt', 'id');
				}
				$departure = $request_data['departure'];
				$arrival = $request_data['arrival'];
				//$round_departure = $request_data['round-departure'];
				//$round_arrival = $request_data['round-arrival'];
				$dep_latitude = $request_data['dep-latitude'];
				$dep_longitude = $request_data['dep-longitude'];
				$arr_latitude = $request_data['arr-latitude'];
				$arr_longitude = $request_data['arr-longitude'];
				$plane_type = $request_data['planes'];
				$trip_type = $request_data['trips'];
				$adults = $request_data['adults'];
				$round_adults = $request_data['round-adults'];
				$date = $request_data['date'];      
				$round_date = $request_data['round-date'];  
				$helicopter_departure = $request_data['helicopter-departure'];
				$helicopter_arrival = $request_data['helicopter-arrival'];
				
				if(isset($request_data['multi-departure'])){
					$multi_departure = $request_data['multi-departure'];
					$multi_arrival = $request_data['multi-arrival'];
					$dep_multi_latitude = $request_data['dep-multi-latitude'];
					$dep_multi_longitude = $request_data['dep-multi-longitude'];
					$arr_multi_latitude = $request_data['arr-multi-latitude'];
					$arr_multi_longitude = $request_data['arr-multi-longitude'];
					$helicopter_multi_departure = $request_data['helicopter-multi-departure'];
					$helicopter_multi_arrival = $request_data['helicopter-multi-arrival'];
					$multi_adults = $request_data['multi-adults'];
					$multi_date = $request_data['multi-date'];
				}
				else {
					$multi_departure = array();
					$multi_arrival = array();
					$dep_multi_latitude = array();
					$dep_multi_longitude = array();
					$arr_multi_latitude = array();
					$arr_multi_longitude = array();
					$helicopter_multi_departure = array();
					$helicopter_multi_arrival = array();
					$multi_adults = array();
					$multi_date = array();
				}
				
				$cities = City::get()->keyBy('id');
				$airports = Airport::where('status',1)->orderBy('updated_at')->get()->keyBy('id');
				$airports_by_city = DB::Table('airport')->pluck('city_id', 'id');
				$plane_types = DB::Table('plane_type')->pluck('name','id');
				$plane_subtypes = DB::Table('plane_subtypes')->get();
				$total_adults = ($adults > $round_adults) ? $adults : $round_adults;
				$routes_data = Route::select('route.*', 'location_1_air.latitude as location_1_latitude', 'location_1_air.longitude as location_1_longitude', 'location_2_air.latitude as location_2_latitude', 'location_2_air.longitude as location_2_longitude', DB::raw('CONCAT(plane_id,"-",location_1_air.latitude,"-",location_1_air.longitude) as lat_long'))
										->leftJoin('airport as location_1_air', 'location_1_air.id', '=', 'route.location_1')
										->leftJoin('airport as location_2_air', 'location_2_air.id', '=', 'route.location_2')
										->get()
										->keyBy('lat_long');
										
				return view('plane_list')
					->with('departure', $departure)
					->with('arrival', $arrival)
					->with('gt', $gt)
					->with('gt1', $gt1)
					->with('gt2', $gt2)
					->with('crew_handling', $crew_handling)
					->with('crew_handlings', $crew_handlings)
					//->with('round_departure', $round_departure)
					//->with('round_arrival', $round_arrival)
					->with('dep_latitude', $dep_latitude)
					->with('dep_longitude', $dep_longitude)
					->with('arr_latitude', $arr_latitude)
					->with('arr_longitude', $arr_longitude)
					->with('plane_type', $plane_type)
					->with('plane_subtypes', $plane_subtypes)
					->with('routes_data', $routes_data)
					->with('trip_type', $trip_type)
					->with('adults', $adults)
					->with('round_adults', $round_adults)
					->with('total_adults', $total_adults)
					->with('cities', $cities)
					->with('date', $date)
					->with('multi_departure', $multi_departure)
					->with('multi_arrival', $multi_arrival)
					->with('dep_multi_latitude', $dep_multi_latitude)
					->with('dep_multi_longitude', $dep_multi_longitude)
					->with('arr_multi_latitude', $arr_multi_latitude)
					->with('arr_multi_longitude', $arr_multi_longitude)
					->with('helicopter_multi_departure', $helicopter_multi_departure)
					->with('helicopter_multi_arrival', $helicopter_multi_arrival)
					->with('multi_date', $multi_date)
					->with('multi_adults', $multi_adults)
					->with('round_date', $round_date)
					->with('airports', $airports)
					->with('flower_shower', 0)
					->with('plane_types', $plane_types)
					->with('helicopter_departure', $helicopter_departure)
					->with('helicopter_arrival', $helicopter_arrival);
			}
		}
  }
  
  public function getPlaneList(Request $request) {
    $request_data = $request->all();
	// dd($request_data);
    $dep_latitude = $request_data['dep-latitude'];
    $dep_longitude = $request_data['dep-longitude'];
    $arr_latitude = $request_data['arr-latitude'];
    $arr_longitude = $request_data['arr-longitude'];
    $plane_type = $request_data['plane-type'];
    $arrival = $request_data['arrival'];
    $helicopter_arrival = $request_data['helicopter-arrival']; 
    $departure = $request_data['departure'];
    $helicopter_departure = $request_data['helicopter-departure'];
    $trip_type = $request_data['trip-type'];
    $adults = $request_data['total-adults'];
		$filter_id = $request_data['filter-id'];
		$subtypes_filter_id = $request_data['subtypes-filter-id'];
    $gt1 = $gt2 = 0;
		if($plane_type != 2) {
			$gt1 = (float) Airport::where('id',$request_data['departure'])->value('gt'); 
			$gt2 = (float) Airport::where('id',$request_data['arrival'])->value('gt');
		}
    
    $cities = City::get()->keyBy('id');
    $airports = Airport::where('status',1)->orderBy('updated_at')->get()->keyBy('id');
    $airports_by_city = DB::Table('airport')->pluck('city_id', 'id');
    $handling_charges = DB::Table('handling_charges')->pluck('charges', 'airport_id');
    $plane_types = DB::Table('plane_type')->pluck('name','id');
    $plane_subtypes = DB::Table('plane_subtypes')->pluck('sub_type','id');
    $departure_details = DB::Table('airport')->where('id', $departure)->first(); 
    $arrival_details = DB::Table('airport')->where('id', $arrival)->first(); 
    // Get nearest planes to the selected airport
    
		$sort = 'desc';
		if($filter_id == 0){
			$sort = 'asc';
		}
		
		if($plane_type == 2) {
			$avail_planes = DB::table('plane')
			->select(DB::raw('plane.*, 
				city.name as city_name, 
				CASE WHEN from_date <= "'.date('Y-m-d', strtotime($request_data['date'])).'" AND to_date >= "'.date('Y-m-d', strtotime($request_data['date'])).'" AND temporary_city_id != 0 THEN temporary_city_id ELSE city_id END as city_id,
				CASE WHEN from_date <= "'.date('Y-m-d', strtotime($request_data['date'])).'" AND to_date >= "'.date('Y-m-d', strtotime($request_data['date'])).'" AND temporary_airport_id != 0 THEN temporary_airport_id ELSE city_id END as airport_id,
				CASE WHEN from_date <= "'.date('Y-m-d', strtotime($request_data['date'])).'" AND to_date >= "'.date('Y-m-d', strtotime($request_data['date'])).'" THEN temp_latitude ELSE latitude END as latitude,
				CASE WHEN from_date <= "'.date('Y-m-d', strtotime($request_data['date'])).'" AND to_date >= "'.date('Y-m-d', strtotime($request_data['date'])).'" THEN temp_longitude ELSE longitude END as longitude,
				( 6371 * acos( cos( radians('.$dep_latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$dep_longitude.') ) + sin( radians('.$dep_latitude.') ) * sin( radians( latitude ) ) ) ) AS distance'
			))
			->leftJoin('city', 'city.id', '=', DB::raw('city_id'))
			->where('plane.type_id', $plane_type)
			->where('plane.seats', '>=', $adults)
			//->orderBy('distance')
			->orderBy('price_per_hour',$sort);
			//->take(20)
			// ->get();
			$subtype_filter_ids = $this->subtypeFilterIds($subtypes_filter_id);
			if(count($subtype_filter_ids) > 0){
				$avail_planes = $avail_planes->whereIn('subtype',$subtype_filter_ids);
			}
			$avail_planes = $avail_planes->get();
			// Log::info(json_encode($avail_planes));
			// dd($avail_planes,1);
		}
		else {
			$avail_planes = DB::table('plane')
			->select(DB::raw('plane.*, city.name as city_name, ( 6371 * acos( cos( radians('.$dep_latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$dep_longitude.') ) + sin( radians('.$dep_latitude.') ) * sin( radians( latitude ) ) ) ) AS distance'))
			->leftJoin('city', 'city.id', '=', 'plane.city_id')
			->where('plane.type_id', $plane_type)
			->where('plane.seats', '>=', $adults)
			//->orderBy('distance')
			->orderBy('price_per_hour',$sort);
			//->take(20)
			// ->get();
			// Log::info($subtypes_filter_id);
			$subtype_filter_ids = $this->subtypeFilterIds($subtypes_filter_id);
			if(count($subtype_filter_ids) > 0){
				// dd($subtype_filter_ids,2);
				$avail_planes = $avail_planes->whereIn('subtype',$subtype_filter_ids);
			}
			$avail_planes = $avail_planes->get();
			// Log::info(json_encode($avail_planes));
		}
   
    $planes = array();
    // trip_type = 0 => single trip
    // trip_type = 1 => round trip
    // trip_type = 2 => multi city trip
    // plane_type = 1 => plane
    // plane_type = 2 => helicopter
    // plane_type = 3 => air ambulance
    for($i = 0; $i < count($avail_planes); $i++) {
			if($avail_planes[$i]->distance > 50){
				$dep_latitude = $request_data['dep-latitude'];
				$dep_longitude = $request_data['dep-longitude'];
			}
			else{
				$dep_latitude = $avail_planes[$i]->latitude;
				$dep_longitude = $avail_planes[$i]->longitude;
			}
      $plane_details = new PlaneDetails();
      $plane_details->id = $avail_planes[$i]->id;
      $plane_details->type_id = $avail_planes[$i]->type_id;
      $plane_details->subtype = $avail_planes[$i]->subtype;
      $plane_details->name = $avail_planes[$i]->name;
      $plane_details->Call_Sign = $avail_planes[$i]->Call_Sign;
      $plane_details->city_id = $avail_planes[$i]->city_id;
      $plane_details->airport_id = $avail_planes[$i]->airport_id;
			$plane_details->city_name = $avail_planes[$i]->city_name;
			if(isset($cities[$avail_planes[$i]->city_id])){
				$plane_details->city_name = $cities[$avail_planes[$i]->city_id]->name;
			}
      $plane_details->seats = $avail_planes[$i]->seats;
      $plane_details->speed = $avail_planes[$i]->speed;
      $plane_details->lavatory = $avail_planes[$i]->lavatory;
      $plane_details->display_image = $avail_planes[$i]->display_image;
      $plane_details->price_per_hour = $avail_planes[$i]->price_per_hour;
      
      $plane_details->air_departure_lat = $dep_latitude;
      $plane_details->air_departure_lng = $dep_longitude;
      $plane_details->air_arrival_lat = $arr_latitude;
      $plane_details->air_arrival_lng = $arr_longitude;
    
      $plane_details->avail_planes_lat = $avail_planes[$i]->latitude;
      $plane_details->avail_planes_lng = $avail_planes[$i]->longitude;
      
      $plane_details->avail_distance = $avail_planes[$i]->distance;
      
      $plane_details->speed_coefficient = $avail_planes[$i]->speed_coefficient;
      
      $arrival_city = ($plane_type == 2) ? $helicopter_arrival : $cities[$airports_by_city[$arrival]]->name;
      $departure_city = ($plane_type == 2) ? $helicopter_departure : $cities[$airports_by_city[$departure]]->name;
      $plane_details->plane_type = (isset($plane_types[$avail_planes[$i]->type_id]) ? $plane_types[$avail_planes[$i]->type_id]: '');
      $plane_details->subtype = (isset($plane_subtypes[$avail_planes[$i]->subtype]) ? $plane_subtypes[$avail_planes[$i]->subtype]: '');
      $path = $departure_city.' > '.$arrival_city.' > '.$departure_city;
      
      $distance = $this->getDistance($dep_latitude, $dep_longitude, $arr_latitude, $arr_longitude);
      
      $handling_charges_amount = 0;
      //1-plane, 2-helicopter, 3-Air ambulance
      if($plane_type != 2) {
        $handling_charges_amount = (isset($handling_charges[$arrival])? $handling_charges[$arrival] : $handling_charges[0]) + (isset($handling_charges[$departure])? $handling_charges[$departure] : $handling_charges[0]); //dd($handling_charges[0]);
      } else {
        $get_departure = DB::table('airport')
        ->select(DB::raw('airport.*, city.name as city_name, ( 6371 * acos( cos( radians('.$dep_latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$dep_longitude.') ) + sin( radians('.$dep_latitude.') ) * sin( radians( latitude ) ) ) ) AS distance'))
        ->leftJoin('city', 'city.id', '=', 'airport.city_id')
        ->orderBy('distance')
        ->first();
        
        $get_arrival = DB::table('airport')
        ->select(DB::raw('airport.*, city.name as city_name, ( 6371 * acos( cos( radians('.$arr_latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$arr_longitude.') ) + sin( radians('.$arr_latitude.') ) * sin( radians( latitude ) ) ) ) AS distance'))
        ->leftJoin('city', 'city.id', '=', 'airport.city_id')
        ->orderBy('distance')
        ->first(); 
        
        if(($get_departure->latitude != $dep_latitude || $get_departure->longitude != $dep_longitude) &&  $get_departure->distance >5){
          $handling_charges_amount += 15000;
        }
        
        if(($get_arrival->latitude != $arr_latitude || $get_arrival->longitude != $arr_longitude) && $get_arrival->distance > 5){
          $handling_charges_amount += 15000;
        }
        
		if ($get_departure && isset($get_departure->distance) && $get_departure->distance <= 5) {
			$handling_charges_amount += 15000;
		}

        
        if($trip_type == 1){
          if($get_arrival && isset($get_arrival->distance) && $get_arrival->distance <= 5){
            $handling_charges_amount += 15000;
         }
        }
      }

      $current_date = date("Y-m-d");
      $tax_details = DB::table('setting')
      ->where('setting_type', 0)
      ->where('status', 1)
      ->whereDate('from_date', '<=',  $current_date)
      ->whereDate('to_date', '>', $current_date)
      ->first();
      
      // if distance is not 0 then calculate path
      if($avail_planes[$i]->distance > 0) {
        if($trip_type == 1) {
					if($avail_planes[$i]->distance > 50){

						$path = $plane_details->city_name.' > '.$departure_city.' > '.$arrival_city.' > '.$departure_city.' > '.$plane_details->city_name;
						//var_dump($avail_planes[$i]->distance, $path);
					}
					else{
						$path = $departure_city.' > '.$arrival_city.' > '.$departure_city;

					}
          if($avail_planes[$i]->type_id != 2){
						if($avail_planes[$i]->city_id == $cities[$airports_by_city[$departure]]->id) {
							$path = $departure_city.' > '.$arrival_city.' > '.$departure_city;
						}
					}
          //$temp_distance = $this->getDistance($avail_planes[$i]->latitude, $avail_planes[$i]->longitude, $dep_latitude, $dep_longitude);
          //$distance = ($distance * 2) + ($temp_distance * 2);
        } else {

					if($avail_planes[$i]->distance > 50){
						$path = $plane_details->city_name.' > '.$departure_city.' > '.$arrival_city.' > '.$plane_details->city_name;

						if($avail_planes[$i]->type_id != 2){
							if($airports_by_city[$arrival] == $avail_planes[$i]->city_id ){
								$path = $plane_details->city_name.' > '.$departure_city.' > '.$arrival_city;
							}
						}
						//var_dump($avail_planes[$i]->distance, $path);
						//var_dump($airports_by_city[$arrival].'---'.$avail_planes[$i]->city_id );
						//var_dump($avail_planes[$i]->distance, $path. '====');
          }
					else{
						$path = $departure_city.' > '.$arrival_city.' > '.$plane_details->city_name;
					}

          if($avail_planes[$i]->type_id != 2){
						if($avail_planes[$i]->city_id == $cities[$airports_by_city[$departure]]->id) {
							$path = $departure_city.' > '.$arrival_city.' > '.$departure_city;
						}
					}
          /*$distance = $distance + $this->getDistance($avail_planes[$i]->latitude, $avail_planes[$i]->longitude, $dep_latitude, $dep_longitude);
          $distance = $distance + $this->getDistance($arr_latitude, $arr_longitude, $avail_planes[$i]->latitude, $avail_planes[$i]->longitude);*/
        }
        
        if($plane_type != 2) {
					$handling_charges_amount = 0;
					
          $handling_charges_amount = (isset($handling_charges[$departure])? $handling_charges[$departure] : $handling_charges[0]) *($trip_type == 1 ? 2 : 1) + (isset($handling_charges[$arrival])? $handling_charges[$arrival] : $handling_charges[0]) + (isset($handling_charges[$plane_details->airport_id])? $handling_charges[$plane_details->airport_id] : $handling_charges[0]);
        } else {
					$handling_charges_amount = 0;
          $get_departure = DB::table('airport')
					->select(DB::raw('airport.*, city.name as city_name, ( 6371 * acos( cos( radians('.$dep_latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$dep_longitude.') ) + sin( radians('.$dep_latitude.') ) * sin( radians( latitude ) ) ) ) AS distance'))
          ->leftJoin('city', 'city.id', '=', 'airport.city_id')
          ->orderBy('distance')
          ->first();
        
          $get_arrival = DB::table('airport')
          ->select(DB::raw('airport.*, city.name as city_name, ( 6371 * acos( cos( radians('.$arr_latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$arr_longitude.') ) + sin( radians('.$arr_latitude.') ) * sin( radians( latitude ) ) ) ) AS distance'))
          ->leftJoin('city', 'city.id', '=', 'airport.city_id')
          ->orderBy('distance')
          ->first();
        
          if(($get_departure->latitude != $dep_latitude || $get_departure->longitude != $dep_longitude) &&  $get_departure->distance >5){
            $handling_charges_amount += 15000;
          }
        
          if(($get_arrival->latitude != $arr_latitude || $get_arrival->longitude != $arr_longitude) && $get_arrival->distance > 5){
            $handling_charges_amount += 15000;
          }
          
		if ($get_departure && isset($get_departure->distance) && $get_departure->distance <= 5) {
			$handling_charges_amount += 15000;
		}

         
			if ($get_arrival && isset($get_arrival->distance) && $get_arrival->distance <= 5) {
				$handling_charges_amount += 15000;
			}

        }
      }
      
      //$cost = ($distance / $avail_planes[$i]->speed) * $avail_planes[$i]->price_per_hour;
      $plane_details->path = $path;
      $plane_details->handling_charges = $handling_charges_amount;
      $plane_details->medical_cost = $this->medicalCostAmount($avail_planes[$i]->type_id);
			$plane_details->tax = $this->gstRateAmount($tax_details);
      $plane_details->trip_type = $trip_type;
			$plane_details->departure_city_id = 0;
			if($plane_type != 2){
				$plane_details->departure_city_id = $departure_details->city_id;
			}
			$plane_details->arrival_city_id = 0;
			if($plane_type != 2){
				$plane_details->arrival_city_id = $arrival_details->city_id;
			}
      //$plane_details->cost = round($cost);
      $planes[] = $plane_details;
    }
    return response()->json(array('planes' => $planes), 200);
  }
	
	public function getPlaneListMulti(Request $request){
		$request_data = $request->all();
		
		$sort = 'desc';
		$plane_types = DB::Table('plane_type')->pluck('name','id');
		$plane_subtypes = DB::Table('plane_subtypes')->pluck('sub_type','id');
		$request_data['arrival'] = json_decode($request_data['arrival'], true);
		$request_data['departure'] = json_decode($request_data['departure'], true);
		$request_data['dep-multi-latitude'] = json_decode($request_data['dep-multi-latitude'], true);
		$request_data['dep-multi-longitude'] = json_decode($request_data['dep-multi-longitude'], true);
		$request_data['arr-multi-latitude'] = json_decode($request_data['arr-multi-latitude'], true);
		$request_data['arr-multi-longitude'] = json_decode($request_data['arr-multi-longitude'], true);
		$request_data['helicopter-multi-departure'] = json_decode($request_data['helicopter-multi-departure'], true);
		$request_data['helicopter-multi-arrival'] = json_decode($request_data['helicopter-multi-arrival'], true);
		$request_data['date'] = json_decode($request_data['date'], true);
		
		$request_data['adults'] = json_decode($request_data['adults'], true);
		$airports = Airport::where('status',1)->get()->keyBy('id');
		$cities = City::get()->keyBy('id');
		$handling_charges = DB::Table('handling_charges')->pluck('charges', 'airport_id');
		
		//$keys = array_keys($request_data['departure']);
		$keys = array_keys((array)$request_data['departure']);
		$first_index = current($keys);
		$last_index = '';
		foreach($request_data['arr-multi-latitude'] as $index => $arr){
			$last_index = $index;
		}
		$arr_last_lat = $request_data['arr-multi-latitude'][$last_index];
		$arr_last_lng = $request_data['arr-multi-longitude'][$last_index];
		$dep_first_lat = $request_data['dep-multi-latitude'][$first_index];
		$dep_first_lng = $request_data['dep-multi-longitude'][$first_index];
		$tax_details = DB::table('setting')
      ->where('setting_type', 0)
      ->where('status', 1)
      ->whereDate('from_date', '<=',  date('Y-m-d'))
      ->whereDate('to_date', '>', date('Y-m-d'))
      ->first();
			
		$departure_details = DB::Table('airport')->where('id',$request_data['departure'][$first_index])->first(); 
		
		if($request_data['filter-id'] == 0){
			$sort = 'asc';
		}
		$subtypes_filter_id = $request_data['subtypes-filter-id'];
		if($request_data['plane-type'] == 2) {
			$avail_planes = DB::table('plane')
			->select(DB::raw('plane.*, 
				city.name as city_name, 
				CASE WHEN from_date <= "'.date('Y-m-d', strtotime($request_data['date'][$first_index])).'" AND to_date >= "'.date('Y-m-d', strtotime($request_data['date'][$first_index])).'" AND temporary_city_id != 0 THEN temporary_city_id ELSE city_id END as city_id,
				CASE WHEN from_date <= "'.date('Y-m-d', strtotime($request_data['date'][$first_index])).'" AND to_date >= "'.date('Y-m-d', strtotime($request_data['date'][$first_index])).'" AND temporary_airport_id != 0 THEN temporary_airport_id ELSE city_id END as airport_id,
				CASE WHEN from_date <= "'.date('Y-m-d', strtotime($request_data['date'][$first_index])).'" AND to_date >= "'.date('Y-m-d', strtotime($request_data['date'][$first_index])).'" THEN temp_latitude ELSE latitude END as latitude,
				CASE WHEN from_date <= "'.date('Y-m-d', strtotime($request_data['date'][$first_index])).'" AND to_date >= "'.date('Y-m-d', strtotime($request_data['date'][$first_index])).'" THEN temp_longitude ELSE longitude END as longitude,
				( 6371 * acos( cos( radians('.$request_data['dep-multi-latitude'][$first_index].') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$request_data['dep-multi-longitude'][$first_index].') ) + sin( radians('.$request_data['dep-multi-latitude'][$first_index].') ) * sin( radians( latitude ) ) ) ) AS distance'
			))
			->leftJoin('city', 'city.id', '=', 'plane.city_id')
			->where('plane.type_id', $request_data['plane-type'])
			->where('plane.seats', '>=', max($request_data['adults']))
			//->orderBy('distance')
			->orderBy('price_per_hour',$sort);
			//->take(20)
			// ->get();
			$subtype_filter_ids = $this->subtypeFilterIds($subtypes_filter_id);
			if(count($subtype_filter_ids) > 0){
				$avail_planes = $avail_planes->whereIn('subtype',$subtype_filter_ids);
			}
			$avail_planes = $avail_planes->get();
			// dd($avail_planes);
		}
		else {
			$avail_planes = DB::table('plane')
			->select(DB::raw('plane.*, city.name as city_name, ( 6371 * acos( cos( radians('.$request_data['dep-multi-latitude'][$first_index].') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$request_data['dep-multi-longitude'][$first_index].') ) + sin( radians('.$request_data['dep-multi-latitude'][$first_index].') ) * sin( radians( latitude ) ) ) ) AS distance'))
			->leftJoin('city', 'city.id', '=', 'plane.city_id')
			->where('plane.type_id', $request_data['plane-type'])
			->where('plane.seats', '>=', max($request_data['adults']))
			//->orderBy('distance')
			->orderBy('price_per_hour',$sort);
			//->take(20)
			// ->get();
			// Log::info($subtypes_filter_id);
			$subtype_filter_ids = $this->subtypeFilterIds($subtypes_filter_id);
			if(count($subtype_filter_ids) > 0){
				$avail_planes = $avail_planes->whereIn('subtype',$subtype_filter_ids);
			}
			$avail_planes = $avail_planes->get();
			// dd($avail_planes,2);

			// Log::info(json_encode($avail_planes));
		}
		
		
		$planes = array();
		
    for($i = 0; $i < count($avail_planes); $i++) {
			if($avail_planes[$i]->distance > 50){
				$request_data['dep-multi-latitude'][$first_index] = $dep_first_lat;
				$request_data['dep-multi-longitude'][$first_index] =	$dep_first_lng;
				
			}
			else{
				$request_data['dep-multi-latitude'][$first_index] = $avail_planes[$i]->latitude;
				$request_data['dep-multi-longitude'][$first_index] = $avail_planes[$i]->longitude;
			}
			
			$last_index = '';
			foreach($request_data['arr-multi-latitude'] as $index => $dep){
				$last_index = $index;
			}
			$arrival_dst = $this->getDistance($avail_planes[$i]->latitude, $avail_planes[$i]->longitude, $request_data['arr-multi-latitude'][$last_index], $request_data['arr-multi-longitude'][$last_index]);
			
			if($arrival_dst > 25){
				$request_data['arr-multi-latitude'][$last_index] = $arr_last_lat;
				$request_data['arr-multi-longitude'][$last_index] =	$arr_last_lng;
				
			}
			else{
				$request_data['arr-multi-latitude'][$last_index] = $avail_planes[$i]->latitude;
				$request_data['arr-multi-longitude'][$last_index] = $avail_planes[$i]->longitude;
			}
			
			$plane_details = new PlaneDetails();
      $plane_details->id = $avail_planes[$i]->id;
      $plane_details->airport_id = $avail_planes[$i]->airport_id;
      $plane_details->type_id = $avail_planes[$i]->type_id;
	  $plane_details->subtype = $avail_planes[$i]->subtype;
      $plane_details->name = $avail_planes[$i]->name;
      $plane_details->Call_Sign = $avail_planes[$i]->Call_Sign;
      $plane_details->city_id = $avail_planes[$i]->city_id;
			$plane_details->city_name = $avail_planes[$i]->city_name;
			if(isset($cities[$avail_planes[$i]->city_id])){
				$plane_details->city_name = $cities[$avail_planes[$i]->city_id]->name;
			}
      $plane_details->seats = $avail_planes[$i]->seats;
      $plane_details->speed = $avail_planes[$i]->speed;
      $plane_details->lavatory = $avail_planes[$i]->lavatory;
      $plane_details->display_image = $avail_planes[$i]->display_image;
      $plane_details->price_per_hour = $avail_planes[$i]->price_per_hour;
			$plane_details->avail_planes_lat = $avail_planes[$i]->latitude;
      $plane_details->avail_planes_lng = $avail_planes[$i]->longitude;
      
      $plane_details->air_departure_lat = array_values($request_data['dep-multi-latitude']);
      $plane_details->air_departure_lng = array_values($request_data['dep-multi-longitude']);
      $plane_details->air_arrival_lat = array_values($request_data['arr-multi-latitude']);
			
      $plane_details->air_arrival_lng = array_values($request_data['arr-multi-longitude']);
      $plane_details->arrival = array_values($request_data['arrival']);
      $plane_details->departure = array_values($request_data['departure']);
			$plane_details->plane_type = (isset($plane_types[$avail_planes[$i]->type_id]) ? $plane_types[$avail_planes[$i]->type_id]: '');
			$plane_details->subtype = (isset($plane_subtypes[$avail_planes[$i]->subtype]) ? $plane_subtypes[$avail_planes[$i]->subtype]: '');
      $plane_details->avail_distance = $avail_planes[$i]->distance;
			$path = '';
			if($avail_planes[$i]->distance > 0) {
				if(($request_data['plane-type'] == 2 && $avail_planes[$i]->distance > 50) || $request_data['plane-type'] != 2){
					$path = $plane_details->city_name.' > ';
				}
			}
			
			if($request_data['plane-type'] != 2){
				$last_index = '';
				foreach($request_data['departure'] as $index => $dep){
					$path .= $cities[$airports[$dep]->city_id]->name.' > ';
					$last_index = $index;
				}
				if(isset($request_data['arrival'][$last_index])){
					$path .= $cities[$airports[$request_data['arrival'][$last_index]]->city_id]->name;
					if($avail_planes[$i]->city_id != $airports[$request_data['arrival'][$last_index]]->city_id){
						$path .= ' > '.$plane_details->city_name;
					}
				}
			}
			else{
				$last_index = '';
				foreach($request_data['helicopter-multi-departure'] as $index => $dep){
					$path .= $dep.' > ';
					$last_index = $index;
				}
				if(isset($request_data['helicopter-multi-arrival'][$last_index])){
					$path .= $request_data['helicopter-multi-arrival'][$last_index];
					if(isset($plane_details->air_arrival_lat[$last_index]) && isset($plane_details->air_arrival_lng[$last_index])){
						if($avail_planes[$i]->latitude != $plane_details->air_arrival_lat[$last_index] && $avail_planes[$i]->longitude != $plane_details->air_arrival_lng[$last_index]){
							$path .= ' > '.$plane_details->city_name;
						}
					}
				}
			}
			
			$handling_charges_amount = 0;
			
			if($request_data['plane-type'] != 2){
				if(round($avail_planes[$i]->distance) > 0) {
					if(isset($handling_charges[$airports[$request_data['departure'][$first_index]]->id])){
						$handling_charges_amount += $handling_charges[$airports[$request_data['departure'][$first_index]]->id]; 
					}
					else{
						$handling_charges_amount += $handling_charges[0];
					}
					if(isset($handling_charges[$plane_details->airport_id])){
						$handling_charges_amount += $handling_charges[$plane_details->airport_id];
					}
					else{
						$handling_charges_amount += $handling_charges[0];
					}
				}
				foreach($request_data['arrival'] as $arr){
					if(isset($handling_charges[$airports[$arr]->id])){
						$handling_charges_amount += $handling_charges[$airports[$arr]->id];
					}
					else{
						$handling_charges_amount += $handling_charges[0];
					}
				}
			}
			else{
				$last_index = 0;
				foreach($request_data['helicopter-multi-arrival'] as $index => $arr){
					//$handling_charges_amount += 15000;
					$last_index = $index;
				}

				if($handling_charges_amount > 15000){
					$handling_charges_amount -= 15000;
				}

				$dep_latitude = $request_data['dep-multi-latitude'][$first_index];
				$dep_longitude = $request_data['dep-multi-longitude'][$first_index];

				$arr_longitude = $request_data['arr-multi-longitude'][$last_index];
				$arr_latitude = $request_data['arr-multi-latitude'][$last_index];

				$get_departure = DB::table('airport')
					->select(DB::raw('airport.*, city.name as city_name, ( 6371 * acos( cos( radians('.$dep_latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$dep_longitude.') ) + sin( radians('.$dep_latitude.') ) * sin( radians( latitude ) ) ) ) AS distance'))
          ->leftJoin('city', 'city.id', '=', 'airport.city_id')
          ->orderBy('distance')
          ->first();

				$get_arrival = DB::table('airport')
          ->select(DB::raw('airport.*, city.name as city_name, ( 6371 * acos( cos( radians('.$arr_latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$arr_longitude.') ) + sin( radians('.$arr_latitude.') ) * sin( radians( latitude ) ) ) ) AS distance'))
          ->leftJoin('city', 'city.id', '=', 'airport.city_id')
          ->orderBy('distance')
          ->first();

				if(($get_departure->latitude != $dep_latitude || $get_departure->longitude != $dep_longitude) &&  $get_departure->distance >5){
          $handling_charges_amount += 15000;
        }

        if(($get_arrival->latitude != $arr_latitude || $get_arrival->longitude != $arr_longitude) && $get_arrival->distance > 5){
          $handling_charges_amount += 15000;
        }

        if((isset($get_departure)) &&  $get_departure->distance <= 5){
          $handling_charges_amount += 15000;
        }

        if($last_index >= 1){
					foreach($request_data['helicopter-multi-arrival'] as $arr){
						$handling_charges_amount += 15000;
						//$last_index = $index;
					}
					if($handling_charges_amount > 15000){
						$handling_charges_amount -= 15000;
					}
				}

        /*if($trip_type == 1){
          if(count($get_arrival) > 0 &&  $get_arrival->distance <= 5){
            $handling_charges_amount += 15000;
         }
        }*/
			}
			
      $plane_details->speed_coefficient = $avail_planes[$i]->speed_coefficient;
      $plane_details->handling_charges = $handling_charges_amount;
      $plane_details->medical_cost = $this->medicalCostAmount($avail_planes[$i]->type_id);
			$plane_details->tax = $this->gstRateAmount($tax_details);
      $plane_details->trip_type = 2;
			$plane_details->departure_city_id = 0;
			if($request_data['plane-type'] != 2 && isset($departure_details)){
				$plane_details->departure_city_id = $departure_details->city_id;
			}
      $plane_details->path = $path;
			$planes[] = $plane_details;
		}
		return response()->json(array('planes' => $planes), 200);
	}
  
    
  //get plane details
  public function getPlane(Request $request){
    $request_data = $request->all();
	if(isset($request_data['flower-shower']) && (int) $request_data['flower-shower'] === 1) {
    return $this->getFlowerShowerPlaneDetails($request_data);
}
		$routes_data = Route::select('route.*', 'location_1_air.latitude as location_1_latitude', 'location_1_air.longitude as location_1_longitude', 'location_2_air.latitude as location_2_latitude', 'location_2_air.longitude as location_2_longitude', DB::raw('CONCAT(location_1_air.latitude,"-",location_1_air.longitude) as lat_long'))
									->leftJoin('airport as location_1_air', 'location_1_air.id', '=', 'route.location_1')
									->leftJoin('airport as location_2_air', 'location_2_air.id', '=', 'route.location_2')
									->where('route.plane_id', $request_data['plane-id'])
									->get()
									->keyBy('lat_long');
										
    $planes = DB::table('plane')->orderBy('price_per_hour', 'asc')->get();
    $plane_types = DB::Table('plane_type')->pluck('name','id');
    $airports = Airport::where('status',1)->orderBy('updated_at')->get();
    $plane = Plane::find($request_data['plane-id']);
		$medical_cost = $this->medicalCostAmount($plane->type_id);
		if($plane->type_id == 2){
			if(	$plane->from_date <= date('Y-m-d', strtotime($request_data['date'])) && $plane->to_date >= date('Y-m-d', strtotime($request_data['date']))){
				if($plane->temporary_city_id != 0){
					$plane->city_id = $plane->temporary_city_id;
				}
				if($plane->temporary_airport_id != 0){
					$plane->airport_id = $plane->temporary_airport_id;
				}
			}
		}
    $plane_images = PlaneImage::where('plane_id',$request_data['plane-id'])->get();
    $departure = $request_data['departure'];
    $arrival = $request_data['arrival'];
    $latitude = $request_data['latitude'];
    $longitude = $request_data['longitude'];
    $adults = $request_data['adults'];
    $date = $request_data['date'];
    $round_date = $request_data['round-date'];
		
    $travel_distance = $request_data['travel-distance']; 
    $plane_distance = $request_data['plane-distance'];
    $plane_single_distance = $request_data['plane-distance-single'];
    $helicopter_arrival = $request_data['helicopter-arrival']; 
    $helicopter_departure = $request_data['helicopter-departure'];
		$ground_handlings = DB::table('handling_charges')->pluck('charges', 'airport_id');
    $helicopter_dep_lat = $request_data['helicopter-dep-lat'];
    $helicopter_dep_long = $request_data['helicopter-dep-long'];
    $helicopter_arr_lat = $request_data['helicopter-arr-lat'];
    $helicopter_arr_long = $request_data['helicopter-arr-long'];
		
    $plane_type = $request_data['plane-type'];
    $trip_type = $request_data['trip-type'];
    
    $gt1 = $gt2 = 0;
		if($plane->type_id != 2){
			$gt1 = (float) Airport::where('id',$request_data['departure'])->value('gt'); 
			$gt2 = (float) Airport::where('id',$request_data['arrival'])->value('gt');
		}
		
    if($request_data['speed_coefficient'] == 0){
      $speed_coefficient = 1;
    } else {
      $speed_coefficient = $request_data['speed_coefficient'];
    }
    
    $owner_id = Plane::where('id',$plane->id)->pluck('owner_id');
    $owner = Owner::where('id',$owner_id)->first();
    $sec_details = SecondaryContact::where('owner_id',$owner_id)->first();
    $owner_details = array();
    $owner_details['name'] = $owner->name;
    $owner_details['email1'] = $owner->email_1;
    $owner_details['contact1'] = $owner->contact_number_1;
    $owner_details['sec_name'] = $sec_details->name;
    $owner_details['sec_contact'] = $sec_details->contact;
    $owner_details['sec_email'] = $sec_details->email;
    
    
    $export_details = '/plane/machine-details-report?plane-id='.$request_data['plane-id'].'&arrival='.$arrival.'&departure='.$departure.'&adults='.$adults.'&date='.$date.'&round-date='.$round_date.'&plane-type='.$plane_type.'&latitude='.$latitude.'&longitude='.$longitude.'&trip-type='.$trip_type.'&helicopter-departure'.$helicopter_departure.'&helicopter-arrival='.$helicopter_arrival.'&travel-distance='.$travel_distance.'&plane-distance='.$plane_distance.'&plane-distance-single='.$plane_single_distance;
		
		$arrival_airport = Airport::find($arrival); 
		$departure_airport = Airport::find($departure);
		$crew_handling_rate = $this->airportCrewHandlingAmount($arrival);
		
    $cities = City::get()->keyBy('id');
		$route_debug = [];
		$route_debug_entry = function($label, $from_lat, $from_lng, $to_lat, $to_lng, $request_distance) use ($routes_data) {
			$key = $from_lat.'-'.$from_lng;
			$route = isset($routes_data[$key]) ? $routes_data[$key] : null;
			$matched = false;

			if($route) {
				$matched = ((string) $route->location_2_latitude === (string) $to_lat && (string) $route->location_2_longitude === (string) $to_lng);
			}

			return [
				'label' => $label,
				'lookup_key' => $key,
				'target_latitude' => $to_lat,
				'target_longitude' => $to_lng,
				'route_found_for_from_location' => (bool) $route,
				'route_target_latitude' => $route ? $route->location_2_latitude : null,
				'route_target_longitude' => $route ? $route->location_2_longitude : null,
				'route_matched' => $matched,
				'manual_time_minutes' => $matched ? $route->time : null,
				'manual_distance_nm' => $matched ? $route->distance : null,
				'request_distance_nm' => $request_distance,
				'distance_source' => ($matched && $route->distance > 0) ? 'manual route distance' : 'geometry distance',
				'time_source' => $matched ? 'manual route time' : 'geometry + speed time',
			];
		};

		if($plane_type != 2 && $departure_airport && $arrival_airport){
			$route_debug[] = $route_debug_entry('base_to_departure', $plane->latitude, $plane->longitude, $departure_airport->latitude, $departure_airport->longitude, $plane_distance);
			$route_debug[] = $route_debug_entry('departure_to_base', $departure_airport->latitude, $departure_airport->longitude, $plane->latitude, $plane->longitude, $request_data['plane-distance']);
			$route_debug[] = $route_debug_entry('departure_to_arrival', $departure_airport->latitude, $departure_airport->longitude, $arrival_airport->latitude, $arrival_airport->longitude, $travel_distance);
			$route_debug[] = $route_debug_entry('arrival_to_departure', $arrival_airport->latitude, $arrival_airport->longitude, $departure_airport->latitude, $departure_airport->longitude, $request_data['travel-distance']);
			$route_debug[] = $route_debug_entry('arrival_to_base', $arrival_airport->latitude, $arrival_airport->longitude, $plane->latitude, $plane->longitude, $plane_single_distance);
		}
		//$path = $cities[$departure_airport->city_id]->name.' > '.$cities[$arrival_airport->city_id]->name.' > '.$cities[$departure_airport->city_id]->name;
		//$distance = $this->getDistance($departure_airport->latitude, $departure_airport->longitude, $arrival_airport->latitude, $arrival_airport->longitude);
		
		$flights = array();
    
    $plane_hours = $plane_minutes = 0;
		$time = 0;
		if(round($plane_distance) != 0 ){
			if($plane_distance >= 100){
				$time = $gt1 + $gt2 + ((100/(($plane->speed * $speed_coefficient)/60))) + ((100/(($plane->speed * $speed_coefficient)/60))) + (($plane_distance-200)/($plane->speed/60)); 
			}
			else{
				$time = $gt1 + ($plane_distance/(($plane->speed * $speed_coefficient)/60))+$gt2;
			}
		}
		
		if($plane_type != 2){
			if(isset($routes_data[$plane->latitude.'-'.$plane->longitude])){
				if($routes_data[$plane->latitude.'-'.$plane->longitude]->location_2_latitude ==  $departure_airport->latitude && $routes_data[$plane->latitude.'-'.$plane->longitude]->location_2_longitude == $departure_airport->longitude){
					$time = $routes_data[$plane->latitude.'-'.$plane->longitude]->time;
					if($routes_data[$plane->latitude.'-'.$plane->longitude]->distance > 0){
						$plane_distance = $routes_data[$plane->latitude.'-'.$plane->longitude]->distance;
					}
				}
			}
		}
		
		$ctime = $time;
		if($time < 120){			
			if(round($plane_distance) != 0 ){
				//$ctime = 120;
			}
		}
		
    $plane_hours = floor(round($time) / 60);       
    $plane_minutes = round($time)%60;
    $cplane_hours = floor(round($ctime) / 60);       
    $cplane_minutes = round($ctime)%60;
		
		$plane_distance_r = $request_data['plane-distance'];
		$time = 0 ;
		if(round($plane_distance_r) != 0 ){
			if($plane_distance_r >= 100) {
				$time = $gt1 + $gt2 + ((100/(($plane->speed * $speed_coefficient)/60))) + ((100/(($plane->speed * $speed_coefficient)/60))) + (($plane_distance_r-200)/($plane->speed/60)); 
			}
			else{
				$time = $gt1 + ($plane_distance_r/(($plane->speed * $speed_coefficient)/60))+$gt2;
			}
		}
		
		if($plane_type != 2){
			if(isset($routes_data[$departure_airport->latitude.'-'.$departure_airport->longitude])){
				if($routes_data[$departure_airport->latitude.'-'.$departure_airport->longitude]->location_2_latitude ==  $plane->latitude && $routes_data[$departure_airport->latitude.'-'.$departure_airport->longitude]->location_2_longitude == $plane->longitude){
					$time = $routes_data[$departure_airport->latitude.'-'.$departure_airport->longitude]->time;
					if($routes_data[$departure_airport->latitude.'-'.$departure_airport->longitude]->distance > 0) {
						$plane_distance_r = $routes_data[$departure_airport->latitude.'-'.$departure_airport->longitude]->distance;
					}
				}
			}
		}
		
		$ctime = $time;
		if($time < 120){
			if(round($plane_distance) != 0 ){
				//$ctime = 120;
			}
		}
		
		$plane_hours_r = floor(round($time) / 60);       
		$plane_minutes_r = round($time)%60;
		$cplane_hours_r = floor(round($ctime) / 60);       
		$cplane_minutes_r = round($ctime)%60;
		
    $travel_hours = $travel_minutes = 0;
		$time = 0 ;
		if(round($travel_distance) != 0 ){
			if($travel_distance >= 100){
				$time = $gt1 + $gt2 + ((100/(($plane->speed * $speed_coefficient)/60))) + ((100/(($plane->speed * $speed_coefficient)/60))) + (($travel_distance-200)/($plane->speed/60)); 
			}
			else{
				$time = $gt1 + ($travel_distance/(($plane->speed * $speed_coefficient)/60))+$gt2;
			}
		}
		
		if($plane_type != 2){
			if(isset($routes_data[$departure_airport->latitude.'-'.$departure_airport->longitude])){
				if($routes_data[$departure_airport->latitude.'-'.$departure_airport->longitude]->location_2_latitude ==  $arrival_airport->latitude && $routes_data[$departure_airport->latitude.'-'.$departure_airport->longitude]->location_2_longitude == $arrival_airport->longitude){
					$time = $routes_data[$departure_airport->latitude.'-'.$departure_airport->longitude]->time;
					if($routes_data[$departure_airport->latitude.'-'.$departure_airport->longitude]->distance > 0) {
						$travel_distance = $routes_data[$departure_airport->latitude.'-'.$departure_airport->longitude]->distance;
					}
				}
			}
		}
		
		$ctime = $time;
		if($time < 120){
			//$ctime = 120;
		}
		
		
		$travel_hours = floor(round($time) / 60);       
		$travel_minutes = round($time)%60;
		$ctravel_hours = floor(round($ctime) / 60);       
		$ctravel_minutes = round($ctime)%60;
		
		$trav_distance = $request_data['travel-distance'];
		$time = 0 ;
		if(round($trav_distance) != 0 ){
			if($trav_distance >= 100){
				$time = $gt1 + $gt2 + ((100/(($plane->speed * $speed_coefficient)/60))) + ((100/(($plane->speed * $speed_coefficient)/60))) + (($trav_distance-200)/($plane->speed/60)); 
			}
			else{
				$time = $gt1 + ($trav_distance/(($plane->speed * $speed_coefficient)/60))+$gt2;
			}
		}
		
		if($plane_type != 2){	
			if(isset($routes_data[$arrival_airport->latitude.'-'.$arrival_airport->longitude])){
				if($routes_data[$arrival_airport->latitude.'-'.$arrival_airport->longitude]->location_2_latitude ==  $departure_airport->latitude && $routes_data[$arrival_airport->latitude.'-'.$arrival_airport->longitude]->location_2_longitude == $departure_airport->longitude){
					$time = $routes_data[$arrival_airport->latitude.'-'.$arrival_airport->longitude]->time;
					if($routes_data[$arrival_airport->latitude.'-'.$arrival_airport->longitude]->distance > 0){
						$trav_distance = $routes_data[$arrival_airport->latitude.'-'.$arrival_airport->longitude]->distance;
					}
				}
			}
		}
		
		$ctime = $time;
		if($time < 120){
			//$ctime = 120;
		}
		
		
		$trav_hours = floor(round($time) / 60);
		$trav_minutes = round($time)%60;
		$ctrav_hours = floor(round($ctime) / 60);
		$ctrav_minutes = round($ctime)%60;
    
		
    $plane_single_hours = $plane_single_minutes = 0;
		$time = 0 ;
		if(round($plane_single_distance) != 0 ){
			if($plane_single_distance >= 100){
				$time = $gt1 + $gt2 + ((100/(($plane->speed * $speed_coefficient)/60))) + ((100/(($plane->speed * $speed_coefficient)/60))) + (($plane_single_distance-200)/($plane->speed/60)); 
			}
			else{
				$time = $gt1 + ($plane_single_distance/(($plane->speed * $speed_coefficient)/60))+$gt2;
			}
		}
		
		if($plane_type != 2){
			if(isset($routes_data[$arrival_airport->latitude.'-'.$arrival_airport->longitude])){
				if($routes_data[$arrival_airport->latitude.'-'.$arrival_airport->longitude]->location_2_latitude ==  $plane->latitude && $routes_data[$arrival_airport->latitude.'-'.$arrival_airport->longitude]->location_2_longitude == $plane->longitude){
					$time = $routes_data[$arrival_airport->latitude.'-'.$arrival_airport->longitude]->time;
					if($routes_data[$arrival_airport->latitude.'-'.$arrival_airport->longitude]->distance > 0){
						$plane_single_distance = $routes_data[$arrival_airport->latitude.'-'.$arrival_airport->longitude]->distance;
					}
				}
			}
		}
		
		$ctime = $time;
		if($time < 120){
			//$ctime = 120;
		}
		
		
    $plane_single_hours = floor(round($time) / 60);       
    $plane_single_minutes = round($time)%60;
    $cplane_single_hours = floor(round($ctime) / 60);       
    $cplane_single_minutes = round($ctime)%60;
    
		
		$additional_cost = $crew_handling = 0;
		$ground_handling = $arrival_ground_handling = $departure_ground_handling = $plane_ground_handling = 0;
		if($plane_type == 2) {
			$departure_title = $helicopter_departure;
			$arrival_title = $helicopter_arrival;
			$city_check = true;
		} else {
			$departure_title = $cities[$departure_airport->city_id]->name;
			$arrival_title = 	$cities[$arrival_airport->city_id]->name;
			$city_check = ($plane->city_id != $departure_airport->city_id);
			
			if(isset($ground_handlings[$departure_airport->id])) {
				$departure_ground_handling = $ground_handlings[$departure_airport->id];
			}
			elseif(isset($ground_handlings[0])){
				$departure_ground_handling = $ground_handlings[0];
			}
			
			if(isset($ground_handlings[$arrival_airport->id])) {
				$arrival_ground_handling = $ground_handlings[$arrival_airport->id];
			}
			elseif(isset($ground_handlings[0])){
				$arrival_ground_handling = $ground_handlings[0];
			}
			
			if(isset($ground_handlings[$plane->airport_id])) {
				$plane_ground_handling = $ground_handlings[$plane->airport_id];
			}
			elseif(isset($ground_handlings[0])){
				$plane_ground_handling = $ground_handlings[0];
			}
		}
		
		$total_hours = $total_mins = 0;
		//departure ariport to arrival airport
		$flights[0]['departure'] = $departure_title; 
		$flights[0]['arrival'] = $arrival_title;
		$flights[0]['departure_time'] = $date;
		$flights[0]['distance'] = $travel_distance;
		$flights[0]['hours'] = $travel_hours;
		$flights[0]['minutes'] = $travel_minutes;
		//$flights[0]['cost'] = $travel_distance/$plane->speed * $plane->price_per_hour;
		$flights[0]['cost'] = ($ctravel_hours * $plane->price_per_hour) + ($plane->price_per_hour * ($ctravel_minutes / 60)); 
    //dd($flights[0]['cost']);
		$flights[0]['arrival_time'] = date('Y-m-d H:i', strtotime($date."+$travel_hours hours +$travel_minutes minutes"));
		$flights[0]['details'] = 'With Passengers';
		$ground_handling += $arrival_ground_handling;
		
		//arrival airport to departure airport
		$flights[1]['departure'] = $arrival_title;
		$flights[1]['arrival'] = $departure_title;
		$flights[1]['departure_time'] = date('Y-m-d H:i', strtotime($flights[0]['arrival_time']."+1 hours"));
		if($trip_type == 1){
			$flights[1]['departure_time'] = date('Y-m-d H:i', strtotime($round_date));
		}
		$flights[1]['distance'] = $trav_distance;
		$flights[1]['hours'] = $trav_hours;
		$flights[1]['minutes'] = $trav_minutes;
		//$flights[1]['cost'] = $travel_distance/$plane->speed * $plane->price_per_hour;
		$flights[1]['cost'] = ($ctrav_hours * $plane->price_per_hour) + ($plane->price_per_hour * ($ctrav_minutes / 60)); 
		$flights[1]['arrival_time'] = date('Y-m-d H:i', strtotime($flights[1]['departure_time']."+$trav_hours hours +$trav_minutes minutes"));
		$flights[1]['details'] = 'Empty Leg';
		if($trip_type == 1) {
			$flights[1]['details'] = 'With Passengers';
		}
		$ground_handling += $departure_ground_handling;
		$total_hours = $ctrav_hours + $ctravel_hours;
		$total_mins = $ctrav_minutes + $ctravel_minutes;
		
		$halt_start_time = date('Y-m-d H:i:s', strtotime($date."+$travel_hours hours +$travel_minutes minutes"));
		$halt_end_time = date('Y-m-d H:i:s', strtotime($round_date));
		$datetime1 = new DateTime($halt_start_time);

		$datetime2 = new DateTime($halt_end_time);
		$difference = $datetime1->diff($datetime2);
		
		if($difference->d > 0) {
			//$crew_handling = 25000 * $difference->d;
			//$additional_cost += $plane->price_per_hour * 2 * $difference->d;
		}
		elseif($difference->h > 4){
			//$crew_handling = 25000;
		}
		
		if($trip_type == 1){
			$crew_handling = $crew_handling_rate * (float)($request_data['crew-additional-days'] ?? 0);
		}
		//$additional_cost += $plane->price_per_hour * 2 * $request_data['additional-days'];
		
		if($city_check) {
			$total_hours = $total_mins = 0;
			$ground_handling = 0;
			if($trip_type == 1) {
				//if round trip
				$flights =array();
				//$path = $cities[$plane->city_id]->name.' > '.$departure_airport->name.' > '.$arrival_airport->name.' > '.$departure_airport->name.' > '.$cities[$plane->city_id]->name;
				
				//plane base to departure airport
				if($plane_distance != 0){
					$flights[0]['departure'] = $cities[$plane->city_id]->name;
					$flights[0]['arrival'] = $departure_title; //$departure_airport->name;
					$flights[0]['departure_time'] = date('Y-m-d H:i', strtotime($date."-$plane_hours hours -1 hours -$plane_minutes minutes"));
					$flights[0]['distance'] = $plane_distance;
					$flights[0]['hours'] = $plane_hours;
					$flights[0]['minutes'] = $plane_minutes;
					//$flights[0]['cost'] = $plane_distance/$plane->speed * $plane->price_per_hour;
					$flights[0]['cost'] = ($cplane_hours * $plane->price_per_hour) + ($plane->price_per_hour * ($cplane_minutes / 60)); 
					$flights[0]['arrival_time'] = date('Y-m-d H:i', strtotime($date."-1 hours"));
					$flights[0]['details'] = 'Empty Leg';
					$ground_handling += $departure_ground_handling;
				}
				//departure airport to arrival airport
				$flights[1]['departure'] = $departure_title; //$departure_airport->name;
				$flights[1]['arrival'] = $arrival_title; //$arrival_airport->name;
				$flights[1]['departure_time'] = $date;
				$flights[1]['distance'] = $travel_distance;
				$flights[1]['hours'] = $travel_hours;
				$flights[1]['minutes'] = $travel_minutes;
				//$flights[1]['cost'] = $travel_distance/$plane->speed * $plane->price_per_hour;
				$flights[1]['cost'] = ($ctravel_hours * $plane->price_per_hour) + ($plane->price_per_hour * ($ctravel_minutes / 60)); 
				$flights[1]['arrival_time'] = date('Y-m-d H:i', strtotime($date."+$travel_hours hours +$travel_minutes minutes"));
				$flights[1]['details'] = 'With Passengers';
				$ground_handling += $arrival_ground_handling;
				
				//arrival airport to departure airport
				$flights[2]['departure'] = $arrival_title; //$arrival_airport->name;
				$flights[2]['arrival'] =  $departure_title; //$departure_airport->name;
				$flights[2]['departure_time'] = $round_date;
				$flights[2]['distance'] = $trav_distance;
				$flights[2]['hours'] = $trav_hours;
				$flights[2]['minutes'] = $trav_minutes;
				//$flights[2]['cost'] = $travel_distance/$plane->speed * $plane->price_per_hour;
        $flights[2]['cost'] = ($ctrav_hours * $plane->price_per_hour) + ($plane->price_per_hour * ($ctrav_minutes / 60)); 
				$flights[2]['arrival_time'] = date('Y-m-d H:i', strtotime($flights[2]['departure_time']."+$trav_hours hours +$trav_minutes minutes"));
				$flights[2]['details'] = 'With Passengers';
        
        if($plane_type == 2){
					$ground_handling = 0;
          $get_dep_location = DB::table('airport')->select(DB::raw('airport.*, city.name as city_name, ( 6371 * acos( cos( radians('.$helicopter_dep_lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$helicopter_dep_long.') ) + sin( radians('.$helicopter_dep_lat.') ) * sin( radians( latitude ) ) ) ) AS distance'))
          ->leftJoin('city', 'city.id', '=', 'airport.city_id')
          ->orderBy('distance')
          ->first();
          
          $get_arr_location = DB::table('airport')->select(DB::raw('airport.*, city.name as city_name, ( 6371 * acos( cos( radians('.$helicopter_arr_lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$helicopter_arr_long.') ) + sin( radians('.$helicopter_arr_lat.') ) * sin( radians( latitude ) ) ) ) AS distance'))
          ->leftJoin('city', 'city.id', '=', 'airport.city_id')
          ->orderBy('distance')
          ->first();
          
          if(($get_dep_location->latitude != $helicopter_dep_lat || $get_dep_location->longitude != $helicopter_dep_long) && $get_dep_location->distance > 5){
            $ground_handling += 15000; 
          }
          
          if(($get_arr_location->latitude != $helicopter_arr_lat || $get_arr_location->longitude != $helicopter_arr_long) && $get_arr_location->distance > 5){
            $ground_handling += 15000; 
          }
          
          if($get_dep_location && isset($get_dep_location->distance) && $get_dep_location->distance <= 5){
            $ground_handling += 15000;
          }
          
          if($get_arr_location && isset($get_arr_location->distance) && $get_arr_location->distance <= 5){
            $ground_handling += 15000;
          }
          
        } else {
          $ground_handling += $departure_ground_handling;
        }
				
				
				//departure airport to plane base
				if($plane_distance_r != 0){
					$flights[3]['departure'] = $departure_title;//$departure_airport->name;
					$flights[3]['arrival'] = $cities[$plane->city_id]->name;
					$flights[3]['departure_time'] = date('Y-m-d H:i', strtotime($flights[2]['arrival_time']."+1 hours "));
					$flights[3]['distance'] = $plane_distance_r;
					$flights[3]['hours'] = $plane_hours_r;
					$flights[3]['minutes'] = $plane_minutes_r;
					//$flights[3]['cost'] = $plane_distance/$plane->speed * $plane->price_per_hour;
					$flights[3]['cost'] = ($cplane_hours_r * $plane->price_per_hour) + ($plane->price_per_hour * ($cplane_minutes_r / 60)); 
					$flights[3]['arrival_time'] = date('Y-m-d H:i', strtotime($flights[3]['departure_time']."+$plane_hours hours +$plane_minutes minutes"));
					$flights[3]['details'] = 'Empty Leg';
        }
        if($plane_type == 2){
					$ground_handling = 0;
          $get_dep_location = DB::table('airport')->select(DB::raw('airport.*, city.name as city_name, ( 6371 * acos( cos( radians('.$helicopter_dep_lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$helicopter_dep_long.') ) + sin( radians('.$helicopter_dep_lat.') ) * sin( radians( latitude ) ) ) ) AS distance'))
          ->leftJoin('city', 'city.id', '=', 'airport.city_id')
          ->orderBy('distance')
          ->first();
          
          $get_arr_location = DB::table('airport')->select(DB::raw('airport.*, city.name as city_name, ( 6371 * acos( cos( radians('.$helicopter_dep_lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$helicopter_dep_long.') ) + sin( radians('.$helicopter_dep_lat.') ) * sin( radians( latitude ) ) ) ) AS distance'))
          ->leftJoin('city', 'city.id', '=', 'airport.city_id')
          ->orderBy('distance')
          ->first();
          
          if(($get_dep_location->latitude != $helicopter_dep_lat || $get_dep_location->longitude != $helicopter_dep_long) && $get_dep_location->distance > 5){
            $ground_handling += 15000; 
          }
          
          if(($get_arr_location->latitude != $helicopter_arr_lat || $get_arr_location->longitude != $helicopter_arr_long) && $get_arr_location->distance > 5){
            $ground_handling += 15000; 
          }
          
          if($get_dep_location && isset($get_dep_location->distance) && $get_dep_location->distance <= 5){
            $ground_handling += 15000;
          }
          
          if($get_arr_location && isset($get_arr_location->distance) && $get_arr_location->distance <= 5){
            $ground_handling += 15000;
          }
        } else {
          $ground_handling += $plane_ground_handling;
        }
				
				$total_hours += $ctravel_hours + $ctrav_hours;
				$total_mins += $ctravel_minutes+ $ctrav_minutes ;
				$total_hours += $cplane_hours + $cplane_hours_r;
				$total_mins += $cplane_minutes + $cplane_minutes_r;
			} 
			else {
				//$path = $cities[$plane->city_id]->name.' > '.$departure_airport->name.' > '.$arrival_airport->name.' > '.$cities[$plane->city_id]->name;
				$flights = array();
				//plane base to departure airport
				if($plane_distance != 0){
					$flights[0]['departure'] = $cities[$plane->city_id]->name;
					$flights[0]['arrival'] =  $departure_title; //$departure_airport->name;
					$flights[0]['departure_time'] = date('Y-m-d H:i', strtotime($date."-$plane_hours hours -1 hours -$plane_minutes minutes"));
					$flights[0]['distance'] = $plane_distance;
					$flights[0]['hours'] = $plane_hours;
					$flights[0]['minutes'] = $plane_minutes;
					//$flights[0]['cost'] = $plane_distance/$plane->speed * $plane->price_per_hour;
					$flights[0]['cost'] = ($cplane_hours * $plane->price_per_hour) + ($plane->price_per_hour * ($cplane_minutes / 60)); 
					$flights[0]['arrival_time'] = date('Y-m-d H:i', strtotime($date."-1 hours"));
					$flights[0]['details'] = 'Empty Leg';
				}
			
        if($plane_type == 2){ 
					$ground_handling = 0;
          $get_dep_location = DB::table('airport')->select(DB::raw('airport.*, city.name as city_name, ( 6371 * acos( cos( radians('.$helicopter_dep_lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$helicopter_dep_long.') ) + sin( radians('.$helicopter_dep_lat.') ) * sin( radians( latitude ) ) ) ) AS distance'))
          ->leftJoin('city', 'city.id', '=', 'airport.city_id')
          ->orderBy('distance')
          ->first(); 
          
          $get_arr_location = DB::table('airport')->select(DB::raw('airport.*, city.name as city_name, ( 6371 * acos( cos( radians('.$helicopter_arr_lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$helicopter_arr_long.') ) + sin( radians('.$helicopter_arr_lat.') ) * sin( radians( latitude ) ) ) ) AS distance'))
          ->leftJoin('city', 'city.id', '=', 'airport.city_id')
          ->orderBy('distance')
          ->first();
          
          if(($get_dep_location->latitude != $helicopter_dep_lat || $get_dep_location->longitude != $helicopter_dep_long) && $get_dep_location->distance > 5){
            $ground_handling += 15000; 
          }
          
          if(($get_arr_location->latitude != $helicopter_arr_lat || $get_arr_location->longitude != $helicopter_arr_long) && $get_arr_location->distance > 5){
            $ground_handling += 15000; 
          }
          
        //   if(count($get_dep_location) > 0 &&  $get_dep_location->distance <= 5){
        //     $ground_handling += 15000; 
        //   }
		if ($get_dep_location && $get_dep_location->distance <= 5) {
    $ground_handling += 15000;
}

          
        //   if(count($get_arr_location) > 0 &&  $get_arr_location->distance <= 5){
        //     $ground_handling += 15000; 
        //   }


		if ($get_arr_location && $get_arr_location->distance <= 5) {
    $ground_handling += 15000;
}

        } else {
            $ground_handling += $departure_ground_handling;
        }
				
				$total_hours += $cplane_hours;
				$total_mins += $cplane_minutes;
				//departure airport to arrival airport
				$flights[1]['departure'] =  $departure_title; //$departure_airport->name;
				$flights[1]['arrival'] = $arrival_title; //$arrival_airport->name;
				$flights[1]['departure_time'] = $date;
				$flights[1]['distance'] = $travel_distance;
				$flights[1]['hours'] = $travel_hours;
				$flights[1]['minutes'] = $travel_minutes;
				//$flights[1]['cost'] = $travel_distance/$plane->speed * $plane->price_per_hour;
        $flights[1]['cost'] = ($ctravel_hours * $plane->price_per_hour) + ($plane->price_per_hour * ($ctravel_minutes / 60));
				$flights[1]['arrival_time'] = date('Y-m-d H:i', strtotime($date."+$travel_hours hours +$travel_minutes minutes"));
				$flights[1]['details'] = 'With Passengers';
        
        if($plane_type == 2){
					$ground_handling = 0;
          $get_dep_location = DB::table('airport')->select(DB::raw('airport.*, city.name as city_name, ( 6371 * acos( cos( radians('.$helicopter_dep_lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$helicopter_dep_long.') ) + sin( radians('.$helicopter_dep_lat.') ) * sin( radians( latitude ) ) ) ) AS distance'))
          ->leftJoin('city', 'city.id', '=', 'airport.city_id')
          ->orderBy('distance')
          ->first();
          
          $get_arr_location = DB::table('airport')->select(DB::raw('airport.*, city.name as city_name, ( 6371 * acos( cos( radians('.$helicopter_arr_lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$helicopter_arr_long.') ) + sin( radians('.$helicopter_arr_lat.') ) * sin( radians( latitude ) ) ) ) AS distance'))
          ->leftJoin('city', 'city.id', '=', 'airport.city_id')
          ->orderBy('distance')
          ->first();
          
          if(($get_dep_location->latitude != $helicopter_dep_lat || $get_dep_location->longitude != $helicopter_dep_long) && $get_dep_location->distance > 5){
            $ground_handling += 15000; 
          }
          
          if(($get_arr_location->latitude != $helicopter_arr_lat || $get_arr_location->longitude != $helicopter_arr_long) && $get_arr_location->distance > 5){
            $ground_handling += 15000; 
          }
        
        //   if(count($get_dep_location) > 0 &&  $get_dep_location->distance <= 5){
        //     $ground_handling += 15000;
        //   }
		if ($get_dep_location && $get_dep_location->distance <= 5) {
    $ground_handling += 15000;
}

          
        //   if(count($get_arr_location) > 0 &&  $get_arr_location->distance <= 5){
        //     $ground_handling += 15000;
        //   }

		if ($get_arr_location && $get_arr_location->distance <= 5) {
    $ground_handling += 15000;
}

        } else {
          $ground_handling += $arrival_ground_handling;
        }
				$total_hours += $ctravel_hours;
				$total_mins += $ctravel_minutes;
				
				if($plane_type !=2) {
					//$ground_handling = 0;
					if($plane_single_distance != 0){
						if($arrival_airport->latitude != $plane->latitude || $arrival_airport->longitude != $plane->longitude ){
							//arrival airport to plane base
							$flights[2]['departure'] = $arrival_title; //$arrival_airport->name;
							$flights[2]['arrival'] = $cities[$plane->city_id]->name;
							$flights[2]['departure_time'] = date('Y-m-d H:i', strtotime($flights[1]['arrival_time']."+1 hours "));
							$flights[2]['distance'] = $plane_single_distance;
							$flights[2]['hours'] = $plane_single_hours;
							$flights[2]['minutes'] = $plane_single_minutes;
							//$flights[2]['cost'] = $plane_single_distance/$plane->speed * $plane->price_per_hour;
							$flights[2]['cost'] = ($cplane_single_hours * $plane->price_per_hour) + ($plane->price_per_hour * ($cplane_single_minutes / 60));
							$flights[2]['arrival_time'] = date('Y-m-d H:i', strtotime($flights[2]['departure_time']."+$plane_single_hours hours +$plane_single_minutes minutes"));
							$flights[2]['details'] = 'Empty Leg';
							$ground_handling += $plane_ground_handling;
							$total_hours += $cplane_single_hours;
							$total_mins += $cplane_single_minutes;
						}
					}
				}
				else {
					//arrival airport to plane base
					
					if($plane_single_distance != 0){
						$flights[2]['departure'] = $arrival_title; //$arrival_airport->name;
						$flights[2]['arrival'] = $cities[$plane->city_id]->name;
						$flights[2]['departure_time'] = date('Y-m-d H:i', strtotime($flights[1]['arrival_time']."+1 hours "));
						$flights[2]['distance'] = $plane_single_distance;
						$flights[2]['hours'] = $plane_single_hours;
						$flights[2]['minutes'] = $plane_single_minutes;
						//$flights[2]['cost'] = $plane_single_distance/$plane->speed * $plane->price_per_hour;
						$flights[2]['cost'] = ($cplane_single_hours * $plane->price_per_hour) + ($plane->price_per_hour * ($cplane_single_minutes / 60));
						$flights[2]['arrival_time'] = date('Y-m-d H:i', strtotime($flights[2]['departure_time']."+$plane_single_hours hours +$plane_single_minutes minutes"));
						$flights[2]['details'] = 'Empty Leg';
					}
					$total_hours += $cplane_single_hours;
					$total_mins += $cplane_single_minutes;
					
					$get_dep_location = DB::table('airport')->select(DB::raw('airport.*, city.name as city_name, ( 6371 * acos( cos( radians('.$helicopter_dep_lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$helicopter_dep_long.') ) + sin( radians('.$helicopter_dep_lat.') ) * sin( radians( latitude ) ) ) ) AS distance'))
					->leftJoin('city', 'city.id', '=', 'airport.city_id')
					->orderBy('distance')
					->first();
				
					$get_arr_location = DB::table('airport')->select(DB::raw('airport.*, city.name as city_name, ( 6371 * acos( cos( radians('.$helicopter_arr_lat.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$helicopter_arr_long.') ) + sin( radians('.$helicopter_arr_lat.') ) * sin( radians( latitude ) ) ) ) AS distance'))
					->leftJoin('city', 'city.id', '=', 'airport.city_id')
					->orderBy('distance')
					->first();
					
					
					if(($get_dep_location->latitude != $helicopter_dep_lat || $get_dep_location->longitude != $helicopter_dep_long) && $get_dep_location->distance > 5){
						$ground_handling += 15000; 
					}
				
					if(($get_arr_location->latitude != $helicopter_arr_lat || $get_arr_location->longitude != $helicopter_arr_long) && $get_arr_location->distance > 5){
						$ground_handling += 15000; 
					}
				
					// if(count($get_dep_location) > 0 &&  $get_dep_location->distance <= 5){
					// 	$ground_handling += 15000;
					// }
					if ($get_dep_location && $get_dep_location->distance <= 5) {
    $ground_handling += 15000;
}

					
					// if(count($get_arr_location) > 0 &&  $get_arr_location->distance <= 5){
					// 	$ground_handling += 15000;
					// }

					if ($get_arr_location && $get_arr_location->distance <= 5) {
    $ground_handling += 15000;
}

				}
			}
		}
		
    $stay_time = $stay_time_hours = $stay_time_minutes = 0;
		if($trip_type == 1){
			$halt_start_time = date('Y-m-d 00:00:00', strtotime($date."+$travel_hours hours +$travel_minutes minutes"));
			$halt_end_time = date('Y-m-d 00:00:00', strtotime($round_date));
			$datetime1 = new DateTime($halt_start_time);

			$datetime2 = new DateTime($halt_end_time);
			$difference = $datetime1->diff($datetime2);
			$days = $difference->d + 1;
			//dd($days * 120, $days, $total_mins , $total_hours, $request_data['additional-time']);
			//dd(($days * 120) ,($total_mins + $total_hours*60));
			//dd(($days * 120) > ($total_mins + $total_hours*60));
			if(($days * 120) > ($total_mins + $request_data['additional-time'] + $total_hours*60)){
        $stay_time = ($days * 120) - ($total_mins + $request_data['additional-time'] + $total_hours*60);
        $stay_time_hours = floor($stay_time / 60);
        $stay_time_minutes = ($stay_time % 60);
				$additional_cost += $plane->price_per_hour * $stay_time / 60;
			}
			else{
				$request_data['additional-days'] = 0;
			}
		}
		
		$ground_handling = $request_data['ground-handling'];
		$total_mins = round($total_mins);
		$total_mins += $request_data['additional-time'];
		$hours = floor($total_mins / 60);
		$minutes = ($total_mins % 60);
		$total_mins = sprintf('%02d', $minutes);
		$total_hours += $hours;
    $additional_cost += ($plane->price_per_hour * ($request_data['additional-time'] / 60));
		$total_flying_cost = 0;
		foreach($flights as $flight){
			$total_flying_cost += isset($flight['cost']) ? $flight['cost'] : 0;
		}
		$points = 0;
		$settings = BookingSetting::where('field', 'points')->first();
if ($settings && isset($settings->value)) {
    $points = $settings->value;
}

    return view('plane_details')
			->with('points',$points)
			->with('flights',$flights)
			->with('route_debug',$route_debug)
			->with('additional_cost',$additional_cost)
			->with('additional_days',$request_data['additional-days'])
			->with('owner_details',$owner_details)
			->with('ground_handling',$ground_handling)
			->with('crew_handling',$crew_handling)
			->with('medical_cost',$medical_cost)
			->with('total_flying_cost',$total_flying_cost)
			->with('plane_images',$plane_images)
			->with('airports',$airports)
			->with('departure',$departure)
			->with('arrival',$arrival)
			->with('latitude',$latitude)
			->with('longitude',$longitude)
			->with('adults',$adults)
			->with('date',$date)
			->with('total_hours',$total_hours)
			->with('total_mins',$total_mins)
			->with('plane_types',$plane_types)
			->with('plane_type',$plane_type)
			->with('cities',$cities)
			->with('stay_time_hours',$stay_time_hours)
			->with('stay_time_minutes',$stay_time_minutes)
			->with('export_details',$export_details)
			->with('plane',$plane);
  }
	
	public function getPlaneMulti(Request $request){
		$request_data = $request->all();
		//dd($request_data);
		$request_data["json-data"] = json_decode($request_data["json-data"]);
		$request_data["date"] = json_decode($request_data["date"], true);
		$request_data["arrival"] = json_decode($request_data["arrival"], true);
		$request_data["departure"] = json_decode($request_data["departure"], true);
		$request_data["helicopter-departure"] = json_decode($request_data["helicopter-departure"], true);
		$request_data["helicopter-arrival"] = json_decode($request_data["helicopter-arrival"], true);
		$keys = array_keys((array)$request_data['departure']);
		$additional_days = 0;
		$first_index = current($keys);
		$airports = Airport::where('status',1)->get()->keyBy('id');
		$cities = City::get()->keyBy('id');
		$plane = Plane::find($request_data['plane-id']);
		if($plane->type_id == 2){
			if(	$plane->from_date <= date('Y-m-d', strtotime($request_data['date'][$first_index])) && $plane->to_date >= date('Y-m-d', strtotime($request_data['date'][$first_index]))){
				if($plane->temporary_city_id != 0){
					$plane->city_id = $plane->temporary_city_id;
				}
				if($plane->temporary_airport_id != 0){
					$plane->airport_id = $plane->temporary_airport_id;
				}
			}
		}
		$points = 0;
		$settings = BookingSetting::where('field', 'points')->first();
		if($settings) {
			$points = $settings->value;
		}
    $plane_images = PlaneImage::where('plane_id',$request_data['plane-id'])->get();
		$ground_handling = $request_data["ground_handling"];
		$flight_cost = $request_data["flight_cost"];
		$medical_cost = $this->medicalCostAmount($plane->type_id);
		$additional_cost = $crew_handling = $total_hours = $total_mins = 0;
		$flights = array();
		//dd($request_data);
		$last_index = 0;
		foreach($request_data["arrival"] as $index => $arrival){
			$last_index = $index;
		}
		$previous_index = 0;
		foreach($request_data["json-data"] as $index => $json_data){
			//var_dump($json_data);
			if(isset($json_data->distance)){
				$flights[$index]['distance'] = 0;
				if(round($json_data->distance) != 0){
					$flights[$index]['distance'] = round($json_data->distance, 2);
				}
				$hours = floor($json_data->time / 60);
				$minutes = round($json_data->time)%60;
				$flights[$index]['hours'] = $hours;
				$flights[$index]['minutes'] = $minutes;
				$total_hours += $hours;
				$total_mins += $minutes;
				$flights[$index]['cost'] = 0;
				if(round($flights[$index]['distance']) != 0){
					$flights[$index]['cost'] = ($plane->price_per_hour * ($json_data->time / 60));
				}
				$flights[$index]['cost'] = round($json_data->cost, 2);
				$flights[$index]['details'] = 'Empty Leg';
				if($index !== 'base' && $index !== 'arr'){
					if($request_data["plane-type"] != 2){
						$flights[$index]['arrival'] = $flights[$index]['departure'] = '';
						if(isset($airports[$request_data["departure"][$index]]) && isset($cities[$airports[$request_data["departure"][$index]]->city_id])){
							$flights[$index]['departure'] = $cities[$airports[$request_data["departure"][$index]]->city_id]->name;
						}
						if(isset($airports[$request_data["arrival"][$index]]) && isset($cities[$airports[$request_data["arrival"][$index]]->city_id])){
							$flights[$index]['arrival'] = $cities[$airports[$request_data["arrival"][$index]]->city_id]->name;
						}
					}
					else{
						$flights[$index]['departure'] = $request_data["helicopter-departure"][$index];
						$flights[$index]['arrival'] = $request_data["helicopter-arrival"][$index];
					}
					$flights[$index]['departure_time'] = $request_data["date"][$index];
					$flights[$index]['arrival_time'] = date('Y-m-d H:i', strtotime($flights[$index]['departure_time']."+$hours hours +$minutes minutes"));
					$flights[$index]['details'] = 'With Passengers';
					
					if($index != $first_index && isset($flights[$previous_index]['arrival_time'])){
						$halt_start_time = date('Y-m-d H:i:s', strtotime($flights[$previous_index]['arrival_time']));
						$halt_end_time = date('Y-m-d H:i:s', strtotime($flights[$index]['departure_time']));
						$datetime1 = new DateTime($halt_start_time);

						$datetime2 = new DateTime($halt_end_time);
						$difference = $datetime1->diff($datetime2);
						if($difference->d > 0) {
							//$crew_handling += 25000 * $difference->d;
							//$additional_cost += $plane->price_per_hour * 2 * $difference->d;
						}
						elseif($difference->h > 4){
							//$crew_handling += 25000;
						}
						
						if(isset($json_data->additional_days)){
							//$additional_days += $json_data->additional_days;
							//$additional_cost += $plane->price_per_hour * 2 * $json_data->additional_days;
						}
						
						if(isset($json_data->crew_handling_additional_days)){
							$crew_handling_airport_id = isset($request_data["arrival"][$previous_index]) ? $request_data["arrival"][$previous_index] : 0;
							$crew_handling += $this->airportCrewHandlingAmount($crew_handling_airport_id) * (float) $json_data->crew_handling_additional_days;
						}
					}
				}
				else{
					$flights[$index]['arrival'] = '';
					$flights[$index]['departure'] = '';
					$flights[$index]['departure_time'] = '';
					$flights[$index]['arrival_time'] = '';
					if($index == 'base' ){
						$flights[$index]['departure'] = $cities[$plane->city_id]->name;
						$flights[$index]['departure_time'] = date('Y-m-d H:i:s', strtotime($request_data["date"][$first_index]. "- ".round($json_data->time + 60)." minutes"));
						$flights[$index]['arrival_time'] = date('Y-m-d H:i:s', strtotime($flights[$index]['departure_time']. "+ ".round($json_data->time)." minutes"));
						if($request_data["plane-type"] != 2){
							if(isset($airports[$request_data["departure"][$first_index]]) && isset($cities[$airports[$request_data["departure"][$first_index]]->city_id])){
								$flights[$index]['arrival'] = $cities[$airports[$request_data["departure"][$first_index]]->city_id]->name;
							}
						}
						else{
							$flights[$index]['arrival'] = $request_data["helicopter-departure"][$first_index];
						}
					}
					else{
						$flights[$index]['arrival'] = $cities[$plane->city_id]->name;
						$flights[$index]['departure_time'] = date('Y-m-d H:i:s', strtotime($request_data["date"][$last_index]. " +".round($request_data["json-data"]->{$last_index}->time + 60)." minutes"));
						
						$flights[$index]['arrival_time'] = date('Y-m-d H:i:s', strtotime($flights[$index]['departure_time']. "+ ".round($json_data->time )." minutes"));
						if($request_data["plane-type"] != 2){
							if(isset($airports[$request_data["arrival"][$last_index]]) && isset($cities[$airports[$request_data["arrival"][$last_index]]->city_id])){
								$flights[$index]['departure'] = $cities[$airports[$request_data["arrival"][$last_index]]->city_id]->name;
							}
						}
						else{
							$flights[$index]['departure'] = $request_data["helicopter-arrival"][$last_index];
						}
					}
				}
			}
			if($index !== 'base' && $index !== 'arr'){
				$previous_index = $index;
			}
		
		}
		
		//dd($flights);
		$owner_id = Plane::where('id',$plane->id)->pluck('owner_id');
    $owner = Owner::where('id',$owner_id)->first();
    $sec_details = SecondaryContact::where('owner_id',$owner_id)->first();
    $owner_details = array();
    $owner_details['name'] = $owner->name;
    $owner_details['email1'] = $owner->email_1;
    $owner_details['contact1'] = $owner->contact_number_1;
    $owner_details['sec_name'] = $sec_details->name;
    $owner_details['sec_contact'] = $sec_details->contact;
    $owner_details['sec_email'] = $sec_details->email;
		$export_details = '';
		$hours = floor($total_mins / 60);
		$minutes = ($total_mins % 60);
		$total_mins = sprintf('%02d', $minutes);
		$total_hours += $hours;
		
	
		$halt_start_time = date('Y-m-d 00:00:00', strtotime($request_data["date"][$first_index]));
		$halt_end_time = date('Y-m-d 00:00:00', strtotime($request_data["date"][$last_index]));
		$datetime1 = new DateTime($halt_start_time);

		$datetime2 = new DateTime($halt_end_time);
		$difference = $datetime1->diff($datetime2);
		$days = $difference->d + 1;
		//dd($days * 120, $days, $total_mins , $total_hours, $request_data['additional-time']);
		//dd(($days * 120) ,($total_mins + $total_hours*60));
		//dd(($days * 120) > ($total_mins + $total_hours*60));
    $stay_time = $stay_time_hours = $stay_time_minutes = 0;
		if(($days * 120) > ($total_mins + $total_hours*60)){
			//$additional_cost += $plane->price_per_hour * 2 * $request_data['additional-days'];
			if(($days * 120) > ($total_mins + $request_data['additional-time'] + $total_hours*60)){
        $stay_time = ($days * 120) - ($total_mins + $request_data['additional-time'] + $total_hours*60);
        $stay_time_hours = floor($stay_time / 60);
        $stay_time_minutes = ($stay_time % 60);
				$additional_cost += $plane->price_per_hour * $stay_time / 60;
			}
			else{
				$request_data['additional-days'] = 0;
			}
		}
		else{
			$additional_cost = $additional_days = 0;
		}
		
		$total_mins = round($request_data['total_minutes']) + round($request_data['additional-time']);
		$total_hours = $request_data['total_hours'];
		
		if((($total_mins + $total_hours * 60) % 120) == 1){
			$total_mins--;
		}
		$hours = floor($total_mins / 60);
		$minutes = ($total_mins % 60);
		$total_mins = sprintf('%02d', $minutes);
		$total_hours += $hours;
    //$additional_cost += ($plane->price_per_hour * ($request_data['additional-time'] / 60));
		$flying_cost = ($total_hours * $plane->price_per_hour) + ($plane->price_per_hour * ($total_mins / 60)) + $additional_cost; 
		return view('plane_details_multi')
			->with('points',$points)
			->with('flights',$flights)
			->with('owner_details',$owner_details)
			->with('additional_cost',$additional_cost)
			->with('additional_days',$additional_days)
			->with('ground_handling',$ground_handling)
			->with('crew_handling',$crew_handling)
			->with('medical_cost',$medical_cost)
			->with('plane_images',$plane_images)
			->with('total_hours',$total_hours)
			->with('total_mins',$total_mins)
			->with('plane_type',$request_data['plane-type'])
			->with('export_details',$export_details)
      ->with('stay_time_hours',$stay_time_hours)
      ->with('stay_time_minutes',$stay_time_minutes)
      ->with('flying_cost',$flying_cost)
			->with('plane',$plane);
		
		
	}
	private function getFlowerShowerPlaneDetails($request_data)
{
    $plane = Plane::find($request_data['plane-id']);

    if(!$plane) {
        abort(404);
    }

    $selected_lat = (float) $request_data['selected-lat'];
    $selected_long = (float) $request_data['selected-long'];
    $selected_location = isset($request_data['selected-location']) && trim($request_data['selected-location']) !== ''
        ? $request_data['selected-location']
        : 'Selected Location';
    $flower_shower_time = isset($request_data['flower-shower-time'])
        ? (int) $request_data['flower-shower-time']
        : (int) Session::get('flower_shower_time', 0);
    if($flower_shower_time > 0) {
        Session::put('flower_shower_time', $flower_shower_time);
    }

    $speed_coefficient = isset($request_data['speed_coefficient']) && (float) $request_data['speed_coefficient'] > 0
        ? (float) $request_data['speed_coefficient']
        : 1;

    $plane_images = PlaneImage::where('plane_id', $plane->id)->get();
    $plane_types = DB::Table('plane_type')->pluck('name','id');
    $airports = Airport::where('status',1)->orderBy('updated_at')->get();
    $cities = City::get()->keyBy('id');

    $base_name = isset($cities[$plane->city_id]) ? $cities[$plane->city_id]->name : 'Base';
    $distance = $this->getDistance($plane->latitude, $plane->longitude, $selected_lat, $selected_long);

    $time = 0;
    if($distance > 0 && $plane->speed > 0) {
        if($distance > 200) {
            $time = (200 / (($plane->speed * $speed_coefficient) / 60)) + (($distance - 200) / ($plane->speed / 60));
        } else {
            $time = $distance / (($plane->speed * $speed_coefficient) / 60);
        }
    }

    $one_way_hours = floor(round($time) / 60);
    $one_way_minutes = round($time) % 60;

    $total_minutes_raw = round($time) * 2;
    $additional_time = ($total_minutes_raw > 0 && $total_minutes_raw < 120) ? (120 - $total_minutes_raw) : 0;
    $total_minutes = $total_minutes_raw + $additional_time;

    $total_hours = floor($total_minutes / 60);
    $total_mins = $total_minutes % 60;

    $ground_handling = 0;
    if($distance != 0) {
        $ground_handling = $this->flowerShowerGroundHandlingAmount($plane->airport_id);
    }

    $flight_cost = (($total_minutes + $flower_shower_time) / 60) * $plane->price_per_hour;

    $date = date('Y-m-d H:i');
    $flights = array();

    $flights[0]['departure'] = $base_name;
    $flights[0]['arrival'] = $selected_location;
    $flights[0]['departure_time'] = $date;
    $flights[0]['distance'] = $distance;
    $flights[0]['hours'] = $one_way_hours;
    $flights[0]['minutes'] = $one_way_minutes;
    $flights[0]['cost'] = ($flight_cost / 2);
    $flights[0]['arrival_time'] = date('Y-m-d H:i', strtotime($date.' +'.$one_way_hours.' hours +'.$one_way_minutes.' minutes'));
    $flights[0]['details'] = 'Flower Shower';

    $flights[1]['departure'] = $selected_location;
    $flights[1]['arrival'] = $base_name;
    $flights[1]['departure_time'] = date('Y-m-d H:i', strtotime($flights[0]['arrival_time'].' +1 hours'));
    $flights[1]['distance'] = $distance;
    $flights[1]['hours'] = $one_way_hours;
    $flights[1]['minutes'] = $one_way_minutes;
    $flights[1]['cost'] = ($flight_cost / 2);
    $flights[1]['arrival_time'] = date('Y-m-d H:i', strtotime($flights[1]['departure_time'].' +'.$one_way_hours.' hours +'.$one_way_minutes.' minutes'));
    $flights[1]['details'] = 'Empty Leg';

    $points = 0;
    $settings = BookingSetting::where('field', 'points')->first();
    if($settings && isset($settings->value)) {
        $points = $settings->value;
    }

    $owner = Owner::find($plane->owner_id);
    $sec_details = SecondaryContact::where('owner_id', $plane->owner_id)->first();
    $owner_details = array(
        'name' => $owner ? $owner->name : '',
        'email1' => $owner ? $owner->email_1 : '',
        'contact1' => $owner ? $owner->contact_number_1 : '',
        'sec_name' => $sec_details ? $sec_details->name : '',
        'sec_contact' => $sec_details ? $sec_details->contact : '',
        'sec_email' => $sec_details ? $sec_details->email : '',
    );

    return view('plane_details')
        ->with('points', $points)
        ->with('flights', $flights)
        ->with('route_debug', array())
        ->with('additional_cost', 0)
        ->with('additional_days', 0)
        ->with('owner_details', $owner_details)
        ->with('ground_handling', $ground_handling)
        ->with('crew_handling', 0)
        ->with('medical_cost', 0)
        ->with('total_flying_cost', $flight_cost)
        ->with('plane_images', $plane_images)
        ->with('airports', $airports)
        ->with('departure', '')
        ->with('arrival', '')
        ->with('latitude', $selected_lat)
        ->with('longitude', $selected_long)
        ->with('adults', 1)
        ->with('date', $date)
        ->with('flower_shower_time', $flower_shower_time)
        ->with('total_hours', $total_hours)
        ->with('total_mins', $total_mins)
        ->with('plane_types', $plane_types)
        ->with('plane_type', $plane->type_id)
        ->with('cities', $cities)
        ->with('stay_time_hours', 0)
        ->with('stay_time_minutes', 0)
        ->with('export_details', '')
        ->with('flower_shower', 1)
        ->with('plane', $plane);
}
  
  private function flowerShowerGroundHandlingAmount($airport_id = 0)
  {
    $handling_charges = DB::table('handling_charges')->pluck('charges', 'airport_id');

    if($airport_id && isset($handling_charges[$airport_id]) && is_numeric($handling_charges[$airport_id])){
      return (float) $handling_charges[$airport_id];
    }

    if(isset($handling_charges[0]) && is_numeric($handling_charges[0])){
      return (float) $handling_charges[0];
    }

    return 0;
  }
	function getDistance($lat1, $lon1, $lat2, $lon2) {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    return ($miles * 0.8684);
  }
	
	function postBookingConfirmation(Request $request) {
    $request_data = $request->all();
		
		$plane_id = $request_data['plane-id'];
		$plane_name = $request_data['plane-name'];
		$flying_hours = $request_data['flying-hours'];
		$flying_mins = $request_data['flying-mins'];
		$stay_hours = $request_data['stay-hours'];
		$stay_mins = $request_data['stay-mins'];
		$total_hours = $request_data['total-hours'];
		$total_mins = $request_data['total-mins'];
		$total_flying_cost = $request_data['total-flying-cost'];
		$ground_handling = $request_data['ground-handling'];
		$crew_handling = $request_data['crew-handling'];
		$flights = (array)json_decode($request_data['flights']);
		unset($request_data['_token']);
		$points = 0;
		$settings = BookingSetting::where('field', 'points')->first();
		if($settings) {
			$points = $settings->value;
		}
		
		return view('booking_confirmation')
			->with('points', $points)
			->with('plane_id', $plane_id)
			->with('plane_name', $plane_name)
			->with('total_hours', $total_hours)
			->with('total_mins', $total_mins)
			->with('stay_hours', $stay_hours)
			->with('stay_mins', $stay_mins)
			->with('flying_hours', $flying_hours)
			->with('flying_mins', $flying_mins)
			->with('total_flying_cost', $total_flying_cost)
			->with('ground_handling', $ground_handling)
			->with('crew_handling', $crew_handling)
			->with('request_data', $request_data)
			->with('flights', $flights);
		
	}
	
	function postBook(Request $request) {
		$request_data = $request->all();
		$points = 0;
		$settings = BookingSetting::where('field', 'points')->first();
		if ($settings && isset($settings->value)) {
			$points = $settings->value;
		}

		$points_redeemed = isset($request_data['redeem']) && is_numeric($request_data['redeem']) ? (float)$request_data['redeem'] : 0;
		$points_redeemed = max(0, $points_redeemed);
		//$request_data = (array)json_decode($request_data['request-data']);
		$total_flying_cost = (float)($request_data['total-flying-cost'] ?? 0);
		$ground_handling = (float)($request_data['ground-handling'] ?? 0);
		$crew_handling = (float)($request_data['crew-handling'] ?? 0);
		$grand_total = round(($total_flying_cost + $ground_handling + $crew_handling + (($total_flying_cost + $ground_handling + $crew_handling) * 18/100)));
		$points_earned = round($grand_total * $points / 100);
		
		$booking = new Booking;
		$booking->plane_id = $request_data['plane-id'];
		$booking->plane_name = $request_data['plane-name'];
		$booking->total_hours = $request_data['total-hours'];
		$booking->total_mins = $request_data['total-mins'];
		$booking->stay_hours = $request_data['stay-hours'];
		$booking->stay_mins = $request_data['stay-mins'];
		$booking->flying_hours = $request_data['flying-hours'];
		$booking->flying_mins = $request_data['flying-mins'];
		$booking->total_flying_cost = $request_data['total-flying-cost'];
		$booking->ground_handling = $request_data['ground-handling'];
		$booking->crew_handling = $request_data['crew-handling'];
		$booking->flights = $request_data['flights'];
		$booking->points_earned = $points_earned;
		$available_points = (float)auth()->user()->points;
		if($available_points < $points_redeemed) {
			$points_redeemed = $available_points;
		}
		$booking->points_redeemed = $points_redeemed;
		$booking->user_id = auth()->user()->id;
		$booking->save();
		
		$user = User::find(auth()->user()->id);
		// $user->points = $user->points + $points_earned - $points_redeemed;
		$user->points = (float)$user->points + (float)$points_earned - (float)$points_redeemed;

		$user->save();
		$data = [
			'name' 			=> $user->name,
			'booking' 	=> $booking,
		];
		$email = auth()->user()->email;
		try {
			$mail_content = MailContent::where('name', 'Booking')->first();
			if(!empty($mail_content)) {
				$bookings_html = '<div class="row">';
				$bookings_html .= '<table border="1" style="border-collapse: collapse;" class="table table-hover table-bordered">';
				$bookings_html .= '		<thead>';
				$bookings_html .= '					<tr>';
				$bookings_html .= '						<th>Departure Time</th>';
				$bookings_html .= '						<th>Departure</th>';
				$bookings_html .= '						<th>Flight Time</th>';
				$bookings_html .= '						<th>Arrival</th>';
				$bookings_html .= '						<th>Arrival Time</th>';
				$bookings_html .= '						<th>Distance (In NM)</th>';
				$bookings_html .= '						<th>Cost (In Rs.)</th>';
				$bookings_html .= '						<th>Particular</th>';
				$bookings_html .= '					</tr>';
				$bookings_html .= '				</thead>';
				$bookings_html .= '				<tbody>';
				$flights = (array)json_decode($booking->flights);
				if(isset($flights['base'])) {
					$flights['base'] = (array)$flights['base'];
					$bookings_html .= '					<tr>';
					$bookings_html .= '						<td>'. date('d M, Y H:i', strtotime($flights['base']['departure_time'])) .'</td>';
					$bookings_html .= '						<td>'. $flights['base']['departure'] .'</td>';
					$bookings_html .= '						<td>'. ( $flights['base']['hours'] != 0 ? $flights['base']['hours'].' hour ' : '').($flights['base']['minutes'] != 0 ?$flights['base']['minutes'].' minute':'')  .'</td>';
					$bookings_html .= '						<td>'. $flights['base']['arrival'] .'</td>';
					$bookings_html .= '						<td>'. date('d M, Y H:i', strtotime($flights['base']['arrival_time'])) .'</td>';
					$bookings_html .= '						<td>'. round($flights['base']['distance'],2) .'</td>';
					$bookings_html .= '						<td>'. round($flights['base']['cost'],2) .'</td>';
					$bookings_html .= '						<td>'. $flights['base']['details'] .'</td>';
					$bookings_html .= '					</tr>';
				}
				foreach($flights as $index => $flight) {
					$flight = (array)$flight;
					if($index !== 'base' && $index !== 'arr') {
						$bookings_html .= '				<tr>';
						$bookings_html .= '					<td>'. date('d M, Y H:i', strtotime($flight['departure_time'])) .'</td>';
						$bookings_html .= '					<td>'. $flight['departure'] .'</td>';
						$bookings_html .= '					<td>'. ( $flight['hours'] != 0 ? $flight['hours'].' hour ' : '').($flight['minutes'] != 0 ?$flight['minutes'].' minute':'')  .'</td>';
						$bookings_html .= '					<td>'. $flight['arrival'] .'</td>';
						$bookings_html .= '					<td>'. date('d M, Y H:i', strtotime($flight['arrival_time'])) .'</td>';
						$bookings_html .= '					<td>'. round($flight['distance'],2) .'</td>';
						$bookings_html .= '					<td>'. round($flight['cost'],2) .'</td>';
						$bookings_html .= '					<td>'. $flight['details'] .'</td>';
						$bookings_html .= '				</tr>';
								
					}
				}
				if(isset($flights['arr'])) {
					$flights['arr'] = (array)$flights['arr'];
					$bookings_html .= '					<tr>';
					$bookings_html .= '						<td>'. date('d M, Y H:i', strtotime($flights['arr']['departure_time'])) .'</td>';
					$bookings_html .= '						<td>'. $flights['arr']['departure'] .'</td>';
					$bookings_html .= '						<td>'. ( $flights['arr']['hours'] != 0 ? $flights['arr']['hours'].' hour ' : '').($flights['arr']['minutes'] != 0 ?$flights['arr']['minutes'].' minute':'')  .'</td>';
					$bookings_html .= '						<td>'. $flights['arr']['arrival'] .'</td>';
					$bookings_html .= '						<td>'. date('d M, Y H:i', strtotime($flights['arr']['arrival_time'])) .'</td>';
					$bookings_html .= '						<td>'. round($flights['arr']['distance'],2) .'</td>';
					$bookings_html .= '						<td>'. round($flights['arr']['cost'],2) .'</td>';
					$bookings_html .= '						<td>'. $flights['arr']['details'] .'</td>';
					$bookings_html .= '					</tr>';
				}
				$bookings_html .= '				</tbody>';
				$bookings_html .= '			</table>';
				$bookings_html .= '		</div>';
				$bookings_html .= '		<br/><br/>';
				$bookings_html .= '		<div class="row">';
				$bookings_html .= '			<div class="col-md-6 col-md-offset-3">';
				$bookings_html .= '				<table style="border-collapse: collapse;" border="1" class="table table-bordered">';
				$bookings_html .= '					<thead>';
				$bookings_html .= '						<tr>';
				$bookings_html .= '							<th>Cost Details</th>';
				$bookings_html .= '							<th class="text-right">Amount</th>';
				$bookings_html .= '						</tr>';
				$bookings_html .= '					</thead>';
				$bookings_html .= '					<tbody>';
				$bookings_html .= '						<tr>';
				$bookings_html .= '							<td>Total Flight Time</td>';
				$bookings_html .= '							<td class="text-right">'. $booking->flying_hours .' Hrs '. $booking->flying_mins .' Mins</td>';
				$bookings_html .= '						</tr>';
				if(($booking->stay_hours + $booking->stay_mins) != 0) {
					$bookings_html .= '							<tr>';
					$bookings_html .= '								<td>Stay Time</td>';
					$bookings_html .= '								<td class="text-right">'. $booking->stay_hours .' Hrs '. $booking->stay_mins .' Mins</td>';
					$bookings_html .= '							</tr>';
					 
					$total_billing_minutes = ($stay_hours + $total_hours)*60 + $total_mins + $stay_mins;
					$total_hours = $billing_hours = floor($total_billing_minutes / 60);
					$total_mins = $billing_minutes = ($total_billing_minutes % 60);
					
					$bookings_html .= '						<tr>';
					$bookings_html .= '							<td>Total Billing Time</td>';
					$bookings_html .= '							<td class="text-right">'. $billing_hours .' Hrs '. $billing_minutes .' Mins</td>';
					$bookings_html .= '						</tr>';
				}
					$bookings_html .= '					<tr>';
				$bookings_html .= '						<td>Flying Cost</td>';
				$bookings_html .= '						<td class="text-right">'. round($booking->total_flying_cost) .'</td>';
				$bookings_html .= '					</tr>';
				$bookings_html .= '					<tr>';
				$bookings_html .= '						<td>Ground Handling</td>';
				$bookings_html .= '						<td class="text-right">'. $booking->ground_handling .'</td>';
				$bookings_html .= '					</tr>';
				$bookings_html .= '					<tr>';
				$bookings_html .= '						<td>Crew Handling</td>';
				$bookings_html .= '						<td class="text-right">'. $booking->crew_handling .'</td>';
				$bookings_html .= '					</tr>';
				$bookings_html .= '					<tr>';
				$bookings_html .= '						<td>Other Charges</td>';
				$bookings_html .= '						<td class="text-right">As per actual</td>';
				$bookings_html .= '					</tr>';
				$bookings_html .= '					<tr>';
				$bookings_html .= '						<td>Sub Total</td>';
				$bookings_html .= '						<td class="text-right">'. ($booking->total_flying_cost + $booking->ground_handling + $booking->crew_handling).'</td>';
				$bookings_html .= '					</tr>';
				$bookings_html .= '					<tr>';
				$bookings_html .= '						<td>GST @ 18%</td>';
				$bookings_html .= '						<td class="text-right">'. round((($booking->total_flying_cost + $booking->ground_handling + $booking->crew_handling ) * 18/100)) .'</td>';
				$bookings_html .= '					</tr>';
				$bookings_html .= '					<tr>';
				$bookings_html .= '						<td>Grand Total</td>';
				
				$grand_total = round(($booking->total_flying_cost + $booking->ground_handling + $booking->crew_handling + (($booking->total_flying_cost + $booking->ground_handling + $booking->crew_handling ) * 18/100)));
				
				$bookings_html .= '						<td class="text-right">'.  round(($booking->total_flying_cost + $booking->ground_handling + $booking->crew_handling + (($booking->total_flying_cost + $booking->ground_handling + $booking->crew_handling ) * 18/100))).'</td>';
				$bookings_html .= '					</tr>';
				$bookings_html .= '					<tr>';
				$bookings_html .= '						<td>Points Earned</td>';
				$bookings_html .= '						<td class="text-right">'. $booking->points_earned .'</td>';
				$bookings_html .= '					</tr>';
				$bookings_html .= '					<tr>';
				$bookings_html .= '						<td>Points Redeemed</td>';
				$bookings_html .= '						<td class="text-right">'. $booking->points_redeemed .'</td>';
				$bookings_html .= '					</tr>';
				$bookings_html .= '					<tr>';
				$bookings_html .= '						<td>Final Total</td>';
				$points_redeemed = !empty($booking->points_redeemed) ? (float)$booking->points_redeemed : 0;
				$bookings_html .= ' 				<td class="text-right">'. ($grand_total - $points_redeemed) .'</td>';
				$bookings_html .= '					</tr>';
				$bookings_html .= '				</tbody>';
				$bookings_html .= '			</table>';
				$bookings_html .= '		</div>';
				$bookings_html .= '	</div>';
				
				$subject = $mail_content->subject;
				$content = str_replace('---name---', $user->name, $mail_content->content);
				$content = str_replace('---bookings---', $bookings_html, $content);
				$content = str_replace('---plane---', $booking->plane_name, $content);
				
				Mail::send([], [], function($message) use ($subject, $email, $content): void {
					$message->to($email);
					$message->subject($subject);
					$message->html($content);
				});
			}
			else {
				Mail::send('emails.booking', array('data' => $data), function($message) use($data, $email): void {
					$message->to($email)->subject('Air Accretion - Booking Details');
				});
			}
		} catch (\Throwable $e) {
			Log::warning('Booking email could not be sent.', [
				'booking_id' => $booking->id,
				'user_id' => $user->id,
				'email' => $email,
				'error' => $e->getMessage(),
			]);
		}
		return redirect('/my-bookings');
	}
  
  public function postMachineDetailsReport(Request $request) {
    $request_data = $request->all();
    $plane_id = $request_data['plane-id'];
    $plane_details = Plane::find($request_data['plane-id']);
    $plane_city_text = '';
    if($plane_details->city_id != 0 ){
      $plane_city = City::where('id',$plane_details->city_id)->first();
      if($plane_city){
        $plane_city_text = $plane_city->name;
      }
    }
    $price = $plane_details->price_per_hour;
    $plane_type = $plane_details->type_id;
    $plane_name = $request_data['plane-name'];
    $total_hours = $request_data['total-hours'];
    $total_mins = $request_data['total-mins'];
    $total_flying_cost = $request_data['total-flying-cost'];
    $ground_handling = $request_data['ground-handling'];
    $crew_handling = $request_data['crew-handling'];
    $medical_cost = isset($request_data['medical-cost']) ? (float) $request_data['medical-cost'] : $this->medicalCostAmount($plane_type);
    if((int) $plane_type !== 3) {
      $medical_cost = 0;
    }
    $flights = json_decode($request_data['flights']);
		$all_flights = (array)$flights;
		$flights = array();
		if(isset($all_flights['base'])){
			$flights[] = $all_flights['base'];
		}
		foreach($all_flights as $index => $flight_data) {
			if($index !== 'arr' && $index !== 'base') {
				$flights[] = $flight_data;
			}
		}
		if(isset($all_flights['arr'])){
			$flights[] = $all_flights['arr'];
		}
    $sub_total = $total_flying_cost + $ground_handling + $crew_handling + $medical_cost; 
		$tax_details = DB::table('setting')
      ->where('setting_type', 0)
      ->where('status', 1)
      ->whereDate('from_date', '<=',  date('Y-m-d'))
      ->whereDate('to_date', '>', date('Y-m-d'))
      ->first();
		$gst = 0;
		if($tax_details) {	
			$gst = round((($sub_total) * $tax_details->gst/100),2);
		}
		if($plane_type == 3) {
			$gst = 0;
		}
    $grand_total = round($sub_total + $gst);
  
    return Excel::download(new MachineDetailsExport(
      (int) $plane_type,
      $plane_name,
      $total_hours,
      $total_mins,
      $total_flying_cost,
      $ground_handling,
      $crew_handling,
      $medical_cost,
      $flights,
      $sub_total,
      $gst,
      $grand_total,
      $plane_city_text,
      $price
    ), $plane_name . '.xls');
  }
  
  public function getPlaneListByFlowerShower(Request $request) { 
    $request_data = $request->all(); 
    $latitude = (float) $request_data['lat'];
    $longitude = (float) $request_data['long'];
    $location_name = isset($request_data['location']) && trim($request_data['location']) !== '' ? $request_data['location'] : 'Selected Location';
    $filter_id = $request_data['filter-id'];
    $sort = 'asc';
		if($filter_id == 1){
			$sort = 'desc';
		}
    $plane_types = DB::Table('plane_type')->pluck('name','id');
    $plane_subtypes = DB::Table('plane_subtypes')->pluck('sub_type','id');
    $tax_details = DB::table('setting')
      ->where('setting_type', 0)
      ->where('status', 1)
      ->whereDate('from_date', '<=', date('Y-m-d'))
      ->whereDate('to_date', '>', date('Y-m-d'))
      ->first();
    
    $avail_planes = DB::table('plane')
			->select(
				'plane.*', 
				'city.name as city_name', 
				DB::raw('CASE WHEN from_date <= "'.date('Y-m-d').'" AND to_date >= "'.date('Y-m-d').'" THEN temp_latitude ELSE latitude END as latitude'),
				DB::raw('CASE WHEN from_date <= "'.date('Y-m-d').'" AND to_date >= "'.date('Y-m-d').'" THEN temp_longitude ELSE longitude END as longitude'),
				DB::raw('( 6371 * acos( cos( radians('.$latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( latitude ) ) ) ) AS distance'
				)
			)
			->leftJoin('city', 'city.id', '=', 'plane.city_id')
      ->where('plane.flower_shower', '=', 1)
      ->orderBy('distance',$sort)
			->get(); 
			
      $planes = array();
      
      for($i = 0; $i < count($avail_planes); $i++) {
      $plane_details = new PlaneDetails();
      $plane_details->id = $avail_planes[$i]->id;
      $plane_details->type_id = $avail_planes[$i]->type_id;
      $plane_details->name = $avail_planes[$i]->name;
      $plane_details->Call_Sign = $avail_planes[$i]->Call_Sign;
      $plane_details->city_id = $avail_planes[$i]->city_id;
      $plane_details->city_name = $avail_planes[$i]->city_name;
      $plane_details->airport_id = isset($avail_planes[$i]->airport_id) ? $avail_planes[$i]->airport_id : 0;
      $plane_details->seats = $avail_planes[$i]->seats;
      $plane_details->distance = $avail_planes[$i]->distance;
      $plane_details->speed = $avail_planes[$i]->speed;
      $plane_details->lavatory = $avail_planes[$i]->lavatory;
      $plane_details->display_image = $avail_planes[$i]->display_image;
      $plane_details->price_per_hour = $avail_planes[$i]->price_per_hour;
      $plane_details->avail_planes_lat = $avail_planes[$i]->latitude;
      $plane_details->avail_planes_lng = $avail_planes[$i]->longitude;
      $plane_details->avail_distance = $avail_planes[$i]->distance;
      $plane_details->speed_coefficient = $avail_planes[$i]->speed_coefficient;
      $plane_details->flower_shower = 1;
      $plane_details->owner_id = $avail_planes[$i]->owner_id;
      $plane_details->price = $avail_planes[$i]->price_per_hour;
      $plane_details->plane_type = isset($plane_types[$avail_planes[$i]->type_id]) ? $plane_types[$avail_planes[$i]->type_id] : '';
      $plane_details->subtype = isset($plane_subtypes[$avail_planes[$i]->subtype]) ? $plane_subtypes[$avail_planes[$i]->subtype] : '';
      $plane_details->tax = $this->gstRateAmount($tax_details);
      $plane_details->selected_location = $location_name;
      $plane_details->selected_lat = $latitude;
      $plane_details->selected_lng = $longitude;
      $plane_details->path = $plane_details->city_name.' > '.$location_name.' > '.$plane_details->city_name;
      $flower_shower_ground_handling = ((float) $avail_planes[$i]->distance > 0) ? $this->flowerShowerGroundHandlingAmount($plane_details->airport_id) : 0;
      $plane_details->handling_charges = $flower_shower_ground_handling;      
      $planes[] = $plane_details;
    }
    
    return response()->json(array('planes' => $planes), 200);
   
  }

  //Citywise airports
  public function getCitywiseAirports(Request $request)
  {
    if(!Auth::check())
    {
      return response()->json([], 401);
    }

    $city_id = $request->query('city-id');

    if(!$city_id)
    {
      return response()->json([]);
    }

    $airports = Airport::select('id', 'name', 'latitude', 'longitude', 'city_id', 'gt', 'crew_handling')
      ->where('status',1)
      ->where('city_id', $city_id)
      ->orderBy('name')
      ->get()
      ->values();

    return response()->json($airports);
  } 
  
  //Locationwise cities
  public function getLocationwiseAirport(Request $request)
  {
    if(Auth::check() && Auth::User()->user_type == 0)
    {
      Session::forget('menu');
      $request_data = $request->all();
      if(!isset($request_data['lat']) || !isset($request_data['long']) || !is_numeric($request_data['lat']) || !is_numeric($request_data['long']))
      {
        return response()->json(null, 422);
      }

      $latitude = (float) $request_data['lat'];
      $longitude = (float) $request_data['long'];
     
      $location = DB::table('airport')->select(DB::raw('airport.*, city.name as city_name, ( 6371 * acos( cos( radians('.$latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( latitude ) ) ) ) AS distance'))
      ->leftJoin('city', 'city.id', '=', 'airport.city_id')
      ->orderBy('distance')
      ->first();
      
      return response()->json($location);
    }
    else
    {
      return response()->json(null, 401);
    }
  } 

  private function airportCrewHandlingAmount($airport_id, $default = 25000)
  {
    $amount = null;

    if($airport_id){
      $amount = Airport::where('id', $airport_id)->value('crew_handling');
    }

    return $this->normalizeCrewHandlingAmount($amount, $default);
  }

  private function medicalCostAmount($plane_type, $default = 40000)
  {
    if((int) $plane_type !== 3){
      return 0;
    }

    $setting = DB::table('setting')
      ->where('setting_type', 1)
      ->where('status', 1)
      ->whereDate('from_date', '<=', date('Y-m-d'))
      ->whereDate('to_date', '>', date('Y-m-d'))
      ->orderBy('created_at', 'desc')
      ->first();

    if($setting && is_numeric($setting->amount)){
      return (float) $setting->amount;
    }

    return (float) $default;
  }

  private function normalizeCrewHandlingAmount($amount, $default = 25000)
  {
    if(is_numeric($amount) && (float) $amount >= 0){
      return (float) $amount;
    }

    return (float) $default;
  }

  private function gstRateAmount($tax_details = null, $default = 18)
  {
    if($tax_details && isset($tax_details->gst) && is_numeric($tax_details->gst) && (float) $tax_details->gst > 0){
      return (float) $tax_details->gst;
    }

    $setting = DB::table('setting')
      ->where('setting_type', 0)
      ->where('status', 1)
      ->orderBy('updated_at', 'desc')
      ->first();

    if($setting && isset($setting->gst) && is_numeric($setting->gst) && (float) $setting->gst > 0){
      return (float) $setting->gst;
    }

    return (float) $default;
  }

  private function subtypeFilterIds($value)
  {
    if(is_array($value)){
      $values = $value;
    } else {
      $values = explode(',', (string) $value);
    }

    return array_values(array_filter(array_map('trim', $values), function($id) {
      return $id !== '' && $id !== '0' && strtolower($id) !== 'null' && strtolower($id) !== 'undefined';
    }));
  }
  
  }
class PlaneDetails{}
