<?php

use Illuminate\Database\Seeder;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $data = array(
      ['id' => 1, 'name' => 'India']);
      DB::table('country')->insert($data);
    }
}
