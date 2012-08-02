<?php 

require_once('conf.php'); 
require_once('query.php');

/* Join Handler */

if(isset($_SESSION['username'])){
	$submit = $_POST['submit']; //Form Submit Boolean (1 or 0)

	if ($submit == '1'){ //Join Form Submission
		
		//Get POST values and trim whitespace
		$name = trim($_POST['join_name']);
		$code = trim($_POST['join_code']);
		
		if (strlen($name) == 0 or strlen($code) != 6){//Entry Validation ($name can't be empty, $code must be 6 digits)
			?>
			<div class="message error-message" onclick="go_join();">
				<p><strong>One or more of your fields was improperly entered. (Click to try again.)</strong></p>
			</div>
			<?
		}

		else{
			$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
			$userID = $userSelect[0]['id'];//Get ID of joining user
			
			if (getNumOf('psets', array('title'), array("'" . sanitize($name) . "'")) > 0) {
				//For admins
				if (getNumOf('psets', array('title', 'admin_access_code'), array("'" . sanitize($name) . "'", "'" . sanitize($code) . "'")) > 0){			
					$setsSelect = selectFrom('psets', array('id'), array('title'), array("'" . sanitize($name) . "'"));//Get tournaments.
					$setID = $setsSelect[0]['id'];//Get ID of set, if exists
					
					if (getNumOf('permissions', array('psets_id', 'users_id'), array("'" . sanitize($setID) . "'", "'" . sanitize($userID) . "'")) == 0){
						$columns = array('users_id', 'psets_id', 'role');
						$values = array("'" . sanitize($userID) . "'", "'" . sanitize($setID) . "'", "'" . sanitize('a') . "'");
						insertInto('permissions', $columns, $values);
					
						?>
						<div class="message thank-message">
							<p><strong>You have successfully gained access to this set.</strong></p>
						</div>
						<?
					}
					
					else{
						?>
						<div class="message thank-message">
							<p><strong>You already have access permissions to this set.</strong></p>
						</div>
						<?
					}
				}
				
				//For managers
				else if (getNumOf('psets', array('title', 'manager_access_code'), array("'" . sanitize($name) . "'", "'" . sanitize($code) . "'")) > 0){
					$setsSelect = selectFrom('psets', array('id'), array('title'), array("'" . sanitize($name) . "'"));//Get tournaments.
					$setID = $setsSelect[0]['id'];//Get ID of set, if exists
					
					if (getNumOf('permissions', array('psets_id', 'users_id'), array("'" . sanitize($setID) . "'", "'" . sanitize($userID) . "'")) == 0){
						$columns = array('users_id', 'psets_id', 'role');
						$values = array("'" . sanitize($userID) . "'", "'" . sanitize($setID) . "'", "'" . sanitize('m') . "'");
						insertInto('permissions', $columns, $values);
						
						?>
						<div class="message thank-message">
							<p><strong>You have successfully gained access to this set.</strong></p>
						</div>
						<?
					}
					
					else{
						?>
						<div class="message thank-message">
							<p><strong>You already have access permissions to this set.</strong></p>
						</div>
						<?
					}
				}
				
				//For editors
				else if (getNumOf('psets', array('title', 'editor_access_code'), array("'" . sanitize($name) . "'", "'" . sanitize($code) . "'")) > 0){
					$setsSelect = selectFrom('psets', array('id'), array('title'), array("'" . sanitize($name) . "'"));//Get tournaments.
					$setID = $setsSelect[0]['id'];//Get ID of set, if exists
					
					if (getNumOf('permissions', array('psets_id', 'users_id'), array("'" . sanitize($setID) . "'", "'" . sanitize($userID) . "'")) == 0){
						$columns = array('users_id', 'psets_id', 'role');
						$values = array("'" . sanitize($userID) . "'", "'" . sanitize($setID) . "'", "'" . sanitize('e') . "'");
						insertInto('permissions', $columns, $values);
						
						?>
						<div class="message thank-message">
							<p><strong>You have successfully gained access to this set.</strong></p>
						</div>
						<?
					}
					
					else{
						?>
						<div class="message thank-message">
							<p><strong>You already have access permissions to this set.</strong></p>
						</div>
						<?
					}
				}
				
				else{
					?>
					<div class="message error-message" onclick="go_join();">
						<p><strong>You entered an invalid tournament access code. (Click to try again.)</strong></p>
					</div>
					<?
				}
			}
			
			else{
				?>
				<div class="message error-message" onclick="go_join();">
					<p><strong>You entered an inaccurate tournament name. (Click to try again.)</strong></p>
				</div>
				<?
			}
		}
	}
	
	else{//General Link
		echo $conf['join'];
		?>
		<p><div id="joinform" style="text-align:center;"><div class="box">
			<form id="join" class="postform" onsubmit="submit_join(document.getElementById('join')); return false;">
				<label>Tournament or Set Name: <input type="text" id="join_name" name="join_name"></label><br><br>
				<label>Tournament Access Code: <input type="text" id="join_code" name="join_code"></label><br><br>
				<input type="submit" value="Join">
			</form>
		</div></div></p>
		<?
	}
}

else{
	echo $conf['unauthorized'];
}

?>