
<html>
<head>
<title>Bob's Auto Parts - Order Results</title>
</head>
<body>
	<h1>Bob's Auto Parts</h1>
	<h2>Order Results</h2>
	
<?php

include 'connect.php';

// Initialize Variables
$ass_name= $_POST["assignment_name"];
$class_id= $_POST["class_id"];
$date_assigned= $_POST["date_assigned"];
$due= date("Y-m-d\ H:i:s", strtotime($_POST["date_due"]));
$done= $_POST["done"];
$weight= $_POST["weight"];


echo "<p>Index Inserted!"."</br>";


echo "$ass_name <br> $class_id <br> $date_assigned <br> $due <br> $done <br> $weight";


$stmt = $db->prepare("INSERT INTO assignments (ass_id,ass_name,class_id,date_assigned,due,done,weight) VALUES (:ass_id,:ass_name,:class_id,:date_assigned,:due,:done,:weight)");

$stmt->bindValue(':ass_id','');
$stmt->bindParam(':ass_name',$ass_name);
$stmt->bindParam(':class_id',$class_id);
$stmt->bindParam(':date_assigned',$date_assigned);
$stmt->bindParam(':due',$due);
$stmt->bindParam(':done',$done);
$stmt->bindParam(':weight',$weight);
$stmt->execute();


?>
	
</body>
</html>