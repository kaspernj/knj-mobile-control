<?
	class SocketServer{
		var $win_main;
		var $clients;
		
		function __construct(WinMain $win_main){
			$this->clients = array();
			$this->win_main = $win_main;
		}
		
		function startListen(){
			$this->socket = stream_socket_server("tcp://0.0.0.0:10647");
			if (!$this->socket){
				return false;
			}
			
			$watch = Gtk::io_add_watch($this->socket, Gtk::IO_IN, array($this, "socketAccept"));
			return true;
		}
		
		function stopListen(){
			foreach($this->clients AS $key => $client){
				$client->socketClose();
				$this->clients[$key] = null;
			}
			fclose($this->socket);
		}
		
		function socketAccept(){
			$client_socket = stream_socket_accept($this->socket);
			
			require_once "include/socket_client.php";
			$client = new SocketClient($client_socket, $this);
			
			//List programs.
			$f_gp = sqlite_query(getDB(), "SELECT * FROM programs ORDER BY title") or die(sqlite_last_error(getDB()));
			while($d_gp = sqlite_fetch_array($f_gp)){
				$client->socketWrite("program:" . $d_gp["id"] . ":" . $d_gp["title"] . "\n");
				
				//List commands.
				$f_gc = sqlite_query(getDB(), "SELECT * FROM programs_commands WHERE program_id = '$d_gp[id]' ORDER BY name") or die(sqlite_last_error(getDB()));
				while($d_gc = sqlite_fetch_array($f_gc)){
					$client->socketWrite("cmd:" . $d_gc["id"] . ":" . $d_gc["name"] . "\n");
				}
				
				//List searches.
				$f_gs = sqlite_query(getDB(), "SELECT * FROM programs_searches WHERE program_id = '$d_gp[id]' ORDER BY title") or die(sqlite_last_error(getDB()));
				while($d_gs = sqlite_fetch_array($f_gs)){
					$client->socketWrite("search:" . $d_gs["id"] . ":" . $d_gs["title"] . "\n");
				}
			}
			
			$client->socketWrite("endprogram\n");
			$this->clients[] = $client;
			$this->updClientsID();
			$this->win_main->updateClients();
			return true;
		}
		
		private function updClientsID(){
			foreach($this->clients AS $key => $client){
				$client->server_key = $key;
			}
		}
	}
?>