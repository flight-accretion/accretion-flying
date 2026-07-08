<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingsTables extends Migration
{
	public function up()
	{
		Schema::create('bookings', function(Blueprint $table): void{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('plane_id');
			$table->string('plane_name', 100); 
			$table->string('stay_hours', 10); 
			$table->string('stay_mins', 10); 
			$table->string('flying_hours', 10); 
			$table->string('flying_mins', 10); 
			$table->string('total_hours', 10); 
			$table->string('total_mins', 10); 
			$table->double('total_flying_cost'); 
			$table->double('ground_handling'); 
			$table->double('crew_handling'); 
			$table->text('flights'); 
			$table->double('points_earned'); 
			$table->double('points_redeemed'); 
			$table->timestamps();
		});
		
		Schema::create('booking_settings', function(Blueprint $table): void{
			$table->increments('id');
			$table->string('field', 30);
			$table->string('value', 30);
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('booking_settings');
		Schema::drop('bookings');
	}
}
