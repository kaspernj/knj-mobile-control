<?
	/**
		An easy msgbox-function - Just like in Visal Basic. This makes the use of dialog-boxes a lot 
		easier. Look a these examples:
		<?
			if (msgbox("Question", "Do you want to continue?", "yesno"){
				echo "You pressed yes.\n";
			}else{
				echo "You pressed no or close the question-window.\n";
			}
			
			$answer = msgbox("Question", "Do you want to continue?", "yesno");
			
			if ($answer == "yes"){
				echo "You pressed yes.\n";
			}elseif($answer == "no"){
				echo "You pressed no.\n";
			}elseif($answer == "cancel"){
				echo "You pressed cancel.\n";
			}
			
			
			$name = knj_input("Your name?", "Please enter your name");
			
			if ($name == "cancel"){
				echo "You closed the window.\n";
			}else{
				echo "Your name is " . $name . ".\n";
			}
		?>
	*/
	function msgbox($title, $message, $type = "info"){
		//Make dialog-command:
		$eval = '$dialog = new GtkDialog(\'' . $title . '\', null, Gtk::DIALOG_DESTROY_WITH_PARENT';
		
		$box = new GtkHBox();
		$box->set_border_width(4);
		
		if ($type == "yesno"){
			$eval .= ', array("Ja", Gtk::RESPONSE_YES, "Nej", Gtk::RESPONSE_NO)';
			$image = GtkImage::new_from_stock(Gtk::STOCK_DIALOG_QUESTION, Gtk::ICON_SIZE_DIALOG);
			$box->pack_start($image, false);
		}elseif($type == "info"){
			$image = GtkImage::new_from_stock(Gtk::STOCK_DIALOG_INFO, Gtk::ICON_SIZE_DIALOG);
			$box->pack_start($image, false);
			$eval .= ', array("Ok", Gtk::RESPONSE_YES)';
		}elseif($type == "warning"){
			$image = GtkImage::new_from_stock(Gtk::STOCK_DIALOG_WARNING, Gtk::ICON_SIZE_DIALOG);
			$box->pack_start($image, false);
			$eval .= ', array("Ok", Gtk::RESPONSE_YES)';
		}
		
		$eval .= ');';
		
		eval($eval);
		
		$dialog->set_title($title);
		$dialog->set_position(GTK_WIN_POS_CENTER);
		$dialog->set_has_separator(false);
		
		//Sets width and height on the msgbox.
		$lines = 0;
		foreach(explode("\n", $message) AS $line){
			$lines++;
			$testwidth = round(35 + strlen($line) * 8);
			
			if ($testwidth > $width){
				$width = $testwidth;
			}
		}
		
		$height = 66 + ($lines * 19);
		
		if ($width < 200){
			$width = 200;
		}
		
		if ($height < 100){
			$height = 100;
		}
		
		$dialog->set_size_request($width, $height);
		
		$text = new GtkLabel("\n" . $message . "\n");
		$text->set_alignment(0, 0.5);
		
		$box->pack_start($text, true, true);
		
		$dialog->vbox->add($box);
		
		$dialog->show_all();
		$result = $dialog->run();
		$dialog->destroy();
		
		if ($result == Gtk::RESPONSE_YES){
			return "yes";
		}elseif($result == Gtk::RESPONSE_NO){
			return "no";
		}else{
			return "cancel";
		}
	}
	
	/**
		This will prompt the user for input via a GtkEntry() (a textfield). It will halt the main-loop, until the user have enteret something 
		in the GtkEntry(), and then return it.
		
		It makes it a lot easier to prompt the user for input.
		
		Example:
		<?
			$text = knj_input("Your name", "Please enter your name:");
			echo "Your name is: " . $text . "\n";
		?>
	*/
	function knj_input($title, $message, $default_value = ""){
		//Make dialog-command:
		$dialog = new GtkDialog($title, null, Gtk::DIALOG_DESTROY_WITH_PARENT, array("Ok", Gtk::RESPONSE_YES, "Cancel", Gtk::RESPONSE_NO));
		$box = new GtkVBox();
		$box->set_border_width(4);
		
		$dialog->set_position(GTK_WIN_POS_CENTER);
		$dialog->set_title($title);
		$dialog->set_has_separator(false);
		
		//Sets width on msgbox.
		foreach(explode("\n", $message) AS $line){
			$testwidth = round(35 + strlen($message) * 7);
			
			if ($testwidth > $width){
				$width = $testwidth;
			}
		}
		
		if ($width < 200){
			$width = 200;
		}
		
		$dialog->set_size_request($width, 100);
		
		$text = new GtkLabel("\n" . $message . "\n");
		$text->set_alignment(0, 0.5);
		
		$box->add($text);
		
		$entry = new GtkEntry();
		$entry->set_text($default_value);
		$box->add($entry);
		
		$dialog->vbox->add($box);
		
		$dialog->show_all();
		$result = $dialog->run();
		$dialog->destroy();
		
		if ($result == Gtk::RESPONSE_YES){
			return $entry->get_text();
		}else{
			return "cancel";
		}
	}
	
	/**
		This will prompt the user with a list of choices and return the chossen one (or false if the user-cancels).s
		This makes it a lot easier to prompt the user for a choice.
		
		Example:
		<?
			$choice = knj_listbox("Choice", "Who is your favorite hacker?",
				array(
					"Raymond, Eric" => "Eric Raymond",
					"Reveman, David" => "David Reveman",
					"Johansen, Kasper" => "Kasper Johansen"
				)
			);
			
			echo "You think that " . $choice . " is your favorite hacker.\n";
		?>
	*/
	function knj_listbox($title, $message, $items){
		$dialog = new GtkDialog($title, null, Gtk::DIALOG_DESTROY_WITH_PARENT, array("Ok", Gtk::RESPONSE_YES, "Cancel", Gtk::RESPONSE_NO));
		$dialog->set_position(GTK_WIN_POS_CENTER);
		$dialog->set_title($title);
		$dialog->set_has_separator(false);
		$dialog->set_size_request(300, 300);
		
		$tv_items = new GtkTreeView(new GtkListStore(Gtk::TYPE_STRING, Gtk::TYPE_STRING));
		$tv_items->set_reorderable(true);
		$tv_items->set_enable_search(true);
		$tv_items->set_search_column(1);
		$tv_items->append_column(
			new GtkTreeViewColumn("ID", new GtkCellRendererText(), "text", 0)
		);
		$tv_items->append_column(
			new GtkTreeViewColumn("Items", new GtkCellRendererText(), "text", 1)
		);
		$tv_items->get_column(0)->set_visible(false);
		
		foreach($items AS $key => $value){
			$tv_items->get_model()->append(array($key, $value));
		}
		
		$text = new GtkLabel("\n" . $message . "\n");
		$text->set_alignment(0, 0.5);
		
		$box = new GtkVBox();
		$box->pack_start($text, false, false);
		
		$scrwin = new GtkScrolledWindow();
		$scrwin->set_policy(Gtk::POLICY_AUTOMATIC, Gtk::POLICY_ALWAYS);
		$scrwin->add($tv_items);
		$box->add($scrwin);
		
		$dialog->vbox->add($box);
		
		$dialog->show_all();
		$result = $dialog->run();
		
		if ($result == Gtk::RESPONSE_YES){
			$columns = $tv_items->get_columns();
			
			$selection = $tv_items->get_selection();
			list($model, $iter) = $selection->get_selected();
			
			if ($iter && $model){
				$return = $model->get_value($iter, 0);
				
				$dialog->destroy();
				return $return;
			}else{
				$dialog->destroy();
				return false;
			}
		}else{
			$dialog->destroy();
			return false;
		}
	}
?>