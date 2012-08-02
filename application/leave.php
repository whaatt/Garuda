<?php 

require_once('conf.php'); 

/* Leave Handler */

if(isset($_SESSION['username'])){

	$really = $_POST['really'];//Confirmation

	if ($really == 1){
		unset($_SESSION['tournament']);
	}

	else{
		?>
		<div id="leave" class="message error-message">
		<p><strong>Do you want to exit to the dashboard? <a onclick="cont_leave()">Yes</a> or <a onclick="cont_remove('leave', 1);">No</a>.</strong></p>
		</div>
		<?
	}

}

else{
	//Static text that is single use will not get a $conf entry
	echo "<p>This function is only for authenticated users. Please refresh the page to restart your session. Sorry for any inconvenience!</p>";
}
	
?>