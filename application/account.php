<?php 

require_once('conf.php');
require_once('query.php');

/* Account Handler */

if(isset($_SESSION['username'])){
	$submit = $_POST['submit']; //Form Submit Boolean (1 or 0)

	if ($submit == '1'){ //Create Form Submission

		//Get POST stuff and trim whitespace
		$name = trim($_POST['upd_name']);
		$old = trim($_POST['upd_pass']);//Old Password
		$new = trim($_POST['upd_new']);//New Password
		
		$userSelect = selectFrom('users', array('password'), array('username'), array("'" . $_SESSION['username'] . "'"));
		$check = $userSelect[0]['password'];//Get password of submitting user
		
		$pass = hash('whirlpool', $old);
		
		if (strlen($name) <= 0 or strlen($name) > 40 or (strlen($new) > 0 and strlen($new) < 5) or strlen($new) > 20 or $pass != $check){//Entry Validation
			?>
			<div class="message error-message" onclick="go_account();">
				<p><strong>One or more of your fields was improperly entered. (Click to try again.)</strong></p>
			</div>
			<?
		}
		
		else{//TODO
			if (strlen($new) > 0){
				$columns = array('name','password');
				$items = array("'" . sanitize($name) . "'", "'" . hash('whirlpool', sanitize($new)) . "'");
				
				$conditions = array('username');
				$values = array("'" . $_SESSION['username']	. "'");
				
				updateIn('users', $columns, $items, $conditions, $values);//Update user information
			}
			
			else{
				$columns = array('name');
				$items = array("'" . sanitize($name) . "'");
				
				$conditions = array('username');
				$values = array("'" . $_SESSION['username']	. "'");
				
				updateIn('users', $columns, $items, $conditions, $values);//Update user information
			}
			
			?>
			<div class="message thank-message">
				<p><strong>You have successfully updated your account information.</strong></p>
			</div>
			<?
		}
	}

	else{//General Link
		echo $conf['account'];
		
		$userSelect = selectFrom('users', array('name'), array('username'), array("'" . $_SESSION['username'] . "'"));
		$userName = $userSelect[0]['name'];//Get name of submitting user -- not the username, but the user's name
		
		?>
		<p><div id="accountform" style="text-align:center;"><div class="box">
			<form id="account" class="postform" onsubmit="submit_account(document.getElementById('account')); return false;">
				<label>Your Name: <input type="text" id="upd_name" name="upd_name" value="<? echo $userName; ?>"></label><br><br>
				<label>Current Password: <input type="password" id="upd_pass" name="upd_pass"></label><br><br>
				<label>New Password: <input type="password" id="upd_new" name="upd_new"></label><br><br>
				<input type="submit" value="Update">
			</form>
		</div></div></p>
		<?
	}
}

else{
	echo "<p>This page is only for authenticated users. Please refresh the page to restart your session. Sorry for any inconvenience!</p>";
}

?>