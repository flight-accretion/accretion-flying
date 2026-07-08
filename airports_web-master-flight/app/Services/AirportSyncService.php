<?php namespace FlyingCalculation\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

use FlyingCalculation\Airport;
use FlyingCalculation\HandlingCharge;

class AirportSyncService
{
  protected $city_service;
  const SYNC_INTERVAL_DAYS = 30;

  public function __construct(CityService $city_service)
  {
    $this->city_service = $city_service;
  }

  public function syncAirports($countryCode = 'IN')
  {
    $countryCode = trim($countryCode) != '' ? trim($countryCode) : 'IN';
    $apiKey = env('API_NINJAS_KEY', 'FKjSFrU5QUZBvT3PkH6FS0O6n0zHXyl9XVdpaufm');
    $url = "https://api.api-ninjas.com/v1/airports?country={$countryCode}";

    try {
      $response = Http::withHeaders([
        'X-Api-Key' => $apiKey
      ])->connectTimeout(10)->timeout(30)->get($url);
    } catch (ConnectionException $e) {
      return $this->result(false, 'Airport API connection timed out. Please check internet/DNS and try again.');
    } catch (\Throwable $e) {
      return $this->result(false, 'Airport API request failed. Please try again.');
    }

    if($response->failed()){
      return $this->result(false, 'Failed to fetch airport data.');
    }

    $airports = $response->json() ?? [];

    if(!is_array($airports) || empty($airports)){
      return $this->result(false, 'No airport data received.');
    }

    $insertedCount = 0;
    $updatedCount = 0;
    $skippedCount = 0;
    $insertedNames = [];

    foreach($airports as $airportData){
      $iata = $airportData['iata'] ?? null;
      $icao = $airportData['icao'] ?? null;

      if(!$iata && !$icao){
        $skippedCount++;
        continue;
      }

      $cityName = $airportData['city'] ?? '';
      $stateName = $airportData['state'] ?? '';
      $countryName = $airportData['country'] ?? '';
      $stateName = $this->city_service->resolveStateNameForCity($cityName, $stateName, $countryName, $countryCode);

      if($stateName == ''){
        $stateName = 'Unknown';
      }

      $cityId = $this->city_service->getOrCreateCityId($cityName, $stateName, $countryName, $countryCode);
      $existing = $this->findExistingAirport($iata, $icao);

      if($existing){
        $existing->city_id = $cityId;
        $existing->state_name = $stateName;
        $existing->city_name = $cityName;
        $existing->country_name = $countryName;
        $existing->save();
        HandlingCharge::syncAirportCharge($existing);
        $updatedCount++;
        continue;
      }

      $airport = new Airport();
      $airport->name = $airportData['name'] ?? '';
      $airport->latitude = $airportData['latitude'] ?? 0;
      $airport->longitude = $airportData['longitude'] ?? 0;
      $airport->icao = $icao ?? '';
      $airport->iata = $iata ?? '';
      $airport->city_id = $cityId;
      $airport->state_name = $stateName;
      $airport->city_name = $cityName;
      $airport->country_name = $countryName;
      $airport->status = 1;
      $airport->save();

      HandlingCharge::syncAirportCharge($airport);

      $insertedCount++;
      $insertedNames[] = $cityName;
    }

    $updatedCityCount = $this->city_service->backfillUnknownCityStates(
      $this->city_service->getCountryNameForLookup('', $countryCode),
      $countryCode,
      50
    );

    return $this->result(true, $this->buildMessage($insertedCount, $insertedNames, $updatedCityCount), [
      'inserted_count' => $insertedCount,
      'updated_count' => $updatedCount,
      'skipped_count' => $skippedCount,
      'updated_city_count' => $updatedCityCount,
    ]);
  }

  public function markSuccessfulSync($countryCode = 'IN')
  {
    Cache::forever($this->lastRunCacheKey($countryCode), Carbon::now()->toDateTimeString());
  }

  public function nextRunAt($countryCode = 'IN')
  {
    $lastRunAt = Cache::get($this->lastRunCacheKey($countryCode));

    if(!$lastRunAt){
      return null;
    }

    return Carbon::parse($lastRunAt)->addDays(self::SYNC_INTERVAL_DAYS);
  }

  public function isDue($countryCode = 'IN')
  {
    $nextRunAt = $this->nextRunAt($countryCode);

    return empty($nextRunAt) || Carbon::now()->gte($nextRunAt);
  }

  private function findExistingAirport($iata, $icao)
  {
    return Airport::where(function($query) use ($iata, $icao) {
      if($iata){
        $query->where('iata', $iata);
      }

      if($icao){
        if($iata){
          $query->orWhere('icao', $icao);
        } else {
          $query->where('icao', $icao);
        }
      }
    })->first();
  }

  private function buildMessage($insertedCount, array $insertedNames, $updatedCityCount)
  {
    if($insertedCount > 0){
      $displayNames = array_slice($insertedNames, 0, 2);
      $message = implode(', ', $displayNames);

      if($insertedCount > 2){
        $message .= ', ...';
      }

      $message .= ' added successfully.';
    } else {
      $message = 'No new airports were added.';
    }

    if($updatedCityCount > 0){
      $message .= ' '.$updatedCityCount.' city state values updated.';
    }

    return $message;
  }

  private function result($success, $message, array $data = [])
  {
    return array_merge([
      'success' => $success,
      'message' => $message,
      'inserted_count' => 0,
      'updated_count' => 0,
      'skipped_count' => 0,
      'updated_city_count' => 0,
    ], $data);
  }

  private function lastRunCacheKey($countryCode)
  {
    return 'airports_sync_last_run_at_'.strtolower(trim($countryCode) != '' ? trim($countryCode) : 'in');
  }
}
