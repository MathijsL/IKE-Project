<?php
	include_once("musicMatcherArtist.class.php");
	
	$keyword = $_GET['keyword'];
	
	$artist = new musicMatcherArtist($keyword);
	
	
?>