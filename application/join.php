<?php 

require_once('conf.php'); 
require_once('query.php');

/* Create Handler */

if(isset($_SESSION['username'])){
	echo "<p>To be completed. User join page.</p>";
}

else{
	echo "<p>This page is only for authenticated users. Please refresh the page to restart your session. Sorry for any inconvenience!</p>";
}

?>