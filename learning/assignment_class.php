<?php
class assignment_class {
	private $ass_id;
	private $ass_name;
	private $class_id;
	private $date_assigned;
	private $due;
	private $done;
	private $weight;
	public function __construct($a1, $a2, $a3, $a4, $a5, $a6, $a7) {
		$this->ass_id = $a1;
		$this->ass_name = $a2;
		$this->class_id = $a3;
		$this->date_assigned = $a4;
		$this->due = $a5;
		$this->done = $a6;
		$this->weight = $a7;
	}
	public function isDone() {
		return $this->done;
	}
	public function __destruct() {
	}
	public function __toString() {
		return $this->ass_id . " | " . $this->ass_name . " | " . $this->class_id . " | " . $this->date_assigned . " | " . $this->due . " | " . $this->done . " | " . $this->weight . " </br>";
	}
}
?>