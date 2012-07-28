<?php 

require_once('conf.php');
require_once('query.php'); 

/* Dashboard Handler */

function sortTournaments($a, $b) { //Used to sort by sub-array value
   return strcmp($a[0], $b[0]);
}

if(isset($_SESSION['username'])){
	echo $conf['dashboard'];
	$tournaments = array();
	
	$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
	$userID = $userSelect[0]['id'];//Get ID of user
	
	$columns = array('psets_id', 'role');
	$permsSelect = selectFrom('permissions', $columns, array('users_id'), array("'" . sanitize($userID) . "'"));

	foreach ($permsSelect as $perm){ //Iterate through tournaments with some permission stored for user
		$columns = array('title', 'director_users_id', 'created', 'target');
		$setsSelect = selectFrom('psets', $columns, array('id'), array("'" . sanitize($perm['psets_id']) . "'"));//Get tournaments.
		
		array_push($tournaments, array(0 => $setsSelect[0]['title'], 1 => '', 2 => '', 3 => $setsSelect[0]['created'], 4 => ''));
		
		$userSelect = selectFrom('users', array('username', 'name'), array('id'), array("'" . $setsSelect[0]['director_users_id'] . "'"));
		$director = $userSelect[0]['name'] . ' (' . $userSelect[0]['username'] . ')';//Get director
		
		$tournaments[count($tournaments)-1][1] = $director;//Set Director
		
		switch ($perm['role']){//Access Level
			case 'd': $tournaments[count($tournaments)-1][2] = 'Director'; break;
			case 'a': $tournaments[count($tournaments)-1][2] = 'Administrator'; break;
			case 'm': $tournaments[count($tournaments)-1][2] = 'Manager'; break;
			case 'e': $tournaments[count($tournaments)-1][2] = 'Editor'; break;
		}
		
		if ($setsSelect[0]['target'] != '0000-00-00 00:00:00'){//Set Target Date
			$tournaments[count($tournaments)-1][4] = $setsSelect[0]['target'];
		}
		
		else{
			$tournaments[count($tournaments)-1][4] = 'None Set';
		}
	}
	
	//Start Boilerplate ?>
	<table class="display" id="sets">
		<thead>
			<tr>
				<th>Tournament</th>
				<th>Director</th>
				<th>Access Level</th>
				<th>Creation Date</th>
				<th>Target Date</th>
			</tr>
		</thead>
		<tbody>
	<? //End Boilerplate
	
		usort($tournaments, 'sortTournaments');
		foreach ($tournaments as $tournament){
			if ($tournament[4] == 'None Set'){
				echo '<tr class="blueRow">';
			}
			
			else if (time() - strtotime($tournament[4]) > 0){
				echo '<tr class="redRow">';
			}
			
			else{
				echo '<tr class="greenRow">';
			}
			
			foreach ($tournament as $parameter){
				echo '<td>' . $parameter . '</td>';
			}
			
			echo '</tr>';
		}
		
	//More Boilerplate ?>
		</tbody>
	</table><p></p>
	<script type="text/javascript">
		fancy_sets('sets');
	</script>
	<? //End Boilerplate
}

else{
	echo "<p>This page is only for authenticated users. Please refresh the page to restart your session. Sorry for any inconvenience!</p>";
}

?>