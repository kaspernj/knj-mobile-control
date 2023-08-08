<?
	//function to parse SQL, so that it can be insertet into a SQL-database.
	function sql($string){
		$string = str_replace("'", "\\'", $string);
		
		if (substr($string, -1, 1) == "\\" && substr($string, -2, 2) !== "\\\\"){
			$string = substr($string, 0, -1) . "\\\\";
		}
		
		return $string;
	}
?>