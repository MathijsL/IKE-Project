<?php
	class ratingTable{
		private $table = array();
		
		function __construct() {
			$tableLines = file("recommender/table.txt");
			for($i=0; $i < count($tableLines); $i++) {
				$tableFractions = explode($tableLines[$i]," ");
				$table[$i] = array();
				for($j=0; $j < count($tableFractions); $i++) {
					$table[$i][$j] = $tableFractions[$j];
				}
			}
		}
		
		public function getRelatedArtist($artist,$amount) {
			include("connect.php");
			$artistId = 0;
			$sql = "SELECT id FROM artists WHERE name='".$artist."'";
			$result = mysql_query($query) or die(mysql_error());
			while($row = mysql_fetch_array($result)){
				$artistId = $row['id'];
			}
			$key = array_search($artistId, $this->table[0]);
			$searchArray = $this->table[$key];
			$returnArray = array();
			if(count($searchArray)-1 < $amount) {
				$amount = count($searchArray)-1;
			}
			for($i = 0; $i < $amount; $i++) {
				$artistId = max($searchArray);
				$sql = "SELECT name FROM artists WHERE id='".$artistId."'";
				$result = mysql_query($query) or die(mysql_error());
				while($row = mysql_fetch_array($result)){
					$returnArray[$i] = $row['name'];
				}
				$removeKey = array_search($artistId,$searchArray);
				unset($searchArray[$removeKey]);
			}
			return $returnArray;
		}
	}
?>