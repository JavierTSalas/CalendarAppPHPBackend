<?php


include 'connect.php';

$stmt = $db->prepare("SELECT  * FROM classes WHERE class_name = :class_name");
$class = 'General Physics B';
$stmt->bindParam(':class_name',$class);
$stmt->execute();


while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
{
	
	echo htmlentities($row['class_id'])." ".htmlentities($row['class_name'])."<br>";
	
}
?>