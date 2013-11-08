<?php 

require_once('conf.php');
require_once('query.php');

/* Login Handler */

$submit = $_POST['submit']; //Form Submit Boolean

if ($submit == '1'){ //Login Form Submission

	$user = $_POST['login_username'];
	$pass = $_POST['login_password'];
	
	if (strlen($user) < 5 or strlen($pass) < 5){//Entry Validation
		?>
		<div class="message error-message" onclick="go_login();">
			<p><strong>One or more of your credentials was improperly entered. (Click to try again.)</strong></p>
		</div>
		<?
	}
	
	else{
		$columns = array('username', 'password');
		$columnsTemp = array('username', 'temp');
		$values = array("'" . sanitize($user) . "'", "'" . hash('whirlpool', sanitize($pass)) . "'");
		if (getNumOf('users', $columns, $values) == 1 or getNumOf('users', $columnsTemp, $values) == 1){//Check For Validation with either regular or temp pass
			$_SESSION['username'] = $user;
			unset($_SESSION['context']);
			$_SESSION['context'] = 'dash';
			
			$userSelect = selectFrom('users', array('email'), array('username'), array("'" . $_SESSION['username'] . "'"));
			$currentEmail = $userSelect[0]['email'];
			
			if (strlen($currentEmail) > 0){
				?>
				<div class="message thank-message" onclick="cont_dashboard();">
					<p><strong>Your credentials have been validated. (Click to continue.)</strong></p>
				</div>
				<?
			}
			
			else{
				?>
				<div class="message error-message" onclick="cont_password();">
					<p><strong>You need to set an account email! (Click to continue.)</strong></p>
				</div>
				<?
			}
		}
		else{
			?>
			<div class="message error-message" onclick="go_login();">
				<p><strong>One or more of your credentials was improperly entered. (Click to try again.)</strong></p>
			</div>
			<?
		}
	}
}

else{//General Link
	echo $conf['login'];
	?>
	<p><div id="loginform" style="text-align:center;"><div class="box">
		<form id="login" class="postform" onsubmit="submit_login(document.getElementById('login')); return false;">
			<label>Username: <input type="text" id="login_username" name="login_username"></label><br><br>
			<label>Password: <input type="password" id="login_password" name="login_password"></label><br><br>
			<input type="submit" value="Log In">
		</form>
	</div></div></p>
	<?
}

?>