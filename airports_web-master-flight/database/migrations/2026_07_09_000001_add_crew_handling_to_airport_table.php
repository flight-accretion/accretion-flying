<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCrewHandlingToAirportTable extends Migration
{
    public function up()
    {
        if(!Schema::hasColumn('airport', 'crew_handling')){
            Schema::table('airport', function (Blueprint $table): void {
                $table->decimal('crew_handling', 10, 2)->nullable()->default(25000)->after('gt');
            });
        }
    }

    public function down()
    {
        if(Schema::hasColumn('airport', 'crew_handling')){
            Schema::table('airport', function (Blueprint $table): void {
                $table->dropColumn('crew_handling');
            });
        }
    }
}
