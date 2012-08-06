<?php 

require_once('conf.php');
require_once('query.php'); 

/* Delete Handler */

if(isset($_SESSION['username'])){
	$type = $_POST['type'];
	
	$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
	$userID = $userSelect[0]['id'];//Get current user's ID
	
	$roleSelect = selectFrom('permissions', array('role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
	$role = $roleSelect[0]['role'];//Get role of current user
	
	if ($role != 'd' and $role != 'a'){
		echo $conf['noperms'];
	}
	
	else{
		switch($type){
			case 'set': deleteFrom('permissions', array('psets_id'), array("'" . $_SESSION['tournament'] . "'")); break; //Delete all perms from set
			case 'member': deleteFrom('permissions', array('psets_id', 'users_id'), array("'" . $_SESSION['tournament'] . "'", "'" . $_POST['id'] . "'")); break; //Delete this user's perms from set
		}
	}
}

else{
	echo $conf['unauthorized2'];
}

?>