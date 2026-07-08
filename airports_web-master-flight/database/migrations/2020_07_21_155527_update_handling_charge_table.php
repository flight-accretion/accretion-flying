<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateHandlingChargeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('handling_charges', function(Blueprint $table): void{
        $table->integer('airport_id')->nullable(); 
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('handling_charges', function(Blueprint $table): void{
        $table->dropColumn('airport_id');
      });
    }
}
