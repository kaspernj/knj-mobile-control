import javax.microedition.io.*;
import javax.microedition.midlet.*;

import java.io.*;
import java.util.*;

class ListPrograms implements CommandListener{
	MobilKlient mobil_klient;
	List list;
	Display display;
	
	public ListPrograms(MobilKlient in_mobil_klient){
		this.mobil_klient = in_mobil_klient;
		this.display = mobil_klient.display;
		
		Vector tha_program;
		String tha_title;
		Vector tha_commands;
		
		this.list = new List("Choose program", List.IMPLICIT);
		this.list.setCommandListener(this);
		this.list.addCommand(new Command("OK", Command.SCREEN, 1));
		
		for(int i = 0; i < this.mobil_klient.programs.size(); i++){
			tha_program = (Vector)this.mobil_klient.programs.elementAt(i);
			tha_commands = (Vector)tha_program.elementAt(2);
			tha_title = (String)tha_program.elementAt(1) + " (" + tha_commands.size() + ")";
			
			this.list.append(tha_title, null);
		}
	}
	
	public void commandAction(Command c, Displayable s){
		String tha_label = c.getLabel();
		
		if (tha_label.equals("OK")){
			int tha_program_sel = this.list.getSelectedIndex();
			Vector tha_program = (Vector)this.mobil_klient.programs.elementAt(tha_program_sel);
			
			ListCommands list_commands = new ListCommands(this.mobil_klient, tha_program);
			display.setCurrent(list_commands.list);
		}else{
			System.err.println("Didnt understand the command: " + tha_label);
		}
	}
}