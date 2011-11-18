<table>
<?php

	require_once("phpapi/phpBrainz.class.php");
	
	//Create new phpBrainz object
	$phpBrainz = new phpBrainz();
	
	$keyword = $_GET['keyword'];
	$args = array(
		"artist"=>$keyword,
		"title"=>$keyword
	);
	
	$releaseFilter = new phpBrainz_ReleaseFilter($args);
	$releaseResults = $phpBrainz->findRelease($releaseFilter);

	foreach ($releaseResults as $result) {
		echo '<tr><td>'.$result->getTitle().'</td><td>'.$result->getArtist()->getName().'</td></tr>';
	}
	


?>
</table>