<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlaneToRouteTable extends Migration
{
    
    public function up()
    {
      Schema::table('route', function(Blueprint $table): void{
        $table->integer('plane_id'); 
      });
    }

    
    public function down()
    {
      Schema::table('route', function(Blueprint $table): void{
        $table->dropColumn('plane_id');
      });
    }
}
