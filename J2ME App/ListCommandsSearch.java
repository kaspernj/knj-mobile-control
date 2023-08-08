import javax.microedition.lcdui.*;
import javax.microedition.io.*;
import javax.microedition.midlet.*;

class ListCommandsSearch implements CommandListener{
	Form form;
	TextField tex_search;
	ListCommands list_commands;
	
	public ListCommandsSearch(ListCommands in_list_commands){
		list_commands = in_list_commands;
		
		form = new Form("Enter text");
		form.setCommandListener(this);
		form.addCommand(new Command("Search", Command.SCREEN, 1));
		form.addCommand(new Command("Back", Command.SCREEN, 2));
		
		tex_search = new TextField("Text:", "", 25, TextField.ANY);
		form.append(tex_search);
	}
	
	public void commandAction(Command c, Displayable s){
		String tha_label = c.getLabel();
		
		if (tha_label.equals("Search")){
			String tha_search = this.tex_search.getString();
			list_commands.initSearch(tha_search);
		}else if(tha_label.equals("Back")){
			list_commands.mobil_klient.display.setCurrent(list_commands.list);
		}else{
			System.err.println("Command not understood in ListCommandSearch()->CommandsAction(): " + tha_label);
		}
	}
}