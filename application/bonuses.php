<?php 

require_once('conf.php');
require_once('query.php'); 

/* Bonuses Handler */

function sortBonuses($a, $b) { //Used to sort by sub-array value
   return strcmp($a[0], $b[0]);
}

if(isset($_SESSION['username'])){
	$submit = $_POST['submit']; //Form Submit Boolean

	if ($submit == '2'){ //Delete Bonus Submission
		$deleteID = $_POST['id'];
		
		$bonusSelect = selectFrom('bonuses', array('psets_allocations_id', 'creator_users_id'), array('id'), array("'" . $deleteID . "'"));//Get bonus
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
		
		else if ($userRole == 'm' and strlen($userFocus) > 0 and in_array($bonus['psets_allocations_id'], explode(',', $userFocus))){
			$access = true;
		}

		else if ($userRole == 'a' or $userRole == 'd'){
			$access = true;
		}
		
		if ($access == true){
			?>
			<div id="delete" class="message error-message">
				<p><strong>Are you sure you want to delete this bonus? <a onclick="cont_delete('bonus', <? echo $deleteID; ?>);"><small><small>Yes</small></small></a> or <a onclick="cont_remove($(this).closest('#delete'), 1);"><big><big>No</big></big></a>.</strong></p>
			</div>
			<?
		}
		
		else{
			?>
				<div class="message error-message" onclick="cont_remove(this, 1);">
					<p><strong>You do not have sufficient permissions for this. (Click to hide.)</strong></p>
				</div>
			<?
		}
	}
	
	else if ($submit == '1'){ //Create Form Submission
		$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
		$userID = $userSelect[0]['id'];//Get current user's ID
		
		//Get POST stuff 
		$leadin = $_POST['crb_lead'];
		$subject = $_POST['crb_subj'];
		
		$body1 = $_POST['crb_body1'];
		$body2 = $_POST['crb_body2'];
		$body3 = $_POST['crb_body3'];
		$body4 = $_POST['crb_body4'];
		
		$answer1 = $_POST['crb_ans1'];
		$answer2 = $_POST['crb_ans2'];
		$answer3 = $_POST['crb_ans3'];
		$answer4 = $_POST['crb_ans4'];
		
		if (strlen($body1) < 5000 and strlen($body2) < 5000 and strlen($body3) < 5000 and strlen($body4) < 5000 and strlen($leadin) < 500 and strlen($answer1) < 500 and strlen($answer2) < 500 and strlen($answer3) < 500 and strlen($answer4) < 500){
			if ($subject != 'None'){
				$idSelect = selectFrom('psets_allocations', array('id'), array('subject', 'psets_id'), array("'" . sanitize($subject) . "'", "'" . $_SESSION['tournament'] . "'"));
				$subjectID = $idSelect[0]['id'];//Get the subject's ID
				
				$columns = array('psets_id', 'creator_users_id', 'psets_allocations_id', 'leadin', 'question1', 'question2', 'question3', 'question4', 'answer1', 'answer2', 'answer3', 'answer4', 'approved', 'promoted', 'created');
				$values = array("'" . $_SESSION['tournament'] . "'", "'" . $userID . "'", "'" . $subjectID . "'", "'" . sanitize(trim($leadin), false, '<b><i><u>') . "'", "'" . sanitize(trim($body1), false, '<b><i>') . "'", "'" . sanitize(trim($body2), false, '<b><i>') . "'", "'" . sanitize(trim($body3), false, '<b><i>') . "'", "'" . sanitize(trim($body4), false, '<b><i>') . "'", "'" . sanitize(trim($answer1), false, '<b><i><u>') . "'", "'" . sanitize(trim($answer2), false, '<b><i><u>') . "'", "'" . sanitize(trim($answer3), false, '<b><i><u>') . "'", "'" . sanitize(trim($answer4), false, '<b><i><u>') . "'", "'0'", "'0'", "NOW()");
				insertInto('bonuses', $columns, $values);//Add bonus to database
			}
			
			else{//Subject NULL in DB
				$columns = array('psets_id', 'creator_users_id', 'psets_allocations_id', 'leadin', 'question1', 'question2', 'question3', 'question4', 'answer1', 'answer2', 'answer3', 'answer4', 'approved', 'promoted', 'created');
				$values = array("'" . $_SESSION['tournament'] . "'", "'" . $userID . "'", "NULL", "'" . sanitize(trim($leadin), false, '<b><i><u>') . "'", "'" . sanitize(trim($body1), false, '<b><i>') . "'", "'" . sanitize(trim($body2), false, '<b><i>') . "'", "'" . sanitize(trim($body3), false, '<b><i>') . "'", "'" . sanitize(trim($body4), false, '<b><i>') . "'", "'" . sanitize(trim($answer1), false, '<b><i><u>') . "'", "'" . sanitize(trim($answer2), false, '<b><i><u>') . "'", "'" . sanitize(trim($answer3), false, '<b><i><u>') . "'", "'" . sanitize(trim($answer4), false, '<b><i><u>') . "'", "'0'", "'0'", "NOW()");
				insertInto('bonuses', $columns, $values);//Add bonus to database
			}
		
			?>
			<div class="message thank-message" onclick="go_bonuses()">
				<p><strong>You have successfully created a bonus. (Click to refresh.)</strong></p>
			</div>
			<?
		}
		
		else{
			?>
			<div class="message error-message" onclick="cont_remove(this, 1);">
				<p><strong>One of your fields was way too long. (Click to hide.)</strong></p>
			</div>
			<?
		}
	}
	
	else if ($submit == '3'){ //Edit Form Submission
		$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
		$userID = $userSelect[0]['id'];//Get current user's ID
		
		//Get POST stuff
		$id = $_POST['edb_id'];
		$leadin = $_POST['edb_lead'];
		$subject = $_POST['edb_subj'];
		
		$body1 = $_POST['edb_body1'];
		$body2 = $_POST['edb_body2'];
		$body3 = $_POST['edb_body3'];
		$body4 = $_POST['edb_body4'];
		
		$answer1 = $_POST['edb_ans1'];
		$answer2 = $_POST['edb_ans2'];
		$answer3 = $_POST['edb_ans3'];
		$answer4 = $_POST['edb_ans4'];
		
		$bonusSelect = selectFrom('bonuses', array('psets_allocations_id', 'creator_users_id'), array('id'), array("'" . $id . "'"));//Get bonuses
		$bonus = $bonusSelect[0];
		
		$permSelect = selectFrom('permissions', array('role', 'psets_allocations_id'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
		$userRole = $permSelect[0]['role'];//Get current user's role
		$userFocus = isset($permSelect[0]['psets_allocations_id']) ? $permSelect[0]['psets_allocations_id'] : 'None';
		
		$access = false;
		
		if ($userID == $bonus['creator_users_id']){
			$access = true;
		}
		
		else if ($userRole == 'm' and strlen($userFocus) > 0 and in_array($bonus['psets_allocations_id'], explode(',', $userFocus))){
			$access = true;
		}

		else if ($userRole == 'a' or $userRole == 'd'){
			$access = true;
		}
		
		if ($access == true){
			if (strlen($body1) < 5000 and strlen($body2) < 5000 and strlen($body3) < 5000 and strlen($body4) < 5000 and strlen($leadin) < 500 and strlen($answer1) < 500 and strlen($answer2) < 500 and strlen($answer3) < 500 and strlen($answer4) < 500){
				if ($subject != 'None'){
					$idSelect = selectFrom('psets_allocations', array('id'), array('subject', 'psets_id'), array("'" . sanitize($subject) . "'", "'" . $_SESSION['tournament'] . "'"));
					$subjectID = $idSelect[0]['id'];//Get the subject's ID
					
					$columns = array('psets_allocations_id', 'leadin', 'question1', 'question2', 'question3', 'question4', 'answer1', 'answer2', 'answer3', 'answer4', 'editor_users_id');
					$values = array("'" . $subjectID . "'", "'" . sanitize(trim($leadin), false, '<b><i><u>') . "'", "'" . sanitize(trim($body1), false, '<b><i>') . "'", "'" . sanitize(trim($body2), false, '<b><i>') . "'", "'" . sanitize(trim($body3), false, '<b><i>') . "'", "'" . sanitize(trim($body4), false, '<b><i>') . "'", "'" . sanitize(trim($answer1), false, '<b><i><u>') . "'", "'" . sanitize(trim($answer2), false, '<b><i><u>') . "'", "'" . sanitize(trim($answer3), false, '<b><i><u>') . "'", "'" . sanitize(trim($answer4), false, '<b><i><u>') . "'", "'" . $userID . "'");
					updateIn('bonuses', $columns, $values, array('id'), array("'" . $id . "'"));//Update bonus in database
				}
				
				else{//Subject NULL in DB
					$columns = array('psets_allocations_id', 'leadin', 'question1', 'question2', 'question3', 'question4', 'answer1', 'answer2', 'answer3', 'answer4', 'editor_users_id');
					$values = array("NULL", "'" . sanitize(trim($leadin), false, '<b><i><u>') . "'", "'" . sanitize(trim($body1), false, '<b><i>') . "'", "'" . sanitize(trim($body2), false, '<b><i>') . "'", "'" . sanitize(trim($body3), false, '<b><i>') . "'", "'" . sanitize(trim($body4), false, '<b><i>') . "'", "'" . sanitize(trim($answer1), false, '<b><i><u>') . "'", "'" . sanitize(trim($answer2), false, '<b><i><u>') . "'", "'" . sanitize(trim($answer3), false, '<b><i><u>') . "'", "'" . sanitize(trim($answer4), false, '<b><i><u>') . "'", "'" . $userID . "'");
					updateIn('bonuses', $columns, $values, array('id'), array("'" . $id . "'"));//Update bonus in database
				}
			
				?>
				<div class="message thank-message" onclick="go_bonuses()">
					<p><strong>You have successfully edited this bonus. (Click to refresh.)</strong></p>
				</div>
				<?
			}
			
			else{
				?>
				<div class="message error-message" onclick="cont_remove(this, 1);">
					<p><strong>One of your fields was way too long. (Click to hide.)</strong></p>
				</div>
				<?
			}
		}
		
		else {
			echo $conf['noperms'];
		}
	}
	
	else if ($submit == '4'){ //Mark Form Submission
		$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
		$userID = $userSelect[0]['id'];//Get current user's ID
		
		//Get POST stuff 
		$id = $_POST['mrb_id'];
		
		$approved = isset($_POST['mrb_app']) and $_POST['mrb_app'] == '' ? '0' : '1';
		$promoted = isset($_POST['mrb_pro']) and $_POST['mrb_pro'] == '' ? '0' : '1';
		$duplicate = ltrim(preg_replace('/\D/', '', $_POST['mrb_dup']),'0');//Format number appropriately, remove leading zeros
		
		$bonusSelect = selectFrom('bonuses', array('psets_allocations_id', 'creator_users_id'), array('id'), array("'" . $id . "'"));//Get bonus
		$bonus = $bonusSelect[0];
		$bonus['psets_allocations_id'] = isset($bonusSelect[0]['psets_allocations_id']) ? $bonusSelect[0]['psets_allocations_id'] : '';
			
		$permSelect = selectFrom('permissions', array('role', 'psets_allocations_id'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
		$userRole = $permSelect[0]['role'];//Get current user's role
		$userFocus = isset($permSelect[0]['psets_allocations_id']) ? $permSelect[0]['psets_allocations_id'] : '';
		
		if ($duplicate != ''){
			$bonusSelect = selectFrom('bonuses', array('id', 'duplicate_bonuses_id'), array('id'), array("'" . $duplicate . "'"));
			if (count($bonusSelect) > 0){
				if (isset($bonusSelect[0]['duplicate_bonuses_id'])){
					$duplicate = $bonusSelect[0]['duplicate_bonuses_id'];//Bubble up to master bonus
				}
				
				else{
					$duplicate = $bonusSelect[0]['id'];//It is the master
				}
			}
			
			else{
				$duplicate = '';//Invalid duplicate
			}
		}
		
		if ($userRole == 'd' or $userRole == 'a' or (strlen($userFocus) > 0 and in_array($bonus['psets_allocations_id'], explode(',', $userFocus)))){
			if ($duplicate != ''){
				$columns = array('approved', 'promoted', 'duplicate_bonuses_id');
				$values = array("'" . $approved . "'", "'" . $promoted . "'", "'" . $duplicate . "'");
				updateIn('bonuses', $columns, $values, array('id'), array("'" . $id . "'"));//Update bonus markdown in database
			
				$columns = array('duplicate_bonuses_id');
				$values = array("'" . $duplicate . "'");
			
				$where = array('duplicate_bonuses_id');
				$equals = array("'" . $id . "'");
			
				updateIn('bonuses', $columns, $values, $where, $equals);//Bubble down, as well
			}
			
			else{
				$columns = array('approved', 'promoted', 'duplicate_bonuses_id');
				$values = array("'" . $approved . "'", "'" . $promoted . "'", "NULL");
				updateIn('bonuses', $columns, $values, array('id'), array("'" . $id . "'"));//Update bonus markdown in database
			}
			
			?>
				<div class="message thank-message" onclick="go_bonuses()">
					<p><strong>You have successfully marked this bonus. (Click to refresh.)</strong></p>
				</div>
			<?
		}
		
		else{
			echo $conf['noperms'];
		}
	}
	
	else if ($submit == 5){ //Send Form Submission
		$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
		$userID = $userSelect[0]['id'];//Get current user's ID
		
		//Get POST stuff
		$id = $_POST['sdb_id'];
		$message = $_POST['sdb_msg'];
		
		if (strlen($message) < 1000){
			$columns = array('tossup_or_bonus', 'tub_id', 'users_id', 'message');
			$values = array("'1'", "'" . $id . "'", "'" . $userID. "'", "'" . sanitize(trim($message), false, '<b><i><u>') . "'");
			insertInto('messages', $columns, $values);//Add message to database
			
			?>
			<div class="message thank-message" onclick="go_bonuses()">
				<p><strong>You have successfully added your message. (Click to refresh.)</strong></p>
			</div>
			<?
		}
		
		else{
			?>
			<div class="message error-message" onclick="cont_remove(this, 1);">
				<p><strong>Your message was way too long. (Click to hide.)</strong></p>
			</div>
			<?
		}
	}
	
	else if ($submit == '6'){ //Delete Entry Submission
		$deleteID = $_POST['id'];
		
		$messageSelect = selectFrom('messages', array('tub_id', 'users_id'), array('id'), array("'" . $deleteID . "'"));//Get bonuses
		$bonusID = $messageSelect[0]['tub_id'];
		
		$bonusSelect = selectFrom('bonuses', array('psets_allocations_id', 'creator_users_id'), array('id'), array("'" . $bonusID . "'"));//Get bonus
		$bonus = $bonusSelect[0];
		$bonus['psets_allocations_id'] = isset($bonus['psets_allocations_id']) ? $bonus['psets_allocations_id'] : '';
		
		$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
		$userID = $userSelect[0]['id'];//Get current user's ID
		
		$permSelect = selectFrom('permissions', array('psets_allocations_id', 'role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
		$userRole = $permSelect[0]['role'];//Get current user's role
		$userFocus = isset($permSelect[0]['psets_allocations_id']) ? $permSelect[0]['psets_allocations_id'] : '';
		
		$access = false;
		
		if ($userID == $bonus['creator_users_id'] or $userID == $messageSelect[0]['users_id']){
			$access = true;
		}
		
		else if ($userRole == 'm' and strlen($userFocus) > 0 and in_array($bonus['psets_allocations_id'], explode(',', $userFocus))){
			$access = true;
		}

		else if ($userRole == 'a' or $userRole == 'd'){
			$access = true;
		}
		
		if ($access == true){
			?>
				<div id="delete" class="message error-message">
					<p><strong>Are you sure you want to delete this entry? <a onclick="cont_delete('message', <? echo $deleteID; ?>); cont_remove($(this).closest('#delete'), 1);"><small><small>Yes</small></small></a> or <a onclick="cont_remove($(this).closest('#delete'), 1);"><big><big>No</big></big></a>.</strong></p>
				</div>
			<?
		}
		
		else{
			?>
				<div class="message error-message" onclick="cont_remove(this, 1);">
					<p><strong>You do not have sufficient permissions for this. (Click to hide.)</strong></p>
				</div>
			<?
		}
	}
	
	else{
		echo $conf['bonuses'];
		echo '<p>You may add messages to a particular bonus, whether or not you have edit permissions for it. If you have manager or administrator permissions, you may additionally mark a bonus as a duplicate of another. <a onclick="modal_create_bonus()">Click here</a> to create a bonus, but please check for duplicate entries here before you do so.</p>';
		
		$bonuses = array();
		
		$columns = array('id', 'psets_allocations_id', 'answer1', 'answer2', 'answer3', 'answer4', 'duplicate_bonuses_id', 'approved', 'promoted');
		$bonusesSelect = selectFrom('bonuses', $columns, array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));

		foreach ($bonusesSelect as $bonus){ //Iterate through $bonusesSelect, building up $bonuses
			$items = array('subject'); $columns = array('id');
			$values = array("'" . sanitize($bonus['psets_allocations_id']) . "'");
			
			$subjectSelect = selectFrom('psets_allocations', $items, $columns, $values);//Get subject.
			$subject = isset($subjectSelect[0]['subject']) ? $subjectSelect[0]['subject'] : 'None Set';
			
			$duplicate = !empty($bonus['duplicate_bonuses_id']) ? $bonus['duplicate_bonuses_id'] : 'No';
			$approved = $bonus['approved'] == '1' ? 'Yes' : 'No';
			$promoted = $bonus['promoted'] == '1' ? 'Yes' : 'No';
			
			$answer1 = (isset($bonus['answer1']) and $bonus['answer1'] != '') ? $bonus['answer1'] : '';
			$answer2 = (isset($bonus['answer2']) and $bonus['answer2'] != '') ? '; ' . $bonus['answer2'] : '';
			$answer3 = (isset($bonus['answer3']) and $bonus['answer3'] != '') ? '; ' . $bonus['answer3'] : '';
			$answer4 = (isset($bonus['answer4']) and $bonus['answer4'] != '') ? '; ' . $bonus['answer4'] : '';
			
			$answer = $answer1 . $answer2 . $answer3 . $answer4;
			array_push($bonuses, array(0 => $bonus['id'], 1 => $subject, 2 => $answer, 3 => $duplicate, 4 => $approved, 5 => $promoted, 6 => ''));
		}
		
		//Start Boilerplate ?>
		<table class="display" id="bonuses">
			<thead>
				<tr>
					<th>ID</th>
					<th>Subject</th>
					<th>Answers</th>
					<th>Duplicate</th>
					<th>Approved</th>
					<th>Promoted</th>
					<th>Messages</th>
				</tr>
			</thead>
			<tbody>
		<? //End Boilerplate
		
			//Print bonuses, color coding by approval/promotion
			usort($bonuses, 'sortBonuses');
			foreach ($bonuses as $bonus){
				if ($bonus[5] == 'Yes'){
					echo '<tr class="greenRow">';
				}
				
				else if ($bonus[4] == 'Yes'){
					echo '<tr class="blueRow">';
				}
				
				else{
					echo '<tr class="redRow">';
				}
				
				foreach ($bonus as $key => $parameter){
					if ($key == 0){
						echo '<td><a onclick="modal_edit_bonus(' . $parameter . ')">' . $parameter . '</a></td>';
					}
					
					else if ($key == 3 or $key == 4 or $key == 5){
						echo '<td><a onclick="modal_mark_bonus(' . $bonus[0] . ')">' . $parameter . '</a></td>';
					}
					
					else if ($key == 6){
						echo '<td><a onclick="modal_messages_bonus(' . $bonus[0] . ')">Show</a>/<a onclick="modal_send_bonus(' . $bonus[0] . ')">Add</a></td>';
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
			fancy_bonuses('bonuses');//Make the table pretty and searchable
		</script>
		<? //End Boilerplate
		
	}
}

else{
	echo $conf['unauthorized'];
}

?>