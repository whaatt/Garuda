<?php 

require_once('conf.php');
require_once('query.php'); 

/* Members Handler */

function sortMembers($a, $b) { //Used to sort by sub-array value
   return strcmp($a[0], $b[0]);
}

if(isset($_SESSION['username'])){
	$submit = $_POST['submit']; //Form Submit Boolean (2 or 1 or 0)

	if ($submit == '2'){ //Delete Submission
		$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
		$userID = $userSelect[0]['id'];//Get current user's ID
	
		$roleSelect = selectFrom('permissions', array('role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
		$role = $roleSelect[0]['role'];
	
		if ($role == 'd' or $role == 'a'){
			$deleteID = $_POST['id'];
			
			if ($deleteID != $userID){
				?>
				<div id="delete" class="message error-message">
					<p><strong>Are you sure you want to delete this member? <a onclick="cont_delete('member', <? echo $deleteID; ?>);"><small><small>Yes</small></small></a> or <a onclick="cont_remove($(this).closest('#delete'), 1);"><big><big>No</big></big></a>.</strong></p>
				</div>
				<?
			}
			
			else{
				?>
				<div id="delete" class="message error-message" onclick="cont_remove(this, 1);">
					<p><strong>You cannot delete your own membership in a particular tournament. (Click to hide.)</strong></p>
				</div>
				<?
			}
		}
		
		else{
			echo $conf['noperms'];
		}
	}
	
	else if ($submit == '1'){ //Update Form Submission
		$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
		$userID = $userSelect[0]['id'];//Get current user's ID
	
		$roleSelect = selectFrom('permissions', array('role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
		$role = $roleSelect[0]['role'];
		
		if ($role == 'd'){
			//Get POST stuff 
			$role = $_POST['mem_role'];//This role is a full-name type, so later we use strtolower() on it
			$focus = $_POST['mem_focus'];
			$userID = $_POST['mem_id'];
			
			if (isset($focus) and !in_array('None', $focus)){
				$focusIDs = '';//To be used later
				$locations = array('psets_id', 'subject');
				$items = array("'" . $_SESSION['tournament'] . "'", "'" . $focus . "'");
				
				$entrySelect = selectFrom('psets_allocations', array('id'), $locations, $items);//Get ID of allocation
				$entry = $entrySelect[0]['id'];
				
				foreach($focus as $topic){
					$subjectSelect = selectFrom('psets_allocations', array('id'), array('subject', 'psets_id'), array("'" . sanitize($topic) . "'", "'" . $_SESSION['tournament'] . "'"));//Get subject
					$focusIDs = $focusIDs . ',' . $subjectSelect[0]['id'];
				}
				
				$focusIDs = ltrim($focusIDs, ',');
				
				if ($role != 'Manager'){
					$focusIDs = 'NULL';
				}
				
				else{
					$focusIDs = "'" . sanitize($focusIDs) . "'";
				}
				
				$columns = array('role', 'psets_allocations_id');
				$values = array("'" . sanitize(strtolower($role)) . "'", $focusIDs);
				updateIn('permissions', $columns, $values, array('psets_id', 'users_id'), array("'" . $_SESSION['tournament'] . "'", "'" . $userID . "'"));//Update member in database
			}
			
			else{//Focus NULL in DB
				$columns = array('role', 'psets_allocations_id');
				$values = array("'" . sanitize(strtolower($role)) . "'", "NULL");
				updateIn('permissions', $columns, $values, array('psets_id', 'users_id'), array("'" . $_SESSION['tournament'] . "'", "'" . $userID . "'"));//Update member in database
			}
			
			?>
			<div class="message thank-message" onclick="go_members()">
				<p><strong>You have successfully updated the member information. (Click to refresh.)</strong></p>
			</div>
			<?
		}
		
		else{
			echo $conf['noperms'];
		}
	}
	
	else{
		echo $conf['members'];
		$members = array();
		$multiple = array();
		
		$columns = array('users_id', 'psets_allocations_id', 'role');
		$permsSelect = selectFrom('permissions', $columns, array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));

		foreach ($permsSelect as $perm){ //Iterate through members with some permission stored for the tournament, building up $members
			$columns = array('name', 'username');
			$userSelect = selectFrom('users', $columns, array('id'), array("'" . sanitize($perm['users_id']) . "'"));//Get tournaments.
			
			array_push($members, array(0 => $userSelect[0]['name'], 1 => $userSelect[0]['username'], 2 => '', 3 => '', 4 => 'Update User', 5 => $perm['users_id'], 6 => ''));
			
			switch ($perm['role']){//Access Level
				case 'd': $members[count($members)-1][2] = 'Director'; break;
				case 'a': $members[count($members)-1][2] = 'Administrator'; break;
				case 'e': $members[count($members)-1][2] = 'Editor'; break;
				case 'm': $members[count($members)-1][2] = 'Manager'; break;
			}
			
			if ($perm['psets_allocations_id'] != ''){
				$topics = explode(',', $perm['psets_allocations_id']);
				
				if (count($topics) > 1){
					$members[count($members)-1][3] = 'Multiple, Hover to See';
					$multiple[strval(count($members)-1)] = '';
					
					foreach($topics as $topic){
						$subjectSelect = selectFrom('psets_allocations', array('subject'), array('id'), array("'" . sanitize($topic) . "'"));//Get subject
						$multiple[strval(count($members)-1)] = $multiple[strval(count($members)-1)] . $subjectSelect[0]['subject'] . ', ';
					}
					
					$multiple[strval(count($members)-1)] = rtrim($multiple[strval(count($members)-1)], ', ');
					$members[count($members)-1][6] = strval(count($members)-1);
				}
				
				else{
					$subjectSelect = selectFrom('psets_allocations', array('subject'), array('id'), array("'" . sanitize($topics[0]) . "'"));//Get subject
					$members[count($members)-1][3] = $subjectSelect[0]['subject'];
				}
			}
			
			else{
				$members[count($members)-1][3] = 'None Set';
			}
		}
		
		//Start Boilerplate ?>
		<table class="display" id="members">
			<thead>
				<tr>
					<th>Member Name</th>
					<th>Username</th>
					<th>Access Level</th>
					<th>Subject Focus</th>
					<th>Modify Membership</th>
				</tr>
			</thead>
			<tbody>
		<? //End Boilerplate
		
			//Print members, color coding by roles
			usort($members, 'sortMembers');
			foreach ($members as $member){
				if ($member[2] == 'Manager'){
					echo '<tr class="blueRow">';
				}
				
				else if ($member[2] == 'Director' or $member[2] == 'Administrator'){
					echo '<tr class="redRow">';
				}
				
				else{
					echo '<tr class="greenRow">';
				}
				
				foreach ($member as $key => $parameter){
					if ($key == 4){
						echo '<td><a onclick="modal_member(' . $member[5] . '); return false;">' . $parameter . '</a></td>';
					}
					
					else if ($key == 3){
						echo '<td><span id="' . $member[6] . '">' . $parameter . '</span></td>';
					}
					
					else if ($key != 5 and $key != 6){
						echo '<td>' . $parameter . '</td>';
					}
				}
				
				echo '</tr>';
			}
			
		//More Boilerplate ?>
			</tbody>
		</table><p></p>
		<script type="text/javascript">
			fancy_members('members');//Make the table pretty and searchable
			
			<? foreach ($multiple as $key => $tip){ ?>
				$('#<? echo $key ?>').qtip({content: '<? echo $tip; ?>'});
			<? } ?>
		</script>
		<? //End Boilerplate
	}
}

else{
	echo $conf['unauthorized'];
}

?>