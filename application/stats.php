<?php 

require_once('conf.php');
require_once('query.php'); 

/* Statistics Handler */

function sortStats($a, $b) { //Used to sort by sub-array value
   return strcmp($a[0], $b[0]);
}

if(isset($_SESSION['username'])){
	$submit = $_POST['submit']; //Form Submit Boolean (0)
	echo $conf['stats'];
	
	$members = array();
	$subjects = array();
	
	$columns = array('users_id', 'psets_allocations_id'); //Get users
	$permsSelect = selectFrom('permissions', $columns, array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));

	foreach ($permsSelect as $perm){ //Iterate through members with some permission stored for the tournament, building up $members
		$columns = array('name', 'username');
		$userSelect = selectFrom('users', $columns, array('id'), array("'" . sanitize($perm['users_id']) . "'"));//Get tournaments.
		
		//Get name and username
		$name = $userSelect[0]['name'];
		$username = $userSelect[0]['username'];
		
		//Get tossup and bonus counts for user in tournament
		$contribSelectTU = getNumOf('tossups', array('creator_users_id', 'psets_id'), array("'" . sanitize($perm['users_id']) . "'", "'" . $_SESSION['tournament'] . "'"));
		$contribSelectB = getNumOf('bonuses', array('creator_users_id', 'psets_id'), array("'" . sanitize($perm['users_id']) . "'", "'" . $_SESSION['tournament'] . "'"));
	
		array_push($members, array('name' => $name, 'username' => $username, 'tossups' => $contribSelectTU, 'bonuses' => $contribSelectB));
	}
	
	$columns = array('id', 'subject'); //Get subjects -- ID and name -- by Tournament ID
	$subjectsSelect = selectFrom('psets_allocations', $columns, array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));

	foreach ($subjectsSelect as $subject){
		$name = $subject['subject'];
		$ID = $subject['id'];
		
		//Get tossup and bonus counts for subject in tournament
		$countSelectTU = getNumOf('tossups', array('psets_allocations_id', 'psets_id'), array("'" . sanitize($ID) . "'", "'" . $_SESSION['tournament'] . "'"));
		$countSelectB = getNumOf('bonuses', array('psets_allocations_id', 'psets_id'), array("'" . sanitize($ID) . "'", "'" . $_SESSION['tournament'] . "'"));
	
		array_push($subjects, array('name' => $name, 'tossups' => $countSelectTU, 'bonuses' => $countSelectB));
	}
	
	//Chart Boilerplate ?>
	<script type="text/javascript">
		function drawUserChart() {
			data = new google.visualization.DataTable();
			data.addColumn('number', 'Tossups');
			data.addColumn('number', 'Bonuses');
			data.addColumn({type: 'string', role: 'tooltip'});
  
			data.addRows([
				<?
					foreach ($members as $person){
						echo '[' . $person['tossups'] . ', ' . $person['bonuses'] . ", '" . 
							$person['name'] . " - " . $person['tossups'] . ' Tossups, ' . $person['bonuses'] . " Bonuses'],";
					}
				?>
			]);

			options = {
				hAxis: {title: 'Tossups', minValue: 0, maxValue: 15, titleTextStyle: {italic: false}},
				vAxis: {title: 'Bonuses', minValue: 0, maxValue: 15, titleTextStyle: {italic: false}},
				backgroundColor: { fill: 'transparent' },
				colors: ['blue'],
				legend: 'none'
			};

			chart = new google.visualization.ScatterChart(document.getElementById('userplot'));
			chart.draw(data, options);
		}
		
		function drawSubjectChart() {
			data = new google.visualization.DataTable();
			data.addColumn('number', 'Tossups');
			data.addColumn('number', 'Bonuses');
			data.addColumn({type: 'string', role: 'tooltip'});
  
			data.addRows([
				<?
					foreach ($subjects as $item){
						echo '[' . $item['tossups'] . ', ' . $item['bonuses'] . ", '" . 
							$item['name'] . " - " . $item['tossups'] . ' Tossups, ' . $item['bonuses'] . " Bonuses'],";
					}
				?>
			]);

			options = {
				hAxis: {title: 'Tossups', minValue: 0, maxValue: 15, titleTextStyle: {italic: false}},
				vAxis: {title: 'Bonuses', minValue: 0, maxValue: 15, titleTextStyle: {italic: false}},
				backgroundColor: { fill: 'transparent' },
				colors: ['red'],
				legend: 'none'
			};

			chart = new google.visualization.ScatterChart(document.getElementById('subjectplot'));
			chart.draw(data, options);
		}
		
		drawUserChart();
		drawSubjectChart();
	</script>
    
	<div style="width: 100%; text-align: center; font-size: 0;">
		<div id="userplot" style="display: inline-block; width: 47%;"></div>
		<div id="subjectplot" style="display: inline-block; width: 47%;"></div>
	</div>
	<? //End Chart Boilerplate
}

else{
	echo $conf['unauthorized'];
}

?>