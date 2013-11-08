<?php 

require_once('conf.php'); 
require_once('query.php'); 

/* Registration Handler */

$submit = $_POST['submit']; //Form Submit Boolean

if ($submit == '1'){ //Registration Form Submission

	$name = trim($_POST['reg_name']);
	$user = trim($_POST['reg_username']);
	$pass = trim($_POST['reg_password']);
	$check = trim($_POST['reg_password2']);
	$email = trim($_POST['reg_email']);
	
	if (strlen($name) <= 0 or strlen($user) < 5 or strlen($pass) < 5 or strlen($email) < 5 or strlen($name) > 40 or strlen($user) > 20 or strlen($pass) > 20 or $pass != $check or strlen($email) > 500){//Entry Validation
		?>
		<div class="message error-message" onclick="go_register();">
			<p><strong>One or more of your fields was improperly entered. (Click to try again.)</strong></p>
		</div>
		<?
	}
	
	else{
		$columns = array('username');
		$values = array("'" . sanitize($user) . "'");
		if (getNumOf('users', $columns, $values) > 0){//Check For Duplicates
			?>
			<div class="message error-message" onclick="go_register();">
				<p><strong>There already exists an account with your chosen credentials. (Click to try again.)</strong></p>
			</div>
			<?
		}
		
		else{
			$columns = array('name', 'username', 'password', 'email', 'created', 'updated');
			$values = array("'" . sanitize($name) . "'", "'" . sanitize($user) . "'", "'" . hash('whirlpool', sanitize($pass)) . "'", "'" . sanitize($email) . "'", "NOW()", "'null'");
			insertInto('users', $columns, $values);
		
			?>
			<div class="message thank-message">
				<p><strong>Thank you for your registration. You may now log in.</strong></p>
			</div>
			<?
		}
	}
}

else{//General Link
	echo $conf['register'];
	?>
	<p><div id="registerform" style="text-align:center;"><div class="box">
		<form id="register" class="postform" onsubmit="submit_register(document.getElementById('register')); return false;">
			<label>Full Name: <input type="text" id="reg_name" name="reg_name"></label><br><br>
			<label>Email: <input type="text" id="reg_email" name="reg_email"></label><br><br>
			<label>Username: <input type="text" id="reg_username" name="reg_username"></label><br><br>
			<label>Password: <input type="password" id="reg_password" name="reg_password"></label><br><br>
			<label>Confirm Password: <input type="password" id="reg_password2" name="reg_password2"></label><br><br>
			<input type="submit" value="Register">
		</form>
	</div></div></p>
	<?
}

?>