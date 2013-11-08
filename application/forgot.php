<?php 

require_once('conf.php'); 
require_once('query.php'); 

/* Forgot Handler */

$submit = $_POST['submit']; //Form Submit Boolean

if ($submit == '1'){ //Forgot Form Submission

	$user = trim($_POST['for_username']);
	$email = trim($_POST['for_email']);
	
	if (strlen($user) <= 0 and strlen($email) <= 0){//Entry Validation
		?>
		<div class="message error-message" onclick="go_forgot();">
			<p><strong>You did not enter any info! (Click to try again.)</strong></p>
		</div>
		<?
	}
	
	else if (strlen($email) <= 0 and strlen($user) > 0){//username but no email
		?>
		<div class="message error-message" onclick="go_forgot();">
			<p><strong>You entered a username, but no email. (Click to try again.)</strong></p>
		</div>
		<?
	}
	
	else{
		if (strlen($user) <= 0){//only email entered
			$stuff = array('username');
			$columns = array('email');
			$values = array("'" . sanitize($email) . "'");
			
			$userSelect = selectFrom('users', $stuff, $columns, $values);
			
			if (count($userSelect) <= 0){
				?>
				<div class="message error-message" onclick="go_forgot();">
					<p><strong>There are no users on file with that email. (Click to try again.)</strong></p>
				</div>
				<?
			}
			
			else{
				$message = "Dear " . $conf['name'] . " User,\n\nIf you believe this message was sent in error, please ignore it. The following usernames are on file for this email address:\n\n";
				foreach($userSelect as $user){
					$message = $message . $user['username'] . "\n";
				}
				$message = $message . "\nRegards,\nAdministrator";
				
				$to = $email;
				$subject = $conf['name'] . ' Username Request';
				$headers = 'From: ' . $conf['name'] . ' Support <' . $conf['email'] . '>';
				mail($to, $subject, $message, $headers);
				
				?>
				<div class="message thank-message">
					<p><strong>All usernames associated with your email have been sent to you.</strong></p>
				</div>
				<?
			}
		}
	
		
		else{
			$columns = array('username', 'email');
			$values = array("'" . sanitize($user) . "'", "'" . sanitize($email) . "'");
			
			if (getNumOf('users', $columns, $values) > 0){//Check For Duplicates
				$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
				$password = substr(str_shuffle($chars), 0, 10);
				$dbPass = hash('whirlpool', $password);
				
				$columns = array('temp');
				$items = array("'" . sanitize($dbPass) . "'");
				
				$conditions = array('username');
				$values = array("'" . sanitize($user) .  "'");
				
				updateIn('users', $columns, $items, $conditions, $values);//Update user information
				
				//email message
				//starts here
				
				$message = "Dear " . $conf['name'] . " User,\n\nIf you believe this message was sent in error, please delete it. A temporary password ";
				$message = $message . $password . " has been set for your account with username " . $user . ". Simply log in with this password and your username, and use this to change your password on the Account page.\n\n";
				$message = $message . "Regards,\nAdministrator";
				
				$to = $email;
				$subject = $conf['name'] . ' Password Request';
				$headers = 'From: ' . $conf['name'] . ' Support <' . $conf['email'] . '>';
				mail($to, $subject, $message, $headers);
				
				?>
				<div class="message thank-message">
					<p><strong>A temporary password has been sent to your email.</strong></p>
				</div>
				<?
			}
		
			else{
				?>
				<div class="message error-message" onclick="go_forgot();">
					<p><strong>Your credentials do not match any on file. (Click to try again.)</strong></p>
				</div>
				<?
			}
		}
	}
}

else{//General Link
	echo $conf['forgot'];
	?>
	<p><div id="forgotform" style="text-align:center;"><div class="box">
		<form id="forgot" class="postform" onsubmit="submit_forgot(document.getElementById('forgot')); return false;">
			<label>Email: <input type="text" id="for_email" name="for_email"></label><br><br>
			<label>Username: <input type="text" id="for_username" name="for_username"></label><br><br>
			<input type="submit" value="I'm Forgetful">
		</form>
	</div></div></p>
	<?
}

?>