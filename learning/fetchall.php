<?php


include 'connect.php';

$stmt = $db->query("SELECT  * FROM classes");

$results = $stmt->fetchAll();

foreach ($results as $row)
{
	
	$class_id = htmlentities($row['0']);
	$class_name = htmlentities($row['1']);
	
	echo $class_id." ".$class_name."<br>";
	
}
?>