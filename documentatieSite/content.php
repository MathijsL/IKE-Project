<?php
	$page = "home";
	$text = "under construction";
	$side = array();
	$subpage = "";
	
	if(isset($_REQUEST["page"])){
		$page = $_REQUEST["page"];
	}
	if(isset($_REQUEST["subpage"])){
		$subpage = $_REQUEST["subpage"];
	}
	if($page == "documentation"){
		$side = array("Requirements", "Iteratie 1", "Iteratie 2", "Iteratie 3");
	}
	if($page == "files"){
		$side = array("Deliverables", "Code");
	}
	
	
	
	if($subpage == "Iteratie1"){
		$file = "./iteratie1.html";
		$content = file($file);
		$text=implode($content);
	}
	if($subpage == "Requirements"){
		$file = "./requirements.html";
		$content = file($file);
		$text = implode($content);
	}
	
	if($subpage == "Deliverables"){
		$text = "<ul>".files(".\Deliverables")."</ul>";
	}
	
	
	function files($path){
		$dir = $path;
		$files = scandir($dir);
		$res = "";
		foreach($files as &$file){
			if($file!="." && $file!=".."){
				$res .= '<li class="document"><a href='.$dir."/".$file.">".$file."</a></li>";
			}
		}
		return $res;
	}
?>