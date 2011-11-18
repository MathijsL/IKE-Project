<?php
	//Temp database
	$db = array("Phil Collins", "U2", "60 Cent", "K3", "Jean Paul", "Jean Kingston", "Phil Collins", "U2", "60 Cent", "K3", "Jean Paul", "Jean Kingston", "Phil Collins", "U2", "60 Cent", "K3", "Jean Paul", "Jean Kingston", "Phil Collins", "U2", "60 Cent", "K3", "Jean Paul", "Jean Kingston", "Phil Collins", "U2", "60 Cent", "K3", "Jean Paul", "Jean Kingston", "Phil Collins", "U2", "60 Cent", "K3", "Jean Paul", "Jean Kingston", "Phil Collins", "U2", "60 Cent", "K3", "Jean Paul", "Jean Kingston", "Phil Collins", "U2", "60 Cent", "K3", "Jean Paul", "Jean Kingston", "Phil Collins", "U2", "60 Cent", "K3", "Jean Paul", "Jean Kingston", "Phil Collins", "U2", "60 Cent", "K3", "Jean Paul", "Jean Kingston", "Phil Collins", "U2", "60 Cent", "K3", "Jean Paul", "Jean Kingston");
	
	//Create result string
	$rdb = "<ul>";
	
	//Input variables
	$keyword = $_GET["keyword"];
	
	//The actual 'search'
	for ($i = 0; $i <= sizeof($db); $i++) {
		if(strstr($db[$i], $keyword) != false){
			$rdb = $rdb . "<li>" . $db[$i] . "</li>";
		}
	}

	//End result string
	if($rdb != "<ul>"){
		$rdb = $rdb . "</ul>";
	}
	else{
		$rdb = "No Results";
	}
	
	//Return result
	echo $rdb;
?>