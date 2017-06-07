<?php

include 'connect.php';


$stmt = $db->prepare("INSERT INTO classes (class_id,class_name) VALUES (?,?)");

$stmt->bindValue(1,'');
$stmt->bindValue(2,'Test Class Name');
$stmt->execute();

?>