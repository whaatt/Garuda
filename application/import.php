<?php

require_once('conf.php');
require_once('query.php'); 

/* Import Handler */

if(isset($_SESSION['username'])){
	$submit = $_POST['submit']; //Form Submit Boolean (1 or 0)

	if ($submit == '1'){ //Import Form Submission
		
		//Get POST values and trim whitespace
		$name = trim($_POST['import_name']);
		$code = trim($_POST['import_code']);
		
		if (strlen($name) == 0 or strlen($code) != 6){//Entry Validation ($name can't be empty, $code must be 6 digits)
			?>
			<div class="message error-message" onclick="go_import();">
				<p><strong>One or more of your fields was improperly entered. (Click to try again.)</strong></p>
			</div>
			<?
		}

		else{
			$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
			$userID = $userSelect[0]['id'];//Get ID of user
			
			$permSelect = selectFrom('permissions', array('psets_allocations_id', 'role'), array('users_id', 'psets_id'), array("'" . $userID . "'", "'" . $_SESSION['tournament'] . "'"));
			$userRole = $permSelect[0]['role'];//Get current user's role
		
			if ($userRole == 'd' or $userRole == 'a'){//Perm check
				if (getNumOf('psets', array('title'), array("'" . sanitize($name) . "'")) > 0) {//Name Check
					if (getNumOf('psets', array('title', 'admin_access_code'), array("'" . sanitize($name) . "'", "'" . sanitize($code) . "'")) > 0 or
						getNumOf('psets', array('title', 'manager_access_code'), array("'" . sanitize($name) . "'", "'" . sanitize($code) . "'")) > 0 or
						getNumOf('psets', array('title', 'editor_access_code'), array("'" . sanitize($name) . "'", "'" . sanitize($code) . "'")) > 0){//Code check			
						
						$setsSelect = selectFrom('psets', array('id'), array('title'), array("'" . sanitize($name) . "'"));//Get tournament
						$setID = $setsSelect[0]['id'];//Get ID of set, if exists
						
						if ($setID != $_SESSION['tournament']){
							copyEntries('tossups', 'tossups', array('psets_id', 'creator_users_id', 'approved', 'psets_allocations_id', 'duplicate_tossups_id', 'round_id', 'round_num'), array("'" . $_SESSION['tournament'] . "'", "'0'", "'0'", "NULL", "NULL", "NULL", "NULL"), array('psets_id', 'promoted'), array("'" . $setID . "'", "'0'")); //copy tossups
							copyEntries('bonuses', 'bonuses', array('psets_id', 'creator_users_id', 'approved', 'psets_allocations_id', 'duplicate_bonuses_id', 'round_id', 'round_num'), array("'" . $_SESSION['tournament'] . "'", "'0'", "'0'", "NULL", "NULL", "NULL", "NULL"), array('psets_id', 'promoted'), array("'" . $setID . "'", "'0'")); //copy bonuses
					
							?>
							<div class="message thank-message">
								<p><strong>You have successfully imported this set.</strong></p>
							</div>
							<?
						}
						
						else{
							?>
							<div class="message error-message" onclick="go_import();">
								<p><strong>You cannot import from the same tournament. (Click to try again.)</strong></p>
							</div>
							<?
						}
					}
					
					else{
						?>
						<div class="message error-message" onclick="go_import();">
							<p><strong>You entered an invalid tournament access code. (Click to try again.)</strong></p>
						</div>
						<?
					}
				}
				
				else{				
					?>
					<div class="message error-message" onclick="go_import();">
						<p><strong>You entered an inaccurate tournament name. (Click to try again.)</strong></p>
					</div>
					<?
				}
			}
			
			else{
				?>
				<div class="message error-message">
					<p><strong>You have insufficient permissions to import a tournament.</strong></p>
				</div>
				<?
			}
		}
	}
	
	else{//General Link
		echo $conf['import'];
		?>
		<p><div id="importform" style="text-align:center;"><div class="box">
			<form id="import" class="postform" onsubmit="submit_import(document.getElementById('import')); return false;">
				<label>Tournament or Set Name: <input type="text" id="import_name" name="import_name"></label><br><br>
				<label>Tournament Access Code: <input type="text" id="import_code" name="import_code"></label><br><br>
				<input type="submit" value="Import">
			</form>
		</div></div></p>
		<?
	}
}

else{
	echo $conf['unauthorized'];
}

?>