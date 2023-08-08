<?
	function getDB(){
		global $db;
		
		if (!$db){
			$db = sqlite_open("knj_mobilecontrol.sqlite");
		}
		
		return $db;
	}
	
	function GID($id, $table){
		$f_gdata = sqlite_query(getDB(), "SELECT * FROM " . $table . " WHERE id = '" . sql($id) . "' LIMIT 1") or die(sqlite_last_error(getDB()));
		$d_gdata = sqlite_fetch_array($f_gdata);
		
		return $d_gdata;
	}
	
	function GOne($id, $data, $columns){
		$f_gdata = sqlite_query(getDB(), "SELECT " . $columns . " FROM " . $table . " WHERE id = '" . sql($id) . "' LIMIT 1") or die(sqlite_last_error(getDB()));
		$d_gdata = sqlite_fetch_array($f_gdata);
		
		return $d_gdata;
	}
?>