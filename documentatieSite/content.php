<?php
	$page = "home";
	$text = "";
	$side = array();
	
	if(isset($_REQUEST["page"])){
		$page = $_REQUEST["page"];
	}
	
	if($page == "documentation"){
		$text = "<h1>Sample Text</h1><p>hello world!</p>";
		$side = array("ding 1", "ding 2", "ding 3");
	}
	if($page == "files"){
		$side = array("files1", "files2");
		$text = "<ul>".files()."</ul>";
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