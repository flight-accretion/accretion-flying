<?php

namespace FlyingCalculation;

use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
  protected $table = 'airport';

  protected $fillable = [
        'iata',
        'icao',
        'name',
        'latitude',
        'longitude',
        'city_id',
        'crew_handling'
    ];
}
