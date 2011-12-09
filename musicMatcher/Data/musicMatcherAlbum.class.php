<?php
	require_once("musicMatcherTrack.class.php");

	class musicMatcherAlbum{
		public $mbid;
		public $name;
		public $releaseDate;
		public $picture;
		public $listeners;
		public $playcount;
		public $tracks = array();
		
		public function __construct($artist,$album) {
			$json_info=json_decode(file_get_contents('http://ws.audioscrobbler.com/2.0/?method=album.getinfo&artist=' . rawurlencode($artist) . '&album=' .rawurlencode($album). '&api_key=b25b959554ed76058ac220b7b2e0a026&format=json'), true);
			$this->name = str_replace("\\", "", (str_replace("\"", "'", $json_info[album][name])));
			$this->mbid = $json_info[album][mbid];
			$this->releaseDate = str_replace("\\", "", (str_replace("\"", "'", $json_info[album][releasedate])));
			$this->picture = str_replace("\\", "", (str_replace("\"", "'", $json_info[album][image][2]["#text"])));
			$this->listeners = str_replace("\\", "", (str_replace("\"", "'", $json_info[album][listeners])));
			$this->playcount = str_replace("\\", "", (str_replace("\"", "'", $json_info[album][playcount])));
		
			if(isset($json_info[album][tracks][track][0])){
				foreach($json_info[album][tracks][track] as $t){
					array_push($this->tracks,new musicMatcherTrack($t));
				}	
			}			
		}
		
		public function getMbid() {
			return $this->mbid;
		}
		
		public function getName() {
			return $this->name;
		}
		
		public function getReleaseDate() {
			return $this->releaseDate;
		}
		
		public function getTracks() {
			return $this->tracks;
		}
		
		public function getPicture() {
			return $this->picture;
		}
		
		public function toString() {
			
			$result = "{\"mbid\":\"".$this->getMbid()."\", \"name\":\"" . $this->getName() . "\", \"releasedate\":\"".$this->getReleaseDate(). "\", \"picture\":\"".$this->getPicture()."\",\"tracks\":[";
			
			foreach($this->tracks as $track) {
				$result .= $track->toString().",";
			}
			if(count($this->tracks) > 0)
				$result = substr($result, 0, -1);
			
			return $result . "]}";
		}
	}
?>