<?php 

require_once('conf.php');
require_once('query.php'); 

/* Tossups Handler */

function sortTossups($a, $b){ //Used to sort by sub-array value
	return (int) $a[0] - (int) $b[0];
}

if(isset($_SESSION['username'])){
	$submit = $_POST['submit']; //Form Submit Boolean

	if ($submit == '2'){ //Delete Tossup Submission
		$deleteID = $_POST['id'];
		
		$tossupSelect = selectFrom('tossups', array('psets_allocations_id', 'creator_users_id'), array('id'), array("'" . $deleteID . "'"));//Get tossup
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
		
		else if ($userRole == 'm' and strlen($userFocus) > 0 and in_array($tossup['psets_allocations_id'], explode(',', $userFocus))){
			$access = true;
		}

		else if ($userRole == 'a' or $userRole == 'd'){
			$access = true;
		}
		
		if ($access == true){
			?>
			<div id="delete" class="message error-message">
				<p><strong>Are you sure you want to delete this tossup? <a onclick="cont_delete('tossup', <? echo $deleteID; ?>);"><small><small>Yes</small></small></a> or <a onclick="cont_remove($(this).closest('#delete'), 1);"><big><big>No</big></big></a>.</strong></p>
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
		
		foreach($_POST as &$p){ //Kill Whitespace
			$p = trim(str_replace('&nbsp;', ' ', $p));
		}
		
		//Get POST stuff 
		$question = $_POST['crt_body'];
		$answer = $_POST['crt_ans'];
		$subject = $_POST['crt_subj'];
		
		if (strlen($question) < 5000 and strlen($answer) < 500){
			if ($subject != 'None'){
				$idSelect = selectFrom('psets_allocations', array('id'), array('subject', 'psets_id'), array("'" . sanitize($subject) . "'", "'" . $_SESSION['tournament'] . "'"));
				$subjectID = $idSelect[0]['id'];//Get the subject's ID
				
				$columns = array('psets_id', 'creator_users_id', 'psets_allocations_id', 'tossup', 'answer', 'approved', 'promoted', 'created');
				$values = array("'" . $_SESSION['tournament'] . "'", "'" . $userID . "'", "'" . $subjectID . "'", "'" . sanitize(trim($question), false, '<b><i>') . "'", "'" . sanitize(trim($answer), false, '<b><i><u>') . "'", "'0'", "'0'", "NOW()");
				insertInto('tossups', $columns, $values);//Add tossup to database
			}
			
			else{//Subject NULL in DB
				$columns = array('psets_id', 'creator_users_id', 'psets_allocations_id', 'tossup', 'answer', 'approved', 'promoted', 'created');
				$values = array("'" . $_SESSION['tournament'] . "'", "'" . $userID . "'", "NULL", "'" . sanitize(trim($question), false, '<b><i>') . "'", "'" . sanitize(trim($answer), false, '<b><i><u>') . "'", "'0'", "'0'", "NOW()");
				insertInto('tossups', $columns, $values);//Add tossup to database
			}
		
			?>
			<div class="message thank-message" onclick="go_tossups()">
				<p><strong>You have successfully created a tossup. (Click to refresh.)</strong></p>
			</div>
			<?
		}
		
		else{
			?>
			<div class="message error-message" onclick="cont_remove(this, 1);">
				<p><strong>Either your question or answer was way too long. (Click to hide.)</strong></p>
			</div>
			<?
		}
	}
	
	else if ($submit == '3'){ //Edit Form Submission
		$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
		$userID = $userSelect[0]['id'];//Get current user's ID
		
		foreach($_POST as &$p){ //Kill Whitespace
			$p = trim(str_replace('&nbsp;', ' ', $p));
		}
		
		//Get POST stuff 
		$id = $_POST['edt_id'];
		$question = $_POST['edt_body'];
		$answer = $_POST['edt_ans'];
		$subject = $_POST['edt_subj'];
		
		$tossupSelect = selectFrom('tossups', array('psets_allocations_id', 'creator_users_id'), array('id'), array("'" . $id . "'"));//Get tossups
		$tossup = $tossupSelect[0];
		
		$permSelect = selectFrom('permissions', array('role', 'psets_allocations_id'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
		$userRole = $permSelect[0]['role'];//Get current user's role
		$userFocus = isset($permSelect[0]['psets_allocations_id']) ? $permSelect[0]['psets_allocations_id'] : 'None';
		
		$access = false;
		
		if ($userID == $tossup['creator_users_id']){
			$access = true;
		}
		
		else if ($userRole == 'm' and strlen($userFocus) > 0 and in_array($tossup['psets_allocations_id'], explode(',', $userFocus))){
			$access = true;
		}

		else if ($userRole == 'a' or $userRole == 'd'){
			$access = true;
		}
		
		if ($access == true){
			if (strlen($question) < 5000 and strlen($answer) < 500){
				if ($subject != 'None'){
					$idSelect = selectFrom('psets_allocations', array('id'), array('subject', 'psets_id'), array("'" . sanitize($subject) . "'", "'" . $_SESSION['tournament'] . "'"));
					$subjectID = $idSelect[0]['id'];//Get the subject's ID
					
					$columns = array('psets_allocations_id', 'tossup', 'answer', 'editor_users_id');
					$values = array("'" . $subjectID . "'", "'" . sanitize(trim($question), false, '<b><i>') . "'", "'" . sanitize(trim($answer), false, '<b><i><u>') . "'", "'" . $userID . "'");
					updateIn('tossups', $columns, $values, array('id'), array("'" . $id . "'"));//Update tossup in database
				}
				
				else{//Subject NULL in DB
					$columns = array('psets_allocations_id', 'tossup', 'answer', 'editor_users_id');
					$values = array("NULL", "'" . sanitize(trim($question), false, '<b><i>') . "'", "'" . sanitize(trim($answer), false, '<b><i><u>') . "'", "'" . $userID . "'");
					updateIn('tossups', $columns, $values, array('id'), array("'" . $id . "'"));//Update tossup in database
				}
			
				?>
				<div class="message thank-message" onclick="go_tossups()">
					<p><strong>You have successfully edited this tossup. (Click to refresh.)</strong></p>
				</div>
				<?
			}
			
			else{
				?>
				<div class="message error-message" onclick="cont_remove(this, 1);">
					<p><strong>Either your question or answer was way too long. (Click to hide.)</strong></p>
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
		$id = $_POST['mrt_id'];
		$difficulty = $_POST['mrt_dif'];
		
		if ($difficulty == 'Easy') { $difficulty = 'e'; }
		else if ($difficulty == 'Medium') { $difficulty = 'm'; }
		else if ($difficulty == 'Hard') { $difficulty = 'h'; }
		else { $difficulty = 'm'; } //Protect against POST injection, but I'm sort of inconsistent
		
		$approved = isset($_POST['mrt_app']) and $_POST['mrt_app'] == '' ? '0' : '1';
		$promoted = isset($_POST['mrt_pro']) and $_POST['mrt_pro'] == '' ? '0' : '1';
		$duplicate = ltrim(preg_replace('/\D/', '', $_POST['mrt_dup']),'0');//Format number appropriately, remove leading zeros
		
		$tossupSelect = selectFrom('tossups', array('psets_allocations_id', 'creator_users_id'), array('id'), array("'" . $id . "'"));//Get tossup
		$tossup = $tossupSelect[0];
		$tossup['psets_allocations_id'] = isset($tossupSelect[0]['psets_allocations_id']) ? $tossupSelect[0]['psets_allocations_id'] : '';
			
		$permSelect = selectFrom('permissions', array('role', 'psets_allocations_id'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
		$userRole = $permSelect[0]['role'];//Get current user's role
		$userFocus = isset($permSelect[0]['psets_allocations_id']) ? $permSelect[0]['psets_allocations_id'] : '';
		
		if ($duplicate != ''){
			$tossupSelect = selectFrom('tossups', array('id', 'duplicate_tossups_id'), array('id'), array("'" . $duplicate . "'"));
			if (count($tossupSelect) > 0){
				if (isset($tossupSelect[0]['duplicate_tossups_id'])){
					$duplicate = $tossupSelect[0]['duplicate_tossups_id'];//Bubble up to master tossup
				}
				
				else{
					$duplicate = $tossupSelect[0]['id'];//It is the master
				}
			}
			
			else{
				$duplicate = '';//Invalid duplicate
			}
		}
		
		if ($userRole == 'd' or $userRole == 'a' or ($userRole == 'm' and strlen($userFocus) > 0 and in_array($tossup['psets_allocations_id'], explode(',', $userFocus)))){
			if ($duplicate != ''){
				$columns = array('approved', 'promoted', 'duplicate_tossups_id', 'difficulty');
				$values = array("'" . $approved . "'", "'" . ($userRole == 'm' ? '0' : $promoted) . "'", "'" . $duplicate . "'", "'" . $difficulty . "'");
				updateIn('tossups', $columns, $values, array('id'), array("'" . $id . "'"));//Update tossup markdown in database
			
				$columns = array('duplicate_tossups_id');
				$values = array("'" . $duplicate . "'");
			
				$where = array('duplicate_tossups_id');
				$equals = array("'" . $id . "'");
			
				updateIn('tossups', $columns, $values, $where, $equals);//Bubble down, as well
			}
			
			else{
				$columns = array('approved', 'promoted', 'duplicate_tossups_id', 'difficulty');
				$values = array("'" . $approved . "'", "'" . $promoted . "'", "NULL", "'" . $difficulty . "'");
				updateIn('tossups', $columns, $values, array('id'), array("'" . $id . "'"));//Update tossup markdown in database
			}
			
			?>
				<div class="message thank-message" onclick="go_tossups()">
					<p><strong>You have successfully marked this tossup. (Click to refresh.)</strong></p>
				</div>
			<?
		}
		
		else{
			echo $conf['noperms'];
		}
	}
	
	else if ($submit == '5'){ //Send Form Submission
		$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
		$userID = $userSelect[0]['id'];//Get current user's ID
		
		foreach($_POST as &$p){ //Kill Whitespace
			$p = trim(str_replace('&nbsp;', ' ', $p));
		}
		
		//Get POST stuff
		$id = $_POST['sdt_id'];
		$message = $_POST['sdt_msg'];
		
		if (strlen($message) < 1000){
			$columns = array('tossup_or_bonus', 'tub_id', 'users_id', 'message');
			$values = array("'0'", "'" . $id . "'", "'" . $userID. "'", "'" . sanitize(trim($message), false, '<b><i><u>') . "'");
			insertInto('messages', $columns, $values);//Add message to database
			
			?>
			<div class="message thank-message" onclick="go_tossups()">
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
		
		$messageSelect = selectFrom('messages', array('tub_id', 'users_id'), array('id'), array("'" . $deleteID . "'"));//Get tossups
		$tossupID = $messageSelect[0]['tub_id'];
		
		$tossupSelect = selectFrom('tossups', array('psets_allocations_id', 'tossup', 'answer', 'creator_users_id'), array('id'), array("'" . $tossupID . "'"));//Get tossup
		$tossup = $tossupSelect[0];
		$tossup['psets_allocations_id'] = isset($tossup['psets_allocations_id']) ? $tossup['psets_allocations_id'] : '';
		
		$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
		$userID = $userSelect[0]['id'];//Get current user's ID
		
		$permSelect = selectFrom('permissions', array('psets_allocations_id', 'role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
		$userRole = $permSelect[0]['role'];//Get current user's role
		$userFocus = isset($permSelect[0]['psets_allocations_id']) ? $permSelect[0]['psets_allocations_id'] : '';
		
		$access = false;
		
		if ($userID == $tossup['creator_users_id'] or $userID == $messageSelect[0]['users_id']){
			$access = true;
		}
		
		else if ($userRole == 'm' and strlen($userFocus) > 0 and in_array($tossup['psets_allocations_id'], explode(',', $userFocus))){
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
		echo $conf['tossups'];
		echo '<p>You may add messages to a particular tossup, whether or not you have edit permissions for it. If you have manager or administrator permissions, you may additionally mark a tossup as a duplicate of another. <a onclick="modal_create_tossup()">Click here</a> to create a tossup, but please check for duplicate entries here before you do so.</p>';
		
		$tossups = array();
		
		$columns = array('id', 'psets_allocations_id', 'answer', 'duplicate_tossups_id', 'approved', 'promoted', 'creator_users_id', 'difficulty');
		$tossupsSelect = selectFrom('tossups', $columns, array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));

		foreach ($tossupsSelect as $tossup){ //Iterate through $tossupsSelect, building up $tossups
			$items = array('subject'); $columns = array('id');
			$values = array("'" . sanitize($tossup['psets_allocations_id']) . "'");
			
			$subjectSelect = selectFrom('psets_allocations', $items, $columns, $values);//Get subject.
			$subject = isset($subjectSelect[0]['subject']) ? $subjectSelect[0]['subject'] : 'None Set';
			
			$items = array('name'); $columns = array('id');
			$values = array("'" . sanitize($tossup['creator_users_id']) . "'");
			
			$userSelect = selectFrom('users', $items, $columns, $values);//Get user name.
			$name = $userSelect[0]['name'];
			
			$items = array('tossup_or_bonus', 'tub_id'); $values = array("'" . '0' . "'", "'" . sanitize($tossup['id']) . "'");
			$msgCount = strval(getNumOf('messages', $items, $values));//Get number of messages
			
			$duplicate = !empty($tossup['duplicate_tossups_id']) ? $tossup['duplicate_tossups_id'] : 'No';
			$approved = $tossup['approved'] == '1' ? 'Yes' : 'No';
			$promoted = $tossup['promoted'] == '1' ? 'Yes' : 'No';
			
			if ($tossup['difficulty'] == 'e') { $difficulty = 'Easy'; }
			else if ($tossup['difficulty'] == 'm') { $difficulty = 'Medium'; }
			else if ($tossup['difficulty'] == 'h') { $difficulty = 'Hard'; }
			
			array_push($tossups, array(0 => $tossup['id'], 1 => $name, 2 => $subject, 3 => $tossup['answer'], 4 => $difficulty, 5 => $duplicate, 6 => $approved, 7 => $promoted, 8 => '', 9 => $msgCount));
		}
		
		//Start Boilerplate ?>
		<table class="display" id="tossups">
			<thead>
				<tr>
					<th>ID</th>
					<th>Creator</th>
					<th>Subject</th>
					<th>Answer</th>
					<th>Difficulty</th>
					<th>Duplicate</th>
					<th>Approved</th>
					<th>Promoted</th>
					<th>Messages</th>
				</tr>
			</thead>
			<tbody>
		<? //End Boilerplate
		
			//Print tossups, color coding by approval/promotion
			uasort($tossups, 'sortTossups');
			foreach ($tossups as $tossup){
				if ($tossup[7] == 'Yes'){
					echo '<tr class="greenRow">';
				}
				
				else if ($tossup[6] == 'Yes'){
					echo '<tr class="blueRow">';
				}
				
				else{
					echo '<tr class="redRow">';
				}
				
				foreach ($tossup as $key => $parameter){
					if ($key == 0){
						echo '<td><a onclick="modal_edit_tossup(' . $parameter . ')">' . $parameter . '</a></td>';
					}
					
					else if ($key == 4 or $key == 5 or $key == 6 or $key == 7){
						echo '<td><a onclick="modal_mark_tossup(' . $tossup[0] . ')">' . $parameter . '</a></td>';
					}
					
					else if ($key == 8){
						echo '<td><a onclick="modal_messages_tossup(' . $tossup[0] . ')">' . $tossup[9] . '</a>/<a onclick="modal_send_tossup(' . $tossup[0] . ')">Add</a></td>';
					}
					
					else if ($key != 9){
						echo '<td>' . $parameter . '</td>';
					}
				}
				
				echo '</tr>';
			}
			
		//More Boilerplate ?>
			</tbody>
		</table><p></p>
		<script type="text/javascript">
			fancy_tossups('tossups');//Make the table pretty and searchable
		</script>
		<? //End Boilerplate
		
	}
}

else{
	echo $conf['unauthorized'];
}

?>