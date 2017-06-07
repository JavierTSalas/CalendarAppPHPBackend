<?php
try {
	$db = new PDO('mysql:host=localhost;dbname=agenda', 'root', '');
	
} catch (Exception $e) {
	echo "An error has occured";
}
?>