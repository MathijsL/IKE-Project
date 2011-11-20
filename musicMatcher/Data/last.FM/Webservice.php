<?php
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
		$xmlReader->open('http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist=' . ($artist) . '&api_key=b25b959554ed76058ac220b7b2e0a026', null, LIBXML_NOBLANKS);

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
	
	//Returns the related artists (from top 50) by checking for the same tag
	function getRelatedArtist($relatedartist){
		$result = "<ul>";
		
		//Get the fist tag from the input artist
		$artisttag = getTagByArtist($relatedartist);
		
		//Get the top 50 artists
		$array = getTopArtist();
		$topartists = explode("|", $array);
		
		//Find the artists with the same tag as the input artist and add to result
		for($i = 0; $i < sizeof($topartists); $i++){
			$topartisttag = getTagByArtist($topartists[$i]);
			if($topartisttag == $artisttag && $topartists[$i] != ""){
				$result .= "<li onclick='ShowArtist(\"" . $topartists[$i] . "\")'>" . $topartists[$i] . "</li>";
			}
		}
		
		//Return the found artists
		return $result . "</ul>";
	}
	
	function getArtistInfo($artist){
		//Get artist information
		$xmlReader = new XMLReader();
		$xmlReader->open('http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist=' . rawurlencode($artist) . '&api_key=b25b959554ed76058ac220b7b2e0a026', null, LIBXML_NOBLANKS);

		//Read until the name and the image is found
		$result = "<div style='float:left;'>";
		while ($xmlReader->read())
		{
			//Check for name
			if($xmlReader->name == "name"){
				//Enter name node
				$xmlReader->read();
				//Add node value
				$result .= "<b>" . $xmlReader->value . "</b><br />";
			}
			//Check for large image
			if($xmlReader->name == "image" && $xmlReader->getAttribute("size") == "large"){
				//Enter image node
				$xmlReader->read();
				//Add node value & break
				$result .= "<img src='" . $xmlReader->value . "' style='margin-top: 0px; width:126px;'>";
				break;
			}
		}
		$xmlReader->close();
		
		$result .= "</div>";
		$result .= "<div style='float:left; margin-left:20px;'>";
		$result .= "<b>Albums</b><br /><ul>";
		
		//Get artist top albums
		$xmlReader = new XMLReader();
		$xmlReader->open('http://ws.audioscrobbler.com/2.0/?method=artist.gettopalbums&artist=' . rawurlencode($artist) . '&api_key=b25b959554ed76058ac220b7b2e0a026&limit=5', null, LIBXML_NOBLANKS);
		while ($xmlReader->read())
		{
			if($xmlReader->name == "name"){
				$xmlReader->read();
				if($xmlReader->value != ""){
					$result .= "<li>" . $xmlReader->value . "</li>";
				}
			}
			if($xmlReader->name == "artist"){
				$xmlReader->read();
			}
		}
	
		$result .= "</ul></div><br /><div style='position:absolute; right:10px; top:10px; cursor:pointer;'><a onclick='HideArtist();'>Close</a></div>";
		
		//Close reader and return found tag
		$xmlReader->close();
		return $result;
	}

	
	//Find related artists / return top artist
	if($_GET['req'] == "getRelatedArtist")
		echo "<div class='serviceresult'>" . getRelatedArtist($_GET['keyword']) . "</div>";
	else if ($_GET['req'] == "getTopArtist")
		echo getTopArtist();
	else if ($_GET['req'] == "getArtistInfo")
		echo getArtistInfo($_GET['keyword']);