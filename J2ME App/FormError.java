import javax.microedition.io.*;
import javax.microedition.midlet.*;

class FormError implements CommandListener{
	Form last_form;
	List last_list;
	String error;
	Form form;
	Display display;
	
	public FormError(Form in_last_form, Display in_display, String message){
		last_form = in_last_form;
		display = in_display;
		error = message;
		this.showError();
	}
	
	public FormError(List in_last_list, Display in_display, String message){
		last_list = in_last_list;
		display = in_display;
		error = message;
		this.showError();
	}
	
	public void showError(){
		form = new Form("Error");
		form.append(error);
		form.setCommandListener(this);
		form.addCommand(new Command("OK", Command.SCREEN, 1));
		
		this.display.setCurrent(this.form);
	}
	
	public void commandAction(Command c, Displayable s){
		String tha_label = c.getLabel();
		
		if (tha_label.equals("OK")){
			if (this.last_form != null){
				display.setCurrent(this.last_form);
			}else if(this.last_list != null){
				display.setCurrent(this.last_list);
			}
		}
	}
}