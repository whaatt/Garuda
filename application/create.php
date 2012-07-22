<?php 

require_once('conf.php'); 
require_once('query.php');

/* Create Handler */

if(isset($_SESSION['username'])){
	$submit = $_POST['submit']; //Form Submit Boolean

	if ($submit == '1'){ //Registration Form Submission

		$name = $_POST['cre_name'];
		$date = $_POST['cre_date'];
		$info = $_POST['cre_info'];
		
		$pDate = date_parse_from_format('Y-m-d H:i:s', $date);//Parsed Date
		$pTest = (isset($_POST['cre_test']) and $_POST['cre_test'] == 'on') ? 1 : 0;//Parsed Checkbox

		if (strlen($name) <= 0 or strlen($name) > 200 or strlen($info) > 1000 or (isset($pDate['errors']) and $date != '' and $pDate['error_count'] + $pDate['warning_count'] > 0)){//Entry Validation
			?>
			<div class="message error-message" onclick="go_create();">
				<p><strong>One or more of your fields was improperly entered. (Click to try again.)</strong></p>
			</div>
			<?
		}
		
		else{
			$columns = array('title');
			$values = array("'" . sanitize($name) . "'");
			if (getNumOf('psets', $columns, $values) > 0){//Check For Duplicates
				?>
				<div class="message error-message" onclick="go_create();">
					<p><strong>There already exists a set with the exact same name. (Click to try again.)</strong></p>
				</div>
				<?
			}
			
			else{
				$userSelect = selectFrom('users', array('id'), array('username'), array("'" . $_SESSION['username'] . "'"));
				$userID = $userSelect[0]['id'];
				
				$adminPass = strval(mt_rand(100000,999999));
				$managerPass = strval(mt_rand(100000,999999));
				$editorPass = strval(mt_rand(100000,999999));
				
				$columns = array('title', 'info', 'director_users_id', 'is_users_tournament', 'admin_access_code', 'manager_access_code', 'editor_access_code', 'created', 'target');
				$values = array("'" . sanitize($name) . "'", "'" . sanitize($info) . "'", "'" . sanitize($userID) . "'", "'" . sanitize(strval($pTest)) . "'", "'" . sanitize(strval($adminPass)) . "'", "'" . sanitize(strval($managerPass)) . "'", "'" . sanitize(strval($editorPass)) . "'", "NOW()", "'null'");
				insertInto('psets', $columns, $values);
			
				?>
				<div class="message thank-message">
					<p><strong>Thank you for your submission. You may now use your set.</strong></p>
				</div>
				<?
			}
		}
	}

	else{//General Link
		echo $conf['create'];
		?>
		<p><div id="createform" style="text-align:center;"><div class="box">
			<form id="create" class="postform" onsubmit="submit_create(document.getElementById('create')); return false;">
				<label>Tournament or Set Name: <input type="text" id="cre_name" name="cre_name"></label><br><br>
				<label>Tournament Target Date: <input type="text" id="cre_date" name="cre_date"></label><br><br>
				<label>Private Test: <input type="checkbox" id="cre_test" name="cre_test"></label><br><br>
				<label>Tournament Information:<br> <textarea type="text" id="cre_info" name="cre_info"></textarea></label><br><br>
				<input type="submit" value="Register">
			</form>
		</div></div></p>			
		<script type="text/javascript">
			fancy_date('cre_date');
		</script>
		<?
	}
}

else{
	echo "<p>This page is only for authenticated users. Please refresh the page to restart your session. Sorry for any inconvenience!</p>";
}

?>