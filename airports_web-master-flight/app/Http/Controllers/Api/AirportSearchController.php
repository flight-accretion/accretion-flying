<?php

namespace FlyingCalculation\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use FlyingCalculation\Airport;
use FlyingCalculation\Http\Controllers\Controller;

class AirportSearchController extends Controller
{
  public function index(Request $request)
  {
    if(!$this->hasValidApiKey($request)){
      return $this->apiResponse([
        'success' => false,
        'message' => 'Invalid API key.',
      ], 401, $request);
    }

    try {
      $columns = Schema::getColumnListing('airport');
      $query = Airport::where('status', 1);
      $search = trim((string) $request->query('q', $request->query('search', '')));
      $limit = (int) $request->query('limit', 500);
      $limit = max(1, min($limit, 1000));

      if($search !== ''){
        $query->where(function($sub_query) use ($search, $columns) {
          $sub_query->where('name', 'like', '%'.$search.'%');

          foreach(['city_name', 'state_name', 'country_name', 'iata', 'icao'] as $column){
            if(in_array($column, $columns, true)){
              $sub_query->orWhere($column, 'like', '%'.$search.'%');
            }
          }
        });
      }

      if(in_array('city_name', $columns, true)){
        $query->orderBy('city_name');
      }

      $airports = $query
        ->orderBy('name')
        ->limit($limit)
        ->get()
        ->map(function($airport) {
          return [
            'id' => (int) $airport->id,
            'name' => $airport->name,
            'city_id' => (int) $airport->city_id,
            'city_name' => isset($airport->city_name) ? $airport->city_name : '',
            'state_name' => isset($airport->state_name) ? $airport->state_name : '',
            'country_name' => isset($airport->country_name) ? $airport->country_name : '',
            'iata' => isset($airport->iata) ? $airport->iata : '',
            'icao' => isset($airport->icao) ? $airport->icao : '',
            'latitude' => is_numeric($airport->latitude) ? (float) $airport->latitude : null,
            'longitude' => is_numeric($airport->longitude) ? (float) $airport->longitude : null,
            'gt' => isset($airport->gt) && is_numeric($airport->gt) ? (float) $airport->gt : 0,
          ];
        })
        ->values();

      return $this->apiResponse([
        'success' => true,
        'meta' => [
          'count' => $airports->count(),
          'search' => $search,
        ],
        'data' => $airports,
      ], 200, $request);
    } catch(Exception $exception) {
      return $this->apiResponse([
        'success' => false,
        'message' => 'Unable to fetch airport data.',
        'error' => config('app.debug') ? $exception->getMessage() : null,
      ], 500, $request);
    }
  }

  public function options(Request $request)
  {
    return $this->apiResponse(null, 204, $request);
  }

  private function hasValidApiKey(Request $request)
  {
    $api_key = env('MACHINE_API_KEY', '');

    if($api_key === ''){
      return true;
    }

    $given_key = $request->header('X-API-Key', $request->query('api_key', ''));

    return hash_equals($api_key, (string) $given_key);
  }

  private function apiResponse($data, $status, Request $request)
  {
    $response = $data === null ? response('', $status) : response()->json($data, $status);

    $origin = $request->headers->get('Origin', '*');
    $allowed_origins = array_filter(array_map('trim', explode(',', env('MACHINE_API_ALLOWED_ORIGINS', '*'))));
    $allow_origin = '*';

    if(!in_array('*', $allowed_origins, true)){
      $allow_origin = in_array($origin, $allowed_origins, true) ? $origin : reset($allowed_origins);
    }

    $response->headers->set('Access-Control-Allow-Origin', $allow_origin ?: '*');
    $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
    $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, X-API-Key');
    $response->headers->set('Access-Control-Max-Age', '86400');

    return $response;
  }
}
