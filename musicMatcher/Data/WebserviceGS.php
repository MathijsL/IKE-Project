<?php
	$artist = "";
	$title = "";
	if(isset($_REQUEST["artist"])){
		$artist = $_REQUEST["artist"];
	}
	if(isset($_REQUEST["title"])){
		$title = $_REQUEST["title"];
	}
	$ar = explode(" ", $artist);
	$query = implode("+", $ar);
	$ar = explode(" ", $title);
	$query .= "+".implode("+", $ar);
	$search = file_get_contents('http://tinysong.com/s/'.$query.'?key=d5ec464bc1b68d927d556bbdbabd2ca1&limit=5&format=json');
	$search = json_decode($search, true);
	$smallest=100000;
	$bestID = $search[0][SongID];
	foreach($search as $arr){
		 $current = levenshtein($arr[SongName], $title);
		 if($current < $smallest){
			 $bestID = $arr[SongID];
			 $smallest = $current;
		 }
	}
	$song = $bestID;
	echo('<object width="250" height="40"><param name="movie" value="http://listen.grooveshark.com/songWidget.swf" /><param name="flashvars" value="hostname=cowbell.grooveshark.com&amp;songID='.$song.'&amp;style=metal&amp;p=0" /><embed src="http://listen.grooveshark.com/songWidget.swf" type="application/x-shockwave-flash" wmode="window" width="250" height="40" flashvars="hostname=cowbell.grooveshark.com&amp;songID='.$song.'&amp;style=metal&amp;p=0">');
?>