<?
	class WinMainCommands extends WinMainSearches{
		function __construct(){
			$this->treeview_commands = $this->glade->get_widget("treeview_commands");
			
			//treeview_commands
			treeview_addcolumn($this->treeview_commands, array(
					"ID",
					"Name"
				)
			);
			$this->treeview_commands->set_model(
				new GtkListStore(Gtk::TYPE_STRING, Gtk::TYPE_STRING)
			);
			$this->treeview_commands->get_selection()->connect("changed", array($this, "treeviewCommandsChanged"));
			$this->treeview_commands->get_column(0)->set_visible(false);
			$this->treeview_commands->set_search_column(1);
			
			//Load WinMainSearches().
			parent::__construct();
		}
		
		function treeviewCommandsChanged(){
			$command_sel = treeview_getSelection($this->treeview_commands);
			$command = GID($command_sel[0], "programs_commands");
			
			if ($command_sel[0] > 0){
				$this->commandsSet($command["name"], $command["mobile_key"], $command["server_command"]);
			}else{
				$this->commandsSet("", "", "");
			}
		}
		
		function treeviewCommandsUpdate(){
			$program = treeview_getselection($this->treeview_programs);
			$this->treeview_commands->get_model()->clear();
			
			if ($program){
				$this->treeview_commands->get_model()->append(array(
						"",
						"Add new"
					)
				);
				$f_gc = sqlite_query(getDB(), "SELECT * FROM programs_commands WHERE program_id = '" . $program[0] . "'") or die(sqlite_last_error(getDB()));
				while($d_gc = sqlite_fetch_array($f_gc)){
					$this->treeview_commands->get_model()->append(array(
							$d_gc["id"],
							$d_gc["name"]
						)
					);
				}
			}
			
			$this->commandsSet("", "", "");
		}
		
		function commandsSet($name, $key, $command){
			$this->glade->get_widget("txt_commands_name")->set_text($name);
			$this->glade->get_widget("txt_commands_key")->set_text($key);
			$this->glade->get_widget("txt_commands_command")->set_text($command);
		}
		
		function on_btn_commands_save_clicked(){
			$command_sel = treeview_getSelection($this->treeview_commands);
			$program_sel = treeview_getSelection($this->treeview_programs);
			
			if ($program_sel[0] <= 0){
				msgbox("Warning", "Please select a saved program first.", "warning");
				return false;
			}
			
			$name = $this->glade->get_widget("txt_commands_name")->get_text();
			$key = $this->glade->get_widget("txt_commands_key")->get_text();
			$command = $this->glade->get_widget("txt_commands_command")->get_text();
			
			if (!$command_sel || $command_sel[1] == "Add new"){
				sqlite_query(getDB(), "
					INSERT INTO
						programs_commands
					
					(
						program_id,
						name,
						mobile_key,
						server_command
					) VALUES (
						'$program_sel[0]',
						'$name',
						'$key',
						'$command'
					)
				") or die(sqlite_last_error(getDB()));
			}else{
				sqlite_query(getDB(), "
					UPDATE
						programs_commands
					
					SET
						name = '$name',
						mobile_key = '$key',
						server_command = '$command'
					
					WHERE
						id = '$command_sel[0]'
				") or die(sqlite_last_error(getDB()));
			}
			
			$this->treeviewCommandsUpdate();
		}
		
		function on_btn_commands_delete_clicked(){
			$command_sel = treeview_getSelection($this->treeview_commands);
			$program_sel = treeview_getSelection($this->treeview_programs);
			
			if ($program_sel[0] <= 0){
				msgbox("Warning", "Please select a program first.", "warning");
				return false;
			}
			
			if ($command_sel[0] > 0){
				if (msgbox("Question", "Do you want to delete the command: \"" . $command_sel[1] . "\"?", "yesno") == "no"){
					return false;
				}
				
				sqlite_query(getDB(), "DELETE FROM programs_commands WHERE id = '$command_sel[0]'") or die(sqlite_last_error(getDB()));
				$this->treeviewCommandsUpdate();
			}else{
				msgbox("Warning", "Please select a command first.", "warning");
				return false;
			}
		}
	}
?>