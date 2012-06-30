<?php 

require_once('conf.php'); 

/* Logout Handler */

if(isset($_SESSION['username'])){

	$really = $_POST['really'];//Confirmation

	if ($really == 1){
		unset($_SESSION['username']);
		$_SESSION['context'] = 'main';
	}

	else{
		?>
		<div id="logout" class="message error-message">
		<p><strong>Are you sure you want to logout? <a onclick="cont_logout()">Yes</a> or <a onclick="cont_remove('logout', 1);">No</a>.</strong></p>
		</div>
		<?
	}

}

else{
	echo "<p>You are already logged out. Please refresh the page. Sorry for any inconvenience!</p>";
}
	
?>