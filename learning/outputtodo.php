<?php
include 'connect.php';
if ($_SERVER ['REQUEST_METHOD'] == 'GET') {
	showTODO ();
} else {
	echo "This page is ment to be called with a get header </br>";
}
function showTODO() {
	$db = new PDO ( 'mysql:host=localhost;dbname=agenda', 'root', '' );
	$stmt = $db->query ( "SELECT  * FROM todo WHERE done=0" );
	$temp_array = array ();

	while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
		$temp_array [] = $row;
	}

	header ( 'Content-Type: application/json' );
	echo json_encode ( array (
			"TODO" => $temp_array
	) );
}
?>