<?php

/* MYSQL Query Handler */

function sanitize($value, $noEntity = true, $noStrip = ''){//Defend Against Injections
	global $connection;
	
	if ($noEntity){
		return mysqli_real_escape_string($connection, htmlentities(stripslashes($value)));
	}
	
	else{
		return mysqli_real_escape_string($connection, strip_tags(stripslashes($value), $noStrip));
	}
}

function getNumOf($table, $columns, $items){//Get Number Of Instances In Database
	global $connection;
	
	$query = "SELECT * FROM `$table` WHERE ";
	$query = $query . "`" . $columns[0] . "`" . "=" . $items[0];
	
	for($i = 1; $i < count($columns); $i++){
		$query = $query . " AND " . "`" . $columns[$i] . "`" . "=" . $items[$i];
	}

	$duplicates = mysqli_query($connection, $query) or die(mysqli_error($connection));
	return mysqli_num_rows($duplicates);
}

function insertInto($table, $columns, $items){//Insert Items Into Database
	global $connection;
	
	$query = "INSERT INTO `$table` (";//Build Query String
	$query = $query . "`" . $columns[0] . "`";

	for($i = 1; $i < count($columns); $i++){
		$query = $query . ", " . "`" . $columns[$i] . "`";
	}
	
	$query = $query . ") VALUES (";
	$query = $query . $items[0];
	
	for($i = 1; $i < count($items); $i++){
		$query = $query . ", " . $items[$i];
	}
	
	$query = $query . ")";
	
	mysqli_query($connection, $query) or die(mysqli_error($connection));
	return true;
}

//Precondition: $values are valid and $columns correspond one-to-one with $items
//Precondition: $columns and $items are non-empty
function selectFrom($table, $values, $columns, $items){//Make A Database Selection
	global $connection;
	
	$query = "SELECT * FROM `$table`";
	
	if (count($columns) > 0){
		$query = $query . " WHERE " . "`" . $columns[0] . "`" . "=" . $items[0];
		
		for($i = 1; $i < count($columns); $i++){
			$query = $query . " AND " . "`" . $columns[$i] . "`" . "=" . $items[$i];
		}
	}
	
	$selection = mysqli_query($connection, $query) or die(mysqli_error($connection));
	$return = array();
	
	while ($row = mysqli_fetch_assoc($selection)){
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
function updateIn($table, $columns, $items, $conditions, $values){//Update Rows In Database
	global $connection;
	
	$query = "UPDATE `$table` SET ";//Build Query String
	$query = $query . "`" . $columns[0] . "`" . "=" . $items[0];

	for($i = 1; $i < count($columns); $i++){
		$query = $query . ", " . "`" . $columns[$i] . "`" . "=" . $items[$i];
	}
	
	$query = $query . " WHERE ";
	$query = $query . "`" . $conditions[0] . "`" . "=" . $values[0];
	
	for($i = 1; $i < count($conditions); $i++){
		$query = $query . " AND " . "`" . $conditions[$i] . "`" . "=" . $values[$i];
	}
	
	mysqli_query($connection, $query) or die(mysqli_error($connection));
	return true;
}

function deleteFrom($table, $columns, $items){//Delete certain items from database
	global $connection;
	
	$query = "DELETE FROM `$table` WHERE ";
	$query = $query . "`" . $columns[0] . "`" . "=" . $items[0];
	
	for($i = 1; $i < count($columns); $i++){
		$query = $query . " AND " . "`" . $columns[$i] . "`" . "=" . $items[$i];
	}

	mysqli_query($connection, $query) or die(mysqli_error($connection));
	return true;
}

//Precondition: primary key is called 'id' and should be dropped
function copyEntries($tableFrom, $tableTo, $columns, $items, $conditions, $values){//Copy entries between tables, updating as necessary
	global $connection;
	
	$query = "CREATE TABLE `temp` ENGINE=InnoDB SELECT * FROM `$tableFrom`";//Create temporary table for transfer
	
	if (count($conditions) > 0){
		$query = $query . " WHERE " . "`" . $conditions[0] . "`" . "=" . $values[0];
		
		for($i = 1; $i < count($conditions); $i++){
			$query = $query . " AND " . "`" . $conditions[$i] . "`" . "=" . $values[$i];
		}
	}
	
	mysqli_query($connection, $query) or die(mysqli_error($connection));
	
	$query = "UPDATE `temp` SET ";//Update desired fields in temp table
	$query = $query . "`" . $columns[0] . "`" . "=" . $items[0];

	for($i = 1; $i < count($columns); $i++){
		$query = $query . ", " . "`" . $columns[$i] . "`" . "=" . $items[$i];
	}
	
	mysqli_query($connection, $query) or die(mysqli_error($connection));
	
	$query = "UPDATE `temp` SET `id`=0";//drop original primary key
	
	mysqli_query($connection, $query) or die(mysqli_error($connection));
	
	$query = "INSERT IGNORE INTO `$tableTo` SELECT * FROM `temp`";//Transfer from temp to destination
	
	mysqli_query($connection, $query) or die(mysqli_error($connection));
	
	$query = "DROP TABLE `temp`";
	
	mysqli_query($connection, $query) or die(mysqli_error($connection));
	return true;
}

?>