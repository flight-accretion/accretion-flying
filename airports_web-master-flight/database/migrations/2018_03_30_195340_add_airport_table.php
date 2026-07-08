<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAirportTable extends Migration
{
    
    public function up()
    {
      Schema::create('airport', function(Blueprint $table): void{
        $table->increments('id');
        $table->string('name', 100); 
        $table->integer('city_id');
        $table->text('icao'); 
        $table->text('iata'); 
        $table->text('latitude'); 
        $table->text('longitude'); 
        $table->dateTime('open_time');
        $table->dateTime('close_time');
        $table->timestamps();
      }); 
    }

    public function down()
    {
      Schema::drop('airport');
    }
}
