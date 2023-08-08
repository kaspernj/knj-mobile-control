import javax.microedition.io.*;
import javax.microedition.midlet.*;

class ListConnections implements CommandListener{
	List list;
	MobilKlient mobil_klient;
	
	public ListConnections(MobilKlient in_mobilklient){
		mobil_klient = in_mobilklient;
		
		list = new List("Choose connection", List.IMPLICIT);
		list.setCommandListener(this);
		
		list.append("GPRS", null);
		list.append("Bluetooth", null);
	}
	
	public void commandAction(Command c, Displayable s){
		if (c == List.SELECT_COMMAND){
			int tha_choice = this.list.getSelectedIndex();
			
			if (tha_choice == 0){
				//GPRS
				FormConnectionGPRS tha_form = new FormConnectionGPRS(this.mobil_klient);
				this.mobil_klient.display.setCurrent(tha_form.form);
			}else if(tha_choice == 1){
				//Bluetooth
				//Bluetooth is not implemented yet.
			}
		}else{
			System.err.println("Command not understood: " + c);
		}
	}
}