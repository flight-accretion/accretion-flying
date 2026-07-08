<?php

namespace FlyingCalculation\Http\Controllers;

use Illuminate\Http\Request;

use FlyingCalculation\Http\Requests;
use FlyingCalculation\Http\Controllers\Controller;

use FlyingCalculation\Booking;
use FlyingCalculation\BookingSetting;
use FlyingCalculation\User;

class BookingController extends Controller
{
	public function getIndex() {
		if(auth()->check() && auth()->user()->user_type == 0){
			$bookings = Booking::select('bookings.*', 'user.name')
												->leftJoin('user', 'user.id', '=', 'bookings.user_id')
												 ->get();

			return view('admin.view_bookings')
			->with('bookings', $bookings)
			->with('menu', 'bookings')
      ->with('sub_menu', 'view_bookings');
		}
		else {
			return redirect('/');
		}
	}
	
	public function getPoints() {
		$points = 0;
		$settings = BookingSetting::where('field', 'points')->first();
		if(!empty($settings) > 0) {
			$points = $settings->value;
		}
		
		return view('admin.booking_points')
			->with('points', $points)
			->with('menu', 'bookings')
      ->with('sub_menu', 'view_booking_points');
	}
	
	public function postPoints(Request $request) {
		$request_data = $request->all();
		$settings = BookingSetting::firstorNew([
										'field' => 'points'
								]);
		$settings->value = $request_data['points'];
		$settings->save();
		
		return redirect()->back()->with('success', 'Points updated successfully');
	}
	
	public function getMy() {
		if(auth()->check() && auth()->user()->user_type != 0) {
			$bookings = Booking::where('user_id', auth()->user()->id)->get();
			
			return view('my_bookings')->with('bookings',$bookings);
		}
		else {
			return redirect('/');
		}
	}
	
	public function getDetails(Request $request) {
		if(auth()->check() && auth()->user()->user_type == 0) {
			$request_data = $request->all();
			$booking = Booking::find($request_data['id']);
			$user = User::find($booking->user_id);
			
			return view('admin.view_booking')
				->with('booking', $booking)
				->with('user', $user)
				->with('menu', 'bookings')
				->with('sub_menu', 'view_bookings');
		}
		else {
			return redirect('/');
		}
	}
	
	public function getView(Request $request) {
		if(auth()->check() && auth()->user()->user_type != 0) {
			$request_data = $request->all();
			$booking = Booking::find($request_data['id']);
			
			return view('view_booking')->with('booking',$booking);
		}
		else {
			return redirect('/');
		}
	}
}
