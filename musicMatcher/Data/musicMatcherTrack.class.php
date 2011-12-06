<?php
	class musicMatcherTrack{
		public $mbid;
		public $name;
		public $duration;
		
		public function __construct($track) {
			$this->mbid = $track[mbid];
			$this->name = $track[name];
			$this->duration = $track[duration]; 
		}
		
		public function getMbid() {
			return $this->mbid;
		}
		
		public function getName() {
			return $this->name;
		}
		
		public function getDuration() {
			return $this->duration;
		}
		
		public function toString() {
			return "{\"name\":\"" . $this->name . "\", \"duration\":\"" . $this->duration . "\", \"mbid\":\"" . $this->mbid . "\"}";
		}
	}
?>