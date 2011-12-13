<?php
	require_once("musicMatcherAlbum.class.php");
	
	class musicMatcherArtist{
		public $mbid;
		public $name;
		public $begindate;
		public $enddate;
		public $type;
		public $albums = array();
		public $picture;
		public $features;
		
		public function __construct($artist, $values) {
			$this->features = explode(";",$values);
			
			//Get mbid
			if(in_array('name',$this->features) || in_array('picture',$this->features) || in_array('type',$this->features) || in_array('begindate',$this->features) || in_array('enddate',$this->features)){
				$json_info=json_decode(file_get_contents('http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist=' . rawurlencode($artist) . '&api_key=b25b959554ed76058ac220b7b2e0a026&format=json'), true);
				$this->mbid = $json_info[artist][mbid];
			}
		
			//Get last fm info
			if(in_array('name',$this->features) || in_array('picture',$this->features)) {
				if(in_array('name',$this->features)) {
					$this->name = str_replace("\\", "", (str_replace("\"", "'", $json_info[artist][name])));
				}
				if(in_array('picture',$this->features)) {
					$this->picture = str_replace("\\", "", (str_replace("\"", "'", $json_info[artist][image][2]["#text"])));
				}
			}
			
			//Get musicbrainz info
			if(in_array('type',$this->features) || in_array('begindate',$this->features) || in_array('enddate',$this->features)) {
				$xml_info = simplexml_load_file('http://musicbrainz.org/ws/1/artist/'.$this->mbid.'?type=xml');
				if(in_array('type',$this->features)) {
					$this->type = str_replace("\\", "", (str_replace("\"", "'", ((string)$xml_info->{'artist'}['type']))));
				}
				if(in_array('begindate',$this->features)) {
					$this->begindate = str_replace("\\", "", (str_replace("\"", "'", ((string)$xml_info->artist->{'life-span'}['begin']))));
				}
				if(in_array('enddate',$this->features)) {
					$this->enddate = str_replace("\\", "", (str_replace("\"", "'", ((string)$xml_info->artist->{'life-span'}['end']))));
					
					if($this->enddate == "")
						$this->enddate = "Active";
				}
			}
		
			//Get artist albums		
			if(in_array('albums',$this->features)) {
				$json_albums=json_decode(file_get_contents('http://ws.audioscrobbler.com/2.0/?method=artist.gettopalbums&artist=' . rawurlencode($artist) . '&api_key=b25b959554ed76058ac220b7b2e0a026&limit=5&format=json'), true);
				
				foreach($json_albums[topalbums][album] as $a){
					array_push($this->albums, new musicMatcherAlbum($artist, $a[name]));
				}		  
			}
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
		
		public function toString(){
			$result = "{\"artist\":{";
			
		
			if(in_array('name',$this->features)) {
				$result .= "\"name\":\"".$this->name."\",";
			}	
			if(in_array('mbid',$this->features)) {
				$result .= "\"mbid\":\"".$this->mbid."\",";
			}	
			if(in_array('begindate',$this->features)) {
				$result .= "\"begindate\":\"".$this->begindate."\",";
			}
			if(in_array('enddate',$this->features)) {
				$result .= "\"enddate\":\"".$this->enddate."\",";
			}
			if(in_array('type',$this->features)) {
				$result .= "\"type\":\"".$this->type."\",";
			}
			if(in_array('picture',$this->features)) {
				$result .= "\"picture\":\"".$this->picture."\",";
			}
			if(in_array('albums',$this->features)) {
				$result .= "\"albums\":[";
				foreach($this->albums as $album) {
					$result .= $album->toString().",";
				}
				
				$result = substr($result, 0, -1)."],";
			}
			
			return substr($result, 0, -1) . "}}";
		}
	}
?>