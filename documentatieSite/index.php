<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style.css" rel="stylesheet" type="text/css" />
<title>Naamloos document</title>
</head>

<body>
<div class="content">
	<div class="menu">
        <ul>
            <li><a href="?page=files">bestanden</a></li>
            <li><a href="?page=documentation">documentatie</a></li>
        </ul>
    </div>
	<div class="header">
    	<h1>musicMatcher</h1><h3> documentatie</h3>
    </div>
    <div class="sideMenu">
        <ul>
        	<?php include("content.php"); 
			foreach($side as &$item){
				echo '<li><a href=?subpage='.$item.'>'.$item.'</a></li>';
			}?>
        </ul>
    </div>
    <div class="text">
    	<?php echo $text; ?>
    </div>
</div>
</body>
</html>
