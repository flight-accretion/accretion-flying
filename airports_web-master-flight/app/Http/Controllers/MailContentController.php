<?php

namespace FlyingCalculation\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Mail;
use FlyingCalculation\Http\Requests;
use FlyingCalculation\Http\Controllers\Controller;

use FlyingCalculation\Booking;
use FlyingCalculation\MailContent;
use FlyingCalculation\User;

class MailContentController extends Controller
{
	public function getIndex() {
		if(auth()->check() && auth()->user()->user_type == 0) {
			$mail_contents = MailContent::pluck('name', 'id');
			
			return view('admin.mail_contents')
				->with('mail_contents', $mail_contents)
				->with('menu', 'mail_contents')
				->with('sub_menu', 'view_mail_contents');
		}
		else {
			return redirect('/');
		}
	}
	
	public function getEdit(Request $request) {
		if(auth()->check() && auth()->user()->user_type == 0) {
			$request_data = $request->all();
			$mail_content = MailContent::find($request_data['id']);
			
			return view('admin.edit_mail_content')
				->with('mail_content', $mail_content)
				->with('menu', 'mail_contents')
				->with('sub_menu', 'view_mail_contents');
		}
		else {
			return redirect('/');
		}
	}
	
	public function postEdit(Request $request) {
		$request_data = $request->all();
		
		$mail_content = MailContent::find($request_data['id']);
		
		$mail_content->subject = $request_data['subject'];
		$mail_content->content = $request_data['content'];
		$mail_content->save();
		
		return redirect()->back()->with('success', 'Content updated successfully!');
	}
	
	public function getSendMail() {
		$users = User::where('user_type', '!=', 0)->get();
		$points = Booking::select(DB::raw('SUM(points_earned) AS points_earned'), DB::raw('SUM(points_redeemed) AS points_redeemed'), DB::raw('COUNT(id) AS total_bookings'), 'user_id')
											->whereMonth('created_at', '=', date('m', strtotime('previous month')))
											->whereYear('created_at', '=', date('Y', strtotime('previous month')))
											->get()
											->keyBy('user_id');

		$mail_content = MailContent::where('name', 'Points Summary')->first();
		if($mail_content) {
			$subject = $mail_content->subject;
			$content = $mail_content->content;
		}
		else {
			$subject = 'Air Accretion - Points Summary';

			$content = '<div style="padding:0px 15px; color:#000; background-color:#FFF; font-size:14px;">';
			$content .= '	<p>Hello ---name---, </p>';
			$content .= '	You have ---points_available--- points available. Here is the summary of your points and bookings.';
			$content .= '</div>';
			$content .= '<div style="padding:0px 15px; color:#000; background-color:#FFF; font-size:14px;"><br></div>';
			$content .= '<div style="padding:0px 15px; color:#000; background-color:#FFF; font-size:14px;">Points earned: ---points_earned---<br>Points redeemed: ---points_redeemed---</div>';
			$content .= '<div style="padding:0px 15px; color:#000; background-color:#FFF; font-size:14px;">Total bookings: ---total_bookings---</div>';
			$content .= '<div style="padding:0px 15px; color:#000; background-color:#FFF; font-size:14px;">';
			$content .= '	<div><br></div>';
			$content .= '</div>';
			$content .= '<div style="padding:0px 15px; color:#000; background-color:#FFF; font-size:14px;">';
			$content .= '	<p>Best Regards,';
			$content .= '		<br><b>Air Accretion</b>';
			$content .= '	</p>';
			$content .= '</div>';
		}


		foreach($users as $user) {
			$points_earned = $points_redeemed = $total_bookings = 0;
			if(isset($points[$user->id])) {
				$points_earned = $points[$user->id]->points_earned;
				$points_redeemed = $points[$user->id]->points_redeemed;
				$total_bookings = $points[$user->id]->total_bookings;
			}
			$m_content = str_replace('---name---', $user->name, $content);
			$m_content = str_replace('---points_earned---', $points_earned, $m_content);
			$m_content = str_replace('---points_redeemed---', $points_redeemed, $m_content);
			$m_content = str_replace('---total_bookings---', $total_bookings, $m_content);
			$m_content = str_replace('---points_available---', $user->points, $m_content);

			$email = $user->email;
			Mail::send([], [], function($message) use ($subject, $email, $m_content): void {
				$message->to($email);
				$message->subject($subject);
				$message->html($m_content);
			});
		}
		//echo 'Mails sent successfully';
	}
}
