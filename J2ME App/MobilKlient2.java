import javax.microedition.io.ServerSocketConnection;
import javax.microedition.io.UDPDatagramConnection;
import javax.microedition.io.*;

import java.io.*;
import java.util.*;

import javax.microedition.lcdui.*;
import javax.microedition.io.*;
import javax.microedition.midlet.*;

public class MobilKlient extends MIDlet implements CommandListener{
	//Socket vars.
	SocketConnection		conn;
	InputStream				conn_in;
	OutputStream			conn_out;
	
	//Thread vars.
	Thread thread_listen;
	
	//Display vars.
	Display display;
	
	//Program and command-vars.
	int program_count;
	Vector programs;
	Vector programs_current;
	
	//Forms and lists.
	ListConnections list_conns;
	ListPrograms list_programs;
	ListCommandsSearchResult current_searchresult;
	
	public void startApp(){
		programs = new Vector();
		
		display = Display.getDisplay(this);
		list_conns = new ListConnections(this);
		this.showConnections();
	}
	
	public void setCurrentSearchResult(ListCommandsSearchResult in_lcsr){
		this.current_searchresult = in_lcsr;
	}
	
	public void showPrograms(){
		if (this.list_programs == null){
			list_programs = new ListPrograms(this);
		}
		
		display.setCurrent(list_programs.list);
	}
	
	public void showConnections(){
		display.setCurrent(list_conns.list);
	}
	
	public void commandAction(Command c, Displayable s){
		String tha_label = c.getLabel();
		
		if (c.getCommandType() == Command.EXIT){
			notifyDestroyed();
		}else{
			System.err.println("Uknown input in commandAction(): " + tha_label);
		}
	}
	
	public String[] split(String str, String splitter){
		int count = 0;
		String str_rem = "";
		String tha_char;
		String[] tha_return = new String[10];
		
		for(int i = 0; i < str.length(); i++){
			tha_char = "" + str.charAt(i);
			
			if (tha_char.equals(splitter)){
				tha_return[count] = str_rem;
				str_rem = "";
				count++;
			}else{
				str_rem = str_rem + tha_char;
			}
		}
		
		if (!str_rem.equals("")){
			tha_return[count] = str_rem;
		}
		
		return tha_return;
	}
	
	public void socketRec(String data){
		String[] tha_split = this.split(data, ":");
		
		if (tha_split[0].equals("program")){
			//Add new program.
			Vector tha_program = new Vector();
			tha_program.addElement(tha_split[1]);
			tha_program.addElement(tha_split[2]);
			tha_program.addElement(new Vector());
			tha_program.addElement(new Vector());
			
			programs.addElement(tha_program);
			programs_current = tha_program;
		}else if(tha_split[0].equals("cmd")){
			//Add new command to the current program, which is being read.
			Vector tha_command = new Vector();
			tha_command.addElement(tha_split[1]);
			tha_command.addElement(tha_split[2]);
			tha_command.addElement("cmd");
			
			Vector tha_commands = (Vector)programs_current.elementAt(2);
			tha_commands.addElement(tha_command);
		}else if(tha_split[0].equals("search")){
			//Add new search to the current program, which is being read.
			Vector tha_command = new Vector();
			tha_command.addElement(tha_split[1]);
			tha_command.addElement(tha_split[2]);
			tha_command.addElement("search");
			
			Vector tha_commands = (Vector)programs_current.elementAt(2);
			tha_commands.addElement(tha_command);
		}else if(tha_split[0].equals("endprogram")){
			//Show list of programs received.
			System.err.println("Showing list of programs.");
			this.showPrograms();
		}else if(tha_split[0].equals("sr")){
			//A new search-result have been received.
			System.err.println("New search result: " + tha_split[2]);
			this.current_searchresult.addResult(tha_split[1], tha_split[2]);
		}else if(tha_split[0].equals("srend")){
			this.current_searchresult.updateResults();
		}else{
			//Uknown command received.
			System.err.println("Unknown data received: " + data);
		}
	}
	
	public void pauseApp(){}
	public void destroyApp(boolean unconditional){}
	
	public void sendData(String tha_senddata){
		try{
			conn_out.write(tha_senddata.getBytes());
			conn_out.flush();
		}catch(IOException e){
			System.err.println("sendData(\"" + tha_senddata + "\"): " + e);
		}
	}
}