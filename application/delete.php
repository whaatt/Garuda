<?php 

require_once('conf.php');
require_once('query.php'); 

/* Delete Handler */

if(isset($_SESSION['username'])){
	$type = $_POST['type'];
	
	switch ($type){
		case 'set':
			$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
			$userID = $userSelect[0]['id'];//Get current user's ID

			$roleSelect = selectFrom('permissions', array('role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
			$role = $roleSelect[0]['role'];//Get role of current user

			if ($role != 'd' and $role != 'a'){
				echo $conf['noperms'];
			}
			
			else{
				deleteFrom('permissions', array('psets_id'), array("'" . $_SESSION['tournament'] . "'")); //Delete all perms from set
			}
			
			break;
		
		case 'member':
			$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
			$userID = $userSelect[0]['id'];//Get current user's ID

			$roleSelect = selectFrom('permissions', array('role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
			$role = $roleSelect[0]['role'];//Get role of current user

			if ($role != 'd' and $role != 'a'){
				echo $conf['noperms'];
			}
			
			else{
				deleteFrom('permissions', array('psets_id', 'users_id'), array("'" . $_SESSION['tournament'] . "'", "'" . $_POST['id'] . "'")); //Delete this user's perms from set
			}
		
			break;
			
		case 'tossup':
			$tossupID = $_POST['id'];
		
			$tossupSelect = selectFrom('tossups', array('psets_allocations_id', 'creator_users_id'), array('id'), array("'" . $tossupID . "'"));//Get tossups
			$tossup = $tossupSelect[0];
			$tossup['psets_allocations_id'] = isset($tossup['psets_allocations_id']) ? $tossup['psets_allocations_id'] : '';
			
			$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
			$userID = $userSelect[0]['id'];//Get current user's ID
			
			$permSelect = selectFrom('permissions', array('psets_allocations_id', 'role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
			$userRole = $permSelect[0]['role'];//Get current user's role
			$userFocus = isset($permSelect[0]['psets_allocations_id']) ? $permSelect[0]['psets_allocations_id'] : '';
			
			$access = false;
			
			if ($userID == $tossup['creator_users_id']){
				$access = true;
			}
			
			else if ($userRole == 'm' and $userFocus == $tossup['psets_allocations_id'] and $userFocus != ''){
				$access = true;
			}

			else if ($userRole == 'a' or $userRole == 'd'){
				$access = true;
			}
			
			if ($access == true){
				deleteFrom('tossups', array('id'), array("'" . $tossupID. "'")); //Delete this tossup from DB
				
				$columns = array('duplicate_tossups_id');
				$values = array("NULL");
			
				$where = array('duplicate_tossups_id');
				$equals = array("'" . $tossupID . "'");
			
				updateIn('tossups', $columns, $values, $where, $equals);//Bubble down duplicates
			}
			
			break;

		case 'bonus':
			$bonusID = $_POST['id'];
		
			$bonusSelect = selectFrom('bonuses', array('psets_allocations_id', 'creator_users_id'), array('id'), array("'" . $bonusID . "'"));//Get bonus
			$bonus = $bonusSelect[0];
			$bonus['psets_allocations_id'] = isset($bonus['psets_allocations_id']) ? $bonus['psets_allocations_id'] : '';
			
			$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
			$userID = $userSelect[0]['id'];//Get current user's ID
			
			$permSelect = selectFrom('permissions', array('psets_allocations_id', 'role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
			$userRole = $permSelect[0]['role'];//Get current user's role
			$userFocus = isset($permSelect[0]['psets_allocations_id']) ? $permSelect[0]['psets_allocations_id'] : '';
			
			$access = false;
			
			if ($userID == $bonus['creator_users_id']){
				$access = true;
			}
			
			else if ($userRole == 'm' and $userFocus == $bonus['psets_allocations_id'] and $userFocus != ''){
				$access = true;
			}

			else if ($userRole == 'a' or $userRole == 'd'){
				$access = true;
			}
			
			if ($access == true){
				deleteFrom('bonuses', array('id'), array("'" . $bonusID. "'")); //Delete this bonus from DB
				
				$columns = array('duplicate_bonuses_id');
				$values = array("NULL");
			
				$where = array('duplicate_bonuses_id');
				$equals = array("'" . $bonusID . "'");
			
				updateIn('bonuses', $columns, $values, $where, $equals);//Bubble down duplicates
			}
			
			break;
			
		case 'message':
			$messageID = $_POST['id'];
			$messageSelect = selectFrom('messages', array('users_id', 'tub_id', 'tossup_or_bonus'), array('id'), array("'" . $messageID . "'"));//Get message
			$message = $messageSelect[0];
			
			if ($message['tossup_or_bonus'] == 0){
				$questionSelect = selectFrom('tossups', array('psets_allocations_id', 'creator_users_id'), array('id'), array("'" . $message['tub_id'] . "'"));//Get tossups
				$question = $questionSelect[0];
				$question['psets_allocations_id'] = isset($question['psets_allocations_id']) ? $question['psets_allocations_id'] : '';
			}
			
			else{
				$questionSelect = selectFrom('bonuses', array('psets_allocations_id', 'creator_users_id'), array('id'), array("'" . $message['tub_id'] . "'"));//Get bonuses
				$question = $questionSelect[0];
				$question['psets_allocations_id'] = isset($question['psets_allocations_id']) ? $question['psets_allocations_id'] : '';
			}
			
			$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
			$userID = $userSelect[0]['id'];//Get current user's ID
			
			$permSelect = selectFrom('permissions', array('psets_allocations_id', 'role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
			$userRole = $permSelect[0]['role'];//Get current user's role
			$userFocus = isset($permSelect[0]['psets_allocations_id']) ? $permSelect[0]['psets_allocations_id'] : '';
			
			$access = false;
			
			if ($userID == $question['creator_users_id'] or $userID == $message['users_id']){
				$access = true;
			}
			
			else if ($userRole == 'm' and $userFocus == $question['psets_allocations_id'] and $userFocus != ''){
				$access = true;
			}

			else if ($userRole == 'a' or $userRole == 'd'){
				$access = true;
			}
			
			if ($access == true){
				deleteFrom('messages', array('id'), array("'" . $messageID. "'")); //Delete this message from DB
			}
			
			break;
	}
}

else{
	echo $conf['unauthorized2'];
}

?>