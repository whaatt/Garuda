<?php

require_once('conf.php');
require_once('query.php'); 

/* Packets Handler */

function isPosInt($input){
	if (strval(intval($input)) == $input and intval($input) > 0){
		return true;
	}
	
	else{
		return false;
	}
}

function shuffle_assoc($list) { 
	if (!is_array($list)){
		return $list; 
	}
	
	$keys = array_keys($list); 
	shuffle($keys); 
	$random = array(); 
	
	foreach ($keys as $key) { 
		$random[] = $list[$key]; 
	}
	
	return $random; 
} 

function logIt($input){
	error_log($input, 3, "log.txt");
}

if(isset($_SESSION['username'])){
	$submit = isset($_POST['submit']) ? $_POST['submit'] : $_GET['submit']; //Form Submit Boolean
	
	if ($submit == 0){
		echo $conf['packets'];
		echo '<a onclick="modal_packets_auto();">click here</a>.</p>';
		
		$questions = array();
		
		$columns = array('id', 'psets_allocations_id', 'answer', 'round_id');
		$tossupsSelect = selectFrom('tossups', $columns, array('psets_id', 'promoted'), array("'" . $_SESSION['tournament'] . "'", "'1'"));

		foreach ($tossupsSelect as $tossup){ //Iterate through $tossupsSelect, add to overall list of $questions
			$items = array('subject'); $columns = array('id');
			$values = array("'" . sanitize($tossup['psets_allocations_id']) . "'");
			
			$subjectSelect = selectFrom('psets_allocations', $items, $columns, $values);//Get subject.
			$subject = isset($subjectSelect[0]['subject']) ? $subjectSelect[0]['subject'] : 'None Set';
			
			$answer = $tossup['answer'];
			$id = $tossup['id'];
			
			$roundID = isset($tossup['round_id']) ? $tossup['round_id'] : '';
			array_push($questions, array(0 => $id, 1 => 'Tossup', 2 => $subject, 3 => $answer, 4 => $roundID));
		}

		$columns = array('id', 'psets_allocations_id', 'answer1', 'answer2', 'answer3', 'answer4', 'round_id');
		$bonusesSelect = selectFrom('bonuses', $columns, array('psets_id', 'promoted'), array("'" . $_SESSION['tournament'] . "'", "'1'"));

		foreach ($bonusesSelect as $bonus){ //Iterate through $bonusesSelect, add to overall list of $questions
			$items = array('subject'); $columns = array('id');
			$values = array("'" . sanitize($bonus['psets_allocations_id']) . "'");
			
			$subjectSelect = selectFrom('psets_allocations', $items, $columns, $values);//Get subject.
			$subject = isset($subjectSelect[0]['subject']) ? $subjectSelect[0]['subject'] : 'None Set';
			
			$answer1 = (isset($bonus['answer1']) and $bonus['answer1'] != '') ? $bonus['answer1'] : '';
			$answer2 = (isset($bonus['answer2']) and $bonus['answer2'] != '') ? '; ' . $bonus['answer2'] : '';
			$answer3 = (isset($bonus['answer3']) and $bonus['answer3'] != '') ? '; ' . $bonus['answer3'] : '';
			$answer4 = (isset($bonus['answer4']) and $bonus['answer4'] != '') ? '; ' . $bonus['answer4'] : '';
			
			$answer = $answer1 . $answer2 . $answer3 . $answer4;
			$id = $bonus['id'];
			
			$roundID = isset($bonus['round_id']) ? $bonus['round_id'] : '';
			array_push($questions, array(0 => $id, 1 => 'Bonus', 2 => $subject, 3 => $answer, 4 => $roundID));
		}
		
		//Start Boilerplate ?>
		<table class="display" id="questions">
			<thead>
				<tr>
					<th>ID</th>
					<th>Type</th>
					<th>Subject</th>
					<th>Answer</th>
					<th>Packet</th>
				</tr>
			</thead>
			<tbody>
		<? //End Boilerplate
		
			//Print tossups, color coding by packet state
			foreach ($questions as $question){
				if ($question[4] == ''){
					echo '<tr class="blueRow">';
				}
				
				else{
					echo '<tr class="greenRow">';
				}
				
				foreach ($question as $key => $parameter){
					if ($key == 4){
						$which = ($question[1] == 'Tossup') ? '0' : '1';
						
						if ($parameter == ''){
							echo '<td><a onclick="modal_packets_assign(' . $question[0] . ', ' . $which . ')">None Set</a></td>';
						}
						
						else{
							echo '<td><a onclick="modal_packets_assign(' . $question[0] . ', ' . $which . ')">' . $parameter . '</a></td>';
						}
					}
					
					else{
						echo '<td>' . $parameter . '</td>';
					}
				}
				
				echo '</tr>';
			}
			
		//More Boilerplate ?>
			</tbody>
		</table><p></p>
		<script type="text/javascript">
			fancy_questions('questions');//Make the table pretty and searchable
		</script>
		<? //End Boilerplate
		
		$packets = array();
		
		$columns = array('round_id');
		$tossupsSelect = selectFrom('tossups', $columns, array('psets_id', 'promoted'), array("'" . $_SESSION['tournament'] . "'", "'1'"));

		foreach($tossupsSelect as $tossup){
			if (isset($tossup['round_id']) and $tossup['round_id'] != ''){
				if (!isset($packets[$tossup['round_id']])){
					$packets[$tossup['round_id']] = array(1,0);
				}
				
				else{
					$packets[$tossup['round_id']][0] += 1;
				}
			}
		}
		
		$columns = array('round_id');
		$bonusesSelect = selectFrom('bonuses', $columns, array('psets_id', 'promoted'), array("'" . $_SESSION['tournament'] . "'", "'1'"));

		foreach($bonusesSelect as $bonus){
			if (isset($bonus['round_id']) and $bonus['round_id'] != ''){
				if (!isset($packets[$bonus['round_id']])){
					$packets[$bonus['round_id']] = array(0,1);
				}
				
				else{
					$packets[$bonus['round_id']][1] += 1;
				}
			}
		}
		
		//Start Boilerplate ?>
		<table class="display" id="packets">
			<thead>
				<tr>
					<th>Packet ID</th>
					<th>Tossup Count</th>
					<th>Bonus Count</th>
					<th>Packet Download</th>
				</tr>
			</thead>
			<tbody>
		<? //End Boilerplate
			
			ksort($packets); //Sort by ID
			foreach ($packets as $packet => $counts){
				echo '<tr class="greenRow">';
				
				echo '<td>' . $packet . '</td>';
				echo '<td>' . $counts[0] . '</td>';
				echo '<td>' . $counts[1] . '</td>';
				echo '<td><a target="_blank" href="packet/' . $packet . '">Generate Packet</a></td>';
				
				echo '</tr>';
			}
			
		//More Boilerplate ?>
			</tbody>
		</table><p></p>
		<script type="text/javascript">
			fancy_packets('packets');//Make the table pretty and searchable
		</script>
		<? //End Boilerplate
	}
	
	else if ($submit == 1){
		$setID = $_POST['asp_set'];
		$tobID = $_POST['asp_id'];
		$type = $_POST['asp_type'];
		
		$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
		$userID = $userSelect[0]['id'];//Get current user's ID
		
		$permSelect = selectFrom('permissions', array('role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
		$role = $permSelect[0]['role'];//Get current user's role
		
		if ($role == 'd' or $role == 'a'){
			$setID = ltrim(preg_replace('/\D/', '', $setID),'0');
			
			if ($type == 0){
				updateIn('tossups', array('round_id'), array("'" . $setID . "'"), array('id'), array("'" . $tobID . "'"));
					
				?>
					<div class="message thank-message" onclick="go_packets()">
						<p><strong>You have successfully assigned this tossup. (Click to refresh.)</strong></p>
					</div>
				<?
			}
			
			else{
				updateIn('bonuses', array('round_id'), array("'" . $setID . "'"), array('id'), array("'" . $tobID . "'"));
					
				?>
					<div class="message thank-message" onclick="go_packets()">
						<p><strong>You have successfully assigned this bonus. (Click to refresh.)</strong></p>
					</div>
				<?
			}
		}
		
		else{
			echo $conf['noperms'];
		}
	}
	
	else if ($submit == 3){
		$packetNum = ltrim($_POST['aup_num'], '0');
		$tossupNum = ltrim($_POST['aup_tu'], '0');
		$bonusNum = ltrim($_POST['aup_b'], '0');
		$allocations = $_POST['aup_alloc'];
		
		$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
		$userID = $userSelect[0]['id'];//Get current user's ID
		
		$permSelect = selectFrom('permissions', array('role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
		$role = $permSelect[0]['role'];//Get current user's role
		
		if ($role == 'd' or $role == 'a'){
			if (isPosInt($packetNum) and isPosInt($tossupNum) and isPosInt($bonusNum)){
				$allocations = explode("\n", $allocations);
				
				$valid = true;
				$subjects = array();
				$weights = array();
				
				$packetNum = (int) $packetNum;
				$tossupNum = (int) $tossupNum;
				$bonusNum = (int) $bonusNum;
				$savedNum = $packetNum;
				
				$subjectSelect = selectFrom('psets_allocations', array('subject', 'id'), array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));	
				$entries = array(); $IDs = array();
				
				foreach ($subjectSelect as $entry){
					array_push($entries, $entry['subject']);
					array_push($IDs, $entry['id']);
				}
				
				foreach ($allocations as $allocation){
					$allocation = explode(" ", $allocation);
					
					foreach ($allocation as $key => $parameter){
						$allocation[$key] = trim($parameter);
					}
					
					$topic = implode(' ', array_slice($allocation, 0, count($allocation)-1));
					$weight = $allocation[count($allocation)-1];
					
					if (isPosInt($weight) and in_array($topic, $entries)){
						array_push($subjects, $IDs[array_search($topic, $entries)]);
						array_push($weights, intval($weight));
					}
					
					else{
						$valid = false;
					}
				}
				
				if ($valid){
					updateIn('tossups', array('round_id'), array("''"), array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));
					updateIn('bonuses', array('round_id'), array("''"), array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));
					
					$tossupsSelect = selectFrom('tossups', array('id', 'psets_allocations_id'), array('promoted', 'psets_id'), array("'1'", "'" . $_SESSION['tournament'] . "'"));
					$bonusesSelect = selectFrom('bonuses', array('id', 'psets_allocations_id'), array('promoted', 'psets_id'), array("'1'", "'" . $_SESSION['tournament'] . "'"));
					
					foreach ($tossupsSelect as $key => $entry){
						if (!isset($entry['psets_allocations_id']) or $entry['psets_allocations_id'] == ''){
							unset($tossupsSelect[$key]);
						}
						
						if (!in_array($entry['psets_allocations_id'], $subjects)){
							unset($tossupsSelect[$key]);
						}
					}
					
					foreach ($bonusesSelect as $key => $entry){
						if (!isset($entry['psets_allocations_id']) or $entry['psets_allocations_id'] == ''){
							unset($bonusesSelect[$key]);
						}
						
						if (!in_array($entry['psets_allocations_id'], $subjects)){
							unset($bonusesSelect[$key]);
						}
					}
					
					$tossupsSelect = shuffle_assoc($tossupsSelect);
					$bonusesSelect = shuffle_assoc($bonusesSelect);
					
					$currentNum = 1;
					$total = array_sum($weights);
					
					foreach ($weights as $key => $parameter){
						$weights[$key] = (int) (round($parameter*1.0/$total, 2)*100); //Two decimal places
					}
					
					$picksTU = array();
					$picksB = array();
					
					$tossupCount = $tossupNum;
					$bonusCount = $bonusNum;
				
					foreach ($weights as $key => $weight){
						for ($i = 0; $i < $weight; $i++){
							array_push($picksTU, $key);
							array_push($picksB, $key);
						}
					}
					
					while ($packetNum > 0 and count($tossupsSelect) > 0 and $tossupCount > 0){
						$found = false;
						
						while ($found == false){
							$index = array_rand($picksTU);
							$type = $picksTU[$index];
							
							foreach ($tossupsSelect as $key => $entry){
								if ($entry['psets_allocations_id'] == $subjects[$type]){
									updateIn('tossups', array('round_id'), array("'" . $currentNum . "'"), array('id'), array("'" . $entry['id'] . "'"));
								
									$delete = $key;
									$found = true;
									
									break;
								}
							}
							
							if ($found != true){
								$picksTU = array_diff($picksTU, array($type));
							}
							
							else{
								unset($tossupsSelect[$key]);
							}
						}
						
						if ($found == true){
							$tossupCount = $tossupCount - 1;
							
							if ($tossupCount == 0){
								if ($packetNum > 1){
									$tossupCount = $tossupNum;
								}
								
								$packetNum = $packetNum - 1;
								$currentNum = $currentNum + 1;
							}
						}
					}
					
					$errorPacketsTU = $packetNum;
					$errorTossups = $tossupCount;
					
					$currentNum = 1;
					$packetNum = $savedNum;
					
					while ($packetNum > 0 and count($bonusesSelect) > 0 and $bonusCount > 0){
						$found = false;
						
						while ($found == false){
							$index = array_rand($picksB);
							$type = $picksB[$index];
							
							foreach ($bonusesSelect as $key => $entry){	
								if ($entry['psets_allocations_id'] == $subjects[$type]){
									updateIn('bonuses', array('round_id'), array("'" . $currentNum . "'"), array('id'), array("'" . $entry['id'] . "'"));
								
									$delete = $key;
									$found = true;
									
									break;
								}
							}
							
							if ($found != true){
								$picksB = array_diff($picksB, array($type));
							}
							
							else{
								unset($bonusesSelect[$key]);
							}
						}
						
						if ($found == true){
							$bonusCount = $bonusCount - 1;
							
							if ($bonusCount == 0){
								if ($packetNum > 1){
									$bonusCount = $bonusNum;
								}
								
								$packetNum = $packetNum - 1;
								$currentNum = $currentNum + 1;
							}
						}
					}
					
					$errorPacketsB = $packetNum;
					$errorBonuses = $bonusCount;
					
					$problems = $errorPacketsTU + $errorTossups + $errorPacketsB + $errorBonuses;
					
					if ($problems > 0){
						?>
							<div class="message error-message" onclick="go_packets();">
								<p><strong>Your packets could only be partially generated. (Click to refresh.)</strong></p>
							</div>
						<?
					}
					
					else{
						?>
							<div class="message thank-message" onclick="go_packets();">
								<p><strong>Your packets were successfully generated. (Click to refresh.)</strong></p>
							</div>
						<?
					}
				}
				
				else{
					?>
						<div class="message error-message" onclick="cont_remove(this, 1);">
							<p><strong>Some of your entered values were invalid. (Click to hide.)</strong></p>
						</div>
					<?
				}
			}
			
			else{
				?>
					<div class="message error-message" onclick="cont_remove(this, 1);">
						<p><strong>Some of your entered values were invalid. (Click to hide.)</strong></p>
					</div>
				<?
			}
		}
		
		else{
			$conf['noperms'];
		}
	}
	
	else if ($submit == 4){
		if (isset($_SESSION['tournament'])){
			$packet = isset($_GET['packet']) ? $_GET['packet'] : '';
			
			$columns = array('creator_users_id', 'tossup', 'answer');
			$tossupsSelect = selectFrom('tossups', $columns, array('psets_id', 'promoted', 'round_id'), array("'" . $_SESSION['tournament'] . "'", "'1'", "'" . $packet . "'"));
			
			$columns = array('creator_users_id', 'leadin', 'question1', 'answer1', 'question2', 'answer2', 'question3', 'answer3', 'question4', 'answer4');
			$bonusesSelect = selectFrom('bonuses', $columns, array('psets_id', 'promoted', 'round_id'), array("'" . $_SESSION['tournament'] . "'", "'1'", "'" . $packet . "'"));
			
			$setSelect = selectFrom('psets', array('title'), array('id'), array("'" . $_SESSION['tournament'] . "'"));
			$set = $setSelect[0];
			
			if (count($tossupsSelect) + count($bonusesSelect) > 0){
				?>
					<html>
						<head>
							<title><? echo $set['title'] . ' - Round ' . $packet; ?></title>
							<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
							<style type="text/css">
								h1, h2, h3{
									display: inline;
									font: Times New Roman;
								}

								h4, h5, h6{
									display: inline;
									font: normal 1em Times New Roman;
								}
							</style>
						</head>
						<body>
				<?
				
				echo '<h3>' . $set['title'] . '</h3><br><h3>Packet ' . $packet . '</h3><br><br>Tossups: <ol>';
				
				foreach ($tossupsSelect as $key => $entry){
					echo '<li>' . $entry['tossup'] . '</li><br><dd>' . $entry['answer'] . '</dd><br>';
					
					if ($key != count($tossupsSelect) - 1){
						echo '<br>';
					}
				}
				
				echo '</ol>Bonuses: <ol>';
				
				foreach ($bonusesSelect as $entry){
					echo '<li>' . $entry['leadin'] . '</li><br><dd><ol type="A">';
					
					foreach (array('1', '2', '3', '4') as $num){
						if (isset($entry['question' . $num]) and $entry['question' . $num] != ''){
							echo '<li>' . $entry['question' . $num] . '</li><br>';
							echo '<dd>' . $entry['answer' . $num] . '</dd><br><br>';
						}
					}
					
					echo '</ol></dd>';
				}
				
				echo '</ol>';
			}
			
			else{
				echo $conf['invalid'];
			}
		}
		
		else{
			echo $conf['deny'];
		}
	}
}

else{
	echo $conf['unauthorized'];
}

?>