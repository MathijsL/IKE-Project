<?php
	//Get top 50 artist names from list.fm
	function getTopArtist(){
		return implode("|", simplexml_load_file('http://ws.audioscrobbler.com/2.0/?method=chart.gettopartists&api_key=b25b959554ed76058ac220b7b2e0a026&limit=50&page=1')->xpath("//artist/name"));
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
			if($topartists[$i] != "" && $topartists[$i] != $relatedartist){
				$topartisttag = getTagByArtist($topartists[$i]);
				if($topartisttag == $artisttag && $topartists[$i] != ""){
					$result .= "<li onclick='ShowArtist(\"" . $topartists[$i] . "\")'>" . $topartists[$i] . "</li>";
				}
			}
		}
		
		//Return the found artists
		return $result . "</ul>";
	}
	
	function getArtistInfo($artist){
		//Get artist information
		$xmlArtistInfo = simplexml_load_file('http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist=' . rawurlencode($artist) . '&api_key=b25b959554ed76058ac220b7b2e0a026');
		$result = "";
		$result .= "name:" . (string)$xmlArtistInfo->artist->name . "|";
		$image = $xmlArtistInfo->artist->xpath('//artist/image[@size="large"]');
		$result .= "image:" . $image[0] . "|";
		$result .= "$";
		
		$xmlTopAlbums = simplexml_load_file('http://ws.audioscrobbler.com/2.0/?method=artist.gettopalbums&artist=' . rawurlencode($artist) . '&api_key=b25b959554ed76058ac220b7b2e0a026&limit=5');
		$TopAlbums = $xmlTopAlbums->xpath('//topalbums/album');
		
		foreach ($TopAlbums as $TopAlbum) {
			$result .= "name:" . $TopAlbum->name . "/";
			$xmlAlbumInfo = simplexml_load_file('http://ws.audioscrobbler.com/2.0/?method=album.getinfo&artist=' . rawurlencode($artist) . '&album=' . rawurlencode($TopAlbum->name) . '&api_key=b25b959554ed76058ac220b7b2e0a026');
			$AlbumSongs = $xmlAlbumInfo->xpath('//album/tracks/track');
			foreach ($AlbumSongs as $AlbumSong) {
				$result .= $AlbumSong->name;
				$urlparts = explode(" ", $AlbumSong->name);
				$youtubeInfo = simplexml_load_file('http://gdata.youtube.com/feeds/api/videos/-/Music/' . rawurlencode($artist) . '/' . rawurlencode($urlparts[0]) . '?max-results=1&orderby=viewCount');
				$video = explode("/videos/", $youtubeInfo->entry->id);
				$result .= "#" . $video[1] . "\\";
			}
			$result .= "|";
		}
		
		return $result;
	}

	
	//Find related artists / return top artist
	if($_GET['req'] == "getRelatedArtist")
		echo "<div class='serviceresult'>" . getRelatedArtist($_GET['keyword']) . "</div>";
	else if ($_GET['req'] == "getTopArtist")
		echo getTopArtist();
	else if ($_GET['req'] == "getArtistInfo")
		echo getArtistInfo($_GET['keyword']);