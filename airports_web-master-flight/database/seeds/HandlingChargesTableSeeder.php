<?php

use Illuminate\Database\Seeder;

class HandlingChargesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $data = array(
      ['id' => 1, 'city_id' => 0, 'charges' => '25000', 'airport_id' => 0],
      ['id' => 2, 'city_id' => 41, 'charges' => '110000', 'airport_id' => null],
      ['id' => 3, 'city_id' => 19, 'charges' => '65000', 'airport_id' => null]);
    
      DB::table('handling_charges')->insert($data);
    }
}
