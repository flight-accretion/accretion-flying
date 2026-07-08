<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSecondaryContactTable extends Migration
{
    public function up()
    {
        Schema::create('secondary_contact', function(Blueprint $table): void{
        $table->increments('id');
        $table->integer('owner_id')->unsigned();
        $table->foreign('owner_id')->references('id')->on('owner');
        $table->string('name', 100); 
        $table->string('contact', 50);
        $table->string('email', 50);
        $table->timestamps();
      }); 
    }

    
    public function down()
    {
       Schema::drop('secondary_contact');
    }
}
