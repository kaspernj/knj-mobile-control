import javax.microedition.lcdui.*;
import javax.microedition.io.*;
import javax.microedition.midlet.*;

import java.io.*;
import java.util.*;

class ListCommandsSearchResult implements CommandListener{
	ListCommands list_commands;
	List list;
	Vector results;
	
	public ListCommandsSearchResult(ListCommands in_list_commands){
		list_commands = in_list_commands;
		this.results = new Vector();
		
		list = new List("Choose", List.IMPLICIT);
		list.setCommandListener(this);
		list.addCommand(new Command("Back", Command.SCREEN, 1));
	}
	
	public void addResult(String tha_id, String tha_title){
		Vector tha_result = new Vector();
		tha_result.addElement(tha_id);
		tha_result.addElement(tha_title);
		
		results.addElement(tha_result);
	}
	
	public void updateResults(){
		Vector tha_result;
		String tha_title;
		
		for(int i = 0; i < results.size(); i++){
			tha_result = (Vector)this.results.elementAt(i);
			tha_title = (String)tha_result.elementAt(1);
			
			this.list.append(tha_title, null);
		}
	}
	
	public void commandAction(Command c, Displayable s){
		String tha_label = c.getLabel();
		
		if (c == List.SELECT_COMMAND){
			int tha_sel = this.list.getSelectedIndex();
			Vector tha_result = (Vector)results.elementAt(tha_sel);
			String tha_search_id = this.list_commands.last_command_id;
			String tha_result_id = (String)tha_result.elementAt(0);
			
			//Syntax: sch:search_id:result_id\n
			list_commands.mobil_klient.sendData("sch:" + tha_search_id + ":" + tha_result_id + "\n");
		}else if(tha_label.equals("Back")){
			list_commands.mobil_klient.display.setCurrent(list_commands.list);
		}else{
			System.err.println("Didnt understand the command: " + tha_label);
		}
	}
}