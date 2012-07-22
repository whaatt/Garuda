<?php 

require_once('conf.php'); 

/* Dashboard Handler */

if(isset($_SESSION['username'])){
	?>
	<table cellpadding="0" cellspacing="0" border="0" class="display" id="table_id">
		<thead>
			<tr>
				<th>Tournament</th>
				<th>Director</th>
				<th>Access Level</th>
				<th>Creation Date</th>
				<th>Target Date</th>
			</tr>
		</thead>
		<tbody>
			<tr class="gradeX">
				<td>Trident</td>
				<td>Internet
					 Explorer 4.0</td>
				<td>Win 95+</td>
				<td class="center">4</td>
				<td class="center">X</td>
			</tr>
			<tr class="gradeC">
				<td>Trident</td>
				<td>Internet
					 Explorer 5.0</td>
				<td>Win 95+</td>
				<td class="center">5</td>
				<td class="center">C</td>
			</tr>
			<tr class="gradeA">
				<td>Trident</td>
				<td>Internet
					 Explorer 5.5</td>
				<td>Win 95+</td>
				<td class="center">5.5</td>
				<td class="center">A</td>
			</tr>
			<tr class="gradeA">
				<td>Trident</td>
				<td>Internet
					 Explorer 6</td>
				<td>Win 98+</td>
				<td class="center">6</td>
				<td class="center">A</td>
			</tr>
			<tr class="gradeA">
				<td>Trident</td>
				<td>Internet Explorer 7</td>
				<td>Win XP SP2+</td>
				<td class="center">7</td>
				<td class="center">A</td>
			</tr>
			<tr class="gradeA">
				<td>Trident</td>
				<td>AOL browser (AOL desktop)</td>
				<td>Win XP</td>
				<td class="center">6</td>
				<td class="center">A</td>
			</tr>
		</tbody>
	</table><p></p>
	<script type="text/javascript">
		fancy_sets('table_id');
	</script>
	<?
}

else{
	echo "<p>This page is only for authenticated users. Please refresh the page to restart your session. Sorry for any inconvenience!</p>";
}

?>