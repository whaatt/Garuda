<?php 

require_once('conf.php');
require_once('query.php'); 

/* Modal Handler */

function compareSubjects($a, $b) {
	return strcmp($a['subject'], $b['subject']);
}

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
				usort($subjectSelect, 'compareSubjects');
				
				foreach ($subjectSelect as $entry){
					$subjects = $subjects . $entry['subject'] . "\n";
				}
				
				$subjects = rtrim($subjects);
				
				?>
				<h3>Update Information</h3>
				<p><div id="updateform" style="text-align: center;">
					<form id="update" class="postform" onsubmit="submit_update(document.getElementById('update')); return false;">
						<label>Tournament or Set Name: <input type="text" id="upd_name" name="upd_name" value="<? echo $name; ?>"></label><br><br>
						<label>Tournament Target Date: <input type="text" id="upd_date" name="upd_date" value="<? echo $target; ?>"></label><br><br>
						<label>Tournament Info: <br><textarea type="text" id="upd_info" name="upd_info"><? echo $info; ?></textarea></label><br><br>
						<label>Subject Selection: <br><textarea type="text" id="upd_sele" name="upd_sele"><? echo $subjects; ?></textarea></label><br><br>
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
			
			if ($role != 'd' and $role != 'a'){//Directors and Administrators. Not sure why it wasn't like this before...
				echo $conf['noperms'];
			}
			
			else {
				$userID = $_POST['id']; //Set to user we're dealing with
				$roles = array('Editor', 'Manager', 'Administrator');
				
				$subjectSelect = selectFrom('psets_allocations', array('subject'), array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));//Get subjects
				$roleSelect = selectFrom('permissions', array('role', 'psets_allocations_id'), array('users_id', 'psets_id'), array("'" . sanitize($userID) . "'", "'" . $_SESSION['tournament'] . "'"));//Get role and focus
				$topics = explode(',', $roleSelect[0]['psets_allocations_id']); //Get array of foci
				
				if (count($topics) > 0){
					$userFocus = array();
					foreach ($topics as $topic){
						$focusSelect = selectFrom('psets_allocations', array('subject'), array('id'), array("'" . $topic . "'"));//Get focus 
						array_push($userFocus, $focusSelect[0]['subject']);
					}
				}
				
				else{
					$userFocus = array('None');
				}
				
				foreach ($roleSelect as $key => $parameter){
					switch ($parameter['role']){
						case 'd': $roleSelect[$key]['role'] = 'Director'; $roles = array('Director'); break;
						case 'a': $roleSelect[$key]['role'] = 'Administrator'; break;
						case 'm': $roleSelect[$key]['role'] = 'Manager'; break;
						case 'e': $roleSelect[$key]['role'] = 'Editor'; break;
					}
				}
				
				array_push($subjectSelect, array('subject' => 'None')); //Add None for the default option
				usort($subjectSelect, 'compareSubjects');
				$userRole = $roleSelect[0]['role']; //For pre-selected option
				
				if ($userRole != 'Manager'){//Subject switching is only for manager perms
					$subjectSelect = array(array('subject' => 'None'));
				}

				?>
				<h3>Member Information</h3>
				<p><div id="memberform" style="text-align: center;">
					<form id="member" class="postform" onsubmit="submit_member(document.getElementById('member')); return false;">
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
						<? if ($userRole == 'Manager'){ ?>
							<label><select id="mem_focus" name="mem_focus[]" multiple="multiple">
								<?
									foreach ($subjectSelect as $key => $entry){
										if (in_array($entry['subject'], $userFocus)){
												echo '<option selected="selected">' . $entry['subject'] . '</option>';
										}
										
										else{
											echo '<option>' . $entry['subject'] . '</option>';
										}
									}
								?>
							</select></label><br><br>
						<? } ?>
						<input type="hidden" id="mem_id" name="mem_id" value="<? echo $userID ?>">
					</form><button type="button" onclick="submit_member(document.getElementById('member')); return false;">Update</button> or <button type="button" onclick="submit_delete_member('<? echo $userID ?>'); return false;">Delete</button>
				</div></p>
				<?
			}
			
			break;
		case 'create_tossup':
			$subjectSelect = selectFrom('psets_allocations', array('subject'), array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));//Get subjects
			array_push($subjectSelect, array('subject' => 'None')); //Add None for the default option
			usort($subjectSelect, 'compareSubjects');

			?>
				<h3>Create Tossup</h3>
				<p><div id="createtossupform" style="text-align: center;">
					<form id="createtossup" class="postform" onsubmit="submit_create_tossup(document.getElementById('createtossup')); return false;">
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
			
			$tossupSelect = selectFrom('tossups', array('psets_allocations_id', 'tossup', 'answer', 'creator_users_id', 'created'), array('id'), array("'" . $_POST['id'] . "'"));//Get tossups
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
				$subjectSelect = selectFrom('psets_allocations', array('subject', 'id'), array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));//Get subjects
				array_push($subjectSelect, array('subject' => 'None')); //Add None for the default option
				usort($subjectSelect, 'compareSubjects');
				
				$userSelect = selectFrom('users', array('name', 'username'), array('id'), array("'" . $tossup['creator_users_id'] . "'"));
				$user = $userSelect[0];//Get user info
				
				?>
					<h3>Edit Tossup</h3>
					<p><div id="edittossupform" style="text-align: center;">
						<form id="edittossup" class="postform" onsubmit="submit_edit_tossup(document.getElementById('edittossup')); return false;">
							<p style="text-align: left;">Use the top box to type your tossup's body, and the bottom box to type your tossup's answer. Thanks!</p><br>
							<?
								echo '<b>Creator:</b> ' . $user['name'] . ' (' . $user['username'] . ')<br>';
								echo '<b>Created:</b> ' . $tossup['created'];
							?>
							<br><br><label>Categorize By Subject: <select id="edt_subj" name="edt_subj">
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
				$userSelect = selectFrom('users', array('name', 'username'), array('id'), array("'" . $tossup['creator_users_id'] . "'"));
				$user = $userSelect[0];//Get user info
				
				echo '<h3>View Tossup</h3><br>';
				echo '<b>Creator:</b> ' . $user['name'] . ' (' . $user['username'] . ')<br>';
				echo '<b>Created:</b> ' . $tossup['created'];
				
				if (isset($tossup['tossup']) and $tossup['tossup'] != ''){
					echo '<br><br>' . $tossup['tossup'] . '<br><br>';
					echo '<b>Answer: </b>' . $tossup['answer'];
				}
			}
			
			break;
		case 'mark_tossup':
			$tossupID = $_POST['id'];
			
			$tossupSelect = selectFrom('tossups', array('psets_allocations_id', 'duplicate_tossups_id', 'approved', 'promoted', 'difficulty'), array('id'), array("'" . $tossupID . "'"));//Get tossups
			$tossup = $tossupSelect[0];
			$tossup['psets_allocations_id'] = isset($tossup['psets_allocations_id']) ? $tossup['psets_allocations_id'] : '';
			
			$approved = $tossup['approved'];
			$promoted = $tossup['promoted'];
			$duplicate = isset($tossup['duplicate_tossups_id']) ? '"' . $tossup['duplicate_tossups_id'] . '"' : '""';
			
			$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
			$userID = $userSelect[0]['id'];//Get current user's ID
			
			$permSelect = selectFrom('permissions', array('role', 'psets_allocations_id'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
			$userRole = $permSelect[0]['role'];//Get current user's role
			$userFocus = isset($permSelect[0]['psets_allocations_id']) ? $permSelect[0]['psets_allocations_id'] : '';
			
			$difficulties = array('Easy', 'Medium', 'Hard');
			$difficulty = $tossup['difficulty'];
			
			if ($userRole == 'd' or $userRole == 'a' or ($userRole == 'm' and strlen($userFocus) > 0 and in_array($tossup['psets_allocations_id'], explode(',', $userFocus)))){
				?>
					<h3>Mark Tossup</h3>
					<p><div id="marktossupform" style="text-align: center;">
						<form id="marktossup" class="postform" onsubmit="submit_mark_tossup(document.getElementById('marktossup')); return false;">
							<p style="text-align: left;">Both administrators and managers may approve a tossup, but administrators may also promote a tossup for packet use. If you mark a duplicate that is already the duplicate of another tossup, the identifier will be inherited from the master version, if that makes sense.</p><br>
							<label>Difficulty: <select id="mrt_dif" name="mrt_dif">
								<?
									foreach ($difficulties as $level){
										if (substr(lcfirst($level), 0, 1) == $difficulty){
											echo '<option selected="selected">' . $level . '</option>';
										}
										
										else{
											echo '<option>' . $level . '</option>';
										}
									}
								?>
							</select></label><br><br>
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
					<form id="sendtossup" class="postform" onsubmit="submit_send_tossup(document.getElementById('sendtossup')); return false;">
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
					
					echo '<h3><a href="#">From: ' . $userName . ' - <span onclick="submit_delete_message_tossup(' . $entry['id'] . ')">Delete Message</span></a></h3>';
					echo '<div>' . $entry['message'] . '</div>';
				}
				
				echo '</span>';
			}
			
			else{
				echo '<p style="text-align: left;">No messages found! To add a message to this list, please click on the Add button in the Messages table column.</p>';
			}
			
			break;
		case 'create_bonus':
			$subjectSelect = selectFrom('psets_allocations', array('subject'), array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));//Get subjects
			array_push($subjectSelect, array('subject' => 'None')); //Add None for the default option
			usort($subjectSelect, 'compareSubjects');

			?>
				<h3>Create Bonus</h3>
				<p><div id="createbonusform" style="text-align: center;">
					<form id="createbonus" class="postform" onsubmit="submit_create_bonus(document.getElementById('createbonus')); return false;">
						<p style="text-align: left;">I apologize for the giant popup box. Anyways, the boxes are, in order, the lead-in (e.g. FTPE), the first question, the first answer, the second question, and so on until the fourth answer. You do not have to use all of the bonus fields, however (ask your tournament director).</p><br>
						<label>Categorize By Subject: <select id="crb_subj" name="crb_subj">
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
						<label><textarea type="text" id="crb_lead" name="crb_lead" style="height: 50px;"></textarea></label><br>
						<label><textarea type="text" id="crb_body1" name="crb_body1" style="height: 100px;"></textarea></label><br>
						<label><textarea type="text" id="crb_ans1" name="crb_ans1" style="height: 50px;"></textarea></label><br>
						<label><textarea type="text" id="crb_body2" name="crb_body2" style="height: 100px;"></textarea></label><br>
						<label><textarea type="text" id="crb_ans2" name="crb_ans2" style="height: 50px;"></textarea></label><br>
						<label><textarea type="text" id="crb_body3" name="crb_body3" style="height: 100px;"></textarea></label><br>
						<label><textarea type="text" id="crb_ans3" name="crb_ans3" style="height: 50px;"></textarea></label><br>
						<label><textarea type="text" id="crb_body4" name="crb_body4" style="height: 100px;"></textarea></label><br>
						<label><textarea type="text" id="crb_ans4" name="crb_ans4" style="height: 50px;"></textarea></label><br>
					</form><button type="button" onclick="submit_create_bonus(document.getElementById('createbonus')); return false;">Create</button>
				</div></p>
			<?
			
			break;
		case 'edit_bonus':
			$bonusID = $_POST['id'];
			
			$bonusSelect = selectFrom('bonuses', array('psets_allocations_id', 'leadin', 'question1', 'question2', 'question3', 'question4', 'answer1', 'answer2', 'answer3', 'answer4', 'creator_users_id', 'created'), array('id'), array("'" . $_POST['id'] . "'"));//Get bonuses
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
				$subjectSelect = selectFrom('psets_allocations', array('subject', 'id'), array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));//Get subjects
				array_push($subjectSelect, array('subject' => 'None')); //Add None for the default option
				usort($subjectSelect, 'compareSubjects');
				
				$userSelect = selectFrom('users', array('name', 'username'), array('id'), array("'" . $bonus['creator_users_id'] . "'"));
				$user = $userSelect[0];//Get user info
				
				?>
					<h3>Edit Bonus</h3>
					<p><div id="editbonusform" style="text-align: center;">
						<form id="editbonus" class="postform" onsubmit="submit_edit_bonus(document.getElementById('editbonus')); return false;">
							<p style="text-align: left;">I apologize for the giant popup box. Anyways, the boxes are, in order, the lead-in (e.g. FTPE), the first question, the first answer, the second question, and so on until the fourth answer. You do not have to use all of the bonus fields, however (ask your tournament director).</p><br>
							<?
								echo '<b>Creator:</b> ' . $user['name'] . ' (' . $user['username'] . ')<br>';
								echo '<b>Created:</b> ' . $bonus['created'];
							?>
							<br><br><label>Categorize By Subject: <select id="edb_subj" name="edb_subj">
								<?
									foreach ($subjectSelect as $key => $entry){
										if ($entry['id'] == $bonus['psets_allocations_id']){
											echo '<option selected="selected">' . $entry['subject'] . '</option>';
										}
										
										else{
											echo '<option>' . $entry['subject'] . '</option>';
										}
									}
								?>
							</select></label><br><br>
							<label><textarea type="text" id="edb_lead" name="edb_lead" style="height: 50px;"><? echo $bonus['leadin']; ?></textarea></label><br>
							<label><textarea type="text" id="edb_body1" name="edb_body1" style="height: 100px;"><? echo $bonus['question1']; ?></textarea></label><br>
							<label><textarea type="text" id="edb_ans1" name="edb_ans1" style="height: 50px;"><? echo $bonus['answer1']; ?></textarea></label><br>
							<label><textarea type="text" id="edb_body2" name="edb_body2" style="height: 100px;"><? echo $bonus['question2']; ?></textarea></label><br>
							<label><textarea type="text" id="edb_ans2" name="edb_ans2" style="height: 50px;"><? echo $bonus['answer2']; ?></textarea></label><br>
							<label><textarea type="text" id="edb_body3" name="edb_body3" style="height: 100px;"><? echo $bonus['question3']; ?></textarea></label><br>
							<label><textarea type="text" id="edb_ans3" name="edb_ans3" style="height: 50px;"><? echo $bonus['answer3']; ?></textarea></label><br>
							<label><textarea type="text" id="edb_body4" name="edb_body4" style="height: 100px;"><? echo $bonus['question4']; ?></textarea></label><br>
							<label><textarea type="text" id="edb_ans4" name="edb_ans4" style="height: 50px;"><? echo $bonus['answer4']; ?></textarea></label><br>
							<input type="hidden" id="edb_id" name="edb_id" value="<? echo $bonusID; ?>">
						</form><button type="button" onclick="submit_edit_bonus(document.getElementById('editbonus')); return false;">Update</button> or 
						<button type="button" onclick="submit_delete_bonus(<? echo $bonusID; ?>); return false;">Delete</button>
					</div></p>
				<?
			}
			
			else{
				$userSelect = selectFrom('users', array('name', 'username'), array('id'), array("'" . $bonus['creator_users_id'] . "'"));
				$user = $userSelect[0];//Get user info
				
				echo '<h3>View Bonus</h3><br>';
				echo '<b>Creator:</b> ' . $user['name'] . ' (' . $user['username'] . ')<br>';
				echo '<b>Created:</b> ' . $bonus['created'];
				
				if (isset($bonus['leadin']) and $bonus['leadin'] != ''){
					echo '<br><br>' . $bonus['leadin'];
				}
				
				foreach (array('1', '2', '3', '4') as $key){
					if (isset($bonus['question' . $key]) and $bonus['question' . $key] != ''){
						echo '<br><br>' . $bonus['question' . $key] . '<br><br>';
						echo '<b>Answer: </b>' . $bonus['answer' . $key];
					}
				}
			}
			
			break;
		case 'mark_bonus':
			$bonusID = $_POST['id'];
			
			$bonusSelect = selectFrom('bonuses', array('psets_allocations_id', 'duplicate_bonuses_id', 'approved', 'promoted', 'difficulty'), array('id'), array("'" . $bonusID . "'"));//Get bonuses
			$bonus = $bonusSelect[0];
			$bonus['psets_allocations_id'] = isset($bonus['psets_allocations_id']) ? $bonus['psets_allocations_id'] : '';
			
			$approved = $bonus['approved'];
			$promoted = $bonus['promoted'];
			$duplicate = isset($bonus['duplicate_bonuses_id']) ? '"' . $bonus['duplicate_bonuses_id'] . '"' : '""';
			
			$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
			$userID = $userSelect[0]['id'];//Get current user's ID
			
			$permSelect = selectFrom('permissions', array('role', 'psets_allocations_id'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
			$userRole = $permSelect[0]['role'];//Get current user's role
			$userFocus = isset($permSelect[0]['psets_allocations_id']) ? $permSelect[0]['psets_allocations_id'] : '';
			
			$difficulties = array('Easy', 'Medium', 'Hard');
			$difficulty = $bonus['difficulty'];
			
			if ($userRole == 'd' or $userRole == 'a' or (strlen($userFocus) > 0 and in_array($bonus['psets_allocations_id'], explode(',', $userFocus)))){
				?>
					<h3>Mark Bonus</h3>
					<p><div id="markbonusform" style="text-align: center;">
						<form id="markbonus" class="postform" onsubmit="submit_mark_bonus(document.getElementById('markbonus')); return false;">
							<p style="text-align: left;">Both administrators and managers may approve a bonus, but administrators may also promote a bonus for packet use. If you mark a duplicate that is already the duplicate of another bonus, the identifier will be inherited from the master version, if that makes sense.</p><br>
							<label>Difficulty: <select id="mrb_dif" name="mrb_dif">
								<?
									foreach ($difficulties as $level){
										if (substr(lcfirst($level), 0, 1) == $difficulty){
											echo '<option selected="selected">' . $level . '</option>';
										}
										
										else{
											echo '<option>' . $level . '</option>';
										}
									}
								?>
							</select></label><br><br>
							<label>Bonus Approved: <input type="checkbox" id="mrb_app" name="mrb_app" <? if ($approved == 1){echo 'checked="yes"';} ?>></label><br>
							<label>Bonus Promoted: <input type="checkbox" id="mrb_pro" name="mrb_pro" <? if ($promoted == 1){echo 'checked="yes"';} ?> <? if ($userRole == 'm'){echo 'disabled="disabled"';} ?>></label><br><br>
							<label>Duplicate ID: <input type="text" id="mrb_dup" name="mrb_dup" style="width: 25px;" value=<? echo $duplicate; ?>></label><br><br>
							<input type="hidden" id="mrb_id" name="mrb_id" value="<? echo $bonusID; ?>">
						</form><button type="button" onclick="submit_mark_bonus(document.getElementById('markbonus')); return false;">Mark</button>
					</div></p>
				<?
			}
			
			else{
				echo $conf['noperms'];
			}
			
			break;
			
		case 'send_bonus':
			$bonusID = $_POST['id'];
			
			?>
				<h3>Add Message</h3>
				<p><div id="sendbonusform" style="text-align: center;">
					<form id="sendbonus" class="postform" onsubmit="submit_send_bonus(document.getElementById('sendbonus')); return false;">
						<p style="text-align: left;">You may use the box below to add a message to this bonus. Please keep within one thousand characters.</p><br>
						<label><textarea type="text" id="sdb_msg" name="sdb_msg" style="height: 100px;"></textarea></label><br>
						<input type="hidden" id="sdb_id" name="sdb_id" value="<? echo $bonusID; ?>">
					</form><button type="button" onclick="submit_send_bonus(document.getElementById('sendbonus')); return false;">Add</button>
				</div></p>
			<?
			
			break;
			
		case 'messages_bonus':
			$bonusID = $_POST['id'];
			echo '<h3>View Messages</h3>'
		
			?>
				<p style="text-align: left;">
					Below are all of the messages for this entry listed chronologically. With editing rights, you may delete any.
				</p><br>
			<?
			
			$messagesSelect = selectFrom('messages', array('users_id', 'id', 'message'), array('tossup_or_bonus', 'tub_id'), array("'1'", "'" . $bonusID . "'"));
			//This above line should select all of the appropriate messages
			
			if (count($messagesSelect) > 0){
				echo '<span id="messages">';
				
				foreach ($messagesSelect as $key => $entry){
					$userSelect = selectFrom('users', array('name', 'username'), array('id'), array("'" . $entry['users_id']. "'"));
					
					$userName = $userSelect[0]['name'];
					$userUser = $userSelect[0]['username'];
					
					echo '<h3><a href="#">From: ' . $userName . ' - <span onclick="submit_delete_message_bonus(' . $entry['id'] . ')">Delete Message</span></a></h3>';
					echo '<div>' . $entry['message'] . '</div>';
				}
				
				echo '</span>';
			}
			
			else{
				echo '<p style="text-align: left;">No messages found! To add a message to this list, please click on the Add button in the Messages table column.</p>';
			}
			
			break;
		
		case 'packets_assign':
			$questionID = $_POST['id'];
			$type = $_POST['tob'];
			
			$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
			$userID = $userSelect[0]['id'];//Get current user's ID
			
			$permSelect = selectFrom('permissions', array('role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
			$role = $permSelect[0]['role'];//Get current user's role
			
			if ($type == 0){
				$questionSelect = selectFrom('tossups', array('round_id', 'round_num'), array('id'), array("'" . $questionID . "'"));
				$round = (isset($questionSelect[0]['round_id']) and $questionSelect[0]['round_id'] != '') ? $questionSelect[0]['round_id'] : '';
				$number = (isset($questionSelect[0]['round_num']) and $questionSelect[0]['round_num'] != '') ? $questionSelect[0]['round_num'] : '';
			}
			
			else{
				$questionSelect = selectFrom('bonuses', array('round_id', 'round_num'), array('id'), array("'" . $questionID . "'"));
				$round = (isset($questionSelect[0]['round_id']) and $questionSelect[0]['round_id'] != '') ? $questionSelect[0]['round_id'] : '';
				$number = (isset($questionSelect[0]['round_num']) and $questionSelect[0]['round_num'] != '') ? $questionSelect[0]['round_num'] : '';
			}
			
			if ($role == 'd' or $role == 'a'){
				?>
					<h3>Assign Packet</h3>
					<p><div id="packetsassignform" style="text-align: center;">
						<form id="packetsassign" class="postform" onsubmit="submit_packets_assign(document.getElementById('packetsassign')); return false;">
							<p style="text-align: left;">The input field below will allow you to assign a packet ID to this question. You may use any number, even if it is not already a packet; type nothing to de-assign this. You may also assign a question number; if these are duplicated or not sequential throughout the given packet, the algorithm will make its best guess.</p><br>
							<label>Packet ID: <input type="text" id="asp_set" name="asp_set" style="width: 25px;" value="<? echo $round; ?>"></label><br>
							<label>Question: <input type="text" id="asp_num" name="asp_num" style="width: 25px;" value="<? echo $number; ?>"></label><br><br>
							<input type="hidden" id="asp_id" name="asp_id" value="<? echo $questionID; ?>">
							<input type="hidden" id="asp_type" name="asp_type" value="<? echo $type; ?>">
						</form><button type="button" onclick="submit_packets_assign(document.getElementById('packetsassign')); return false;">Assign</button>
					</div></p>
				<?
			}
			
			else{
				echo $conf['noperms'];
			}
				
			break;
			
		case 'packets_auto':
			$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
			$userID = $userSelect[0]['id'];//Get current user's ID
			
			$permSelect = selectFrom('permissions', array('role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
			$role = $permSelect[0]['role'];//Get current user's role
			
			$parameters = array('subject');//Get Tournament Subjects
			$subjectSelect = selectFrom('psets_allocations', $parameters, array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));
			usort($subjectSelect, 'compareSubjects');
			$subjects = '';
			
			foreach ($subjectSelect as $entry){
				$subjects = $subjects . $entry['subject'] . " 1\n";
			}
			
			$subjects = rtrim($subjects);
			
			if ($role == 'd' or $role == 'a'){
				?>
					<h3>Generate Packets</h3>
					<p><div id="packetsautoform" style="text-align: center;">
						<form id="packetsauto" class="postform" onsubmit="submit_packets_auto(document.getElementById('packetsauto')); return false;">
							<p style="text-align: left;">Below, you can enter the number of packets you would like to create. Please specify the number of tossups and bonuses per packet that you wish to have. For the allocations field, type relative numbers of questions for each subject. As an example, one might type <i>Math 8</i> followed by <i>Science 20</i> to have 20 science questions for every 8 math questions. These numbers should be whole numbers, and do not have to necessarily correspond with the numbers of TUs and Bonuses.</p><br>
							<label>Preserve Question Ordering: <input type="checkbox" id="aup_pre" name="aup_pre"></label><br>
							<label>Append To Existing Packets: <input type="checkbox" id="aup_app" name="aup_app"></label><br>
							<label>Factor In Difficulty Settings: <input type="checkbox" id="aup_dfs" name="aup_dfs"></label><br><br>
							<label>Packets: <input type="text" id="aup_num" name="aup_num" style="width: 25px;"></label><br>
							<label>Tossups: <input type="text" id="aup_tu" name="aup_tu" style="width: 25px;"></label><br>
							<label>Bonuses: <input type="text" id="aup_b" name="aup_b" style="width: 25px;"></label><br><br>
							<label>Subject Selection: <br><textarea type="text" id="aup_alloc" name="aup_alloc"><? echo $subjects; ?></textarea></label><br><br>
						</form><button type="button" onclick="submit_packets_auto(document.getElementById('packetsauto')); return false;">Generate</button>
					</div></p>
				<?
			}
			
			else{
				echo $conf['noperms'];
			}
				
			break;
	}
}

else{
	echo $conf['unauthorized2'];
}

?>