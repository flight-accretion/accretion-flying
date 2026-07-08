<?php

use Illuminate\Database\Seeder;

class PlaneTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $data = array(
      ['id' => 1, 'name' => 'Plane'],
      ['id' => 2, 'name' => 'Helicopter'],
      ['id' => 3, 'name' => 'Air Ambulance']);
    
      DB::table('plane_type')->insert($data);
    }
}
