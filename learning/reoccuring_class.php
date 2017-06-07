<?php
include 'connect.php';
class reoccuring_class {
	private $reoccur_id;
	private $name;
	private $class_id;
	private $days_between;
	private $days_of_week;
	private $times;
	private $start_date;
	private $end_date;
	private $next_occurance;
	private $D2FormatArray;
	public function __construct($a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8, $a9) {
		$this->reoccur_id = $a1;
		$this->name = $a2;
		$this->class_id = $a3;
		$this->days_between = $a4;
		$this->days_of_week = $a5;
		$this->times = $a6;
		$this->start_date = $a7;
		$this->end_date = $a8;
		$this->next_occurance = $a9;
		$this->D2FormatArray = new SplFixedArray ( 7 );
	}
	public function timesLeft() {
		return $this->times;
	}
	public function nextOcc() {
		return $this->next_occurance;
	}
	public function __destruct() {
	}
	public function __toString() {
		return $this->reoccur_id . " | " . $this->class_id . " | " . $this->days_between . " | " . $this->days_of_week . " | " . $this->times . " | " . $this->start_date . " | " . $this->end_date . " | " . $this->next_occurance . " </br>";
	}
	
	// Return datediff + 1
	private function getDateDiff() {
		$db = new PDO ( 'mysql:host=localhost;dbname=agenda', 'root', '' );
		$stmt = $db->prepare ( "select (TO_DAYS(end_date) - TO_DAYS(start_date))+1 as date_diff from reoccuring where reoccur_id=:reoccur_id" );
		$stmt->bindParam ( ':reoccur_id', $this->reoccur_id );
		$stmt->execute ();
		$results = $stmt->fetch ();
		return $results ['date_diff'];
	}
	
	// Returns time given id and D1
	private function getTimesD1Format($D1) {
		$date_diff = $this->getDateDiff ();
		//echo "Date_diff = $date_diff </br>";
		$this->times = ($date_diff / ($D1 + 1));
		if (round ( $this->times, 0 ) == $this->times) {
			$SUM = ($this->times + 1); // We add one since this means that the event should occur on end_date
			$this->times = $SUM;
			//echo "From getTimesD1Format returning " . $SUM, "</br>";
			return $SUM;
		} else {
			//echo "From getTimesD1Format returning ", round ( $this->times, 0 ), "</br>";
			return round ( $this->times, 0 );
		}
	}
	
	// TODO: Implement logic if days < 10000000 then call getTimesD1Format
	private function getDNumber() {
		$db = new PDO ( 'mysql:host=localhost;dbname=agenda', 'root', '' );
		$stmt = $db->prepare ( "select days_of_week, if(days_of_week=10000000,days_between,days_of_week) as days from reoccuring where reoccur_id=:reoccur_id" );
		$stmt->bindParam ( ':reoccur_id', $this->reoccur_id );
		$stmt->execute ();
		$results = $stmt->fetch ();
		return $results ['days'];
	}
	
	// Populates the array with 0 or 1 if the event is happening on that day
	private function populateWeekArray($arr, $int) {
		for($i = 1; $i < 8; $i ++) {
			$mod = pow ( 10, $i );
			$ans = $int % $mod / ($mod / 10);
			// We work backwards since the last number is saturday
			$arr [7 - $i] = $ans;
			// Subtract it so we can keep modding nicely
			$int -= $int % $mod;
		}
	}
	
	// Counts the number of times the event is happening every week
	private function sumOfWeekArray($arr) {
		$runningsum = 0;
		for($i = 0; $i < count ( $arr ); $i ++) {
			$runningsum += $arr [$i];
		}
		return $runningsum;
	}
	
	// Return day of week where sunday is 0
	private function getWeekday($date) {
		return date ( 'w', strtotime ( $date ) );
	}
	
	// Returns time given id and D2
	private function getTimesD2Format($D2) {
		$this->populateWeekArray ( $this->D2FormatArray, $this->days_of_week );
		// The remainder is the days that don't make up a full week
		$remainder = ($this->getDateDiff () % 7); // We have to sub
		return $this->getN1 ( $this->D2FormatArray, $remainder ) + $this->getN2 ( $this->D2FormatArray, $remainder );
	}
	
	// Part one of getTimesD2Format
	private function getN1($array, $remainder) {
		$N1 = (floor ( $this->getDateDiff () / 7 ) * $this->sumOfWeekArray ( $array )); // This is for the full week in between
		//echo "Returning N1:$N1 </br>";
		return $N1;
	}
	
	// Part two of getTimesD2Format
	private function getN2($array, $remainder) {
		$N2Sum = 0;
		$Startpos = $this->getWeekday ( $this->start_date );
		//echo "End:" . ($Startpos + $remainder) % 7, "</br>";
		for($i = $Startpos; $i <= ($Startpos + $remainder) - 1; $i ++)
			if ($array [$i % 7] == 1)
				$N2Sum ++;
		//echo "Returning N2Sum:$N2Sum </br>";
		return $N2Sum;
	}
	
	// Returns the distance between the index $D2FormatArray[$posinweek] and $D2FormatArray[n] where $D2FormatArray[n]=1
	private function findNextInArray($posInWeek) {
		$daysbetweennextevent = 0;
		// Add one since we want to check the days after $posInWeek
		for($i = $posInWeek + 1; $i < $posInWeek + 8; $i ++) {
			if ($this->D2FormatArray [$i % 7] == 1)
				return ($i - $posInWeek);
		}
	}
	
	// Returns the next event given D2
	private function NextEventD2() {
		$this->populateWeekArray ( $this->D2FormatArray, $this->days_of_week );
		$dayofweekofprevious = $this->getWeekday ( $this->next_occurance );
		$days = $this->findNextInArray ( $dayofweekofprevious ); // Since were not counting the last day as a day (i.e want 3 between)
		$datestr = $days . ' days';
		$prev_date = new DateTime ( $this->next_occurance );
		date_add ( $prev_date, date_interval_create_from_date_string ( $datestr ) );
		return $prev_date->format ( 'Y-m-d' );
	}
	
	// Returns the next event given D1
	private function NextEventD1() {
		$date = new DateTime ( $this->next_occurance );
		$days = $this->days_between + 1; // Since were not counting the last day as a day (i.e want 3 between)
		$datestr = $days . ' days';
		date_add ( $date, date_interval_create_from_date_string ( $datestr ) );
		return $date->format ( 'Y-m-d' );
	}
	
	// Generates next_occurance
	// If the default value is set then set to the start date
	// If there already is a value then call the correct function
	private function generateNext() {
		if ($this->firstTimeRunning ()) {
			$this->next_occurance = $this->start_date;
			//echo "No previous occurance date. Setting to start_date</br>";
		} else {
			$DNumber = $this->getDNumber ( $this->reoccur_id );
			if ($DNumber < 9999999) {
				$this->next_occurance = $this->NextEventD1 ();
				//echo "Called NextEventD1 </br>";
			} else {
				$this->next_occurance = $this->NextEventD2 ();
				//echo "Called NextEventD2 </br>";
			}
		}
		//echo "Running updateNextOccurance & decrementTimes </br>";
		$this->updateNextOccurance ();
		$this->decrementTimes ();
	}
	
	// Adds event to TODO list
	private function addEventToTODO() {
		$db = new PDO ( 'mysql:host=localhost;dbname=agenda', 'root', '' );
		$stmt = $db->prepare ( "INSERT INTO TODO (todo_name,due) VALUES (:todo_name,:due)" );
		$stmt->bindParam ( ':todo_name', $this->name );
		$stmt->bindParam ( ':due', $this->next_occurance );
		$stmt->execute ();
		//echo "Event $this->name added to TODO list due at $this->next_occurance </br>";
	}
	
	// Bool first time running - Checks for default value in next_occurance
	private function firstTimeRunning() {
		$default_date = new DateTime ( '0000-0-00' );
		$date_next = new DateTime ( $this->next_occurance );
		$interval = $default_date->diff ( $date_next );
		return ($interval->days == 0);
	}
	
	// Adds event to Assignmnets list
	private function addEventToAssignments() {
		$db = new PDO ( 'mysql:host=localhost;dbname=agenda', 'root', '' );
		$stmt = $db->prepare ( "INSERT INTO ASSIGNMENTS (ass_name,class_id,date_assigned,due) VALUES (:ass_name,:class_id,:date_assigned,:due)" );
		$now = new DateTime ();
		$date_now = $now->format ( 'Y-m-d' );
		$stmt->bindParam ( ':ass_name', $this->name );
		$stmt->bindParam ( ':class_id', $this->class_id );
		$stmt->bindParam ( ':date_assigned', $date_now );
		$stmt->bindParam ( ':due', $this->next_occurance );
		//echo "Event $this->name added to ASSIGNMENTS list assigned at $date_now due at $this->next_occurance for class $this->class_id</br>";
		$stmt->execute ();
	}
	
	// Given a reoccur_id will return how many times the event needs to occur
	private function generate_times() {
		//echo "Generating time for ID:" . $this->reoccur_id . "</br>";
		if ($this->firstTimeRunning ()) {
			$DNumber = $this->getDNumber ( $this->reoccur_id );
			if ($DNumber < 9999999) {
				return $this->getTimesD1Format ( $DNumber );
			} else {
				return $this->getTimesD2Format ( $DNumber );
			}
		}
	}
	
	// Sets next_occurance to member variable next_occurance
	private function updateNextOccurance() {
		$db = new PDO ( 'mysql:host=localhost;dbname=agenda', 'root', '' );
		$stmt = $db->prepare ( "update reoccuring set next_occurance=:next_occurance where reoccur_id=:reoccur_id" );
		$stmt->bindParam ( ':reoccur_id', $this->reoccur_id );
		$stmt->bindParam ( ':next_occurance', $this->next_occurance );
		//echo "updateNextOccurance - Set id " . $this->reoccur_id . " next_occurance=" . $this->next_occurance . "</br>";
		$stmt->execute ();
	}
	
	// Bool needs to run today
	// TODO: This breaks if it is not ran everyday. Implement fix by:
	// Checking every day or if it is past due (ie -1 days has passed)
	private function needsToRunToday() {
		$now = new DateTime ( date ( "Y-m-d" ) );
		$date_next = new DateTime ( $this->next_occurance );
		$interval = $now->diff ( $date_next );
		if ($interval->days == 0) {
			//echo "Reoccur_id $this->reoccur_id";
			//echo " Needs to run today interval $interval->days between ", date_format ( $now, 'Y-m-d' ), " and  ", date_format ( $date_next, 'Y-m-d' ), "</br>";
		}
		return ($interval->days == 0);
	}
	
	// Sets the times to times=times-1
	private function decrementTimes() {
		if ($this->times > 0) {
			$db = new PDO ( 'mysql:host=localhost;dbname=agenda', 'root', '' );
			$stmt = $db->prepare ( "update reoccuring set times=:times where reoccur_id=:reoccur_id" );
			$stmt->bindParam ( ':reoccur_id', $this->reoccur_id );
			$decrementvalue = $this->times - 1;
			$this->times = $decrementvalue;
			$stmt->bindParam ( ':times', $this->times );
			$stmt->execute ();
			//echo "decrementTimes called </br>";
		}
	}
	
	// Generate times and write it
	private function populateTimes() {
		// Checks if the times needs to be generating by seeing if the default value is assigned
		if ($this->times == null) {
			$db = new PDO ( 'mysql:host=localhost;dbname=agenda', 'root', '' );
			$stmt = $db->prepare ( "update reoccuring set times=:times where reoccur_id=:reoccur_id" );
			$stmt->bindParam ( ':reoccur_id', $this->reoccur_id );
			$this->times = $this->generate_times ( $this->reoccur_id );
			$stmt->bindParam ( ':times', $this->times );
			$stmt->execute ();
			//echo "populateTimes - Set id ", $this->reoccur_id, " times=", $this->times, "</br>";
		}
	}
	
	// Main function that will be called from outside the class
	public function addReoccuringEvent() {
		// If we haven't generated a next_occurance then set it to the start_date
		if ($this->firstTimeRunning ()) {
			$this->populateTimes (); // Generate times
			$this->generateNext (); // Generate next_occurance
			//echo "First time running </br>";
		}
		// If today = next_occurance and we still need to run the event n times
		if ($this->times > 0 && $this->needsToRunToday ()) {
			$this->generateNext ();
			// Non school acitivity, add to TODO
			//echo "Classs_id ", $this->class_id, "</br>";
			if ($this->class_id == 0) {
				//echo "Adding event to todo </br>";
				$this->addEventToTODO ();
			} else {
				//echo "Adding event to assignments </br>";
				$this->addEventToAssignments ();
			}
		}
	}
}
?>