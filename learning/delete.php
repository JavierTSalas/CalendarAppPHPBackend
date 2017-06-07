<?php

include 'connect.php';


$stmt = $db->prepare("DELETE FROM classes WHERE class_id = :class_id");

$stmt->bindValue(':class_id','6');
$stmt->execute();

?>