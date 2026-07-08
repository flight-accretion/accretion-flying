<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(UserTableSeeder::class);
        $this->call(PlaneTypeTableSeeder::class);
        $this->call(CountryTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        $this->call(CityTableSeeder::class);
        $this->call(HandlingChargesTableSeeder::class);

        Model::reguard();
    }
}
