<?php
	$function = $_GET['function'];
	
	if ($function == "getInfo" && isset($_GET['keyword']) && isset($_GET['selection'])) {
		include_once("musicMatcherArtist.class.php");
		$artist = new musicMatcherArtist($_GET['keyword'], $_GET['selection']);
		echo $artist->toString();
		
	} else if ($function == "getRelatedArtist" && isset($_GET['keyword'])){
		$result = "{\"artists\":[";
		
		$json_o=json_decode(file_get_contents('http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist=' . rawurlencode($_GET['keyword']) . '&api_key=b25b959554ed76058ac220b7b2e0a026&format=json'));
		$artisttag = $json_o->artist->tags->tag[0]->name;

		$json_o=json_decode(file_get_contents('http://ws.audioscrobbler.com/2.0/?method=tag.gettopartists&tag=' . rawurlencode($artisttag) . '&api_key=b25b959554ed76058ac220b7b2e0a026&format=json'));
		foreach($json_o->topartists->artist as $p){
			$result .= "{\"name\":\"" . $p->name . "\"},";
		}
		
		if(count($json_o->topartists->artist) > 0)
				$result = substr($result, 0, -1);
		
		echo $result . "]}";
	}
?>