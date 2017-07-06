<?php
include 'connect.php';

if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
	header ( ("HTTP/1.1 200 OK Recieved " . getActionsCount () . " Actions of type:" . actionIdToString ( determineAction () )) );
	$actionId = determineAction ();
	actionIdExec ( $actionId );
} else {
	"HTTP/1.1 449 Retry With POST header this page is ment to be called to POST item to the assignments table";
	echo "This page is ment to be called with a post header </br>";
}
function actionIdToString($i) {
	switch ($i) {
		case 1 :
			return "ADD";
			break;
		case 2 :
			return "REMOVE";
			break;
		case 3 :
			return "EDIT";
			break;
		case 4 :
			return "SET_DONE";
			break;
	}
}
function actionIdExec($i) {
	switch ($i) {
		case 1 :
			addAssingments ();
			break;
		case 2 :
			removeAssingments ();
			break;
		case 3 :
			editAssingments ();
			break;
		case 4 :
			setdoneAssingments ();
			break;
	}
}
function getActionsCount() {
	$data = json_decode ( file_get_contents ( 'php://input' ), true );

	return sizeof ( $data ["ADD"] );
}
function determineAction() {
	$data = json_decode ( file_get_contents ( 'php://input' ), true );

	if (array_key_exists ( "ADD", $data )) {
		return 1;
	}

	if (array_key_exists ( "REMOVE", $data )) {
		return 2;
	}

	if (array_key_exists ( "EDIT", $data )) {
		return 3;
	}

	if (array_key_exists ( "SET_DONE", $data )) {
		return 4;
	}

	// $ass_name = $data ["Assignments"] [0] ["ass_name"];
	/*
	 * $db = new PDO('mysql:host=localhost;dbname=agenda', 'root', '');
	 * $stmt = $db->prepare("INSERT INTO classes (class_id,class_name) VALUES (?,?)");
	 *
	 * $stmt->bindValue(1,'');
	 * $stmt->bindParam(2,$ass_name);
	 * $stmt->execute();
	 *
	 *
	 */
}
function addAssingments() {
	$data = json_decode ( file_get_contents ( 'php://input' ), true );
	echo "I've got something to say...";
	for($i = 0; $i < sizeof ( $data ["ADD"] ); $i ++) {
		$ass_name = $data ["ADD"] [$i] ["ass_name"];
		$class_id = $data ["ADD"] [$i] ["class_id"];
		$date_assigned = $data ["ADD"] [$i] ["date_assigned"];
		$due = $data ["ADD"] [$i] ["due"];
		$done = $data ["ADD"] [$i] ["done"];
		$weight = $data ["ADD"] [$i] ["weight"];

		$db = new PDO ( 'mysql:host=localhost;dbname=agenda', 'root', '' );
		$stmt = $db->prepare ( "INSERT INTO assignments (ass_id,ass_name,class_id,date_assigned,due,done,weight) VALUES (?,?,?,?,?,?,?)" );

		$stmt->bindValue ( 1, '' );
		$stmt->bindParam ( 2, $ass_name );
		$stmt->bindParam ( 3, $class_id );
		$stmt->bindParam ( 4, $date_assigned );
		$stmt->bindParam ( 5, $due );
		$stmt->bindParam ( 6, $done );
		$stmt->bindParam ( 7, $weight );
		$stmt->execute ();
	}
	echo "That's all folks";
}
function removeAssingments() {
	$data = json_decode ( file_get_contents ( 'php://input' ), true );
	echo "I've got something to say...";
	// for($i = 0; $i < sizeof ( $data ); $i ++) {
	$ass_id = $data ["ADD"] ["ass_id"];
	$db = new PDO ( 'mysql:host=localhost;dbname=agenda', 'root', '' );
	$stmt = $db->prepare ( "DELETE FROM assignments WHERE ass_id = :ass_id" );
	$stmt->bindParam ( ':ass_id', $ass_id );
	$stmt->execute ();
	// }
	echo "That's all folks";
}
function editAssingments() {
	$data = json_decode ( file_get_contents ( 'php://input' ), true );

	$ass_name = $data ["ADD"] ["ass_id"];
	$elements = [
			"ass_id",
			"ass_name",
			"class_id",
			"date_assigned",
			"due",
			"done,weight"
	];
	for($i = 0; $i < count ( $elements ); $i ++) {
		if (array_key_exists ( $elements [$i], $data ["ADD"] )) {
			$value = $data ["ADD"] [$elements [$i]];
			$stmt = $db->prepare ( "UPDATE assignments SET ", $elements [$i], "=", $value, " WHERE ass_id = :ass_id" );
			$stmt->execute ();
		}
	}
}
function setdoneAssingments() {
	$data = json_decode ( file_get_contents ( 'php://input' ), true );
	echo "I've got something to say...";
	// for($i = 0; $i < sizeof ( $data ); $i ++) {
	$ass_id = $data ["ADD"] ["ass_id"];
	$db = new PDO ( 'mysql:host=localhost;dbname=agenda', 'root', '' );
	$stmt = $db->prepare ( "UPDATE assignments SET done=1 WHERE ass_id = :ass_id" );
	$stmt->bindParam ( ':ass_id', $ass_id );
	$stmt->execute ();
	// }
}

?>