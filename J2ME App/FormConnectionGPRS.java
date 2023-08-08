import javax.microedition.io.ServerSocketConnection;
import javax.microedition.io.UDPDatagramConnection;
import javax.microedition.io.*;

import java.io.*;
import java.util.*;

import javax.microedition.lcdui.*;
import javax.microedition.io.*;
import javax.microedition.midlet.*;

class FormConnectionGPRS implements CommandListener{
	MobilKlient mobil_klient;
	Form form;
	TextField tex_ip;
	TextField tex_port;
	
	public FormConnectionGPRS(MobilKlient in_mobil_klient){
		mobil_klient = in_mobil_klient;
		
		form = new Form("Enter details");
		form.setCommandListener(this);
		
		form.addCommand(new Command("Connect", Command.SCREEN, 1));
		form.addCommand(new Command("Back", Command.SCREEN, 2));
		
		tex_ip = new TextField("IP:", "127.0.0.1", 15, TextField.ANY);
		tex_port = new TextField("Port:", "10647", 5, TextField.NUMERIC);
		
		form.append(tex_ip);
		form.append(tex_port);
	}
	
	public void commandAction(Command c, Displayable s){
		String tha_label = c.getLabel();
		
		if (tha_label.equals("Connect")){
			try{
				String tha_ip = this.tex_ip.getString();
				String tha_port = this.tex_port.getString();
				String connection_string = "socket://" + tha_ip + ":" + tha_port;
				
				System.err.println("Connecting...");
				this.mobil_klient.DoGPRSConnect(connection_string);
				
				System.err.println("Thread...");
				
				//Socket-read-thread.
				this.mobil_klient.thread_listen = new Thread(new ThreadListen(this.mobil_klient));
				this.mobil_klient.thread_listen.start();
				
				System.err.println("Show programs...");
				
				//Show programs.
				this.mobil_klient.showPrograms();
			}catch(Exception e){
				System.err.println("Exception: " + e);
			}
		}else if(tha_label.equals("Back")){
			//Show the ListConnection()-list once again.
			//this.mobil_klient.display.setCurrent();
		}else{
			System.err.println("Command not understood: " + c);
		}
	}
}