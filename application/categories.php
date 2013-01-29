<?php

require_once('conf.php');
require_once('query.php'); 

/* Categories Handler */

header('Content-Type: text/csv; charset=utf-8');
header('Cache-Control: no-store, no-cache');
header('Content-Disposition: attachment; filename=categories.csv');

$prefix = (isset($_GET['prefix'])) ? $_GET['prefix'] : 'None'; //Get Round Prefix
$rounds = (isset($_GET['rounds'])) ? $_GET['rounds'] : 0; //Get Number of Rounds
$questions = (isset($_GET['questions'])) ? $_GET['questions'] : 0; //Get Number of Questions PP
$separator = ','; //Default, Standard CSV Separator, defined in some RFC

if (!isset($_SESSION['tournament'])){
	echo 'Permission denied.';
	exit();
}

else if ((int) $rounds != $rounds or (int) $rounds <= 0){
	echo 'Invalid parameters were passed.';
	exit();
}

else if ((int) $questions != $questions && (int) $questions <= 0){
	echo 'Invalid parameters were passed.';
	exit();
}

else if ($questions > 200 or $rounds > 50){
	echo 'Excessive parameters were passed.';
	exit();
}

$questions = (int) $questions;
$rounds = (int) $rounds;

$items = array('subject'); $columns = array('psets_id'); //Get Subjects
$values = array("'" . $_SESSION['tournament'] . "'");
$subjectSelect = selectFrom('psets_allocations', $items, $columns, $values);

foreach ($subjectSelect as $subject){ //Loop and Print Categories
	echo 'Category' . $separator . $subject['subject'] . "\r\n";
}

for ($i = 1; $i <= $rounds; $i++){
	echo 'Packet' . $separator . $prefix . $i . "\r\n";
	for ($j = 1; $j <= $questions; $j++){
		$tossupSelect = selectFrom('tossups', array('psets_allocations_id'), array('psets_id', 'round_id', 'round_num'), array("'" . $_SESSION['tournament'] . "'", "'" . strval($i) . "'", "'" . strval($j) . "'"));
		$bonusSelect = selectFrom('bonuses', array('psets_allocations_id'), array('psets_id', 'round_id', 'round_num'), array("'" . $_SESSION['tournament'] . "'", "'" . strval($i) . "'", "'" . strval($j) . "'"));

		$tossupSubjectID = isset($tossupSelect[0]['psets_allocations_id']) ? $tossupSelect[0]['psets_allocations_id'] : 'None Set';
		$bonusSubjectID = isset($bonusSelect[0]['psets_allocations_id']) ? $bonusSelect[0]['psets_allocations_id'] : 'None Set';	
		
		$subjectSelectTU = selectFrom('psets_allocations', array('subject'), array('id'), array("'" . $tossupSubjectID . "'"));//Get subject by ID
		$tossupSubject = isset($subjectSelectTU[0]['subject']) ? $subjectSelectTU[0]['subject'] : 'Invalid';
		
		$subjectSelectB = selectFrom('psets_allocations', array('subject'), array('id'), array("'" . $bonusSubjectID . "'"));//Get subject by ID
		$bonusSubject = isset($subjectSelectB[0]['subject']) ? $subjectSelectB[0]['subject'] : 'Invalid';
		
		echo 'Question' . $separator . $j . $separator . $tossupSubject . $separator . $bonusSubject . $separator . $bonusSubject . $separator . $bonusSubject . "\r\n";
	}
}

?>