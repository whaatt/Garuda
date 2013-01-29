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
	
	$membersApproved = array();
	$membersAll = array();
	$subjectsApproved = array();
	$subjectsAll = array();
	
	$columns = array('users_id', 'psets_allocations_id'); //Get users
	$permsSelect = selectFrom('permissions', $columns, array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));

	foreach ($permsSelect as $perm){ //Iterate through members with some permission stored for the tournament, building up $members
		$columns = array('name', 'username');
		$userSelect = selectFrom('users', $columns, array('id'), array("'" . sanitize($perm['users_id']) . "'"));//Get tournaments.
		
		//Get name and username
		$name = $userSelect[0]['name'];
		$username = $userSelect[0]['username'];
		
		//Get tossup and bonus counts for user in tournament
		$contribSelectApprovedTU = getNumOf('tossups', array('creator_users_id', 'psets_id', 'approved'), array("'" . sanitize($perm['users_id']) . "'", "'" . $_SESSION['tournament'] . "'", "'1'"));
		$contribSelectApprovedB = getNumOf('bonuses', array('creator_users_id', 'psets_id', 'approved'), array("'" . sanitize($perm['users_id']) . "'", "'" . $_SESSION['tournament'] . "'", "'1'"));
	
		$contribSelectAllTU = getNumOf('tossups', array('creator_users_id', 'psets_id'), array("'" . sanitize($perm['users_id']) . "'", "'" . $_SESSION['tournament'] . "'"));
		$contribSelectAllB = getNumOf('bonuses', array('creator_users_id', 'psets_id'), array("'" . sanitize($perm['users_id']) . "'", "'" . $_SESSION['tournament'] . "'"));
	
		array_push($membersApproved, array('name' => $name, 'username' => $username, 'tossups' => $contribSelectApprovedTU, 'bonuses' => $contribSelectApprovedB));
		array_push($membersAll, array('name' => $name, 'username' => $username, 'tossups' => $contribSelectAllTU, 'bonuses' => $contribSelectAllB));
	}
	
	$columns = array('id', 'subject'); //Get subjects -- ID and name -- by Tournament ID
	$subjectsSelect = selectFrom('psets_allocations', $columns, array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));

	foreach ($subjectsSelect as $subject){
		$name = $subject['subject'];
		$ID = $subject['id'];
		
		//Get tossup and bonus counts for subject in tournament - Approved then All
		$countSelectApprovedTU = getNumOf('tossups', array('psets_allocations_id', 'psets_id', 'approved'), array("'" . sanitize($ID) . "'", "'" . $_SESSION['tournament'] . "'", "'1'"));
		$countSelectApprovedB = getNumOf('bonuses', array('psets_allocations_id', 'psets_id', 'approved'), array("'" . sanitize($ID) . "'", "'" . $_SESSION['tournament'] . "'", "'1'"));
	
		$countSelectAllTU = getNumOf('tossups', array('psets_allocations_id', 'psets_id'), array("'" . sanitize($ID) . "'", "'" . $_SESSION['tournament'] . "'"));
		$countSelectAllB = getNumOf('bonuses', array('psets_allocations_id', 'psets_id'), array("'" . sanitize($ID) . "'", "'" . $_SESSION['tournament'] . "'"));
		
		array_push($subjectsApproved, array('name' => $name, 'tossups' => $countSelectApprovedTU, 'bonuses' => $countSelectApprovedB));
		array_push($subjectsAll, array('name' => $name, 'tossups' => $countSelectAllTU, 'bonuses' => $countSelectAllB));
	}
	
	//Chart Boilerplate ?>
	<script type="text/javascript">
		function drawUserChartApproved() {
			data = new google.visualization.DataTable();
			data.addColumn('number', 'Tossups');
			data.addColumn('number', 'Bonuses');
			data.addColumn({type: 'string', role: 'tooltip'});
  
			data.addRows([
				<?
					foreach ($membersApproved as $person){
						echo '[' . $person['tossups'] . ', ' . $person['bonuses'] . ", '" . 
							$person['name'] . " - " . $person['tossups'] . ' Tossups, ' . $person['bonuses'] . " Bonuses'],";
					}
				?>
			]);

			options = {
				hAxis: {title: 'Approved Tossups', minValue: 0, maxValue: 15, titleTextStyle: {italic: false}},
				vAxis: {title: 'Approved Bonuses', minValue: 0, maxValue: 15, titleTextStyle: {italic: false}},
				backgroundColor: { fill: 'transparent' },
				colors: ['green'],
				legend: 'none'
			};

			chart = new google.visualization.ScatterChart(document.getElementById('userplotapproved'));
			chart.draw(data, options);
		}
		
		function drawUserChartAll() {
			data = new google.visualization.DataTable();
			data.addColumn('number', 'Tossups');
			data.addColumn('number', 'Bonuses');
			data.addColumn({type: 'string', role: 'tooltip'});
  
			data.addRows([
				<?
					foreach ($membersAll as $person){
						echo '[' . $person['tossups'] . ', ' . $person['bonuses'] . ", '" . 
							$person['name'] . " - " . $person['tossups'] . ' Tossups, ' . $person['bonuses'] . " Bonuses'],";
					}
				?>
			]);

			options = {
				hAxis: {title: 'All Tossups', minValue: 0, maxValue: 15, titleTextStyle: {italic: false}},
				vAxis: {title: 'All Bonuses', minValue: 0, maxValue: 15, titleTextStyle: {italic: false}},
				backgroundColor: { fill: 'transparent' },
				colors: ['blue'],
				legend: 'none'
			};

			chart = new google.visualization.ScatterChart(document.getElementById('userplotall'));
			chart.draw(data, options);
		}
		
		function drawSubjectChartApproved() {
			data = new google.visualization.DataTable();
			data.addColumn('number', 'Tossups');
			data.addColumn('number', 'Bonuses');
			data.addColumn({type: 'string', role: 'tooltip'});
  
			data.addRows([
				<?
					foreach ($subjectsApproved as $item){
						echo '[' . $item['tossups'] . ', ' . $item['bonuses'] . ", '" . 
							$item['name'] . " - " . $item['tossups'] . ' Tossups, ' . $item['bonuses'] . " Bonuses'],";
					}
				?>
			]);

			options = {
				hAxis: {title: 'Approved Tossups', minValue: 0, maxValue: 15, titleTextStyle: {italic: false}},
				vAxis: {title: 'Approved Bonuses', minValue: 0, maxValue: 15, titleTextStyle: {italic: false}},
				backgroundColor: { fill: 'transparent' },
				colors: ['orange'],
				legend: 'none'
			};

			chart = new google.visualization.ScatterChart(document.getElementById('subjectplotapproved'));
			chart.draw(data, options);
		}
		
		function drawSubjectChartAll() {
			data = new google.visualization.DataTable();
			data.addColumn('number', 'All Tossups');
			data.addColumn('number', 'All Bonuses');
			data.addColumn({type: 'string', role: 'tooltip'});
  
			data.addRows([
				<?
					foreach ($subjectsAll as $item){
						echo '[' . $item['tossups'] . ', ' . $item['bonuses'] . ", '" . 
							$item['name'] . " - " . $item['tossups'] . ' Tossups, ' . $item['bonuses'] . " Bonuses'],";
					}
				?>
			]);

			options = {
				hAxis: {title: 'All Tossups', minValue: 0, maxValue: 15, titleTextStyle: {italic: false}},
				vAxis: {title: 'All Bonuses', minValue: 0, maxValue: 15, titleTextStyle: {italic: false}},
				backgroundColor: { fill: 'transparent' },
				colors: ['red'],
				legend: 'none'
			};

			chart = new google.visualization.ScatterChart(document.getElementById('subjectplotall'));
			chart.draw(data, options);
		}
		
		drawUserChartApproved();
		drawUserChartAll();
		drawSubjectChartApproved();
		drawSubjectChartAll();
	</script>
    
	<div style="width: 100%; text-align: center; font-size: 0;">
		<div id="userplotapproved" style="display: inline-block; width: 47%;"></div>
		<div id="subjectplotapproved" style="display: inline-block; width: 47%;"></div>
		<br><br>
		<div id="userplotall" style="display: inline-block; width: 47%;"></div>
		<div id="subjectplotall" style="display: inline-block; width: 47%;"></div>
	</div>
	<? //End Chart Boilerplate
}

else{
	echo $conf['unauthorized'];
}

?>