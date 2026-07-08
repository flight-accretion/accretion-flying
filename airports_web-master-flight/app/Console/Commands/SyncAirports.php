<?php

namespace FlyingCalculation\Console\Commands;

use Illuminate\Console\Command;
use FlyingCalculation\Services\AirportSyncService;

class SyncAirports extends Command
{
  protected $signature = 'airports:sync {--country=IN} {--force : Run even if the last successful sync was less than 30 days ago}';

  protected $description = 'Sync airports from API Ninjas and update missing city states';

  protected $airport_sync_service;

  public function __construct(AirportSyncService $airport_sync_service)
  {
    parent::__construct();
    $this->airport_sync_service = $airport_sync_service;
  }

  public function handle()
  {
    $countryCode = $this->option('country') ?: 'IN';
    $nextRunAt = $this->airport_sync_service->nextRunAt($countryCode);

    if(!$this->option('force') && !$this->airport_sync_service->isDue($countryCode)){
        $this->line('Airport sync skipped. Next run due at '.$nextRunAt->toDateTimeString().'.');
        return 0;
    }

    $result = $this->airport_sync_service->syncAirports($countryCode);

    if(!$result['success']){
      $this->error($result['message']);
      return 1;
    }

    $this->airport_sync_service->markSuccessfulSync($countryCode);

    $this->line($result['message']);
    $this->line('Inserted: '.$result['inserted_count']);
    $this->line('Updated airports: '.$result['updated_count']);
    $this->line('Updated city states: '.$result['updated_city_count']);

    return 0;
  }
}
