<?php

/* MySQL Query Handler */

function sanitize($value){//Defend Against Injections
	return mysql_real_escape_string(htmlentities($value));
}

function getNumOf($table,$columns,$items){//Get Number Of Instances In Database
	$query = "SELECT * FROM $table WHERE ";
	$query = $query . $columns[0] . "=" . $items[0];
	
	for($i = 1; $i < count($columns); $i++){
		$query = $query . " AND " . $columns[$i] . "=" . $items[$i];
	}

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

//Precondition: $values are valid and $columns correspond one-to-one with $items
//Precondition: $columns and $items are non-empty
function selectFrom($table,$values,$columns,$items){//Make A Database Selection
	$query = "SELECT * FROM $table";
	
	if (count($columns) > 0){
		$query = $query . " WHERE " . $columns[0] . "=" . $items[0];
		
		for($i = 1; $i < count($columns); $i++){
			$query = $query . " AND " . $columns[$i] . "=" . $items[$i];
		}
	}
	
	$selection = mysql_query($query) or die(mysql_error());
	$return = array();
	
	while ($row = mysql_fetch_assoc($selection)){
		array_push($return, $row);
		foreach ($return[count($return)-1] as $key => $value){
			if (in_array($key, $values) == False){
				unset($return[count($return)-1][$key]);
			}
		}
	}
	
	return $return;
}

//Precondition: $values are valid and $columns correspond one-to-one with $items
//Precondition: $conditions and $values are non-empty
function updateIn($table,$columns,$items,$conditions,$values){//Update Rows In Database
	$query = "UPDATE $table SET ";//Build Query String
	$query = $query . $columns[0] . "=" . $items[0];

	for($i = 1; $i < count($columns); $i++){
		$query = $query . ", " . $columns[$i] . "=" . $items[$i];
	}
	
	$query = $query . " WHERE ";
	$query = $query . $conditions[0] . "=" . $values[0];
	
	for($i = 1; $i < count($conditions); $i++){
		$query = $query . " AND " . $conditions[$i] . "=" . $values[$i];
	}
	
	mysql_query($query) or die(mysql_error());
	return true;
}

?>