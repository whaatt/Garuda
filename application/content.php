<?php 

require_once('conf.php');

/* Content Handler */

$context = $_SESSION['context']; //User Screen

switch($context){
	case 'main': //Without Authentication
		require('welcome.php');
		break;	
	case 'dash': //Dashboard Pages
		require('dashboard.php');
		break;
}

?>