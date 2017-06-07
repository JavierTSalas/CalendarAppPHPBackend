<?php
include 'connect.php';
include 'reoccuring_class.php';

// For every assignment row not done (done=0) create a assignment and call the insertIntoBigArray for it.
$stmt = $db->query ( "SELECT  * FROM reoccuring" );
while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
	$recoccuring = new reoccuring_class ( htmlentities ( $row ['reoccur_id'] ), htmlentities ( $row ['name'] ), htmlentities ( $row ['class_id'] ), htmlentities ( $row ['days_between'] ), htmlentities ( $row ['days_of_week'] ), htmlentities ( $row ['times'] ), htmlentities ( $row ['start_date'] ), htmlentities ( $row ['end_date'] ), htmlentities ( $row ['next_occurance'] ) );
	$recoccuring->addReoccuringEvent ();
}

?>