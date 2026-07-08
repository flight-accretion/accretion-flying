<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePlaneTable extends Migration
{

	public function up()
	{
		Schema::table('plane', function(Blueprint $table): void{
			$table->integer('temporary_airport_id'); 
			$table->integer('temporary_city_id'); 
		});
	}

  
	public function down()
	{
		Schema::table('plane', function(Blueprint $table): void{
			$table->dropColumn('temporary_city_id');
			$table->dropColumn('temporary_airport_id');
		});
	}
}
