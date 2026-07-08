<?php

use Illuminate\Database\Seeder;

class AirportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $data = array(
      ['id' => 1, 'name' => 'Kheria', 'iata_code' => 'AGR', 'country_id' => 1, 'city_id' => 68, 'latitude' => ''],
      ['id' => 2, 'name' => 'Agatti Island', 'iata_code' => 'AGX', 'country_id' => 1, 'city_id' => 69],
      ['id' => 3, 'name' => 'Aizawl', 'iata_code' => 'AJL', 'country' => 'India', 'city' => 'Aizawl'],
      ['id' => 4, 'name' => 'Akola', 'iata_code' => 'AKD', 'country' => 'India', 'city' => 'Akola'],
      ['id' => 5, 'name' => 'Ahmedabad', 'iata_code' => 'AMD', 'country' => 'India', 'city' => 'Ahmedabad'],
      ['id' => 6, 'name' => 'Raja Sansi International Airport', 'iata_code' => 'ATQ', 'country' => 'India', 'city' => 'Amritsar'],
      ['id' => 7, 'name' => 'Bhubaneswar', 'iata_code' => 'BBI', 'country' => 'India', 'city' => 'Bhubaneshwar'],
      ['id' => 8, 'name' => 'Vadodara', 'iata_code' => 'BDQ', 'country' => 'India', 'city' => 'Vadodara'],
      ['id' => 9, 'name' => 'Bareli', 'iata_code' => 'BEK', 'country' => 'India', 'city' => 'Bareli'],
      ['id' => 10, 'name' => 'Bellary', 'iata_code' => 'BEP', 'country' => 'India', 'city' => 'Bellary'],
      ['id' => 11, 'name' => 'Rudra Mata', 'iata_code' => 'BHJ', 'country' => 'India', 'city' => 'Bhuj'],
      ['id' => 12, 'name' => 'Bhopal', 'iata_code' => 'BHO', 'country' => 'India', 'city' => 'Bhopal'],
      ['id' => 13, 'name' => 'Bhavnagar', 'iata_code' => 'BHU', 'country' => 'India', 'city' => 'Bhavnagar'],
      ['id' => 14, 'name' => 'Bikaner', 'iata_code' => 'BKB', 'country' => 'India', 'city' => 'Bikaner'],
      ['id' => 15, 'name' => 'Bangalore International Airport', 'iata_code' => 'BLR', 'country' => 'India', 'city' => 'Bangalore'],
      ['id' => 16, 'name' => 'Chhatrapati Shivaji International (Sahar International)', 'iata_code' => 'BOM', 'country' => 'India', 'city' => 'Mumbai'],
      ['id' => 17, 'name' => 'Begumpet', 'iata_code' => 'BPM', 'country' => 'India', 'city' => 'Hyderabad'],
      ['id' => 18, 'name' => 'Bhatinda', 'iata_code' => 'BUP', 'country' => 'India', 'city' => 'Bhatinda'],
      ['id' => 19, 'name' => 'Car Nicobar', 'iata_code' => 'CBD', 'country' => 'India', 'city' => 'Car Nicobar'],
      ['id' => 20, 'name' => 'Kozhikode Airport', 'iata_code' => 'CCJ', 'country' => 'India', 'city' => 'Calicut'],
      ['id' => 21, 'name' => 'Netaji Subhas Chandra', 'iata_code' => 'CCU', 'country' => 'India', 'city' => 'Kolkata'],
      ['id' => 22, 'name' => 'Cuddapah', 'iata_code' => 'CDP', 'country' => 'India', 'city' => 'Cuddapah'],
      ['id' => 23, 'name' => 'Peelamedu', 'iata_code' => 'CJB', 'country' => 'India', 'city' => 'Coimbatore'],
      ['id' => 24, 'name' => 'Cooch Behar', 'iata_code' => 'COH', 'country' => 'India', 'city' => 'Cooch Behar'],
      ['id' => 25, 'name' => 'Cochin International', 'iata_code' => 'COK', 'country' => 'India', 'city' => 'Kochi'],
      ['id' => 26, 'name' => 'Dhanbad', 'iata_code' => 'DBD', 'country' => 'India', 'city' => 'Dhanbad'],
      ['id' => 27, 'name' => 'Dehra Dun', 'iata_code' => 'DED', 'country' => 'India', 'city' => 'Dehradun'],
      ['id' => 28, 'name' => 'Indira Gandhi International', 'iata_code' => 'DEL', 'country' => 'India', 'city' => 'New Delhi'],
      ['id' => 29, 'name' => 'Deparizo', 'iata_code' => 'DEP', 'country' => 'India', 'city' => 'Deparizo'],
      ['id' => 30, 'name' => 'Gaggal Airport', 'iata_code' => 'DHM', 'country' => 'India', 'city' => 'Dharamsala'],
      ['id' => 31, 'name' => 'Dibrugarh', 'iata_code' => 'DIB', 'country' => 'India', 'city' => 'Dibrugarh'],
      ['id' => 32, 'name' => 'Diu', 'iata_code' => 'DIU', 'country' => 'India', 'city' => 'Diu'],
      ['id' => 33, 'name' => 'Dimapur', 'iata_code' => 'DMU', 'country' => 'India', 'city' => 'Dimapur'],
      ['id' => 34, 'name' => 'Borjhar', 'iata_code' => 'GAU', 'country' => 'India', 'city' => 'Guwahati'],
      ['id' => 35, 'name' => 'Gaya', 'iata_code' => 'GAY', 'country' => 'India', 'city' => 'Gaya'],
      ['id' => 36, 'name' => 'Goa International', 'iata_code' => 'GOI', 'country' => 'India', 'city' => 'Goa'],
      ['id' => 37, 'name' => 'Gorakhpur', 'iata_code' => 'GOP', 'country' => 'India', 'city' => 'Gorakhpur'],
      ['id' => 38, 'name' => 'Guna', 'iata_code' => 'GUX', 'country' => 'India', 'city' => 'Guna'],
      ['id' => 39, 'name' => 'Gwalior', 'iata_code' => 'GUX', 'country' => 'India', 'city' => 'Gwalior'],
      ['id' => 40, 'name' => 'Gwalior', 'iata_code' => 'GWL', 'country' => 'India', 'city' => 'Gwalior'],
      ['id' => 41, 'name' => 'Hubli', 'iata_code' => 'HBX', 'country' => 'India', 'city' => 'Hubli'],
      ['id' => 42, 'name' => 'Khajuraho', 'iata_code' => 'HJR', 'country' => 'India', 'city' => 'Khajuraho'],
      ['id' => 43, 'name' => 'Hissar', 'iata_code' => 'HSS', 'country' => 'India', 'city' => 'Hissar'],
      ['id' => 44, 'name' => 'Hyderabad Airport', 'iata_code' => 'HYD', 'country' => 'India', 'city' => 'Hyderabad'],
      ['id' => 45, 'name' => 'Devi Ahilyabai Holkar', 'iata_code' => 'IDR', 'country' => 'India', 'city' => 'Indore'],
      ['id' => 46, 'name' => 'Municipal', 'iata_code' => 'IDR', 'country' => 'India', 'city' => 'Imphal'],
      );
      DB::table('airports')->insert($data);
    }
}
