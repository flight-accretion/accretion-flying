<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOwnerAndSettingTable extends Migration
{
    public function up()
    {
        Schema::table('owner', function(Blueprint $table): void{
        $table->text('other'); 
      }); 
      
      Schema::table('setting', function(Blueprint $table): void{
        $table->float('cgst'); 
        $table->float('sgst'); 
        $table->float('igst'); 
        $table->float('gst'); 
      }); 
    }

    public function down()
    {
        Schema::table('owner', function(Blueprint $table): void{
        $table->dropColumn('other');
      }); 
      
      Schema::table('setting', function(Blueprint $table): void{
        $table->dropColumn('cgst');
        $table->dropColumn('sgst');
        $table->dropColumn('igst');
        $table->dropColumn('gst');
      }); 
    }
}
