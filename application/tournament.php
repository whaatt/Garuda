<?php 

require_once('conf.php');
require_once('query.php'); 

/* Tournament Handler */

if(isset($_SESSION['username'])){
	echo $conf['dashboard'];//TODO placeholder in the meantime
}

else{
	echo "<p>This page is only for authenticated users. Please refresh the page to restart your session. Sorry for any inconvenience!</p>";
}

?>