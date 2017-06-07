<?php
include 'connect.php';
if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
	$AssingOrTODO = $_POST ["AssingOrTODO"];
	if ($AssingOrTODO == 0)
		finishAssignmnet ();
	if ($AssingOrTODO == 1)
		finishTODO ();
} else { 
	echo "This page is ment to be called with a post header </br>";
}
function finishAssignmnet() {
	$ass_id = $_POST ["ass_id"];
	
	$db = new PDO ( 'mysql:host=localhost;dbname=agenda', 'root', '' );
	$stmt = $db->prepare ( "update assignments set done=:done where ass_id=:ass_id" );
	$stmt->bindValue ( ':done', '1' );
	$stmt->bindParam ( ':ass_id', $ass_id );
	$stmt->execute ();
}
function finishAssignmnet() {
	$todo_id = $_POST ["todo_id"];
	
	$db = new PDO ( 'mysql:host=localhost;dbname=agenda', 'root', '' );
	$stmt = $db->prepare ( "update todo set done=:done where todo_id=:todo_id" );
	$stmt->bindValue ( ':done', '1' );
	$stmt->bindParam ( ':todo_id', $todo_id );
	$stmt->execute ();
}

?>