<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlyingCalculationTables extends Migration
{
    
    public function up()
    {
        Schema::create('user', function(Blueprint $table): void{
        $table->increments('id');
        $table->string('name', 100); 
        $table->dateTime('dob'); 
        $table->char('gender', 1); 
        $table->string('email', 50)->unique();
        $table->string('password', 60);
        $table->text('image');
        $table->text('address');
        $table->integer('user_type'); // 0=admin, 1=user
        $table->string('contact_number', 50);
        $table->double('points');
        $table->integer('city_id')->unsigned();
        $table->text('activation_code');
        $table->integer('status'); // 0=inactive, 1=active, 2=disabled
        $table->rememberToken();
        $table->timestamps();
      });
      
       Schema::create('country', function(Blueprint $table): void{
        $table->increments('id');
        $table->string('name', 100)->unique();
        $table->timestamps();
      });
      
      Schema::create('state', function(Blueprint $table): void{
        $table->increments('id');
        $table->string('name', 100)->unique();
        $table->integer('country_id')->unsigned();
        $table->foreign('country_id')->references('id')->on('country');
        $table->timestamps();
      });

      Schema::create('city', function(Blueprint $table): void{
        $table->increments('id');
        $table->string('name', 100);
        $table->integer('state_id')->unsigned();
        $table->foreign('state_id')->references('id')->on('state');
        $table->timestamps();
      });
      
      Schema::create('owner', function(Blueprint $table): void{
        $table->increments('id');
        $table->string('name', 100); 
        $table->string('contact_number_1', 50);
        $table->string('contact_number_2', 50);
        $table->string('email_1', 50);
        $table->string('email_2', 50);
        $table->timestamps();
      }); 
      
       Schema::create('plane_type', function(Blueprint $table): void{
        $table->increments('id');
        $table->string('name', 100);
        $table->timestamps();
      });
      
      Schema::create('plane', function(Blueprint $table): void{
        $table->increments('id');
        $table->string('name', 100); 
        $table->integer('type_id')->unsigned();
        $table->foreign('type_id')->references('id')->on('plane_type'); // 0=plane, 1=helicopter, 2=air_ambulance
        $table->double('price_per_hour'); 
        $table->integer('city_id')->unsigned();
        $table->foreign('city_id')->references('id')->on('city');
        $table->text('latitude'); 
        $table->text('longitude'); 
        $table->integer('seats'); 
        $table->integer('lavatory'); //0=yes, 1=no
        $table->integer('owner_id')->unsigned();
        $table->foreign('owner_id')->references('id')->on('owner');
        $table->double('speed'); 
        $table->text('display_image'); 
        $table->text('note');
        $table->timestamps();
      });
      
      Schema::create('plane_image', function($table): void{
        $table->increments('id');
        $table->integer('plane_id'); 
        $table->text('images'); 
        $table->timestamps();
      });
      
      Schema::create('setting', function(Blueprint $table): void{
        $table->increments('id');
        $table->integer('setting_type'); // 0=GST, 1=Fixed medical team cost 
        $table->dateTime('from_date');
        $table->dateTime('to_date');
        $table->integer('is_percent'); 
        $table->double('amount');
        $table->integer('status'); // 0=inactive, 1=active
        $table->timestamps();
      });
    }

    public function down()
    {
      Schema::drop('setting');
      Schema::drop('plane_image');
      Schema::drop('plane');
      Schema::drop('plane_type');
      Schema::drop('owner');
      Schema::drop('city');
      Schema::drop('state');
      Schema::drop('country');
      Schema::drop('user');
    }
}
