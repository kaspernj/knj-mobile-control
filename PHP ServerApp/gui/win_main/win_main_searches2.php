<?
<?
	class WinMainSearches{
		function __construct(){
			$this->treeview_searches = $this->glade->get_widget("treeview_searches");
			
			treeview_addcolumn($this->treeview_searches, array(
					"ID",
					"Title"
				)
			);
			$this->treeview_searches->set_model(
				new GtkListStore(Gtk::TYPE_STRING, Gtk::TYPE_STRING)
			);
			$this->treeview_searches->get_selection()->connect("changed", array($this, "treeviewSearchesChanged"));
			$this->treeview_searches->get_column(0)->set_visible(false);
			$this->treeview_searches->set_search_column(1);
		}
		
		function treeviewSearchesChanged(){
			$searchd = treeview_getselection($this->treeview_searches);
			if ($searchd[0] > 0){
				$search_dbdata = GID($searchd[0], "programs_searches");
				$this->setSearches(
					$search_dbdata[title],
					$search_dbdata[mobile_key],
					$search_dbdata[server_command],
					$search_dbdata[filetypes],
					$search_dbdata[server_dir]
				);
			}else{
				$this->setSearches("", "", "", "", "");
			}
		}
		
		function setSearches($title, $mobile_key, $server_command, $filetypes, $dir){
			$this->glade->get_widget("txt_searches_title")->set_text($title);
			$this->glade->get_widget("txt_searches_mobilekey")->set_text($mobile_key);
			$this->glade->get_widget("txt_searches_command")->set_text($server_command);
			$this->glade->get_widget("txt_searches_filetypes")->set_text($filetypes);
			$this->glade->get_widget("txt_searches_dir")->set_text($dir);
		}
		
		function getSearches(){
			return array(
				"title" => $this->glade->get_widget("txt_searches_title")->get_text(),
				"mobilekey" => $this->glade->get_widget("txt_searches_mobilekey")->get_text(),
				"servercommand" => $this->glade->get_widget("txt_searches_command")->get_text(),
				"filetypes" => $this->glade->get_widget("txt_searches_filetypes")->get_text(),
				"dir" => $this->glade->get_widget("txt_searches_dir")->get_text()
			);
		}
		
		function treeviewSearchesUpdate(){
			$this->treeview_searches->get_model()->clear();
			$this->treeview_searches->get_model()->append(array("", "Add new"));
			$f_gs = sqlite_query(getDB(), "SELECT * FROM programs_searches ORDER BY title") or die(sqlite_last_error(getDB()));
			while($d_gs = sqlite_fetch_array($f_gs)){
				$this->treeview_searches->get_model()->append(array($d_gs[id], $d_gs[title]));
			}
			
			$this->setSearches("", "", "", "", "");
		}
		
		function on_btn_searches_save_clicked(){
			$searchd = treeview_getselection($this->treeview_searches);
			$programd = treeview_getSelection($this->treeview_programs);
			$stext = $this->getSearches();
			
			if ($programd[0] <= 0){
				msgbox("Warning", "Choose a program first.", "warning");
				return false;
			}
			
			if ($searchd[0] <= 0){
				sqlite_query(getDB(), "
					INSERT INTO
						programs_searches
					
					(
						program_id,
						title,
						mobile_key,
						server_command,
						filetypes,
						server_dir
					) VALUES (
						'$programd[0]',
						'$stext[title]',
						'$stext[mobilekey]',
						'$stext[servercommand]',
						'$stext[filetypes]',
						'$stext[dir]'
					)
				") or die(sqlite_last_error(getDB()));
			}else{
				sqlite_query(getDB(), "
					UPDATE
						programs_searches
					
					SET
						title='$stext[title]',
						mobile_key='$stext[mobilekey]',
						server_command='$stext[servercommand]',
						filetypes='$stext[filetypes]',
						server_dir='$stext[dir]'
					
					WHERE
						id = '$searchd[0]'
				") or die(sqlite_last_error(getDB()));
			}
			
			$this->treeviewSearchesUpdate();
			return true;
		}
		
		function on_btn_searches_delete_clicked(){
			$searchd = treeview_getselection($this->treeview_searches);
			if ($search[0] <= 0){
				msgbox("Warning", "Please choose a search first.", "warning");
				return false;
			}
			
			if (msgbox("Question", "Do you want to remove the search called: \"" . $searchd[1] . "\"?", "yesno") == "yes"){
				sqlite_query(getDB(), "
					DELETE FROM
						programs_searches
					
					WHERE
						id = '$searchd[0]'
				") or die(sqlite_last_error(getDB()));
				$this->treeviewSearchesUpdate();
			}
		}
	}
?>