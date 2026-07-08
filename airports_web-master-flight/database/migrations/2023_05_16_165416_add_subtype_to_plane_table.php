<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubtypeToPlaneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plane', function (Blueprint $table): void {
            $table->integer('subtype')->unsigned()->after('type_id'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plane', function (Blueprint $table): void {
            $table->dropColumn('subtype');
        });
    }
}
