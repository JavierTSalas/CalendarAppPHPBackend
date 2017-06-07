<?php
include 'connect.php';
include 'assignment_class.php';

// TODO: Fetch for only two weeks ahead OR if weight it substainly big (50+?)

// This function takes a 2D array of arr[class_id-1][new assignmnent_class] and add its to the corepsonding index
function insertIntoBigArray($bigarray, $class_id, $assignment) {
	// Get the index that the class in the 2D array
	$index = $class_id - 1;
	// Get the size so we can make room for this new assignment
	$size_of_index = count ( $bigarray );
	// Increase the size by 1
	$bigarray [$index]->setSize ( $size_of_index + 1 );
	// Set the $assignment to the second to last element
	$bigarray [$index] [$size_of_index - 1] = $assignment;

	/*
	 * So you might be asking yourself why I'm allocating +1 indexs for $assignment
	 * This is because when the the count($bigarray[$index]) when there are no assignments for the class
	 * you are trying to count null elements so we have to create a array there which will let us inrease the size by one
	 * doing it the other way was giving me a fatal error
	 */
}

// Return the min date for a given class_id
function getMinDateOfClass($class_id) {
	$db = new PDO ( 'mysql:host=localhost;dbname=agenda', 'root', '' );
	$stmt = $db->prepare ( "select cast(min(due) as date) from assignments WHERE class_id = :class_id" );
	$stmt->bindParam ( ':class_id', $class_id );
	$stmt->execute ();
	$results = $stmt->fetch ();
	return $results ['cast(min(due) as date)'];
}

// Updates the correspding TODO row given a class_id. These rows are hardcoded and will not change so it's safe to do this
function updateTODOrow($class_id, $done) {
	$db = new PDO ( 'mysql:host=localhost;dbname=agenda', 'root', '' );
	$stmt = $db->prepare ( "UPDATE todo set done = :done , due = :due_date WHERE todo_id = :class_id" );
	$stmt->bindParam ( ':done', $done );
	$mindate = getMinDateOfClass ( $class_id );
	$stmt->bindParam ( ':due_date', $mindate );
	$stmt->bindParam ( ':class_id', $class_id );
	$stmt->execute ();
}

// Create 2D array
// TODO:set length = count(distinct class_id) from classes
$bigarray = new SplFixedArray ( 5 );
for($i = 0; $i < count ( $bigarray ); $i ++) {
	$bigarray [$i] = new SplFixedArray ( 1 );
}

// For every assignment row not done (done=0) create a assignment and call the insertIntoBigArray for it.
$stmt = $db->query ( "SELECT  * FROM assignments WHERE done=0" );
while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
	$assignment = new assignment_class ( htmlentities ( $row ['ass_id'] ), htmlentities ( $row ['ass_name'] ), htmlentities ( $row ['class_id'] ), htmlentities ( $row ['date_assigned'] ), htmlentities ( $row ['due'] ), htmlentities ( $row ['done'] ), htmlentities ( $row ['weight'] ) );
	if (! $assignment->isDone ())
		insertIntoBigArray ( $bigarray, $row ['class_id'], $assignment );
}

// For every assignment in bigarray, see if theres an assignment due then update TODO with the min date and set done=0
for($index = 0; $index < count ( $bigarray ); $index ++) {
	if (1 < count ( $bigarray [$index] )) {
		updateTODOrow ( $index + 1, 0 ); // Plus one since we started at 0
	} else {
		updateTODOrow ( $index + 1, 1 ); // Plus one since we started at 0
	}
}
echo "done";
?>