<?php 

require_once('conf.php'); 

/* Create Handler */

if(isset($_SESSION['username'])){
	echo "<p>To be completed. User creation page.</p>";
}

else{
	echo "<p>This page is only for authenticated users. Please refresh the page to restart your session. Sorry for any inconvenience!</p>";
}

?>