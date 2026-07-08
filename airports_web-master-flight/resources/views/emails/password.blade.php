<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div style="padding:0px 15px; color:#000; background-color:#FFF; font-size:14px;">
			<p>Dear {{$user->name}}, </p>
			Please click on the link below to Reset Your Password:<br/> <a href="{{ url('password/reset/'.$token) }}">{{ url('password/reset/'.$token) }}</a><br/><br/>
			
			We recommend that you change your password to something you can easily remember.
			Please note that your password length must be atleast 8 characters and must contain at least one capital letter, one number and one special character.
			<p>Best Regards,
      <br><b>Air Accretion</b></p>
		</div>
	</body>
</html>
