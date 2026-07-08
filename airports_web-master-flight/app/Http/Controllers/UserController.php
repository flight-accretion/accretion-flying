<?php

namespace FlyingCalculation\Http\Controllers;

use DB;
use Validator;
use Auth;
use Hash;
use Mail;
use Excel;
use Log;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

use FlyingCalculation\Exports\UserListExport;
use FlyingCalculation\Http\Requests;
use FlyingCalculation\Http\Controllers\Controller;

use FlyingCalculation\User;
use FlyingCalculation\MailContent;
use FlyingCalculation\City;

class UserController extends Controller
{
  public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}
	
	public function getIndex() {
		if(auth()->check() && auth()->user()->user_type == 0 ){
			$users = User::select('user.id', 'user.name', 'user.email', 'user.contact_number', 'user.status', 'user.points', 'city.name as city')
									 ->leftJoin('city', 'city.id', '=', 'user.city_id')
									 ->where('user_type', '!=', 0)
									 ->orderby('name')
									 ->get();

			return view('admin.users')
				->with('users', $users)
				->with('menu', 'users')
				->with('sub_menu', 'view_users');
		}
		else {
			return redirect('/');
		}
	}
	
	public function getUpdateProfile() {
		if(auth()->check() && auth()->user()->user_type != 0) {
			
			$cities = City::orderby('name')->pluck('name', 'id');
			return view('update_profile')
				->with('cities', $cities);
		}
		else {
			return redirect('/');
		}
	}
	
	public function postUpdateProfile(Request $request) {
		$request_data = $request->all();
		
		$messages = [
			'name.required' 		=> 'Please enter name',
			'city.required' 		=> 'Please select city',
			'phone.unique'			=> 'Email already used',
			'phone.required'		=> 'Please enter phone number',
			'phone.unique'			=> 'Phone already used'
		];

		$validator = Validator::make($request_data, [
			'name' 			=> 'required|min:2|max:100',
			'city' 			=> 'required',
			'phone' 		=> 'required|unique:user,contact_number,'.auth()->user()->id
		], $messages);
    
		if($validator->fails())
		{ 
			return redirect()->back()->withErrors($validator)->withInput();
		}
		else
		{
			$user = User::find(auth()->user()->id);
			$user->name = $request_data['name'];
			$user->contact_number = $request_data['phone'];
			$user->city_id = $request_data['city'];
			$user->save();
			
			return redirect()->back()->with('success', 'Profile updated successfully');
		}
	}
	
	public function getChangePassword () {
		if(auth()->check() && auth()->user()->user_type != 0) {
			
			$cities = City::orderby('name')->pluck('name', 'id');
			return view('change_password');
		}
		else {
			return redirect('/');
		}
	}
	
	public function postChangePassword (Request $request) {
		$request_data = $request->all();
		
		$messages = [
      'new-password.required' 						=> 'Please enter password.',
      'current-password.required' 				=> 'Please enter current password.',
    ];
    
    $validator = Validator::make($request_data, [
			'new-password'						=> 'required|min:8' ,
      'current-password'			 	=> 'required'
		], $messages);
		
		if($validator->fails())
		{ 
			return redirect()->back()->withErrors($validator)->withInput();
		}
		else
		{
			if (Hash::check($request_data['current-password'], auth()->user()->password)) {                
				$obj_user = User::find(auth()->user()->id);
				$obj_user->password = Hash::make($request_data['new-password']);
				$obj_user->save();
				
				return redirect()->back()->with('success', 'Password updated successfully!');
			}
			else
			{   
				$error = array('current-password' => 'Please enter correct current password'); 
				return redirect()->back()->withErrors($error)->withInput();   
			} 
		}
	}
	
	public function getAdd() {
		if(auth()->check() && auth()->user()->user_type == 0)
		{
			$cities = City::orderBy('name')->pluck('name', 'id');
			
			return view('admin.add_user')
				->with('cities', $cities)
				->with('menu', 'users')
				->with('sub_menu', 'add_user');
				
		}
		else {
			return redirect('/');
		}
	}
	
	public function postAdd(Request $request) {
		$request_data = $request->all();
		
		$messages = [
			'name.required' 		=> 'Please enter name',
			'city.required' 		=> 'Please select city',
			'email.required' 		=> 'Please enter email',
			'email.email' 			=> 'Please enter a valid email',
			'phone.unique'			=> 'Email already used',
			'phone.required'		=> 'Please enter phone number',
			'phone.unique'			=> 'Phone already used',
			'password.required' => 'Please enter password'
		];

		$validator = Validator::make($request_data, [
			'name' 			=> 'required|min:2|max:100',
			'email' 		=> 'required|email|unique:user,email',
			'password' 	=> 'required',
			'city' 			=> 'required',
			'phone' 		=> 'required|unique:user,contact_number'
		], $messages);
    
		if($validator->fails())
		{ 
			return redirect()->back()->withErrors($validator)->withInput();
		}
		else
		{
			$user = new User;
			$user->name = $request_data['name'];
			$user->email = $request_data['email'];
			$user->contact_number = $request_data['phone'];
			$user->password = Hash::make($request_data['password']);
			$user->status = 1;
			$user->city_id = $request_data['city'];
			$user->user_type = 1;
			$user->save();
			$email = $user->email;
			$data = [
				'name' 			=> $user->name,
				'password' 	=> $request_data['password'],
				'email' 		=> $request_data['email'],
				'url'	 			=> config('app.url')
			];
			$mail_sent = $this->sendNewUserMail($user, $request_data['password'], $data);
			$success_message = 'User added successfully!';
			if(!$mail_sent) {
				$success_message .= ' Email notification could not be sent. Please check mail settings.';
			}
			return redirect()->back()->with('success', $success_message);
		}
	}

	private function sendNewUserMail(User $user, $plain_password, array $data)
	{
		try {
			$email = $user->email;
			$mail_content = MailContent::where('name', 'New User')->first();

			if($mail_content) {
				$subject = $mail_content->subject;
				$content = str_replace('---name---', $user->name, $mail_content->content);
				$content = str_replace('---password---', $plain_password, $content);
				$content = str_replace('---email---', $user->email, $content);
				$content = str_replace('---url---', config('app.url'), $content);

				Mail::send([], [], function($message) use ($subject, $email, $content): void {
					$message->to($email);
					$message->subject($subject);
					$message->html($content);
				});
			}
			else {
				Mail::send('emails.new_user', array('data' => $data), function($message) use($email): void {
					$message->to($email)->subject('Air Accretion - Account created');
				});
			}

			return true;
		} catch (\Throwable $e) {
			Log::warning('New user email could not be sent.', [
				'user_id' => $user->id,
				'email' => $user->email,
				'error' => $e->getMessage(),
			]);

			return false;
		}
	}
	
	public function getEdit(Request $request) {
		if(auth()->check() && auth()->user()->user_type == 0)
		{
			$request_data = $request->all();
			$user = User::find($request_data['id']);
			$cities = City::orderBy('name')->pluck('name', 'id');
			
			return view('admin.edit_user')
				->with('user', $user)
				->with('cities', $cities)
				->with('menu', 'users')
				->with('sub_menu', 'view_users');
				
		}
		else {
			return redirect('/');
		}
	}
	
	public function postEdit(Request $request) {
		$request_data = $request->all();
		
		$messages = [
			'name.required' 		=> 'Please enter name',
			'city.required' 		=> 'Please select city',
			'email.required' 		=> 'Please enter email',
			'email.email' 			=> 'Please enter a valid email',
			'phone.unique'			=> 'Email already used',
			'phone.required'		=> 'Please enter phone number',
			'phone.unique'			=> 'Phone already used'
		];

		$validator = Validator::make($request_data, [
			'name' 			=> 'required|min:2|max:100',
			'email' 		=> 'required|email|unique:user,email,'.$request_data['id'],
			'city' 			=> 'required',
			'phone' 		=> 'required|unique:user,contact_number,'.$request_data['id']
		], $messages);
    
		if($validator->fails())
		{ 
			return redirect()->back()->withErrors($validator)->withInput();
		}
		else
		{
			$user = User::find($request_data['id']);
			$user->name = $request_data['name'];
			$user->email = $request_data['email'];
			$user->contact_number = $request_data['phone'];
			if($request_data['password'] != '') {
				$user->password = Hash::make($request_data['password']);
			}
			$user->city_id = $request_data['city'];
			$user->save();
			return redirect()->back()->with('success', 'User updated successfully!');
		}
	}
	
	public function getDeactivate(Request $request) {
		if(auth()->check() && auth()->user()->user_type == 0){
			$request_data = $request->all();
			$user = User::find($request_data['id']);
			$user->status = 0;
			$user->save();
			
			return redirect()->back()->with('success', 'User deactivated successfully!');
		}
		else {
			return redirect('/');
		}
	}
	
	public function getActivate(Request $request) {
		if(auth()->check() && auth()->user()->user_type == 0){
			$request_data = $request->all();
			$user = User::find($request_data['id']);
			$user->status = 1;
			$user->save();
			
			return redirect()->back()->with('success', 'User activated successfully!');
		}
		else {
			return redirect('/');
		}
	}
  
  //User Register
  public function postRegister(Request $request)
  {
    $request_data = $request->all();

    $messages = [
      'email.required' => 'Please enter email',
      'password.required' => 'Please enter password',
      'confirm-password.required' => 'Please enter password',
      'accept-tnc.required' => 'Please accept terms and conditions',
      'name.required' => 'Please enter your name',
    ];
    
    $validator = Validator::make($request_data, [
			'email' => 'required|email|max:255|unique:user',
			'password' => 'required|min:8',
			'accept-tnc' => 'required',
			'name' => 'required|min:3',
      'confirm-password' => 'required|min:8|same:password'         
		], $messages);
    
    if($validator->fails())
    {
      return response()->json(array('error' => $validator->getMessageBag()->toArray()), 400);
    }
    else
    {
      //0=Admin, 1=Executive, 2=Data monitor User, 3=Video Uploader, 4=Customer Admin, 5=SubCustomer Admin, 6=Customer
      $obj_user = new User;
      $obj_user->name = $request_data['name'];
      $obj_user->email = $request_data['email'];
      $obj_user->password = bcrypt($request_data['password']);
      $obj_user->user_type = 1;   
      $obj_user->status = 1;   
      $obj_user->dob = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', '01/01/1980')));    
      $obj_user->image = 'unknown.jpg';      
      $obj_user->save(); 

      return redirect()->back()->with('success', 'Registered successfully');
      return redirect()->to('/');
    }
  }  
  
  //Export user list
  public function getExport(Request $request)
  {
     $users = User::select('user.name', 'user.email', 'user.contact_number', 'user.points', 'city.name as city')
			->leftJoin('city', 'city.id', '=', 'user.city_id')
			->where('user_type', '!=', 0)
			->orderby('name')
			->get();
     $user_data[] = array(' ');
    $user_data[] = array('Flying Calculation ');
    $user_data[] = array('Date: '.date('d M Y'));
    $user_data[] = array(' ');
    $user_data[] = array('Name', 'City', 'Email', 'Phone','Points');
     foreach($users as $user)
     {
       $user_data[]=array(
       'Name'=>$user->name,
       'City'=>$user->city, 
       'Email'=>$user->email, 
       'Phone'=>$user->contact_number,
       'Points'=>$user->points,
       );
     }
     
     return Excel::download(new UserListExport($user_data), 'users_list.xlsx');
    
  }
  
  //Login
  public function postLogin(Request $request)
  {
    $request_data = $request->all();
    $email = $request_data['user-email'];
    
    $messages = [
      'user-email.required' => 'Please enter email',
      'user-password.required' => 'Please enter password',
    ];
    
    $validator = Validator::make($request_data, [
			'user-email' => 'required|email|max:255',
			'user-password' => 'required|min:8' ,       
		], $messages);
    
    if($validator->fails())
    {
      return response()->json(array('error' => $validator->getMessageBag()->toArray()), 400);
    }
    else
    {
      $user_details = User::where('email', $email)->first();
      if(!empty($user_details) > 0)
      {
        
        $credentials = array('email'=> trim($request_data['user-email']) , 'password'=> $request_data['user-password']);        
        if ($this->auth->attempt($credentials, $request->has('remember')))
        {
          return '/';
        }
        else
        {
          return response()->json(array('error' => array('user-password' => 'Please enter a correct password')), 400);
        }
      }
      else
      {
        return response()->json(array('error' => array('user-email' => 'User not found')), 400);
      }     
    }
  }
  
  //Logout
  public function getLogout(Request $request)
	{
		$this->auth->logout();
		return redirect('/'); //Redirect to admin login form
	}
	
	public function getSignUp(){
		$cities = City::orderby('name')->pluck('name', 'id');
		return view('signup')
			->with('cities', $cities);
	}
	
	public function postSignUp(Request $request){
		$request_data = $request->all();
		
		$messages = [
			'name.required' 		=> 'Please enter name',
			'city.required' 		=> 'Please select city',
			'email.required' 		=> 'Please enter email',
			'email.email' 			=> 'Please enter a valid email',
			'phone.unique'			=> 'Email already used',
			'phone.required'		=> 'Please enter phone number',
			'phone.unique'			=> 'Phone already used',
			'password.required' => 'Please enter password'
		];

		$validator = Validator::make($request_data, [
			'name' 			=> 'required|min:2|max:100',
			'email' 		=> 'required|email|unique:user,email',
			'password' 	=> 'required',
			'city' 			=> 'required',
			'phone' 		=> 'required|unique:user,contact_number'
		], $messages);
    
		if($validator->fails())
		{ 
			return redirect()->back()->withErrors($validator)->withInput();
		}
		else
		{
			$user = new User;
			$user->name = $request_data['name'];
			$user->email = $request_data['email'];
			$user->contact_number = $request_data['phone'];
			$user->password = Hash::make($request_data['password']);
			$user->status = 1;
			$user->city_id = $request_data['city'];
			$user->user_type = 1;
			$user->save();
			if ($this->auth->attempt(['email' => $request_data['email'], 'password' => $request_data['password']]))
			{
				return redirect('/');
			}
			else {
				return redirect('/');
			}
		}
	}
}
