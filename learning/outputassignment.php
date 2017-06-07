<?php
include 'connect.php';
include 'occur.php';
if ($_SERVER ['REQUEST_METHOD'] == 'GET') {
	showAssignments ();
} else {
	echo "This page is ment to be called with a get header </br>";
}
function showAssignments() {
	$db = new PDO ( 'mysql:host=localhost;dbname=agenda', 'root', '' );
	$stmt = $db->query ( "SELECT * FROM assignments WHERE done=0 ORDER by class_id ASC,due ASC" );
	$temp_array = array ();

	while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
		$temp_array [] = $row;
	}

	header ( 'Content-Type: application/json' );
	echo json_encode ( array (
			"Assignments" => $temp_array
	) );
}
?>