<?
	class WinMainPrograms extends WinMainCommands{
		function __construct(){
			$this->treeview_programs = $this->glade->get_widget("treeview_programs");
			
			//treeview_programs
			treeview_addcolumn($this->treeview_programs, array(
					"ID",
					"Title"
				)
			);
			$this->treeview_programs->set_model(
				new GtkListStore(Gtk::TYPE_STRING, Gtk::TYPE_STRING)
			);
			$this->treeview_programs->get_selection()->connect("changed", array($this, "treeviewProgramsChanged"));
			$this->treeview_programs->get_column(0)->set_visible(false);
			$this->treeview_programs->set_search_column(1);
			
			//Make commands.
			parent::__construct();
			
			//Read data from database.
			$this->treeviewProgramsUpdate();
			$this->treeviewSearchesUpdate();
		}
		
		function treeviewProgramsUpdate(){
			$this->treeview_programs->get_model()->clear();
			$this->treeview_programs->get_model()->append(array("", "Add new program"));
			
			$f_gp = sqlite_query(getDB(), "SELECT * FROM programs");
			while($d_gp = sqlite_fetch_array($f_gp)){
				$this->treeview_programs->get_model()->append(array(
						$d_gp[id],
						$d_gp[title]
					)
				);
			}
			
			$this->programsSet("", "", "");
			$this->treeviewCommandsUpdate();
			$this->treeviewSearchesUpdate();
		}
		
		function treeviewProgramsChanged(){
			$program = treeview_getSelection($this->treeview_programs);
			if($program[1] == "Add new program"){
				$this->treeviewCommandsUpdate();
				$this->treeviewSearchesUpdate();
				$this->programsSet("", "");
			}elseif($program){
				$data = GID($program[0], "programs");
				$this->programsSet($data[title]);
				$this->treeviewCommandsUpdate();
				$this->treeviewSearchesUpdate();
			}
		}
		
		function programsSet($title){
			$this->glade->get_widget("txt_programs_title")->set_text($title);
		}
		
		function on_btn_programs_save_clicked(){
			$value = treeview_getselection($this->treeview_programs);
			$title = $this->glade->get_widget("txt_programs_title")->get_text();
			
			if ($value[1] == "Add new program" || !$value){
				sqlite_query(getDB(), "
					INSERT INTO programs
					
					(
						title
					) VALUES (
						'$title'
					)
				") or die(sqlite_last_error(getDB()));
			}else{
				sqlite_query(getDB(), "
					UPDATE
						programs
						
					SET
						title='$title'
					
					WHERE
						id = '$value[0]'
				") or die(sqlite_last_error(getDB()));
			}
			
			$this->treeviewProgramsUpdate();
		}
		
		function on_btn_programs_delete_clicked(){
			$program = treeview_getSelection($this->treeview_programs);
			if ($program[0] > 0){
				if (msgbox("Question", "Do you want to delete the program \"" . $program[1] . "\"?", "yesno") == "no"){
					return false;
				}
				
				sqlite_query(getDB(), "DELETE FROM programs_commands WHERE program_id = '$program[0]'") or die(sqlite_last_error(getDB()));
				sqlite_query(getDB(), "DELETE FROM programs WHERE id = '$program[0]'") or die(sqlite_last_error(getDB()));
				
				$this->treeviewProgramsUpdate();
				$this->treeviewCommandsUpdate();
				$this->treeviewSearchesUpdate();
			}else{
				msgbox("Warning", "Please select a program first.", "warning");
				return false;
			}
		}
	}
?>