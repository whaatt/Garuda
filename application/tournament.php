<?php 

require_once('conf.php');
require_once('query.php'); 

/* Tournament Handler */

if(isset($_SESSION['username'])){
	$submit = $_POST['submit']; //Form Submit Boolean (2 or 1 or 0)

	if ($submit == '2'){ //Delete Submission
		$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
		$userID = $userSelect[0]['id'];//Get current user's ID
	
		$roleSelect = selectFrom('permissions', array('role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
		$role = $roleSelect[0]['role'];
	
		if ($role == 'd' or $role == 'a'){
			?>
			<div id="delete" class="message error-message">
				<p><strong>Are you sure you want to delete this tournament? <a onclick="cont_delete('set', 0);"><small><small>Yes</small></small></a> or <a onclick="cont_remove($(this).closest('#delete'), 1);"><big><big>No</big></big></a>.</strong></p>
			</div>
			<?
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
	
		if ($role == 'd' or $role == 'a'){
			//Get POST stuff and trim whitespace
			$name = trim($_POST['upd_name']);
			$date = trim($_POST['upd_date']);
			$info = trim($_POST['upd_info']);
			
			$subjects = trim($_POST['upd_sele']);
			
			if(strlen(trim($subjects)) > 0){//Check if stuff is actually in the subjects field
				$subjects = explode("\n", $subjects);
				
				foreach ($subjects as $key => $value){
					$subjects[$key] = trim($subjects[$key]);
					
					if ($key > 49){//Max of 50 subjects
						break;
					}
					
					if (strlen($subjects[$key]) > 50){
						$subjects[$key] = substr($subjects[$key], 0, 50);
					}
				}
			}
			
			else{
				$subjects = array();
			}
			
			$subjects = array_unique($subjects);
			
			if (strlen($date) > 0){
				$pDate = date_parse_from_format('Y-m-d H:i:s', $date);//Parsed Date
			}
			
			else{
				$date = '0000-00-00 00:00:00';
			}
				
			if (strlen($name) <= 0 or strlen($name) > 200 or strlen($info) > 1000 or (isset($pDate['errors']) and $pDate['error_count'] + $pDate['warning_count'] > 0)){//Entry Validation ($pDate checks $date)
				?>
				<div class="message error-message" onclick="cont_remove(this, 1);">
					<p><strong>One or more of your fields was improperly entered. (Click to hide.)</strong></p>
				</div>
				<?
			}
			
			else{
				$columns = array('id');
				$params = array('title');
				$values = array("'" . sanitize($name) . "'");
				
				$setsSelect = selectFrom('psets', $columns, $params, $values);
				
				if (count($setsSelect) > 0 and $setsSelect[0]['id'] != $_SESSION['tournament']){//Check For Duplicates
					?>
					<div class="message error-message" onclick="cont_remove(this, 1);">
						<p><strong>There already exists a set with the exact same name. (Click to hide.)</strong></p>
					</div>
					<?
				}
				
				else{
					$columns = array('title', 'info', 'target');
					$values = array("'" . sanitize($name) . "'", "'" . sanitize($info) . "'", "'" . sanitize($date) . "'");
					updateIn('psets', $columns, $values, array('id'), array("'" . $_SESSION['tournament'] . "'"));//Update tournament in database
				
					$columns = array('title', 'director_users_id', 'created', 'target');
					$subjectSelect = selectFrom('psets_allocations', array('subject', 'id'), array('psets_id'), array("'" . $_SESSION['tournament'] . "'"));//Get subjects.
					
					foreach ($subjectSelect as $entry){//Delete missing subjects from allocations, focuses, and questions
						if (!in_array($entry['subject'], $subjects)){
							deleteFrom('psets_allocations', array('subject', 'psets_id'), array("'" . $entry['subject'] . "'", "'" . $_SESSION['tournament'] . "'"));
							updateIn('permissions', array('psets_allocations_id'), array("NULL"), array('psets_allocations_id'), array("'" . $entry['id'] . "'"));
							updateIn('tossups', array('psets_allocations_id'), array("NULL"), array('psets_allocations_id'), array("'" . $entry['id'] . "'"));
							updateIn('bonuses', array('psets_allocations_id'), array("NULL"), array('psets_allocations_id'), array("'" . $entry['id'] . "'"));
						}
					}
					
					foreach ($subjects as $subject){//Put subjects into DB if they're not there yet
						if (getNumOf('psets_allocations', array('psets_id', 'subject'), array("'" . $_SESSION['tournament'] . "'", "'" . $subject . "'")) == 0){
							insertInto('psets_allocations', array('psets_id', 'subject'), array("'" . $_SESSION['tournament'] . "'", "'" . sanitize($subject) . "'"));
						}
					}
					
					?>
					<div class="message thank-message" onclick="go_tournament()">
						<p><strong>You have successfully updated your tournament information. (Click to refresh.)</strong></p>
					</div>
					<?
				}
			}
		}
		
		else{
			echo $conf['noperms'];
		}
	}

		else{
		if (isset($_POST['tou_id'])){
			$_SESSION['tournament'] = $_POST['tou_id'];				
			
			$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
			$userID = $userSelect[0]['id'];//Get current user's ID
		
			$roles = getNumOf('permissions', array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_POST['tou_id'] . "'"));
		}
		
		else{
			$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
			$userID = $userSelect[0]['id'];//Get current user's ID
	
			$roles = getNumOf('permissions', array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
		}
		
		if ($roles > 0){
			echo $conf['tournament'];
			
			//Prepare stuff for the pretty boxes
			$parameters = array('title', 'director_users_id', 'created', 'target', 'info');//Get Tournament Data
			$setsSelect = selectFrom('psets', $parameters, array('id'), array("'" . $_SESSION['tournament'] . "'"));
			
			$userSelect = selectFrom('users', array('name', 'username'), array('id'), array("'" . $setsSelect[0]['director_users_id'] . "'"));
			$setDirector = $userSelect[0]['name'] . ' (' . $userSelect[0]['username'] . ')';//Get name and username of this tournament's director
			
			$setName = $setsSelect[0]['title'];
			$setCreate = $setsSelect[0]['created'];
			
			$setInfo = $setsSelect[0]['info'] == '' ? 'Nothing of the sort has been set.' : $setsSelect[0]['info'];
			$setDate = $setsSelect[0]['target'] == '0000-00-00 00:00:00' ? 'No Target Set' : $setsSelect[0]['target'];
			
			//Output the pretty boxes with information
			echo '<p style="text-align:center;"><span class="box property">Tournament Name: ' . $setName . '</span>';
			echo '<span class="box property">Tournament Director: ' . $setDirector . '</span><br>';
			echo '<span class="box property">Tournament Date: ' . $setDate . '</span>';
			echo '<span class="box property">Creation Date: ' . $setCreate . '</span><br>';
			echo '<span class="box property">Tournament Info or Message: ' . $setInfo . '</span>';
			echo '<span class="box property"><a onclick="modal_set(); return false;">Edit Set</a></span></p>';
			
			$roleSelect = selectFrom('permissions', array('role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
			$role = $roleSelect[0]['role'];
			
			if ($role == 'd'){
				$codeSelect = selectFrom('psets', array('admin_access_code', 'manager_access_code', 'editor_access_code'), array('id'), array("'" . $_SESSION['tournament'] . "'"));
				echo $conf['codes'] . ' The administrator code is ' . $codeSelect[0]['admin_access_code'] . ', ' . 'the manager code is ' . $codeSelect[0]['manager_access_code'] . ', ' . 'and the editor code is ' . $codeSelect[0]['editor_access_code'] . '. Please keep these within your staff.';
			}
		}
		
		else{
			echo $conf['noperms2'];
		}
	}
}

else{
	echo $conf['unauthorized'];
}

?>