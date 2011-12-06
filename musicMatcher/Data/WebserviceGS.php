<?php
	if(isset($_REQUEST["query"])){
		$query = $_REQUEST["query"];
		$ar = explode(" ", $query);
		$query = implode("+", $ar);
		$items = array('tinysong link', 'songID', 'songName', 'artistID', 'artistName', 'albumID', 'albumName', 'Grooveshark link'); 
		$search = file_get_contents('http://tinysong.com/b/'.$query.'?key=d5ec464bc1b68d927d556bbdbabd2ca1');
		$search = explode(';', $search);
		$data = array_combine($items, $search);
		echo('<object width="250" height="40"><param name="movie" value="http://listen.grooveshark.com/songWidget.swf" /><param name="flashvars" value="hostname=cowbell.grooveshark.com&amp;songID='.$data[songID].'&amp;style=metal&amp;p=0" /><embed src="http://listen.grooveshark.com/songWidget.swf" type="application/x-shockwave-flash" wmode="window" width="250" height="40" flashvars="hostname=cowbell.grooveshark.com&amp;songID='.$data[songID].'&amp;style=metal&amp;p=0"></embed>');
	}
?>