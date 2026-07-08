<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFlowerShowerToPlaneTable extends Migration
{
    
    public function up()
    {
      Schema::table('plane', function(Blueprint $table): void{
        $table->integer('flower_shower'); // 1=yes, 0=no
        $table->integer('airport_id'); 
      });
    }

    public function down()
    {
      Schema::table('plane', function(Blueprint $table): void{
        $table->dropColumn('airport_id');
        $table->dropColumn('flower_shower');
      });
    }
}
