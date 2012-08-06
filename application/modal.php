<?php 

require_once('conf.php');
require_once('query.php'); 

/* Modal Handler */

if(isset($_SESSION['username'])){
	$type = $_POST['type'];
	
	switch($type){
		case 'set'://Edit set information
			$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
			$userID = $userSelect[0]['id'];//Get current user's ID
			
			$roleSelect = selectFrom('permissions', array('role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
			$role = $roleSelect[0]['role'];//Get role of current user
			
			if ($role != 'd' and $role != 'a'){
				echo $conf['noperms'];
			}
			
			else{
				$parameters = array('title', 'target', 'info');//Get Tournament Data
				$setsSelect = selectFrom('psets', $parameters, array('id'), array("'" . $_SESSION['tournament'] . "'"));
				
				$name = $setsSelect[0]['title'];
				$target = $setsSelect[0]['target'];
				$info = $setsSelect[0]['info'];
				$subjects = '';
				
				if ($target == "0000-00-00 00:00:00"){
					$target = '';
				}
				
				$parameters = array('subject');//Get Tournament Subjects
				$subjectSelect = selectFrom('psets_allocations', $parameters, array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));
				
				foreach ($subjectSelect as $entry){
					$subjects = $subjects . $entry['subject'] . "\n";
				}
				
				?>
				<h3>Update Information</h3>
				<p><div id="updateform" style="text-align: center;">
					<form id="update" class="postform">
						<label>Tournament or Set Name: <input type="text" id="upd_name" name="upd_name" value="<? echo $name; ?>"></label><br><br>
						<label>Tournament Target Date: <input type="text" id="upd_date" name="upd_date" value="<? echo $target; ?>"></label><br><br>
						<label><span class="halfleft">Tournament Information:&nbsp;&nbsp;</span><span class="halfright">Subject Selection:</span><br>
						<textarea type="text" id="upd_info" name="upd_info"><? echo $info; ?></textarea></label>&nbsp;&nbsp;
						<textarea type="text" id="upd_sele" name="upd_sele"><? echo $subjects; ?></textarea></label><br><br>
					</form><button type="button" onclick="submit_update(document.getElementById('update')); return false;">Update</button> or <button type="button" onclick="submit_delete_set(); return false;">Delete</button>
				</div></p>
				<?
			}
			
			break;
		case 'member':
			$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
			$userID = $userSelect[0]['id'];//Get current user's ID
			
			$roleSelect = selectFrom('permissions', array('role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
			$role = $roleSelect[0]['role'];//Get role of current user
			
			if ($role != 'd'){
				echo $conf['noperms'];
			}
			
			else {
				$userID = $_POST['id']; //Set to user we're dealing with
				$roles = array('Editor', 'Manager', 'Administrator');
				
				$subjectSelect = selectFrom('psets_allocations', array('subject'), array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));//Get subjects
				$roleSelect = selectFrom('permissions', array('role', 'psets_allocations_id'), array('users_id', 'psets_id'), array("'" . sanitize($userID) . "'", "'" . $_SESSION['tournament'] . "'"));//Get role and focus
				$focusSelect = selectFrom('psets_allocations', array('subject'), array('id'), array("'" . $roleSelect[0]['psets_allocations_id'] . "'"));//Get focus 
				
				foreach ($roleSelect as $key => $parameter){
					switch ($parameter['role']){
						case 'd': $roleSelect[$key]['role'] = 'Director'; $roles = array('Director'); break;
						case 'a': $roleSelect[$key]['role'] = 'Administrator'; break;
						case 'm': $roleSelect[$key]['role'] = 'Manager'; break;
						case 'e': $roleSelect[$key]['role'] = 'Editor'; break;
					}
				}
				
				array_push($subjectSelect, array('subject' => 'None')); //Add None for the default option
				$userRole = $roleSelect[0]['role']; //For pre-selected option
				$userFocus = isset($focusSelect[0]['subject']) ? $focusSelect[0]['subject'] : 'None'; //For pre-selected option
				
				if ($userRole != 'Manager'){//Subject switching is only for manager perms
					$subjectSelect = array(array('subject' => 'None'));
				}

				?>
				<h3>Member Information</h3>
				<p><div id="memberform" style="text-align: center;">
					<form id="member" class="postform">
						<label>Access Role: <select id="mem_role" name="mem_role">
							<?
								foreach ($roles as $key => $role){
									if ($role == $userRole){
										echo '<option selected="selected">' . $role . '</option>';
									}
									
									else{
										echo '<option>' . $role . '</option>';
									}
								}
							?>
						</select></label><br><br>
						<label>Subject Focus: <select id="mem_focus" name="mem_focus">
							<?
								foreach ($subjectSelect as $key => $entry){
									if ($entry['subject'] == $userFocus){
											echo '<option selected="selected">' . $entry['subject'] . '</option>';
									}
									
									else{
										echo '<option>' . $entry['subject'] . '</option>';
									}
								}
							?>
						</select></label><br><br>
						<input type="hidden" id="mem_id" name="mem_id" value="<? echo $userID ?>">
					</form><button type="button" onclick="submit_member(document.getElementById('member')); return false;">Update</button> or <button type="button" onclick="submit_delete_member('<? echo $userID ?>'); return false;">Delete</button>
				</div></p>
				<?
			}
			
			break;
	}
}

else{
	echo $conf['unauthorized2'];
}

?>