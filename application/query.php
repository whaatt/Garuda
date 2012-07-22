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

function insertInto($table,$columns,$items){//Insert Items Into Database
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
	return true;
}

//Precondition: $values are valid
function selectFrom($table,$values,$columns,$items){//Make A Database Selection
	$query = "SELECT * FROM $table WHERE (";
	$query = $query . $columns[0] . "=" . $items[0];
	
	for($i = 1; $i < count($columns); $i++){
		$query = $query . " AND " . $columns[$i] . "=" . $items[$i];
	}
	
	$query = $query . ")";
	$selection = mysql_query($query) or die(mysql_error());
	$return = array();
	
	while ($row = mysql_fetch_array($selection)){
		array_push($return, array());
		foreach ($values as $value){
			$return[count($return)][$value] = $row[$value];
		}
	}
	
	array_shift($return);//First Element is Blank
	return $return;
}

?>