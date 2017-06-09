<?php
include 'connect.php';

if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
	inputAssignments ();
} else {
	$testing = "Array
(
    [Assignments] => Array
        (
            [0] => Array
                (
                    [ass_name] => Test123
                    [class_id] => 2
                    [date_assigned] => 2017-08-23
                    [done] => false
                    [due] => 2017-08-23 13:34:54
                    [id] => 10
                    [weight] => 65
                )

        )

)
";
	$json = json_decode($testing,true);
	echo $json;
	echo "This page is ment to be called with a post header </br>";
}
function inputAssignments() {

	$req_dump = print_r( $_REQUEST, true );
	$dump = json_decode($req_dump, true);
	$fp = file_put_contents( 'request.log', $req_dump );
	$fp = file_put_contents( 'vardump.log', $dump);
	
	
	
	echo "inputAssignments";
	
	
	$ass_name= $_POST["name1"];
	
	$db = new PDO('mysql:host=localhost;dbname=agenda', 'root', '');
	$stmt = $db->prepare("INSERT INTO classes (class_id,class_name) VALUES (?,?)");
	
	$stmt->bindValue(1,'');
	$stmt->bindParam(2,$ass_name);
	$stmt->execute();
	
	
	
}
?>