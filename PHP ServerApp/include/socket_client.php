<?
	class SocketClient{
		var $data_rec;				//Total bytes received from this client.
		var $data_write;			//Total bytes sent to this client.
		var $socket_server;		//Reference to the server-object, which spawned the client-object.
		var $socket;				//The connection to the client-unit.
		var $watch;					//The GTK()-watch-object, which activates the read-method in proper times (instead of using threads).
		var $ip;						//IP which client is connected through.
		var $port;					//Port which client is connected through.
		
		function __construct($socket, SocketServer $server){
			$this->data_rec = 0;
			$this->data_write = 0;
			
			$this->socket_server = $server;
			$this->socket = $socket;
			
			$info = explode(":", stream_socket_get_name($this->socket, true));
			$this->ip = $info[0];
			$this->port = $info[1];
			
			$this->watch = Gtk::io_add_watch($this->socket, Gtk::IO_IN, array($this, "socketRead"));
		}
		
		function disconnect(){
			//Notify the client, that we are closing the socket.
			$this->socketWrite("bye\n");
			$this->socketClose();
		}
		
		function socketRead(){
			$data = fgets($this->socket, 4096);
			$this->data_rec += strlen($data);
			
			if (!$data){
				//This happens, when the clients closes the socket.
				echo "Got a message with no data. Disconnecting.\n";
				$this->socketClose();
				return false;
			}elseif(preg_match("/^cmd:([0-9]+)\n$/", $data, $match)){
				//The client is trying to run a pre-defined command.
				$this->socket_server->win_main->RunCmd($match[1]);
			}elseif(preg_match("/^search:([0-9]+):([\s\S]+)\n$/", $data, $match)){
				//The client is trying to search.
				$this->socket_server->win_main->RunSearch($match[1], $match[2], $this);
			}elseif(preg_match("/^sch:([0-9]+):([0-9]+)\n$/", $data, $match)){
				//Run file.
				$this->socket_server->win_main->runSearchResult($match[1], $match[2]);
			}else{
				//We didnt understand, what the client gave us.
				echo "Got a unknown command from a client: \"" . $data . "\"\n";
			}
			
			if ($this->socket && $data){
				//Makes the watch over the socket continue (else we wont be getting any more data from the client via the socket).
				return true;
			}
		}
		
		function socketWrite($msg){
			$this->data_write += strlen($msg);
			fwrite($this->socket, $msg);
		}
		
		function socketClose(){
			//Close socket.
			fclose($this->socket);
			$this->socket = null;
			
			//Unset client and free resources.
			$this->socket_server->clients[$this->server_key] = null;
			unset($this->socket_server->clients[$this->server_key]);
			
			//Update clients in WinMain().
			$this->socket_server->win_main->updateClients();
		}
	}
?>