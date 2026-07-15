<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

if (! function_exists('legacy_controller_routes')) {
  function legacy_controller_routes($prefix, $controller)
  {
    $controller_class = 'FlyingCalculation\\Http\\Controllers\\' . $controller;
    $reflection = new ReflectionClass($controller_class);

    foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
      if ($method->getDeclaringClass()->getName() !== $controller_class) {
        continue;
      }

      $method_name = $method->getName();

      if ($method->isConstructor() || ! preg_match('/^(get|post|put|patch|delete|any)(.+)$/', $method_name, $matches)) {
        continue;
      }

      $verb = strtolower($matches[1]);
      $action = $matches[2];
      $uri = trim($prefix, '/');

      if ($action !== 'Index') {
        $uri .= '/' . Illuminate\Support\Str::kebab($action);
      }

      foreach ($method->getParameters() as $parameter) {
        $type = $parameter->getType();
        if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
          continue;
        }

        $uri .= '/{' . $parameter->getName() . '?}';
      }

      Route::$verb($uri, $controller . '@' . $method_name);
    }
  }
}

foreach ([
  'user' => 'UserController',
  'mail-content' => 'MailContentController',
  'booking' => 'BookingController',
  'admin' => 'AdminController',
  'plane' => 'PlaneController',
  'subtype' => 'PlaneSubtypeController',
  'owner' => 'OwnerController',
  'setting' => 'SettingController',
  'home' => 'HomeController',
  'route' => 'RouteController',
  'city' => 'CityController',
  'airport' => 'AirportController',
  'password' => 'Auth\PasswordController',
] as $prefix => $controller) {
  legacy_controller_routes($prefix, $controller);
}

Route::options('/api/v1/machines/search', 'Api\MachineSearchController@options');
Route::match(['GET', 'POST'], '/api/v1/machines/search', 'Api\MachineSearchController@search');
Route::options('/api/v1/airports', 'Api\AirportSearchController@options');
Route::get('/api/v1/airports', 'Api\AirportSearchController@index');

Route::get('/uploads/{path}', function ($path) {
  $uploads_root = realpath(public_path('uploads'));
  $normalized_path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
  $file_path = public_path('uploads' . DIRECTORY_SEPARATOR . $normalized_path);
  $real_file_path = realpath($file_path);

  if($uploads_root && $real_file_path && is_file($real_file_path) && strpos($real_file_path, $uploads_root) === 0) {
    return response()->file($real_file_path, ['Cache-Control' => 'public, max-age=86400']);
  }

  if(app()->environment('local')) {
    $remote_uploads_url = rtrim(env('REMOTE_UPLOADS_URL', 'https://flights.airaccretion.com/uploads'), '/');
    $remote_path = implode('/', array_map('rawurlencode', explode('/', str_replace('\\', '/', $path))));
    return redirect()->away($remote_uploads_url . '/' . $remote_path);
  }

  $placeholder = public_path('img/plane-2image.jpg');
  if(!is_file($placeholder)) {
    $placeholder = public_path('images/unknown.jpg');
  }

  return response()->file($placeholder, ['Cache-Control' => 'public, max-age=300']);
})->where('path', '.*');


Route::get('/my-bookings', 'BookingController@getMy');
Route::get('/update-profile', 'UserController@getUpdateProfile');
Route::get('/change-password', 'UserController@getChangePassword');
Route::get('/sign-up', 'UserController@getSignUp');
Route::get('/airport_list', 'HomeController@getAirportList');
Route::get('/get_ground_time', 'HomeController@getGroundTime');
Route::post('/set_ground_time', 'HomeController@setGroundTime');
Route::get('/plane_list', 'HomeController@getPlaneList');
Route::get('/handling-charge', 'CityController@getHandlingCharge');
Route::post('/handling-charge', 'CityController@postHandlingCharge');
Route::get('/', 'HomeController@index');
