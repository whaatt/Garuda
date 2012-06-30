<?php

/* MySQL Query Handler */

function sanitize($value){//Defend Against Injections
	return mysql_real_escape_string(htmlentities($value));
}

function getNumOf($table,$columns,$items){//Get Number Of Instances In Database
	$query = "SELECT * FROM $table WHERE (";
	$query = $query . $columns[0] . "=" . $items[0];
	
	for($i = 1; $i < count($columns); $i++){
		$query = $query . " AND " . $columns[$i] . "=" . $items[$i];
	}
	
	$query = $query . ")";

	$duplicates = mysql_query($query) or die(mysql_error());
	return mysql_num_rows($duplicates);
}

function insertInto($table,$columns,$items){
	$query = "INSERT INTO $table (";//Build Query String
	$query = $query . $columns[0];

	for($i = 1; $i < count($columns); $i++){
		$query = $query . ", " . $columns[$i];
	}
	
	$query = $query . ") VALUES (";
	$query = $query . $items[0];
	
	for($i = 1; $i < count($items); $i++){
		$query = $query . ", " . $items[$i];
	}
	
	$query = $query . ")";
	
	mysql_query($query) or die(mysql_error());
}

?>