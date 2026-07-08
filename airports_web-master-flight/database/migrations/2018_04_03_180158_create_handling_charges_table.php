<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHandlingChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('handling_charges', function(Blueprint $table): void{
        $table->increments('id');
        $table->integer('city_id');
        $table->double('charges'); 
        $table->timestamps();
      }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('handling_charges');
    }
}
