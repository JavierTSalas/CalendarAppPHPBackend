<?php
include 'connect.php';

$stmt = $db->prepare( "select cast(min(due) as date) from assignments WHERE class_id = :class_id" );
$stmt->bindValue ( ':class_id', '3' );
$stmt->execute();
$results = $stmt->fetch();
echo $results['cast(min(due) as date)'] . "<br>";

?>