<?php 
	error_reporting(E_ALL);

	function Create(){
		$connect=odbc_connect("DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=recdb.mdb;", "", "") or die("fail");
		echo(odbc_errormsg($connect));
		echo(odbc_error($connect));

			$json_topfans=json_decode(file_get_contents('http://ws.audioscrobbler.com/2.0/?method=artist.gettopfans&artist=cher&api_key=b25b959554ed76058ac220b7b2e0a026&format=json'));
			foreach($json_topfans->topfans->user as $u){
				$json_user=json_decode(file_get_contents('http://ws.audioscrobbler.com/2.0/?method=user.gettopartists&user='. $u->name .'&api_key=b25b959554ed76058ac220b7b2e0a026&format=json&limit=30'), true);
				for($i = 0; $i < count($json_user[topartists][artist]); $i++){
					for($p = count($json_user[topartists][artist])-1; $p > $i; $p--){

						//SAVE LINK			
						$name1 = str_replace("'", "", $json_user[topartists][artist][$i][name]);
						$name2 = str_replace("'", "", $json_user[topartists][artist][$p][name]);
						$strength = 1;
						
						//SEARCH FOR LINK
						$result = odbc_exec($connect, "SELECT * FROM Links WHERE Artist1_ID='". $name1."' AND Artist2_ID='". $name2."';");
						$found1 = false;
						while (odbc_fetch_row($result)) {
							$found1 = true;
							$strength = odbc_result($result, 4);
							break;
						}
						
						//SEARCH THE OTHER WAY AROUND
						$result = odbc_exec($connect, "SELECT * FROM Links WHERE Artist1_ID='". $name2."' AND Artist2_ID='". $name1."';");
						$found2 = false;
						while (odbc_fetch_row($result)) {
							$found2 = true;
							$strength = odbc_result($result, 4);
							break;
						}
						
						//INSERT LINK OR STRENGTHEN EXISTING LINK
						if(!$found1 && !$found2){
							odbc_exec($connect, "Insert Into Links (Artist1_ID, Artist2_ID, Strength) Values ('" . $name1 . "', '" . $name2 . "', '1');");
							echo(odbc_errormsg($connect));
						}
						else{
							if($found1){
								odbc_exec($connect, "Update Links set Strength='" . ($strength+1) . "' Where Artist1_ID='". $name1."' AND Artist2_ID='". $name2."';");
								echo(odbc_errormsg($connect));
							}
							else{
								odbc_exec($connect, "Update Links set Strength='" . ($strength+1) . "' Where Artist1_ID='". $name2."' AND Artist2_ID='". $name1."';");
								echo(odbc_errormsg($connect));
							}
						}
					}
				}
			}
			odbc_close($connect); 
	}
	
	function Read($artist){
		//SET CONNECTION
		$connect=odbc_connect("DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=recdb.mdb;", "", "") or die("fail");
		echo(odbc_errormsg($connect));
	
		//CHECK FIRST ARTIST
		$result = odbc_exec($connect, "SELECT * FROM Links WHERE Artist1_ID='". $artist."' OR Artist2_ID='". $artist."' ORDER BY Strength DESC;");
		echo(odbc_errormsg($connect));
		
		while (odbc_fetch_row($result)) {
			if(odbc_result($result, 3) != $artist)
				echo odbc_result($result, 3) . " - " . odbc_result($result, 4) . "<br />";
			else
				echo odbc_result($result, 2) . " - " . odbc_result($result, 4) . "<br />";
		}
	}
	
	//Create();
	Read("Adele");
?>