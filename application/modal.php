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
		case 'create_tossup':
			$subjectSelect = selectFrom('psets_allocations', array('subject'), array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));//Get subjects
			array_push($subjectSelect, array('subject' => 'None')); //Add None for the default option	

			?>
				<h3>Create Tossup</h3>
				<p><div id="createtossupform" style="text-align: center;">
					<form id="createtossup" class="postform">
						<p style="text-align: left;">Use the top box to type your tossup's body, and the bottom box to type your tossup's answer. Thanks!</p><br>
						<label>Categorize By Subject: <select id="crt_subj" name="crt_subj">
							<?
								foreach ($subjectSelect as $key => $entry){
									if ($entry['subject'] == 'None'){
											echo '<option selected="selected">' . $entry['subject'] . '</option>';
									}
									
									else{
										echo '<option>' . $entry['subject'] . '</option>';
									}
								}
							?>
						</select></label><br><br>
						<label><textarea type="text" id="crt_body" name="crt_body" style="height: 100px;"></textarea></label><br>
						<label><textarea type="text" id="crt_ans" name="crt_ans" style="height: 100px;"></textarea></label><br>
					</form><button type="button" onclick="submit_create_tossup(document.getElementById('createtossup')); return false;">Create</button>
				</div></p>
			<?
			
			break;
		case 'edit_tossup':
			$tossupID = $_POST['id'];
			
			$tossupSelect = selectFrom('tossups', array('psets_allocations_id', 'tossup', 'answer', 'creator_users_id'), array('id'), array("'" . $_POST['id'] . "'"));//Get tossups
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
			
			else if ($userRole == 'm' and $userFocus == $tossup['psets_allocations_id'] and $userFocus != ''){
				$access = true;
			}

			else if ($userRole == 'a' or $userRole == 'd'){
				$access = true;
			}
			
			if ($access == true){
				$subjectSelect = selectFrom('psets_allocations', array('subject', 'id'), array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));//Get subjects
				array_push($subjectSelect, array('subject' => 'None')); //Add None for the default option
				
				?>
					<h3>Edit Tossup</h3>
					<p><div id="edittossupform" style="text-align: center;">
						<form id="edittossup" class="postform">
							<p style="text-align: left;">Use the top box to type your tossup's body, and the bottom box to type your tossup's answer. Thanks!</p><br>
							<label>Categorize By Subject: <select id="edt_subj" name="edt_subj">
								<?
									foreach ($subjectSelect as $key => $entry){
										if ($entry['id'] == $tossup['psets_allocations_id']){
											echo '<option selected="selected">' . $entry['subject'] . '</option>';
										}
										
										else{
											echo '<option>' . $entry['subject'] . '</option>';
										}
									}
								?>
							</select></label><br><br>
							<label><textarea type="text" id="edt_body" name="edt_body" style="height: 100px;"><? echo $tossup['tossup']; ?></textarea></label><br>
							<label><textarea type="text" id="edt_ans" name="edt_ans" style="height: 100px;"><? echo $tossup['answer']; ?></textarea></label><br>
							<input type="hidden" id="edt_id" name="edt_id" value="<? echo $tossupID; ?>">
						</form><button type="button" onclick="submit_edit_tossup(document.getElementById('edittossup')); return false;">Update</button> or 
						<button type="button" onclick="submit_delete_tossup(<? echo $tossupID; ?>); return false;">Delete</button>
					</div></p>
				<?
			}
			
			else{
				echo $conf['noperms'];
			}
			
			break;
		case 'mark_tossup':
			$tossupID = $_POST['id'];
			
			$tossupSelect = selectFrom('tossups', array('psets_allocations_id', 'duplicate_tossups_id', 'approved', 'promoted'), array('id'), array("'" . $tossupID . "'"));//Get tossups
			$tossup = $tossupSelect[0];
			$tossup['psets_allocations_id'] = isset($tossup['psets_allocations_id']) ? $tossup['psets_allocations_id'] : '';
			
			$approved = $tossup['approved'];
			$promoted = $tossup['promoted'];
			$duplicate = isset($tossup['duplicate_tossups_id']) ? '"' . $tossup['duplicate_tossups_id'] . '"' : '""';
			
			$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
			$userID = $userSelect[0]['id'];//Get current user's ID
			
			$permSelect = selectFrom('permissions', array('role', 'psets_allocations_id'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
			$userRole = $permSelect[0]['role'];//Get current user's role
			$userSubject = isset($permSelect[0]['psets_allocations_id']) ? $permSelect[0]['psets_allocations_id'] : '';
			
			if ($userRole == 'd' or $userRole == 'a' or ($userRole == 'm' and $userSubject == $tossup['psets_allocations_id'] and $userRole != '')){
				?>
					<h3>Mark Tossup</h3>
					<p><div id="marktossupform" style="text-align: center;">
						<form id="marktossup" class="postform">
							<p style="text-align: left;">Both administrators and managers may approve a tossup, but administrators may also promote a tossup for packet use. If you mark a duplicate that is already the duplicate of another tossup, the identifier will be inherited from the master version, if that makes sense.</p><br>
							<label>Tossup Approved: <input type="checkbox" id="mrt_app" name="mrt_app" <? if ($approved == 1){echo 'checked="yes"';} ?>></label><br>
							<label>Tossup Promoted: <input type="checkbox" id="mrt_pro" name="mrt_pro" <? if ($promoted == 1){echo 'checked="yes"';} ?> <? if ($userRole == 'm'){echo 'disabled="disabled"';} ?>></label><br><br>
							<label>Duplicate ID: <input type="text" id="mrt_dup" name="mrt_dup" style="width: 25px;" value=<? echo $duplicate; ?>></label><br><br>
							<input type="hidden" id="mrt_id" name="mrt_id" value="<? echo $tossupID; ?>">
						</form><button type="button" onclick="submit_mark_tossup(document.getElementById('marktossup')); return false;">Mark</button>
					</div></p>
				<?
			}
			
			else{
				echo $conf['noperms'];
			}
			
			break;	
		case 'send_tossup':
			$tossupID = $_POST['id'];
			
			?>
				<h3>Add Message</h3>
				<p><div id="sendtossupform" style="text-align: center;">
					<form id="sendtossup" class="postform">
						<p style="text-align: left;">You may use the box below to add a message to this tossup. Please keep within one thousand characters.</p><br>
						<label><textarea type="text" id="sdt_msg" name="sdt_msg" style="height: 100px;"></textarea></label><br>
						<input type="hidden" id="sdt_id" name="sdt_id" value="<? echo $tossupID; ?>">
					</form><button type="button" onclick="submit_send_tossup(document.getElementById('sendtossup')); return false;">Add</button>
				</div></p>
			<?
			
			break;
		case 'messages_tossup':
			$tossupID = $_POST['id'];
			echo '<h3>View Messages</h3>'
		
			?>
				<p style="text-align: left;">
					Below are all of the messages for this entry listed chronologically. With editing rights, you may delete any.
				</p><br>
			<?
			
			$messagesSelect = selectFrom('messages', array('users_id', 'id', 'message'), array('tossup_or_bonus', 'tub_id'), array("'0'", "'" . $tossupID . "'"));
			//This above line should select all of the appropriate messages
			
			if (count($messagesSelect) > 0){
				echo '<span id="messages">';
				
				foreach ($messagesSelect as $key => $entry){
					$userSelect = selectFrom('users', array('name', 'username'), array('id'), array("'" . $entry['users_id']. "'"));
					
					$userName = $userSelect[0]['name'];
					$userUser = $userSelect[0]['username'];
					
					echo '<h3><a href="#">From: ' . $userName . ' - <span onclick="submit_delete_message(' . $entry['id'] . ')">Delete Message</span></a></h3>';
					echo '<div>' . $entry['message'] . '</div>';
				}
				
				echo '</span>';
			}
			
			else{
				echo '<p style="text-align: left;">No messages found! To add a message to this list, please click on the Add button in the Messages table column.</p>';
			}
			
			break;
	}
}

else{
	echo $conf['unauthorized2'];
}

?>