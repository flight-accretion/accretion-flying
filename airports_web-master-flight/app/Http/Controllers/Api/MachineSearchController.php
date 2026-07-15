<?php

namespace FlyingCalculation\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use FlyingCalculation\Http\Controllers\Controller;
use FlyingCalculation\Services\MachineApiSearchService;

class MachineSearchController extends Controller
{
  public function search(Request $request, MachineApiSearchService $machine_search_service)
  {
    if(!$this->hasValidApiKey($request)){
      return $this->apiResponse([
        'success' => false,
        'message' => 'Invalid API key.',
      ], 401, $request);
    }

    try {
      $data = $machine_search_service->search($request->all());
      return $this->apiResponse($data, 200, $request);
    } catch(Exception $exception) {
      return $this->apiResponse([
        'success' => false,
        'message' => 'Unable to fetch machine data.',
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
    $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, X-API-Key');
    $response->headers->set('Access-Control-Max-Age', '86400');

    return $response;
  }
}
