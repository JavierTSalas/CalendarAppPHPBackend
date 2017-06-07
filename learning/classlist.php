<?php
include 'connect.php';
if ($_SERVER ['REQUEST_METHOD'] == 'GET') {
	showClasses ();
} else {
	echo "This page is ment to be called with a get header </br>";
}
function showClasses() {
	$db = new PDO ( 'mysql:host=localhost;dbname=agenda', 'root', '' );
	$stmt = $db->query ( "SELECT * FROM classes" );
	$temp_array = array ();

	while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
		$temp_array [] = $row;
	}

	header ( 'Content-Type: application/json' );
	echo json_encode ( array (
			"Classes" => $temp_array
	) );
}
?>