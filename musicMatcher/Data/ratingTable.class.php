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
			include("connect.php");
			$sql = "SELECT id FROM artists WHERE name='".$artist."'";
			
			$result = mysql_query($sql) or die(mysql_error());
			$returnArray = array();
			
			while($row = mysql_fetch_array($result)){
				$artistId = $row['id'];
				break;
			}
			
			$key = array_search($artistId, $this->artists);
			
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
					$artistId = $this->artists[$key2];
					$query = "SELECT name FROM artists WHERE id='".$artistId."'";
					$result = mysql_query($query) or die(mysql_error());
					while($row = mysql_fetch_array($result)){
						$returnArray[$i] = $row['name']."\t".$max;
					}
					$searchArray[$key2] = -1;
				}
			}
			
			return $returnArray;
		}
	}
?>