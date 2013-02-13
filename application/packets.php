<?php

require_once('conf.php');
require_once('query.php'); 
require_once('libs/dompdf/dompdf_config.inc.php'); //Used for generating PDFs

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
	return (int) $a['round_num'] - (int) $b['round_num']; //Don't Worry, (int) '' == 0
	//round_num should never be NULL in the DB by the time this function is applied
}

function diffByID($a, $b){
	return strcmp($a['id'], $b['id']);
}

function getPacketDifficulty($current, $total){
	if ($total == 1) {
		return 'Easy';
	}
	
	else if ($total == 2){
		if ($current == 1){
			return 'Easy';
		}
		
		else if ($current == 2){
			return 'Medium';
		}
	}
	
	else{
		if ($current/$total <= 1/3){
			return 'Easy';
		}
		
		else if ($current/$total <= 2/3){
			return 'Medium';
		}
		
		else{
			return 'Hard';
		}
	}
}

function nextOpenPacket($usedPackets){ //Find unused space in packets
	$packet = 1; 
	while (true){
		$columns = array('promoted', 'psets_id', 'round_id');
		$where = array("'1'", "'" . $_SESSION['tournament'] . "'", "'" . strval($packet) . "'");
		
		$count = getNumOf('tossups', $columns, $where);
		$count = $count + getNumOf('bonuses', $columns, $where);

		if ($count == 0 and !in_array($packet, $usedPackets)){
			return $packet;
		}
		
		else{
			$packet = $packet + 1;
		}
	}
}

function nextOpenTossup($packet, $usedTossups){
	$tossup = 1; 
	while (true){
		$columns = array('promoted', 'psets_id', 'round_id', 'round_num');
		$where = array("'1'", "'" . $_SESSION['tournament'] . "'", "'" . strval($packet) . "'", "'" . strval($tossup) . "'");
		
		//getNumOf should be faster than selectFrom
		$count = getNumOf('tossups', $columns, $where);
		
		if ($count == 0 and !in_array($tossup, $usedTossups)){
			return $tossup;
		}
		
		else{
			$tossup = $tossup + 1;
		}
	}
}

function nextOpenBonus($packet, $usedBonuses){
	$bonus = 1; 
	while (true){
		$columns = array('promoted', 'psets_id', 'round_id', 'round_num');
		$where = array("'1'", "'" . $_SESSION['tournament'] . "'", "'" . strval($packet) . "'", "'" . strval($bonus) . "'");
		
		//getNumOf should be faster than selectFrom
		$count = getNumOf('bonuses', $columns, $where);
		
		if ($count == 0 and !in_array($bonus, $usedBonuses)){
			return $bonus;
		}
		
		else{
			$bonus = $bonus + 1;
		}
	}
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
		
		$columns = array('round_id', 'psets_allocations_id');
		$subjects = array();
		
		$tossupsSelect = selectFrom('tossups', $columns, array('psets_id', 'promoted'), array("'" . $_SESSION['tournament'] . "'", "'1'"));
		$subjectsSelect = selectFrom('psets_allocations', array('subject', 'id'), array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));	

		foreach ($subjectsSelect as $entry){
			$subjects[$entry['id']] = $entry['subject']; //Populate subjects array
		}
		
		foreach($tossupsSelect as $tossup){
			if (isset($tossup['round_id']) and $tossup['round_id'] != ''){
				if (!isset($packets[$tossup['round_id']])){
					$packets[$tossup['round_id']] = array(1, 0, array($subjects[$tossup['psets_allocations_id']] => 1), array());
				}
				
				else{
					$packets[$tossup['round_id']][0] += 1;
					
					if (!isset($packets[$tossup['round_id']][2][$subjects[$tossup['psets_allocations_id']]])){
						$packets[$tossup['round_id']][2][$subjects[$tossup['psets_allocations_id']]] = 1;
					}
					
					else{
						$packets[$tossup['round_id']][2][$subjects[$tossup['psets_allocations_id']]] += 1;
					}
				}
			}
		}
		
		$columns = array('round_id', 'psets_allocations_id');
		$bonusesSelect = selectFrom('bonuses', $columns, array('psets_id', 'promoted'), array("'" . $_SESSION['tournament'] . "'", "'1'"));

		foreach($bonusesSelect as $bonus){
			if (isset($bonus['round_id']) and $bonus['round_id'] != ''){
				if (!isset($packets[$bonus['round_id']])){
					$packets[$bonus['round_id']] = array(0, 1, array(), array($subjects[$bonus['psets_allocations_id']] => 1));
				}
				
				else{
					$packets[$bonus['round_id']][1] += 1;
					
					if (!isset($packets[$bonus['round_id']][3][$subjects[$bonus['psets_allocations_id']]])){
						$packets[$bonus['round_id']][3][$subjects[$bonus['psets_allocations_id']]] = 1;
					}
					
					else{
						$packets[$bonus['round_id']][3][$subjects[$bonus['psets_allocations_id']]] += 1;
					}
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
				ksort($packets[$packet][2]);//sort tooltip subjects alphabetically
				ksort($packets[$packet][3]);
				
				echo '<tr class="greenRow">';
				
				echo '<td>' . $packet . '</td>';
				echo '<td><span id="t' . strval($packet) . '">' . $counts[0] . '</span><span style="display: none;" id="tc' . strval($packet) . '">';
				//Subject Tooltip Code Below
				
				$tracker = 1;
				foreach ($packets[$packet][2] as $topic => $amount){
					if ($tracker < count($packets[$packet][2])){
						echo $topic . ' - ' . strval($amount) . '<br>';
					}
					
					else{
						echo $topic . ' - ' . strval($amount);
					}
					
					$tracker += 1;
				}
				
				echo '</span></td><td><span id="b' . strval($packet) . '">' . $counts[1] . '</span><span style="display: none;" class="subjectTip" id="bc' . strval($packet) . '">';
				
				$tracker = 1;
				foreach ($packets[$packet][3] as $topic => $amount){
					if ($tracker < count($packets[$packet][3])){
						echo $topic . ' - ' . strval($amount) . '<br>';
					}
					
					else{
						echo $topic . ' - ' . strval($amount);
					}
					
					$tracker += 1;
				}
				
				echo '</span></td><td><a target="_blank" href="packet/qb/' . $packet . '">Download</a></td>';
				echo '<td><a target="_blank" href="packet/sb/' . $packet . '">Download</a></td>';
				
				echo '</tr>';
			}
			
		//More Boilerplate ?>
			</tbody>
		</table><p></p>
		<script type="text/javascript">
			fancy_packets('packets');//Make the table pretty and searchable
			
			<? foreach ($packets as $packet => $counts){
				echo "$('#t" . $packet . "').qtip({content: $('#tc" . $packet . "').html()});";//Make tooltips appear
				echo "$('#b" . $packet . "').qtip({content: $('#bc" . $packet . "').html()});";
			} ?>
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
				$tossupSelect = selectFrom('tossups', array('psets_allocations_id'), array('id'), array("'" . $tobID . "'"));
				if (isset($tossupSelect[0]['psets_allocations_id']) and $tossupSelect[0]['psets_allocations_id'] != ''){
					updateIn('tossups', array('round_id', 'round_num'), array("'" . $setID . "'", "'" . $setNum . "'"), array('id'), array("'" . $tobID . "'"));
					
					?>
						<div class="message thank-message" onclick="go_packets()">
							<p><strong>You have successfully assigned this tossup. (Click to refresh.)</strong></p>
						</div>
					<?
				}
				
				else{
					?>
						<div class="message error-message" onclick="cont_remove(this, 1);">
							<p><strong>You must assign a subject to this tossup. (Click to hide.)</strong></p>
						</div>
					<?
				}
			}
			
			else{
				$bonusSelect = selectFrom('bonuses', array('psets_allocations_id'), array('id'), array("'" . $tobID . "'"));
				if (isset($bonusSelect[0]['psets_allocations_id']) and $bonusSelect[0]['psets_allocations_id'] != ''){
					updateIn('bonuses', array('round_id', 'round_num'), array("'" . $setID . "'", "'" . $setNum . "'"), array('id'), array("'" . $tobID . "'"));
						
					?>
						<div class="message thank-message" onclick="go_packets()">
							<p><strong>You have successfully assigned this bonus. (Click to refresh.)</strong></p>
						</div>
					<?
				}
				
				else{
					?>
						<div class="message error-message" onclick="cont_remove(this, 1);">
							<p><strong>You must assign a subject to this bonus. (Click to hide.)</strong></p>
						</div>
					<?
				}
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
		
		$preserve = isset($_POST['aup_pre']) and $_POST['aup_pre'] == '' ? '0' : '1';
		$append = isset($_POST['aup_app']) and $_POST['aup_app'] == '' ? '0' : '1';
		$difficulty = isset($_POST['aup_dfs']) and $_POST['aup_dfs'] == '' ? '0' : '1';
		
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
					if ($packetNum < 51 and $tossupNum < 51 and $bonusNum < 51){
						if ($preserve == 0){ //Overwrite previously generated or assigned packets if user wants
							updateIn('tossups', array('round_id', 'round_num'), array("''", "''"), array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));
							updateIn('bonuses', array('round_id', 'round_num'), array("''", "''"), array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));
						}
						
						//Get all of tournament's promoted questions
						$tossupsSelect = selectFrom('tossups', array('id', 'psets_allocations_id', 'difficulty', 'round_id'), array('promoted', 'psets_id'), array("'1'", "'" . $_SESSION['tournament'] . "'"));
						$bonusesSelect = selectFrom('bonuses', array('id', 'psets_allocations_id', 'difficulty', 'round_id'), array('promoted', 'psets_id'), array("'1'", "'" . $_SESSION['tournament'] . "'"));
						
						//Filter anything that has a malformed or invalid subject
						//Optionally filter anything that is assigned if user wants
						foreach ($tossupsSelect as $key => $entry){
							if (!isset($entry['psets_allocations_id']) or $entry['psets_allocations_id'] == ''){
								unset($tossupsSelect[$key]);
								continue;
							}
							
							if (!in_array($entry['psets_allocations_id'], $subjects)){
								unset($tossupsSelect[$key]);
								continue;
							}
							
							if (isset($entry['round_id']) and $entry['round_id'] != '' and $preserve == 1){
								unset($tossupsSelect[$key]);
								continue;
							}
						}
						
						foreach ($bonusesSelect as $key => $entry){
							if (!isset($entry['psets_allocations_id']) or $entry['psets_allocations_id'] == ''){
								unset($bonusesSelect[$key]);
								continue;
							}
							
							if (!in_array($entry['psets_allocations_id'], $subjects)){
								unset($bonusesSelect[$key]);
								continue;
							}
							
							if (isset($entry['round_id']) and $entry['round_id'] != '' and $preserve == 1){
								unset($bonusesSelect[$key]);
								continue;
							}
						}
						
						$picksTU = $weights; //Weighted Arrays
						$picksB = $weights; //For Bonuses Too
						
						$weightSum = array_sum($weights);
						
						//Get total numbers of questions
						$totalTU = $tossupNum * $packetNum;
						$totalB = $bonusNum * $packetNum;
						
						//Get absolute numbers of weights
						foreach ($picksTU as $key => $value){
							$picksTU[$key] = floor(($value / $weightSum) * $totalTU);
						}
						
						foreach ($picksB as $key => $value){
							$picksB[$key] = floor(($value / $weightSum) * $totalB);
						}
						
						//Filter question set to match weights
						$toDeleteTU = array();
						$toDeleteB = array();
						
						$availableTU = 0;
						$availableB = 0;
						
						foreach ($tossupsSelect as $key => $tossup){
							if ($picksTU[array_search($tossup['psets_allocations_id'], $subjects)] > 0){
								$picksTU[array_search($tossup['psets_allocations_id'], $subjects)]--;
								$availableTU++;
							}
							
							else{
								array_push($toDeleteTU, $key);
							}
						}
						
						foreach ($bonusesSelect as $key => $bonus){
							if ($picksB[array_search($bonus['psets_allocations_id'], $subjects)] > 0){
								$picksB[array_search($bonus['psets_allocations_id'], $subjects)]--;
								$availableB++;
							}
							
							else{
								array_push($toDeleteB, $key);
							}
						}
						
						//Add what's left over to fill out a set
						//Delete the rest!!
						
						shuffle($toDeleteTU);
						shuffle($toDeleteB);
						
						foreach ($toDeleteTU as $key => $TUID){
							if ($availableTU >= $totalTU){
								unset($tossupsSelect[$TUID]);
							}
							
							else{
								$availableTU++;
							}
						}
						
						foreach ($toDeleteB as $key => $BID){
							if ($availableB >= $totalB){
								unset($bonusesSelect[$BID]);
							}
							
							else{
								$availableB++;
							}
						}
						
						shuffle($tossupsSelect);
						shuffle($bonusesSelect);
						
						//Difficulty Stuff
						//Make Three Copies
						
						$tossupsEasy = array();
						$tossupsMed = array();
						$tossupsHard = array();
						
						foreach ($tossupsSelect as $entry){
							if ($entry['difficulty'] == 'e'){
								$tossupsEasy = array_merge($tossupsEasy, array($entry, $entry, $entry, $entry, $entry)); //5x For Easy
								$tossupsMed = array_merge($tossupsMed, array($entry, $entry, $entry)); //3x For Med
								$tossupsHard = array_merge($tossupsHard, array($entry)); //1x For Hard
							}
							
							else if ($entry['difficulty'] == 'm'){
								$tossupsEasy = array_merge($tossupsEasy, array($entry)); //1x For Easy
								$tossupsMed = array_merge($tossupsMed, array($entry)); //1x For Med
								$tossupsHard = array_merge($tossupsHard, array($entry)); //1x For Hard
							}
							
							else if ($entry['difficulty'] == 'h'){
								$tossupsEasy = array_merge($tossupsEasy, array($entry)); //1x For Easy
								$tossupsMed = array_merge($tossupsMed, array($entry, $entry, $entry)); //3x For Med
								$tossupsHard = array_merge($tossupsHard, array($entry, $entry, $entry, $entry, $entry)); //5x For Hard
							}
						}
						
						$bonusesEasy = array();
						$bonusesMed = array();
						$bonusesHard = array();
						
						foreach ($bonusesSelect as $entry){
							if ($entry['difficulty'] == 'e'){
								$bonusesEasy = array_merge($bonusesEasy, array($entry, $entry, $entry, $entry, $entry)); //5x For Easy
								$bonusesMed = array_merge($bonusesMed, array($entry, $entry, $entry)); //3x For Med
								$bonusesHard = array_merge($bonusesHard, array($entry)); //1x For Hard
							}
							
							else if ($entry['difficulty'] == 'm'){
								$bonusesEasy = array_merge($bonusesEasy, array($entry)); //1x For Easy
								$bonusesMed = array_merge($bonusesMed, array($entry)); //1x For Med
								$bonusesHard = array_merge($bonusesHard, array($entry)); //1x For Hard
							}
							
							else if ($entry['difficulty'] == 'h'){
								$bonusesEasy = array_merge($bonusesEasy, array($entry)); //1x For Easy
								$bonusesMed = array_merge($bonusesMed, array($entry, $entry, $entry)); //3x For Med
								$bonusesHard = array_merge($bonusesHard, array($entry, $entry, $entry, $entry, $entry)); //5x For Hard
							}
						}
						
						//Generate list of questions to be filled
						//Using nextOpenBonus() and nextOpenTossup()
						
						$spotsT = array();//TU list
						$spotsB = array();//B list
						
						$usedP = array();
						$usedT = array();
						$usedB = array();
						
						for ($i = 1; $i <= $packetNum; $i++){
							if ($append == 0){
								$focusPacket = nextOpenPacket($usedP);
								$usedP[] = $focusPacket; //Add to list
							}
							
							else{
								$focusPacket = $i;
								$usedP[] = $focusPacket; //Add to list
							}
							
							for ($j = 1; $j <= $tossupNum; $j++){
								$focusTossup = nextOpenTossup($focusPacket, $usedT);
								$spotsT[] = array('packet' => $focusPacket, 'tossup' => $focusTossup);
								$usedT[] = $focusTossup; //Used tossup spot
							}
							
							for ($j = 1; $j <= $bonusNum; $j++){
								$focusBonus = nextOpenBonus($focusPacket, $usedB);
								$spotsB[] = array('packet' => $focusPacket, 'bonus' => $focusBonus);
								$usedB[] = $focusBonus; //Used bonus spot
							}
							
							$usedT = array();
							$usedB = array();
						}
						
						//Everyday I'm Shufflin'
						//Do One Last Shuffle
						
						shuffle($spotsT);
						shuffle($spotsB);
						
						//Randomly select questions
						//Add to packets
						
						while (count($tossupsMed) > 0 and count($spotsT) > 0){//Use TossupsMed for availability check
							$spotIndex = array_rand($spotsT);
							$spot = $spotsT[$spotIndex]; //get packet/TU #s
							
							if ($difficulty == 1){
								$level = getPacketDifficulty($spot['packet'], $packetNum);
							}
							
							else{
								$level = 'Medium'; //This is a neutral difficulty
							}
							
							if ($level == 'Easy'){
								$randomIndex = array_rand($tossupsEasy);
								$currentTossup = $tossupsEasy[$randomIndex];
							}
							
							else if ($level == 'Medium'){
								$randomIndex = array_rand($tossupsMed);
								$currentTossup = $tossupsMed[$randomIndex];
							}
							
							else if ($level == 'Hard'){
								$randomIndex = array_rand($tossupsHard);
								$currentTossup = $tossupsHard[$randomIndex];
							}
							
							//Update Tossup in database with Round ID and Round Number
							updateIn('tossups', array('round_id', 'round_num'), array("'" . strval($spot['packet']) . "'", "'" . strval($spot['tossup']) . "'"), array('id'), array("'" . $currentTossup['id'] . "'"));
							
							//Delete From All Arrays
							$tossupsEasy = array_udiff($tossupsEasy, array($currentTossup), 'diffByID');
							$tossupsMed = array_udiff($tossupsMed, array($currentTossup), 'diffByID');
							$tossupsHard = array_udiff($tossupsHard, array($currentTossup), 'diffByID');
							
							//Remove spot used
							unset($spotsT[$spotIndex]);
						}
						
						//Tossup Nonset Error
						$errorTU = count($spotsT);
						
						while (count($bonusesMed) > 0 and count($spotsB) > 0){//Use BonusesMed for availability check
							$spotIndex = array_rand($spotsB);
							$spot = $spotsB[$spotIndex]; //get packet/TU #s
							
							if ($difficulty == 1){
								$level = getPacketDifficulty($spot['packet'], $packetNum);
							}
							
							else{
								$level = 'Medium'; //This is a neutral difficulty
							}
							
							if ($level == 'Easy'){
								$randomIndex = array_rand($bonusesEasy);
								$currentBonus = $bonusesEasy[$randomIndex];
							}
							
							else if ($level == 'Medium'){
								$randomIndex = array_rand($bonusesMed);
								$currentBonus = $bonusesMed[$randomIndex];
							}
							
							else if ($level == 'Hard'){
								$randomIndex = array_rand($bonusesHard);
								$currentBonus = $bonusesHard[$randomIndex];
							}
							
							//Update Tossup in database with Round ID and Round Number
							updateIn('bonuses', array('round_id', 'round_num'), array("'" . strval($spot['packet']) . "'", "'" . strval($spot['bonus']) . "'"), array('id'), array("'" . $currentBonus['id'] . "'"));
							
							//Delete From All Arrays
							$bonusesEasy = array_udiff($bonusesEasy, array($currentBonus), 'diffByID');
							$bonusesMed = array_udiff($bonusesMed, array($currentBonus), 'diffByID');
							$bonusesHard = array_udiff($bonusesHard, array($currentBonus), 'diffByID');
							
							unset($spotsB[$spotIndex]);//Remove spot
						}
						
						//Bonus Nonset Error
						$errorB = count($spotsB);
						
						//I got 99 problems but a lack of sufficient questions ain't one
						$problems = $errorTU + $errorB;
						
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
								<p><strong>Your input values cannot be above fifty. (Click to hide.)</strong></p>
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
					<!DOCTYPE html>
					<html>
						<head>
							<title><? echo $set['title'] . ' - Round ' . $packet; ?></title>
							<link rel="icon" href="<? echo '../../' . $conf['css_favicon']; ?>" type="image/x-icon" media="all" />
							<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
							<style type="text/css">
								h1, h2, h3 {
									display: inline;
									font-family: Times New Roman;
								}
								
								u b, b u {
									font-weight: bold !important;
									text-decoration: underline !important;
								}
							</style>
							<style type="text/css" media="print">
								.break {
									page-break-before: always;
								}
							</style>
						</head>
						<body>
				<?
				
				echo '<h3>' . $set['title'] . '</h3><br><h3>Round ' . $packet . '</h3><br><br>Tossups: <ol>';
				
				foreach ($tossupsSelect as $key => $entry){
					echo '<li>' . $entry['tossup'] . '</li><br><dd>' . $entry['answer'] . '</dd>';
					
					if ($key != count($tossupsSelect) - 1){
						echo '<br>';
					}
				}
				
				echo '</ol><div class="break"></span>Bonuses: <ol>';
				
				foreach ($bonusesSelect as $entry){
					echo '<li>' . $entry['leadin'] . '</li><br><dd><ol type="A">';
					
					foreach (array('1', '2', '3', '4') as $num){
						if (isset($entry['question' . $num]) and $entry['question' . $num] != ''){
							echo '<li>' . $entry['question' . $num] . '</li><br>';
							echo '<dd>' . $entry['answer' . $num] . '</dd><br>';
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
					<!DOCTYPE html>
					<html>
						<head>
							<title><? echo $set['title'] . ' - Round ' . $packet; ?></title>
							<link rel="icon" href="<? echo '../../' . $conf['css_favicon']; ?>" type="image/x-icon" media="all" />
							<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
							<style type="text/css">
								h1, h2, h3 {
									display: inline;
									font-family: Times New Roman;
								}
								
								u b, b u {
									font-weight: bold !important;
									text-decoration: underline !important;
								}
							</style>
							<style type="text/css" media="print">
								.break {
									page-break-before: always;
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
					
					echo '<li>' . $subject . ' - ' . $type . ' - ' . $entry['tossup'] . '</li><br><dd>' . $entry['answer'] . '</dd>';
					
					if ($key != count($tossupsSelect) - 1){
						echo '<br>';
					}
				}
				
				echo '</ol><div class="break"></span>Bonuses: <ol>';
				
				foreach ($bonusesSelect as $entry){
					$items = array('subject'); $columns = array('id');
					$values = array("'" . sanitize($entry['psets_allocations_id']) . "'");
					
					$subjectSelect = selectFrom('psets_allocations', $items, $columns, $values);//Get subject.
					$subject = $subjectSelect[0]['subject'];
					$selector = substr(trim($entry['answer1']), 0, 2);
					
					if ($selector == 'W:' or $selector == 'X:' or $selector == 'Y:' or $selector == 'Z:'){
						$type = 'Multiple Choice';
					}
					
					else{
						$type = 'Short Answer';
					}
				
					echo '<li>' . $subject . ' - ' . $type . ' - ' . $entry['question1'] . '</li><br><dd>' . $entry['answer1'] . '</dd><br>';
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
	
	else if ($submit == 5 and true == false){//Suspend this temporarily
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
				$html =
					'<!DOCTYPE html>' .
					'<html>' .
						'<head>' .
							'<title>' . $set['title'] . ' - Round ' . $packet . '</title>' .
							'<link rel="icon" href="' . '../../' . $conf['css_favicon'] . '" type="image/x-icon" media="all" />' .
							'<meta http-equiv="Content-type" content="text/html; charset=utf-8" />' .
							'<style type="text/css">' .
							'	h1, h2, h3 {' .
							'		display: inline;' .
							'		font-family: Times New Roman;' .
							'	}' .
								
							'	u b, b u {' .
							'		font-weight: bold !important;' .
							'		text-decoration: underline !important;' .
							'	}' .
							'</style>' .
							'<style type="text/css" media="print">' .
							'	.break {' .
							'		page-break-before: always;' .
							'	}' .
							'</style>' .
						'</head> ' .
						'<body>';
				
				$html .= '<h3>' . $set['title'] . '</h3><br><h3>Round ' . $packet . '</h3><br><br>Tossups: <ol>';
				
				foreach ($tossupsSelect as $key => $entry){
					$html .= '<li>' . $entry['tossup'] . '</li><br><br><dd>' . $entry['answer'] . '</dd>';
					
					if ($key != count($tossupsSelect) - 1){
						$html .= '<br>';
					}
				}
				
				$html .= '</ol><div class="break"></span>Bonuses: <ol>';
				
				foreach ($bonusesSelect as $entry){
					$html .= '<li>' . $entry['leadin'] . '</li><br><dd><ol type="A">';
					
					foreach (array('1', '2', '3', '4') as $num){
						if (isset($entry['question' . $num]) and $entry['question' . $num] != ''){
							$html .= '<li>' . $entry['question' . $num] . '</li><br><br>';
							$html .= '<dd>' . $entry['answer' . $num] . '</dd><br>';
						}
					}
					
					$html .= '</ol></dd>';
				}
				
				$html .= '</ol></body></html>';
			}
			
			else{
				$html = $conf['invalid'];
			}
		}
		
		else{
			$html = $conf['deny'];
		}
		
		//Render PDF
		//$pdf = new DOMPDF();
		//$pdf->load_html($html);
		
		//$pdf->render();
		//$pdf->stream('Packet' . strval($packet) . '.pdf');
	}
	
	else if ($submit == 7 or $submit == 5){
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