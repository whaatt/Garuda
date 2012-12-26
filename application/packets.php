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

function sortQuestions($a, $b){ //Used to sort by sub-array value
	return (int) $a['round_num'] - (int) $b['round_num'];
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
		
		$columns = array('id', 'psets_allocations_id', 'answer', 'round_id', 'difficulty', 'round_num');
		$tossupsSelect = selectFrom('tossups', $columns, array('psets_id', 'promoted'), array("'" . $_SESSION['tournament'] . "'", "'1'"));

		foreach ($tossupsSelect as $tossup){ //Iterate through $tossupsSelect, add to overall list of $questions
			$items = array('subject'); $columns = array('id');
			$values = array("'" . sanitize($tossup['psets_allocations_id']) . "'");
			
			$subjectSelect = selectFrom('psets_allocations', $items, $columns, $values);//Get subject.
			$subject = isset($subjectSelect[0]['subject']) ? $subjectSelect[0]['subject'] : 'None Set';
			
			$difficulty = $tossup['difficulty'];
			$answer = $tossup['answer'];
			$id = $tossup['id'];
			
			if ($difficulty == 'e') { $difficulty = 'Easy'; }
			else if ($difficulty == 'm') { $difficulty = 'Medium'; }
			else if ($difficulty == 'h') { $difficulty = 'Hard'; }
			
			$roundID = isset($tossup['round_id']) ? $tossup['round_id'] : '';
			$roundNum = isset($tossup['round_num']) ? ($tossup['round_num'] != '' ? ' - ' . $tossup['round_num'] : '') : '';
			array_push($questions, array(0 => $id, 1 => 'Tossup', 2 => $subject, 3 => $difficulty, 4 => $answer, 5 => $roundID, 6 => $roundNum));
		}

		$columns = array('id', 'psets_allocations_id', 'answer1', 'answer2', 'answer3', 'answer4', 'round_id', 'difficulty', 'round_num');
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
			
			$difficulty = $bonus['difficulty'];
			$answer = $answer1 . $answer2 . $answer3 . $answer4;
			$id = $bonus['id'];
			
			if ($difficulty == 'e') { $difficulty = 'Easy'; }
			else if ($difficulty == 'm') { $difficulty = 'Medium'; }
			else if ($difficulty == 'h') { $difficulty = 'Hard'; }
			
			$roundID = isset($bonus['round_id']) ? $bonus['round_id'] : '';
			$roundNum = isset($bonus['round_num']) ? ($bonus['round_num'] != '' ? ' - ' . $bonus['round_num'] : '') : '';
			array_push($questions, array(0 => $id, 1 => 'Bonus', 2 => $subject, 3 => $difficulty, 4 => $answer, 5 => $roundID, 6 => $roundNum));
		}
		
		//Start Boilerplate ?>
		<table class="display" id="questions">
			<thead>
				<tr>
					<th>ID</th>
					<th>Type</th>
					<th>Subject</th>
					<th>Difficulty</th>
					<th>Answer</th>
					<th>Packet</th>
				</tr>
			</thead>
			<tbody>
		<? //End Boilerplate
		
			//Print tossups, color coding by packet state
			foreach ($questions as $question){
				if ($question[5] == ''){
					echo '<tr class="blueRow">';
				}
				
				else{
					echo '<tr class="greenRow">';
				}
				
				foreach ($question as $key => $parameter){
					if ($key == 5){
						$which = ($question[1] == 'Tossup') ? '0' : '1';
						
						if ($parameter == ''){
							echo '<td><a onclick="modal_packets_assign(' . $question[0] . ', ' . $which . ')">None Set</a></td>';
						}
						
						else{
							echo '<td><a onclick="modal_packets_assign(' . $question[0] . ', ' . $which . ')">' . $parameter . $question[6] . '</a></td>';
						}
					}
					
					else if ($key != 6){
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
		//Get Packet Values
		
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
					<th>Quiz Bowl</th>
					<th>Science Bowl</th>
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
				echo '<td><a target="_blank" href="packet/qb/' . $packet . '">HTML</a> or <a target="_blank" href="packet/qb/pdf/' . $packet . '">PDF</a></td>';
				echo '<td><a target="_blank" href="packet/sb/' . $packet . '">HTML</a> or <a target="_blank" href="packet/sb/pdf/' . $packet . '">PDF</a></td>';
				
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
		$setNum = $_POST['asp_num'];
		$type = $_POST['asp_type'];
		
		$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
		$userID = $userSelect[0]['id'];//Get current user's ID
		
		$permSelect = selectFrom('permissions', array('role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
		$role = $permSelect[0]['role'];//Get current user's role
		
		if ($role == 'd' or $role == 'a'){
			$setID = ltrim(preg_replace('/\D/', '', $setID),'0');
			$setNum = ltrim(preg_replace('/\D/', '', $setNum),'0');
			
			if ($type == 0){
				updateIn('tossups', array('round_id', 'round_num'), array("'" . $setID . "'", "'" . $setNum . "'"), array('id'), array("'" . $tobID . "'"));
					
				?>
					<div class="message thank-message" onclick="go_packets()">
						<p><strong>You have successfully assigned this tossup. (Click to refresh.)</strong></p>
					</div>
				<?
			}
			
			else{
				updateIn('bonuses', array('round_id', 'round_num'), array("'" . $setID . "'", "'" . $setNum . "'"), array('id'), array("'" . $tobID . "'"));
					
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
		$packetNum = trim(ltrim($_POST['aup_num'], '0'));
		$tossupNum = trim(ltrim($_POST['aup_tu'], '0'));
		$bonusNum = trim(ltrim($_POST['aup_b'], '0'));
		$allocations = $_POST['aup_alloc'];
		
		$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
		$userID = $userSelect[0]['id'];//Get current user's ID
		
		$permSelect = selectFrom('permissions', array('role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
		$role = $permSelect[0]['role'];//Get current user's role
		
		if ($role == 'd' or $role == 'a'){
			//Yeah, I know I could write the next line cleaner, but then I'd have to make a new function....
			if ((isPosInt($packetNum) or (int) $packetNum == 0) and (isPosInt($tossupNum) or (int) $tossupNum == 0) and (isPosInt($bonusNum) or (int) $bonusNum == 0)){
				$allocations = array_values(array_filter(explode("\n", $allocations)));
				
				$valid = true;
				$subjects = array();
				$weights = array();
				
				$packetNum = (int) $packetNum;
				$tossupNum = (int) $tossupNum;
				$bonusNum = (int) $bonusNum;
				$savedNum = $packetNum;
				
				$subjectSelect = selectFrom('psets_allocations', array('subject', 'id'), array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));	
				$entries = array(); $IDs = array(); //Build up an array of subjects and IDs
				
				foreach ($subjectSelect as $entry){
					array_push($entries, $entry['subject']);
					array_push($IDs, $entry['id']);
				}
				
				foreach ($allocations as $allocation){
					$allocation = array_values(array_filter(explode(" ", trim($allocation)))); //Array_filter to get rid of blank delimited entries
					
					foreach ($allocation as $key => $parameter){
						$allocation[$key] = trim($parameter);
					}
					
					//Get weights for every subject
					$topic = implode(" ", array_slice($allocation, 0, count($allocation)-1));
					$weight = $allocation[count($allocation)-1];
					
					//Create two arrays, called subjects and weights
					//$subjects will be populated with subject IDs using array_search from $entries
					//$weights will be an array that has index correspondence with $subjects
					
					if (isPosInt($weight) and in_array($topic, $entries)){
						array_push($subjects, $IDs[array_search($topic, $entries)]);
						array_push($weights, intval($weight));
					}
					
					else{
						$valid = false;
					}
				}
				
				if ($valid){
					//Overwrite previously generated or assigned packets
					updateIn('tossups', array('round_id', 'round_num'), array("''", "''"), array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));
					updateIn('bonuses', array('round_id', 'round_num'), array("''", "''"), array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));
					
					//Get all of tournament's promoted questions
					$tossupsSelect = selectFrom('tossups', array('id', 'psets_allocations_id'), array('promoted', 'psets_id'), array("'1'", "'" . $_SESSION['tournament'] . "'"));
					$bonusesSelect = selectFrom('bonuses', array('id', 'psets_allocations_id'), array('promoted', 'psets_id'), array("'1'", "'" . $_SESSION['tournament'] . "'"));
					
					//Filter anything that has a malformed or invalid subject
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
					
					//For randomness, shuffle selection
					$tossupsSelect = shuffle_assoc($tossupsSelect);
					$bonusesSelect = shuffle_assoc($bonusesSelect);
					
					$currentNum = 1; //Packet Number
					$currentSpot = 1; //Question Number
					$total = array_sum($weights);
					
					$picksTU = $weights; //Weighted Arrays
					$picksB = $weights; //For Bonuses Too
					
					$tossupCount = $tossupNum;
					$bonusCount = $bonusNum;
					
					while ($packetNum > 0 and count($tossupsSelect) > 0 and $tossupCount > 0){
						$found = false;
						
						while ($found == false){
							$alpha = mt_rand(0, $total - 1); //Get Selector, using Twister
							
							foreach ($picksTU as $index => $value){ //Get Random Topic
								$alpha = $alpha - $value;
								
								if ($alpha < 0){
									$type = $index; //$subjects index
								}
							}
							
							foreach ($tossupsSelect as $key => $entry){
								if ($entry['psets_allocations_id'] == $subjects[$type]){
									updateIn('tossups', array('round_id', 'round_num'), array("'" . $currentNum . "'", "'" . $currentSpot . "'"), array('id'), array("'" . $entry['id'] . "'"));
								
									$delete = $key;
									$found = true;
									
									break;
								}
							}
							
							if ($found != true){ //Delete Topic
								unset($picksTU[$type]);
								$picksTU = array_values($picksTU);
							}
							
							else{
								unset($tossupsSelect[$delete]);
							}
						}
						
						//Update Packet and Question Numbers
						if ($found == true){
							$tossupCount = $tossupCount - 1;
							$currentSpot = $currentSpot + 1;
							
							if ($tossupCount == 0){
								if ($packetNum > 1){
									$tossupCount = $tossupNum;
								}
								
								$packetNum = $packetNum - 1;
								$currentNum = $currentNum + 1;
								$currentSpot = 1;
							}
						}
					}
					
					$errorPacketsTU = ($tossupNum == 0) ? 0 : $packetNum; //Packet = 0
					$errorTossups = $tossupCount;
					
					$currentNum = 1;
					$packetNum = $savedNum;
					
					while ($packetNum > 0 and count($bonusesSelect) > 0 and $bonusCount > 0){
						$found = false;
						
						while ($found == false){
							$alpha = mt_rand(0, $total - 1); //Get Selector, using Twister
							
							foreach ($picksB as $index => $value){ //Get Random Topic
								$alpha = $alpha - $value;
								
								if ($alpha < 0){
									$type = $index; //$subjects index
								}
							}
							
							foreach ($bonusesSelect as $key => $entry){
								if ($entry['psets_allocations_id'] == $subjects[$type]){
									updateIn('bonuses', array('round_id', 'round_num'), array("'" . $currentNum . "'", "'" . $currentSpot . "'"), array('id'), array("'" . $entry['id'] . "'"));
								
									$delete = $key;
									$found = true;
									
									break;
								}
							}
							
							if ($found != true){ //Delete Topic
								unset($picksB[$type]);
								$picksB = array_values($picksB);
							}
							
							else{
								unset($bonusesSelect[$delete]);
							}
						}
						
						//Update Packet and Question Numbers
						if ($found == true){
							$bonusCount = $bonusCount - 1;
							$currentSpot = $currentSpot + 1;
							
							if ($bonusCount == 0){
								if ($packetNum > 1){
									$bonusCount = $bonusNum;
								}
								
								$packetNum = $packetNum - 1;
								$currentNum = $currentNum + 1;
								$currentSpot = 1;
							}
						}
					}
					
					$errorPacketsB = ($bonusNum == 0) ? 0 : $packetNum;
					$errorBonuses = $bonusCount;
					
					//I got 99 problems but a lack of sufficient questions ain't one
					$problems = $errorPacketsTU + $errorTossups + $errorPacketsB + $errorBonuses;
					
					if ($problems > 0){
						?>
							<div class="message error-message" onclick="go_packets();">
								<p><strong>Your packets could only be partially generated. (Click to refresh.)<? print($errorPacketsTU); ?></strong></p>
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
			
			$columns = array('creator_users_id', 'tossup', 'answer', 'round_num');
			$tossupsSelect = selectFrom('tossups', $columns, array('psets_id', 'promoted', 'round_id'), array("'" . $_SESSION['tournament'] . "'", "'1'", "'" . $packet . "'"));
			
			$columns = array('creator_users_id', 'leadin', 'question1', 'answer1', 'question2', 'answer2', 'question3', 'answer3', 'question4', 'answer4', 'round_num');
			$bonusesSelect = selectFrom('bonuses', $columns, array('psets_id', 'promoted', 'round_id'), array("'" . $_SESSION['tournament'] . "'", "'1'", "'" . $packet . "'"));
			
			$setSelect = selectFrom('psets', array('title'), array('id'), array("'" . $_SESSION['tournament'] . "'"));
			$set = $setSelect[0];
			
			uasort($tossupsSelect, 'sortQuestions');
			uasort($bonusesSelect, 'sortQuestions');
			
			$tossupsSelect = array_values($tossupsSelect);
			$bonusesSelect = array_values($bonusesSelect);
			
			if (count($tossupsSelect) + count($bonusesSelect) > 0){
				?>
					<html>
						<head>
							<title><? echo $set['title'] . ' - Round ' . $packet; ?></title>
							<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
							<style type="text/css">
								h1, h2, h3{
									display: inline;
									font-family: Times New Roman;
								}
							</style>
						</head>
						<body>
				<?
				
				echo '<h3>' . $set['title'] . '</h3><br><h3>Round ' . $packet . '</h3><br><br>Tossups: <ol>';
				
				foreach ($tossupsSelect as $key => $entry){
					echo '<li>' . $entry['tossup'] . '</li><br><dd>' . $entry['answer'] . '</dd><br>';
					
					if ($key != count($tossupsSelect) - 1){
						echo '<br>';
					}
				}
				
				echo '</ol><span style="page-break-before: always"></span>Bonuses: <ol>';
				
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
				
				echo '</ol></body></html>';
			}
			
			else{
				echo $conf['invalid'];
			}
		}
		
		else{
			echo $conf['deny'];
		}
	}
	
	else if ($submit == 6){
		if (isset($_SESSION['tournament'])){
			$packet = isset($_GET['packet']) ? $_GET['packet'] : '';
			
			$columns = array('creator_users_id', 'tossup', 'answer', 'round_num', 'psets_allocations_id');
			$tossupsSelect = selectFrom('tossups', $columns, array('psets_id', 'promoted', 'round_id'), array("'" . $_SESSION['tournament'] . "'", "'1'", "'" . $packet . "'"));
			
			$columns = array('creator_users_id', 'question1', 'answer1', 'round_num', 'psets_allocations_id');
			$bonusesSelect = selectFrom('bonuses', $columns, array('psets_id', 'promoted', 'round_id'), array("'" . $_SESSION['tournament'] . "'", "'1'", "'" . $packet . "'"));
			
			$setSelect = selectFrom('psets', array('title'), array('id'), array("'" . $_SESSION['tournament'] . "'"));
			$set = $setSelect[0];
			
			uasort($tossupsSelect, 'sortQuestions');
			uasort($bonusesSelect, 'sortQuestions');
			
			$tossupsSelect = array_values($tossupsSelect);
			$bonusesSelect = array_values($bonusesSelect);
			
			if (count($tossupsSelect) + count($bonusesSelect) > 0){
				?>
					<html>
						<head>
							<title><? echo $set['title'] . ' - Round ' . $packet; ?></title>
							<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
							<style type="text/css">
								h1, h2, h3{
									display: inline;
									font-family: Times New Roman;
								}
							</style>
						</head>
						<body>
				<?
				
				echo '<h3>' . $set['title'] . '</h3><br><h3>Round ' . $packet . '</h3><br><br>Tossups: <ol>';
				
				foreach ($tossupsSelect as $key => $entry){						
					$items = array('subject'); $columns = array('id');
					$values = array("'" . sanitize($entry['psets_allocations_id']) . "'");
					
					$subjectSelect = selectFrom('psets_allocations', $items, $columns, $values);//Get subject.
					$subject = $subjectSelect[0]['subject'];
					$selector = substr(trim($entry['answer']), 0, 2);
					
					if ($selector == 'W:' or $selector == 'X:' or $selector == 'Y:' or $selector == 'Z:'){
						$type = 'Multiple Choice';
					}
					
					else{
						$type = 'Short Answer';
					}
					
					echo '<li>' . $subject . ' - ' . $type . ' - ' . $entry['tossup'] . '</li><br><dd>' . $entry['answer'] . '</dd><br>';
					
					if ($key != count($tossupsSelect) - 1){
						echo '<br>';
					}
				}
				
				echo '</ol><span style="page-break-before: always"></span>Bonuses: <ol>';
				
				foreach ($bonusesSelect as $entry){
					$items = array('subject'); $columns = array('id');
					$values = array("'" . sanitize($entry['psets_allocations_id']) . "'");
					
					$subjectSelect = selectFrom('psets_allocations', $items, $columns, $values);//Get subject.
					$subject = $subjectSelect[0]['subject'];
					$selector = substr(trim($entry['answer']), 0, 1);
					
					if ($selector == 'W:' or $selector == 'X:' or $selector == 'Y:' or $selector == 'Z:'){
						$type = 'Multiple Choice';
					}
					
					else{
						$type = 'Short Answer';
					}
				
					echo '<li>' . $subject . ' - ' . $type . ' - ' . $entry['question1'] . '</li><br><dd>' . $entry['answer1'] . '</dd><br><br>';
				}
				
				echo '</ol></body></html>';
			}
			
			else{
				echo $conf['invalid'];
			}
		}
		
		else{
			echo $conf['deny'];
		}
	}
	
	else if ($submit == 5 or $submit == 7){
		if (isset($_SESSION['tournament'])){
			?>
				<h3>Notice</h3>
				<p>At this time, PDF generation is disabled. However, you can save the HTML file on your computer and use any of several free conversion tools. Thank you.</p>
			<?
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