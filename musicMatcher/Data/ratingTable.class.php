<?php
	class ratingTable{
		public $table = array();
		private $artists = array();
		private $tableLines = array();
		
		function __construct() {
			$this->tableLines = file("recommender/table.txt");
			$this->artists = explode("\t",$this->tableLines[0]);
		}
		
		public function getRelatedArtist($artist,$amount) {
			
			$key = array_search($artist, $this->artists);
			
			if($key != 0) {
				$searchArray = explode("\t",$this->tableLines[$key]);
				$searchArray[0] = -1;
				$returnArray = array();
				if(count($searchArray)-1 < $amount) {
					$amount = count($searchArray)-1;
				}
				
				for($i = 0; $i < $amount; $i++) {
					$max = max($searchArray);
					$key2 = array_search($max,$searchArray);
					$artist2 = $this->artists[$key2];
					$returnArray[$i] = $artist2.";".$max;
					$searchArray[$key2] = -1;
				}
			}
			
			return $returnArray;
		}
	}
?>