<?php

namespace FlyingCalculation;

use Illuminate\Database\Eloquent\Model;

class HandlingCharge extends Model
{
	protected $table = 'handling_charges';

  public static function getGeneralCharge()
  {
    $general_charge = self::where('airport_id', 0)->first();

    if(empty($general_charge)){
      $general_charge = self::where('city_id', 0)->first();
      if(!empty($general_charge)){
        $general_charge->airport_id = 0;
        $general_charge->save();
      }
    }

    if(!empty($general_charge)){
      return (float) $general_charge->charges;
    }

    return 0;
  }

  public static function saveGeneralCharge($amount)
  {
    $amount = (float) $amount;
    $general_charges = self::where('airport_id', 0)->get();

    if(count($general_charges) == 0){
      $general_charge = self::where('city_id', 0)->first();

      if(!empty($general_charge)){
        $general_charge->city_id = 0;
        $general_charge->airport_id = 0;
        $general_charge->charges = $amount;
        $general_charge->save();

        return $general_charge;
      }

      $general_charge = new self;
      $general_charge->city_id = 0;
      $general_charge->airport_id = 0;
      $general_charge->charges = $amount;
      $general_charge->save();

      return $general_charge;
    }

    foreach($general_charges as $general_charge){
      $general_charge->city_id = 0;
      $general_charge->airport_id = 0;
      $general_charge->charges = $amount;
      $general_charge->save();
    }

    return $general_charges->first();
  }

  public static function syncAirportCharge($airport, $amount = null)
  {
    if(empty($airport)){
      return null;
    }

    $general_charge = self::getGeneralCharge();
    $amount = (isset($amount) && $amount > 0) ? (float) $amount : $general_charge;
    $amount = max($amount, $general_charge);
    $charge_rows = self::where('airport_id', $airport->id)->get();

    if(count($charge_rows) == 0){
      $charge = new self;
      $charge->city_id = $airport->city_id;
      $charge->airport_id = $airport->id;
      $charge->charges = $amount;
      $charge->save();

      return $charge;
    }

    foreach($charge_rows as $charge){
      $charge->city_id = $airport->city_id;
      $charge->airport_id = $airport->id;
      $charge->charges = $amount;
      $charge->save();
    }

    return $charge_rows->first();
  }

  public static function applyGeneralChargeToAirports($amount)
  {
    $amount = (float) $amount;
    $general_charge = self::saveGeneralCharge($amount);
    self::whereNull('airport_id')->where('charges', 0)->update(['charges' => $amount]);
    $airports = Airport::all();

    foreach($airports as $airport){
      $charge_rows = self::where('airport_id', $airport->id)->get();

      if(count($charge_rows) == 0){
        $charge = new self;
        $charge->city_id = $airport->city_id;
        $charge->airport_id = $airport->id;
        $charge->charges = $amount;
        $charge->save();

        continue;
      }

      foreach($charge_rows as $charge){
        $charge->city_id = $airport->city_id;
        $charge->airport_id = $airport->id;

        if($charge->charges == 0){
          $charge->charges = $amount;
        }

        $charge->save();
      }
    }

    return $general_charge;
  }
}
