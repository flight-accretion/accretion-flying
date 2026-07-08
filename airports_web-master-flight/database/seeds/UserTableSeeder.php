<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = array(
    ['id' => 1, 'name' => 'Pleximus', 'dob' => '1/1/1987', 'gender' => 'M', 'email' => 'admin@accretion.com', 
    'password' => bcrypt('accretion'), 'image' => 'unknown.jpg', 'user_type' => 0, 'status' => 1]);//admin123
    
    DB::table('user')->insert($data);
  }
}
