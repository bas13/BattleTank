
<!DOCTYPE html>

<html>
	<head>
		<style>
			input {
				display: block;
			}
		</style>

	</head> 
<body>  
	<h1>Login</h1>
<?php 
	if (isset($errorMsg)) {
		echo "<p>" . $errorMsg . "</p>";
	}


	echo form_open('account/login');
	echo form_label('Username'); 
	echo form_error('username');
	echo form_input('username',set_value('username'),"required");
	echo form_label('Password'); 
	echo form_error('password');
	echo form_password('password','',"required");
	
	echo form_submit('submit', 'Login');
	
	echo "<p>" . anchor('account/newForm','Create Account') . "</p>";

	echo "<p>" . anchor('account/recoverPasswordForm','Recover Password') . "</p>";
	
	echo form_close();
	
	if (isset($_SESSION['delay']) && isset($_SESSION['fail_count']) && $_SESSION['fail_count'] > 1) {
		sleep($_SESSION['delay']);
	}
	
	
?>	
</body>

</html>

