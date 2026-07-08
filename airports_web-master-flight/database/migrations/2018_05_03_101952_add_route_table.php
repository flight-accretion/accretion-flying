<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRouteTable extends Migration
{
    public function up()
    {
      Schema::create('route', function(Blueprint $table): void{
        $table->increments('id');
        $table->integer('location_1');
        $table->integer('location_2');
        $table->integer('time'); //it should be in minutes(converted time)
        $table->double('distance');
        $table->double('price');
        $table->timestamps();
      });
      
      Schema::table('plane', function(Blueprint $table): void{
        $table->integer('gt'); //it should be in minutes(converted time)
        $table->double('speed_coefficient')->default('0.65');
        $table->text('temp_latitude');
        $table->text('temp_longitude');
        $table->dateTime('from_date');
        $table->dateTime('to_date');
      });
      
      Schema::table('airport', function(Blueprint $table): void{
        $table->integer('gt')->default('10'); //it should be in minutes(converted time)
      });
    }

   
    public function down()
    {
      Schema::table('airport', function(Blueprint $table): void{
        $table->dropColumn('gt');
      });
      
      Schema::table('plane', function(Blueprint $table): void{
        $table->dropColumn('to_date');
        $table->dropColumn('from_date');
        $table->dropColumn('temp_longitude');
        $table->dropColumn('temp_latitude');
        $table->dropColumn('speed_coefficient');
        $table->dropColumn('gt');
      });
      
      Schema::drop('route');
    }
}
