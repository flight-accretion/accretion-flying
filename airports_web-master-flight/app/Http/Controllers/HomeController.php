<?php

namespace FlyingCalculation\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Contracts\Auth\Guard;
use FlyingCalculation\Http\Requests;
use FlyingCalculation\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

use Auth;
use Session;
use DB;

use FlyingCalculation\Plane;
use FlyingCalculation\PlaneType;
use FlyingCalculation\City;
use FlyingCalculation\State;
use FlyingCalculation\Owner;
use FlyingCalculation\PlaneImage;
use FlyingCalculation\Airport;
use FlyingCalculation\HandlingCharge;
use FlyingCalculation\Services\AirportSyncService;
use FlyingCalculation\Services\CityService;

class HomeController extends Controller
{
  protected $cityStateLookupCache = [];

  public function __construct(Guard $auth, CityService $city_service, AirportSyncService $airport_sync_service)
	{
		$this->auth = $auth;
    $this->city_service = $city_service;
    $this->airport_sync_service = $airport_sync_service;
	}

  public function index()
  {
    $plane_types = PlaneType::orderBy('updated_at')->get();
    $cities = DB::Table('city')->pluck('name','id');
    $airports = Airport::where('status',1)->orderBy('updated_at')->get();
    
    if(Auth::check())
    {
      //0=Admin, 1=Executive, 2=Data monitor User, 3=Video Uploader, 4=Customer Admin, 5=SubCustomer Admin, 6=Customer
      if(Auth::User()->user_type == 0)
      {
        Session::forget('menu');
        return redirect()->to('/admin/dashboard');
      }
      else if(Auth::User()->user_type == 1)//Executive
      {
        return view('home')
      ->with('airports', $airports)
      ->with('cities', $cities)
      ->with('plane_types', $plane_types);
      }
      else if(Auth::User()->user_type == 2)//Data monitor User
      {       

      }
      else
      {
        return view('welcome')
					->with('airports', $airports)
					->with('cities', $cities)
					->with('plane_types', $plane_types);
      }
    }
  
    else
    {
      return view('welcome')
      ->with('airports', $airports)
      ->with('cities', $cities)
      ->with('plane_types', $plane_types);
    }    
  }
  
  
  public function getPlaneList(Request $request){
    return view('plane_list')
    ->with('planes', $planes)
    ->with('menu','plane_list');
  }

  private function getOrCreateCountryId($countryName, $countryCode)
  {
    $countryName = trim($countryName);
    $countryCode = trim($countryCode);

    if(strtoupper($countryName) == 'IN' || ($countryName == '' && strtoupper($countryCode) == 'IN')){
      $countryName = 'India';
    }

    if($countryName == ''){
      $countryName = ($countryCode != '' ? $countryCode : 'Unknown');
    }

    $country_id = DB::table('country')
      ->whereRaw('LOWER(name) = ?', [strtolower($countryName)])
      ->value('id');

    if(!empty($country_id)){
      return $country_id;
    }

    return DB::table('country')->insertGetId([
      'name' => $countryName,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s')
    ]);
  }

  private function getCountryNameForLookup($countryName, $countryCode)
  {
    $countryName = trim($countryName);
    $countryCode = trim($countryCode);

    if(strtoupper($countryName) == 'IN' || ($countryName == '' && strtoupper($countryCode) == 'IN')){
      return 'India';
    }

    return $countryName != '' ? $countryName : $countryCode;
  }

  private function getStateNameFromOpenStreetMap($cityName, $countryName, $countryCode)
  {
    $cityName = trim($cityName);
    $lookupCountry = $this->getCountryNameForLookup($countryName, $countryCode);

    if($cityName == ''){
      return '';
    }

    $cacheKey = strtolower($cityName.'|'.$lookupCountry);
    if(array_key_exists($cacheKey, $this->cityStateLookupCache)){
      return $this->cityStateLookupCache[$cacheKey];
    }

    try {
      $response = Http::withHeaders([
        'User-Agent' => 'FlyingCalculation/1.0',
        'Accept' => 'application/json',
      ])->timeout(8)->get('https://nominatim.openstreetmap.org/search', [
        'city' => $cityName,
        'country' => $lookupCountry,
        'format' => 'json',
        'addressdetails' => 1,
        'limit' => 1,
      ]);

      if(!$response->successful()){
        $this->cityStateLookupCache[$cacheKey] = '';
        return '';
      }

      $results = $response->json();
      $address = isset($results[0]['address']) && is_array($results[0]['address']) ? $results[0]['address'] : [];

      foreach(['state', 'province', 'region', 'state_district', 'county'] as $addressKey){
        if(!empty($address[$addressKey])){
          $this->cityStateLookupCache[$cacheKey] = trim($address[$addressKey]);
          return $this->cityStateLookupCache[$cacheKey];
        }
      }
    } catch (\Exception $e) {
      $this->cityStateLookupCache[$cacheKey] = '';
      return '';
    }

    $this->cityStateLookupCache[$cacheKey] = '';
    return '';
  }

  private function resolveStateNameForCity($cityName, $stateName, $countryName, $countryCode)
  {
    $stateName = trim($stateName);

    if($stateName != '' && strtolower($stateName) != 'unknown'){
      return $stateName;
    }

    $resolvedStateName = $this->getStateNameFromOpenStreetMap($cityName, $countryName, $countryCode);

    return $resolvedStateName != '' ? $resolvedStateName : $stateName;
  }

  private function getOrCreateStateId($stateName, $countryName, $countryCode)
  {
    $stateName = trim($stateName);

    if($stateName == ''){
      $stateName = 'Unknown';
    }

    $state_id = State::whereRaw('LOWER(name) = ?', [strtolower($stateName)])->value('id');

    if(!empty($state_id)){
      return $state_id;
    }

    $state = new State;
    $state->name = $stateName;
    $state->country_id = $this->getOrCreateCountryId($countryName, $countryCode);
    $state->save();

    return $state->id;
  }

  private function getOrCreateCityId($cityName, $stateName, $countryName, $countryCode)
  {
    $cityName = trim($cityName);
    $stateName = $this->resolveStateNameForCity($cityName, $stateName, $countryName, $countryCode);

    if($cityName == ''){
      return 0;
    }

    $city = City::whereRaw('LOWER(name) = ?', [strtolower($cityName)])->first();

    if(!empty($city)){
      $currentState = State::find($city->state_id);
      if($stateName != '' && (empty($currentState) || strtolower($currentState->name) == 'unknown')){
        $city->state_id = $this->getOrCreateStateId($stateName, $countryName, $countryCode);
        $city->save();
      }

      return $city->id;
    }

    $city = new City;
    $city->name = $cityName;
    $city->state_id = $this->getOrCreateStateId($stateName, $countryName, $countryCode);
    $city->save();

    return $city->id;
  }

  public function getAirportList(Request $request)
{
    $countryCode = $request->query('country', 'IN');
    $result = $this->airport_sync_service->syncAirports($countryCode);

    if(!$result['success']){
        return redirect()->back()->with('error', $result['message']);
    }

    $this->airport_sync_service->markSuccessfulSync($countryCode);

    return redirect()->back()->with('success', $result['inserted_count'].' airports added: '.$result['message']);
}

//   public function getAirportList(Request $request)
// {
//     $apiKey = 'FKjSFrU5QUZBvT3PkH6FS0O6n0zHXyl9XVdpaufm';
//     $countryCode = $request->query('country', 'IN'); // default India
//     $url = "http://api.aviationstack.com/v1/airports?access_key={$apiKey}&country_iso2={$countryCode}";

//     $response = Http::get($url);

//     if ($response->failed()) {
//         return redirect()->back()->with('error', 'Failed to fetch airport data.');
//     }

//     $airports = $response->json()['data'] ?? [];

//     if (!is_array($airports) || empty($airports)) {
//         return redirect()->back()->with('error', 'No airport data received.');
//     }

//     $insertedCount = 0;
//     $insertedNames = [];

//     foreach ($airports as $airportData) {
//         $iata = $airportData['iata_code'] ?? null;
//         $icao = $airportData['icao_code'] ?? null;

//         if (!$iata && !$icao) continue;

//         // Check if airport already exists using IATA or ICAO
//         $existing = Airport::where('iata', $iata)
//                            ->orWhere('icao', $icao)
//                            ->first();

//         if ($existing) continue;

//         // Find city_id from City table using city name
//         $cityName = $airportData['airport_name'] ?? '';
//         $stateName = $airportData['region_name'] ?? ''; // if available in API
//         $countryName = $airportData['country_name'] ?? '';

//         $city_id = City::where('name', $cityName)
//                         ->value('id') ?? 0;

//         // Insert new airport
//         $airport = new Airport();
//         $airport->name = $airportData['airport_name'] ?? '';
//         $airport->latitude = $airportData['latitude'] ?? 0;
//         $airport->longitude = $airportData['longitude'] ?? 0;
//         $airport->icao = $icao ?? '';
//         $airport->iata = $iata ?? '';
//         $airport->city_id = $city_id;
//         $airport->state_name = $stateName;   // make sure you have this column in DB
//         $airport->city_name = $cityName;
//         $airport->country_name = $countryName; // make sure you have this column in DB
//         $airport->status = 1; // set active
//         $airport->save();

//         $insertedCount++;
//         $insertedNames[] = $cityName;
//     }
//     if ($insertedCount > 0) {
//     $displayNames = array_slice($insertedNames, 0, 2); // first 2 names
//     $message = implode(', ', $displayNames);
//     if ($insertedCount > 2) $message .= ', ...';
//     $message = $message . ' added successfully.';
//   } else {
//       $message = 'No new airports were added.';
//   }

//     return redirect()->back()->with('success', $message);
// }

//  public function getAirportList(Request $request)
// {
//     $apiKey = '5419e97cc520d6a8262a04e8530b4206';
//     $countryCode = $request->query('country', 'IN'); // default India
//     $url = "http://api.aviationstack.com/v1/airports?access_key={$apiKey}&country_iso2={$countryCode}";

//     $response = Http::get($url);

//     if ($response->failed()) {
//         return redirect()->back()->with('error', 'Failed to fetch airport data.');
//     }

//     $airports = $response->json()['data'] ?? [];
//     if (!is_array($airports) || empty($airports)) {
//         return redirect()->back()->with('error', 'No airport data received.');
//     }

//     $insertedCount = 0;
//     $updatedCount = 0;

//     foreach ($airports as $airportData) {
//         $iata = $airportData['iata_code'] ?? null;
//         $icao = $airportData['icao_code'] ?? null;
//         if (!$iata && !$icao) continue;

//         // Try to find existing airport by IATA or ICAO
//         $airport = Airport::where('iata', $iata)
//                           ->orWhere('icao', $icao)
//                           ->first();

//         if ($airport) {
//             // Update existing airport data
//             $airport->update([
//                 'name' => $airportData['airport_name'] ?? $airport->name,
//                 'latitude' => $airportData['latitude'] ?? $airport->latitude,
//                 'longitude' => $airportData['longitude'] ?? $airport->longitude,
//                 'icao' => $icao ?? $airport->icao,
//                 'iata' => $iata ?? $airport->iata,
//                 'city_id' => City::where('name', $airportData['city'] ?? '')->value('id') ?? $airport->city_id
//             ]);
//             $updatedCount++;
//         } else {
//             // Insert new airport
//             Airport::create([
//                 'name' => $airportData['airport_name'] ?? '',
//                 'latitude' => $airportData['latitude'] ?? 0,
//                 'longitude' => $airportData['longitude'] ?? 0,
//                 'icao' => $icao ?? '',
//                 'iata' => $iata ?? '',
//                 'city_id' => City::where('name', $airportData['city'] ?? '')->value('id') ?? 0,
//             ]);
//             $insertedCount++;
//         }
//     }

//     return redirect()->back()->with('success', "$insertedCount new airports added, $updatedCount updated successfully.");
// }
  
  // public function getAirportList(Request $request){
  //   $apiKey = '5419e97cc520d6a8262a04e8530b4206';
  //       $countryCode = $request->query('country', 'IN'); // default India
  //       $url = "http://api.aviationstack.com/v1/airports?access_key={$apiKey}&country_iso2={$countryCode}";

  //       // Fetch data from Aviation Edge API
  //       $response = Http::get($url);

  //       if ($response->failed()) {
  //           return response()->json([
  //               'success' => false,
  //               'message' => 'Failed to fetch airport data.'
  //           ], 500);
  //       }

  //       $airports = $response->json()['data'] ?? [];

  //       if (!is_array($airports) || empty($airports)) {
  //           return response()->json([
  //               'success' => false,
  //               'message' => 'No airport data received.'
  //           ], 404);
  //       }

  //       // Filter by search term if provided
  //      if ($request->has('search')) {
  //           $search = strtolower($request->query('search'));
  //           $airports = array_filter($airports, function ($airport) use ($search) {
  //               return str_contains(strtolower($airport['airport_name'] ?? ''), $search)
  //                   || str_contains(strtolower($airport['iata_code'] ?? ''), $search)
  //                   || str_contains(strtolower($airport['city_iata_code'] ?? ''), $search);
  //           });
  //       }

  //       // Pagination
  //       $perPage = (int) $request->query('per_page', 50);
  //       $page = (int) $request->query('page', 1);
  //       $total = count($airports);
  //       $sliced = array_slice($airports, ($page - 1) * $perPage, $perPage);

  //       // Update database
  //       foreach ($sliced as $airportData) {
  //           $iata = $airportData['iata_code'] ?? null;
  //           if (!$iata) continue;

  //           Airport::updateOrCreate(
  //               ['iata' => $iata],
  //               [
  //                   'name' => $airportData['airport_name'] ?? '',
  //                   'latitude' => $airportData['latitude'] ?? 0,
  //                   'longitude' => $airportData['longitude'] ?? 0,
  //                   'icao' => $airportData['icao_code'] ?? '',
  //                   'city_id' => City::where('name', $airportData['city'] ?? '')->value('id') ?? 0
  //               ]
  //           );
  //       }

  //      return response()->json([
  //           'success' => true,
  //           'total' => $total,
  //           'page' => $page,
  //           'per_page' => $perPage,
  //           'data' => array_values($sliced)
  //       ]);
  //        return redirect()->back()->with('success', 'Airports updated successfully');
  // }
  
  public function getGroundTime(Request $request){
    $airports = Airport::where('status',1)->orderBy('created_at')->get()->keyby('id'); 
    return view('admin.ground_time')
    ->with('airports',$airports)
    ->with('menu','ground_time');
  }
  
  //Get all airports
  public function getAllAirports(Request $request){
    $request_data = $request->all(); 
    $data = DB::Table('airport')->where('name', 'like', '%'.$request_data['name_start_with'].'%')->pluck('name','id');
    return $data; 
  }
  
  
  public function setGroundTime(Request $request){
    $request_data = $request->all();
    DB::table('airport')->where('id', $request_data['airport-id'])->update(['gt' => $request_data['gt']]);
    return redirect()->back()->with('success', 'Ground Time updated successfully');
  }
  
}
