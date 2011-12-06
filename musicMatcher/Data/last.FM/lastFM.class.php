<?php

class lastFM {
	//Get top 50 artist names from list.fm
	function getTopArtist(){
		$artistlist = "";
		$json_o=json_decode(file_get_contents('http://ws.audioscrobbler.com/2.0/?method=chart.gettopartists&api_key=b25b959554ed76058ac220b7b2e0a026&limit=50&page=1&format=json'));
		foreach($json_o->artists->artist as $p){
			$artistlist .= $p->name;
		}
		return $artistlist;
	}
	
	//Returns the first tag submitted by the artist
	function getTagByArtist($artist){
		$json_o=json_decode(file_get_contents('http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist=' . rawurlencode($artist) . '&api_key=b25b959554ed76058ac220b7b2e0a026&format=json'));
		return $json_o->artist->tags->tag[0]->name;
	}
	
	function getRelatedArtist($relatedartist){
		$result = "<div class='serviceresult'><ul>";
		$artisttag = $this->getTagByArtist($relatedartist);
		$json_o=json_decode(file_get_contents('http://ws.audioscrobbler.com/2.0/?method=tag.gettopartists&tag=' . rawurlencode($artisttag) . '&api_key=b25b959554ed76058ac220b7b2e0a026&format=json'));
		foreach($json_o->topartists->artist as $p){
			$result .= "<li onclick='ShowArtist(\"" . $p->name . "\")'>" . $p->name . "</li>";
		}
		return $result . "</ul></div>";
	}
}

?>