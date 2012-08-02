<?php 

require_once('conf.php');
require_once('query.php'); 

/* Tournament Handler */

if(isset($_SESSION['username'])){
	if (isset($_POST['tou_id'])){
		$_SESSION['tournament'] = $_POST['tou_id'];
	}
	
	echo $conf['tournament'];
	
	//Prepare stuff for the pretty boxes
	$parameters = array('title', 'director_users_id', 'created', 'target', 'info');//Get Tournament Data
	$setsSelect = selectFrom('psets', $parameters, array('id'), array("'" . $_SESSION['tournament'] . "'"));
	
	$userSelect = selectFrom('users', array('name', 'username'), array('id'), array("'" . $setsSelect[0]['director_users_id'] . "'"));
	$setDirector = $userSelect[0]['name'] . ' (' . $userSelect[0]['username'] . ')';//Get name and username of this tournament's director
	
	$setName = $setsSelect[0]['title'];
	$setCreate = $setsSelect[0]['created'];
	
	$setInfo = $setsSelect[0]['info'] == '' ? 'Nothing of the sort has been set.' : $setsSelect[0]['info'];
	$setDate = $setsSelect[0]['target'] == '0000-00-00 00:00:00' ? 'No Target Set' : $setsSelect[0]['target'];
	
	//Output the pretty boxes with information
	echo '<p style="text-align:center;"><span class="box property">Tournament Name: ' . $setName . '</span>';
	echo '<span class="box property">Tournament Director: ' . $setDirector . '</span><br>';
	echo '<span class="box property">Tournament Date: ' . $setDate . '</span>';
	echo '<span class="box property">Creation Date: ' . $setCreate . '</span><br>';
	echo '<span class="box property">Tournament Info or Message: ' . $setInfo . '</span></p>';
}

else{
	echo $conf['unauthorized'];
}

?>