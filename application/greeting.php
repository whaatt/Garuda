<?php 

require('conf.php'); 

/* Greeting Handler */

if (isset($_SESSION['username'])){
	echo $_SESSION['username'];
}

else{
	echo "Anonymous";
}

?>