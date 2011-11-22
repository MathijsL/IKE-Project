<?php
	$page = "home";
	$text = "";
	$side = array();
	$subpage = "";
	
	if(isset($_REQUEST["page"])){
		$page = $_REQUEST["page"];
	}
	if(isset($_REQUEST["subpage"])){
		$subpage = $_REQUEST["subpage"];
	}
	if($page == "documentation"){
		$side = array("iteratie 1", "iteratie 2", "iteratie 3");
		$text = "<p> test </p>";
	}
	if($page == "files"){
		$side = array("files1", "files2");
		$text = "<ul>".files()."</ul>";
	}
	
	
	
	if($subpage == "iteratie1"){
		$file = "./text.html";
		$content = file($file);
		$text=implode($content);
	}
	
	function files(){
		$dir = "./documents";
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