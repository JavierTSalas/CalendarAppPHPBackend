<?php
include 'connect.php';

if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
	inputAssignments ();
} else {

	echo "This page is ment to be called with a post header </br>";
}
function inputAssignments() {

	
	$data = json_decode(file_get_contents('php://input'), true);
	var_dump($data);
	
	$ass_name = $data["Assignments"][0]["ass_name"];
	$db = new PDO('mysql:host=localhost;dbname=agenda', 'root', '');
	$stmt = $db->prepare("INSERT INTO classes (class_id,class_name) VALUES (?,?)");
	
	$stmt->bindValue(1,'');
	$stmt->bindParam(2,$ass_name);
	$stmt->execute();
	
	
	
}
?>