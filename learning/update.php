<?php

include 'connect.php';


$stmt = $db->prepare("UPDATE classes set class_name = :class_name WHERE class_id = :class_id");

$stmt->bindValue(':class_id','6');
$stmt->bindValue(':class_name','Updated Name');
$stmt->execute();

?>