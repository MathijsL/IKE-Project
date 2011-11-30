<?php
	require_once("phpBrainz/phpBrainz.class.php");
	require_once("phpBrainz/phpBrainz.artist.class.php");
	
	
	class musicMatcherAlbum{
		public $mbid;
		public $name;
		public $releaseDate;
		public $picture;
		public $listeners;
		public $playcount;
		public $tracks = array();
		
		public function __construct($artist,$album) {
			//Get artist information
			$xmlReader = new XMLReader();
			$xmlReader->open('http://ws.audioscrobbler.com/2.0/?method=album.getinfo&artist=' . rawurlencode($artist) . '&album=' .rawurlencode($album). '&api_key=b25b959554ed76058ac220b7b2e0a026', null, LIBXML_NOBLANKS);
			
			//Read until the name and the image is found
			while ($xmlReader->read())
			{
				//Check for name
				if($xmlReader->name == "name"){
					//Enter name node
					$xmlReader->read();
					//Add node value
					if($xmlReader->value != "") {
						$this->name = $xmlReader->value;
					}
				}
				//Check for mbid
				if($xmlReader->name == "mbid"){
					//Enter name node
					$xmlReader->read();
					//Add node value
					if($xmlReader->value != "") {
						$this->mbid = $xmlReader->value;
					}
				}
				//Check for releasedate
				if($xmlReader->name == "releasedate"){
					//Enter name node
					$xmlReader->read();
					//Add node value
					if($xmlReader->value != "") {
						$this->releaseDate = $xmlReader->value;
					}
				}
				//Check for large image
				if($xmlReader->name == "image" && $xmlReader->getAttribute("size") == "large"){
					//Enter image node
					$xmlReader->read();
					//Add node value & break
					$this->picture = $xmlReader->value;
					break;
				}
			}
			while ($xmlReader->read())
			{
				if($xmlReader->name =="track") {
					$newTrack = "";
					$xmlReader->read();
					if($xmlReader->name == "name"){
						$xmlReader->read();
						if($xmlReader->value != ""){
							$newTrack .= "name[".$xmlReader->value."_";
							$xmlReader->read();
							$xmlReader->read();
							//if($xmlReader->name == "duration") {
								$xmlReader->read();
							//	if($xmlReader->value != "") {
							//		$newTrack .= "duration[".$xmlReader->value;
							//	}
							//}
							$newTrack .= "|";
						}
					}
					array_push($this->tracks,$newTrack);
				}				
				if($xmlReader->name == "artist"){
					$xmlReader->read();
				}
			}
			
			$xmlReader->close();
			
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
			$string = "mbid:".$this->getMbid()."]name[".$this->getName()."]releasedate[".$this->getReleaseDate()."]picture[".$this->getPicture()."]tracks[";
			foreach($this->tracks as $track) {
				$string .= $track;
			}
			return $string;
		}
	}
?>