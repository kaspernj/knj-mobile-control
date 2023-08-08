!#/usr/bin/php5
<?
	require_once "functions/functions_common.php";
	require_once "functions/functions_extensions.php";
	require_once "gui/win_main/win_main.php";
	
	load_ext("gtk2");
	load_ext("sqlite");
	
	$win_main = new WinMain();
	Gtk::main();
?>