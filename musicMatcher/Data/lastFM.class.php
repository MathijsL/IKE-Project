<?php

class lastFM {
	//Get top 50 artist names from list.fm
	function getTopArtist(){
		//Get xml list of top 50 artists
		$xmlReader = new XMLReader();
		$xmlReader->open('http://ws.audioscrobbler.com/2.0/?method=chart.gettopartists&api_key=b25b959554ed76058ac220b7b2e0a026&limit=50&page=1', null, LIBXML_NOBLANKS);
		
		//Add all artist names to string
		$artistlist = "";
		while ($xmlReader->read())
		{
			if($xmlReader->name == "name"){
				$xmlReader->read();
				if($xmlReader->value != ""){
					$artistlist .= $xmlReader->value . "|";
				}
			}
		}
		
		//Close reader
		$xmlReader->close();
		
		//Create and return array of top 50 artist
		return $artistlist;
	}
	
	//Returns the first tag submitted by the artist
	function getTagByArtist($artist){
		//Get artist information
		$xmlReader = new XMLReader();
		$xmlReader->open('http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist=' . rawurlencode($artist) . '&api_key=b25b959554ed76058ac220b7b2e0a026', null, LIBXML_NOBLANKS);

		//Read until the first tag is found then break
		$tag = "";
		while ($xmlReader->read())
		{
			if($xmlReader->name == "tag"){
				$xmlReader->read();
				$xmlReader->read();
				$tag = $xmlReader->value;
				break;
			}
		}
		
		//Close reader and return found tag
		$xmlReader->close();
		return $tag;
	}
	
	function getRelatedArtist($relatedartist){
		$result = "<div class='serviceresult'><ul>";
		
		//Get the fist tag from the input artist
		$artisttag = $this->getTagByArtist($relatedartist);
		
		//Get the top 50 artists
		$xmlReader = new XMLReader();
		$xmlReader->open('http://ws.audioscrobbler.com/2.0/?method=tag.gettopartists&tag=' . rawurlencode($artisttag) . '&api_key=b25b959554ed76058ac220b7b2e0a026', null, LIBXML_NOBLANKS);
		while ($xmlReader->read())
		{
			if($xmlReader->name == "name"){
				$xmlReader->read();
				if($xmlReader->value != ""){
					$result .= "<li onclick='ShowArtist(\"" . $xmlReader->value . "\")'>" . $xmlReader->value . "</li>";
				}
			}
		}
		
		//Return the found artists
		return $result . "</ul></div>";
	}
}

?>