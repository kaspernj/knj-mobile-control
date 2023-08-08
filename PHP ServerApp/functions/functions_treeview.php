<?
	/**
		This returns the selected item for a GtkTreeView().
		Saves code.
	*/
	function treeview_getselection(GtkTreeView $treeview){
		$columns = $treeview->get_columns();
		
		$selection = $treeview->get_selection();
		list($model, $iter) = $selection->get_selected();
		
		if ($iter && $model){
			foreach($columns AS $i => $column){
				$value = $model->get_value($iter, $i);
				
				$return[$i] = $value;
				$return[$column->get_title()] = $value;
			}
			
			return $return;
		}else{
			return false;
		}
	}
	
	/**
		This return the selected items for a GtkTreeView() (if it is possible to select more items).
		Saves code.
	*/
	function treeview_getall(GtkTreeView $treeview){
		$columns = $treeview->get_columns();
		$model = $treeview->get_model();
		
		$first = true;
		while(true){
			if ($first == true){
				$iter = $model->get_iter_first();
				$first = false;
			}else{
				$iter = $model->iter_next($iter);
			}
			
			if (!$iter){
				break;
			}
			
			foreach($columns AS $key => $column){
				$value = $model->get_value($iter, $key);
				
				$return_new[$key] = $value;
				$return_new[$column->get_title()] = $value;
			}
			
			$return[] = $return_new;
		}
		
		return $return;
	}
	
	/**
		Add a column (or more columns if you give it an array instead of a string) to a GtkTreeView().
		Saves code.
	*/
	function treeview_addcolumn(GtkTreeView $treeview, $title){
		if (is_array($title)){
			foreach($title AS $value){
				treeview_addcolumn($treeview, $value);
			}
		}else{
			$number = count($treeview->get_columns());
			$treeview->append_column(new GtkTreeViewColumn($title, new GtkCellRendererText(), "text", $number));
		}
	}
?>