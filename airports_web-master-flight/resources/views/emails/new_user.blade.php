<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div style="padding:0px 15px; color:#000; background-color:#FFF; font-size:14px;">
			<p>Hello {{ $data['name'] }}, </p>
				Your account has been created. Click <a href="{{ $data['url'] }}"> here</a> to login.<br/><br/>
				
				Email: {{ $data['email'] }}<br/>
				Password: {{ $data['password'] }}<br/><br/>
				
				Please change your password upon login.<br/>
				
			<p>Best Regards,
      <br><b>Air Accretion</b></p>
		</div>
	</body>
</html>
