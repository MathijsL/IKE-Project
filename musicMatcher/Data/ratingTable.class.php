<?php
	class ratingTable{
		public $table = array();
		private $artists = array();
		
		function __construct() {
			$tableLines = file("recommender/table.txt");
			for($i=0; $i < count($tableLines); $i++) {
				$tableFractions = explode($tableLines[$i],"\t");
				$this->table[$i] = $tableFractions;
			}
		}
		
		public function getRelatedArtist($artist,$amount) {
			include("connect.php");
			$artId = 0;
			$sql = "SELECT id FROM artists WHERE name='".$artist."'";
			
			$result = mysql_query($sql) or die(mysql_error());
			$returnArray = array();
			
			while($row = mysql_fetch_array($result)){
				$artistId = $row['id'];
				break;
			}
			
			$key = array_search($artistId, $this->table[0]);
			
			//$key = array_search($artistId, $var);
			$searchArray = $this->table[$key];
			$returnArray = array();
			if(count($searchArray)-1 < $amount) {
				$amount = count($searchArray)-1;
			}
			//$returnArray[0] = count($this->table);
			
			for($i = 0; $i < $amount; $i++) {
				$artistId = max($searchArray);
				$query = "SELECT name FROM artists WHERE id='".$artistId."'";
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