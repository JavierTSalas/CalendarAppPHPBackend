<?php


include 'connect.php';

$stmt = $db->query("SELECT  * FROM classes");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	
	echo htmlentities($row['class_id'])." ".htmlentities($row['class_name'])."<br>";
	
}

?>