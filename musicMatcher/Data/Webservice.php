<?php
	$function = $_GET['function'];
	
	if ($function == "getInfo" && isset($_GET['keyword']) && isset($_GET['selection'])) {
		include_once("musicMatcherArtist.class.php");
		
		$keyword = $_GET['keyword'];
		$selection = $_GET['selection'];
		$result = "";
		$artist = new musicMatcherArtist($keyword);
		
		$features = explode(";",$selection);
		
		if(in_array('name',$features)) {
			$result .= "name[".$artist->getName()."*";
		}	
		if(in_array('beginDate',$features)) {
			$result .= "beginDate[".$artist->getBeginDate()."*";
		}
		if(in_array('endDate',$features)) {
			$result .= "endDate[".$artist->getEndDate()."*";
		}
		if(in_array('type',$features)) {
			$result .= "type[".$artist->getType()."*";
		}
		if(in_array('albums',$features)) {
			$result .= "albums[";
			$albums = $artist->getAlbums();
			foreach($albums as $album) {
				$result .= $album->toString().";";
			}
			$result .= "*";
		}
		if(in_array('picture',$features)) {
			$result .= "picture[".$artist->getPicture();
		}
		
		echo $result;
		
	} else if ($function == "autocomplete") {
		include_once("lastFM.class.php");
		
		$lfm = new lastFM();
		$topArtists = $lfm -> getTopArtist();
		echo $topArtists;
	} else if ($function == "getRelatedArtist" && isset($_GET['keyword'])){
		include_once("lastFM.class.php");
		
		$lfm = new lastFM();
		$getRelatedArtists = $lfm -> getRelatedArtist();
		echo $getRelatedArtists;
	}
?>