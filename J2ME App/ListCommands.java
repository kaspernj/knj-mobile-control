import javax.microedition.lcdui.*;
import javax.microedition.io.*;
import javax.microedition.midlet.*;

import java.io.*;
import java.util.*;

class ListCommands implements CommandListener{
	MobilKlient mobil_klient;
	ListPrograms list_programs;
	
	List list;
	Vector program;
	Vector commands;
	Vector searches;
	
	String last_command_id;
	
	public ListCommands(ListPrograms in_list_programs, Vector in_program){
		list_programs = in_list_programs;
		mobil_klient = this.list_programs.mobil_klient;
		program = in_program;
		
		commands = (Vector)in_program.elementAt(2);
		
		Vector tha_command;
		String tha_title;
		
		list = new List("Choose command", List.IMPLICIT);
		list.setCommandListener(this);
		list.addCommand(new Command("Back", Command.SCREEN, 1));
		
		for(int i = 0; i < commands.size(); i++){
			tha_command = (Vector)commands.elementAt(i);
			tha_title = (String)tha_command.elementAt(1);
			
			this.list.append(tha_title, null);
		}
	}
	
	public void initSearch(String search_text){
		mobil_klient.sendData("search:" + this.last_command_id + ":" + search_text + "\n");
		ListCommandsSearchResult tha_results = new ListCommandsSearchResult(this);
		mobil_klient.display.setCurrent(tha_results.list);
		mobil_klient.setCurrentSearchResult(tha_results);
	}
	
	public void commandAction(Command c, Displayable s){
		String tha_label = c.getLabel();
		
		if (c == List.SELECT_COMMAND){
			int tha_command_sel = this.list.getSelectedIndex();
			Vector tha_command = (Vector)this.commands.elementAt(tha_command_sel);
			String tha_type = (String)tha_command.elementAt(2);
			String tha_command_id = (String)tha_command.elementAt(0);
			this.last_command_id = tha_command_id;
			
			if (tha_type.equals("cmd")){
				mobil_klient.sendData("cmd:" + tha_command_id + "\n");
			}else if(tha_type.equals("search")){
				ListCommandsSearch list_commands_search = new ListCommandsSearch(this);
				mobil_klient.display.setCurrent(list_commands_search.form);
			}
		}else if(tha_label.equals("Back")){
			mobil_klient.display.setCurrent(this.list_programs.list);
		}else{
			System.err.println("Didnt understand the command: " + tha_label);
		}
	}
}}