<?php
	require_once("phpBrainz/phpBrainz.class.php");
	require_once("phpBrainz/phpBrainz.artist.class.php");
	require_once("musicMatcherAlbum.class.php");
	
	class musicMatcherArtist{
		public $mbid;
		public $name;
		public $beginDate;
		public $endDate;
		public $type;
		public $albums = array();
		public $picture;
		public $releasesCount;
		public $releasesOffset;
		public $artistObject;
		
		public function __construct($artist) {
			//Get artist information
			$xmlReader = new XMLReader();
			$xmlReader->open('http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist=' . rawurlencode($artist) . '&api_key=b25b959554ed76058ac220b7b2e0a026', null, LIBXML_NOBLANKS);
			
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
				//Check for large image
				if($xmlReader->name == "image" && $xmlReader->getAttribute("size") == "large"){
					//Enter image node
					$xmlReader->read();
					//Add node value & break
					$this->picture = $xmlReader->value;
					break;
				}
			}
			$xmlReader->close();
			
			//Get artist top albums
			$xmlReader = new XMLReader();
			$xmlReader->open('http://ws.audioscrobbler.com/2.0/?method=artist.gettopalbums&artist=' . rawurlencode($artist) . '&api_key=b25b959554ed76058ac220b7b2e0a026&limit=20', null, LIBXML_NOBLANKS);
			while ($xmlReader->read())
			{
				if($xmlReader->name == "name"){
					$xmlReader->read();
					if($xmlReader->value != ""){
						array_push($this->albums,new musicMatcherAlbum($artist,$xmlReader->value));
					}
				}
				if($xmlReader->name == "artist"){
					$xmlReader->read();
				}
			}		
			//Close reader and return found tag
			$xmlReader->close();
			
			
			//MusicBrainz info
			
			//Create new phpBrainz object
			$phpBrainz = new phpBrainz();
					
			$this->artistObject = $phpBrainz -> getArtist($this->mbid);
			$this->beginDate = $this->artistObject -> getBeginDate();
			$this->endDate = $this->artistObject -> getEndDate();
			$this->type = $this->artistObject -> getType();
			$this->releasesCount = $this->artistObject -> getReleasesCount();
			$this->releasesOffset = $this->artistObject -> getReleasesOffset();
		}
		
		public function getMbid() {
			return $this->mbid;
		}
		
		public function getName() {
			return $this->name;
		}
		
		public function getBeginDate() {
			return $this->beginDate;
		}
		
		public function getEndDate() {
			return $this->endDate;
		}
		
		public function getType() {
			return $this->type;
		}
		
		public function getAlbums() {
			return $this->albums;
		}
		
		public function getPicture() {
			return $this->picture;
		}
		
		public function getReleasesCount() {
			return $this->releasesCount;
		}
		
		public function getReleasesOffset() {
			return $this->releasesOffset;
		}
	}
?>