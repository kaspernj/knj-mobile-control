<?
	require_once "functions/functions_treeview.php";
	require_once "functions/functions_knj_msgbox.php";
	require_once "functions/functions_sql.php";
	
	require_once "gui/win_main/win_main_searches.php";
	require_once "gui/win_main/win_main_commands.php";
	require_once "gui/win_main/win_main_programs.php";
	
	class WinMain extends WinMainPrograms{
		/**
			The constructor - this code will be run, when WinMain() is spawned.
		*/
		function __construct(){
			//Load glade.
			$this->glade = new GladeXML("glade/win_main.glade");
			$this->glade->signal_autoconnect_instance($this);
			
			//Load widgets from glade.
			$this->window =				$this->glade->get_widget("window");					//GtkWindow()-widget.
			$this->tbtn_listen =			$this->glade->get_widget("tbtn_listen");			//GtkToggeButton()-widget.
			$this->treeview_clients =	$this->glade->get_widget("treeview_clients");	//GtkTreeView()-widget.
			
			//treeview_clients
			treeview_addcolumn($this->treeview_clients, array(
					"ID",
					"Title"
				)
			);
			$this->treeview_clients->set_model(
				new GtkListStore(Gtk::TYPE_STRING, Gtk::TYPE_STRING)
			);
			
			//Run constructor on WinMainPrograms().
			parent::__construct();
			
			$this->window->show_all();
		}
		
		
		/**
			When the GtkToggleButton() is pressed and the program starts listening.
		*/
		function on_tbtn_listen_toggled(){
			if ($this->tbtn_listen->get_active()){
				if (!$this->socket_server){
					require_once "include/socket_server.php";
					$this->socket_server = new SocketServer($this);
				}
				
				$status = $this->socket_server->startListen();
				if (!$status){
					//Unset the buttons states, so it isnt active and informs the user.
					msgbox("Warning", "Could not open the socket - sorry.", "warning");
					$this->tbtn_listen->set_active(false);
				}
			}else{
				$this->socket_server->stopListen();
			}
		}
		
		
		/**
			Updates the list of clients on the main window.
		*/
		function updateClients(){
			$this->treeview_clients->get_model()->clear();
			if (!$this->socket_server){
				return false;
			}
			
			foreach($this->socket_server->clients AS $key => $client){
				$this->treeview_clients->get_model()->append(array($key, $client->ip . ":" . $client->port));
			}
		}
		
		
		/**
			Event is called, when a client is trying to send a command.
		*/
		function RunCmd($cmd_id){
			$commandd = GID($cmd_id, "programs_commands");
			
			if (!$commandd){
				echo "No such command: " . $cmd_id . "\n";
				return false;
			}
			
			system($commandd["server_command"] . " &");
		}
		
		/**
			Event is called, when a client is trying to search for a file.
		*/
		function RunSearch($search_id, $search_text, $socket_client){
			$search_dbdata = GID($search_id, "programs_searches");
			if (!$search_dbdata){
				return false;
			}
			
			require_once "class_dirsearcher.php";
			require_once "include/functions_string.php";
			$dirsearcher = new DirSearcher();
			$this->last_dirsearch = $dirsearcher;
			
			//Strings and filetypes to search for. Thn init and get the results of the search.
			$dirsearcher->setStrings(searchstring($search_text));
			$dirsearcher->setFiletypes(explode(";", $search_dbdata[filetypes]));
			$dirsearcher->searchDir($search_dbdata[server_dir]);
			$files = $dirsearcher->getFiles();
			
			//Send 15 of the results to the client.
			$count = 0;
			foreach($files AS $key => $file){
				$socket_client->socketWrite("sr:" . $key . ":" . $file . "\n");
				$count++;
				
				if ($count >= 15){
					break;
				}
			}
			
			//Tell the client, that this was the end of the search-result-list.
			$socket_client->socketWrite("srend\n");
		}
		
		/**
			Event is called, when a search-result should be run.
		*/
		function runSearchResult($search_id, $result_id){
			$search_dbdata = GID($search_id, "programs_searches");
			if (!$search_dbdata){
				echo "No dbdata - abort.\n";
				return false;
			}
			
			//Get file and make it Unix-command-friendly.
			$results = $this->last_dirsearch->getResults();
			$file = $results[$result_id];
			
			$file = str_replace("\\", "\\\\", $file);
			$file = str_replace("\$", "\\\$", $file);
			$file = str_replace("&", "\\&", $file);
			
			//Make Unix-command.
			$cmd = $search_dbdata[server_command];
			$cmd = str_replace("%file", $file, $cmd);
			
			//Run Unix-command;
			system($cmd);
		}
		
		
		/**
			Catches the button-press-event (needed for making the rightclick-menu).
		*/
		function on_treeview_clients_button_press_event($selection, $event){
			if ($event->button == 3){
				require_once "include/class_knj_popup.php";
				$popup = new knj_popup(
					array(
						"disconnect" => "Disconnect"
					),
					array(
						$this,
						"on_treeview_clients_rightclickmenu"
					)
				);
			}
		}
		
		
		/**
			Handels the knj_popup()-event when activated.
		*/
		function on_treeview_clients_rightclickmenu($mode){
			if ($mode == "disconnect"){
				$value = treeview_getselection($this->treeview_clients);
				if (!$value){
					msgbox("Warning", "Please select a client first.", "warning");
					return false;
				}
				
				$client = $this->socket_server->clients[$value[0]];
				$client->disconnect();
			}
		}
		
		
		/**
			When window is closed, this event should be called.
		*/
		function closeWindow(){
			echo "knj MobileControl v. 0.3\n";
			Gtk::main_quit();
		}
		
		
		/**
			When window is closed, this event is called.
		*/
		function on_window_destroy(){
			$this->closeWindow();
		}
		
		
		/**
			When the menu-quit is activated, this event is called.
		*/
		function on_menu_quit_activate(){
			$this->closeWindow();
		}
	}
?>