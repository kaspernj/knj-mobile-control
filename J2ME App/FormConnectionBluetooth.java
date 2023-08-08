class FormConnectionBluetooth implements CommandListener{
	MobilKlient mobil_klient;
	Form form;
	
	public FormConnectionBluetooth(MobilKlient in_mobil_klient){
		this.mobil_klient = in_mobil_klient;
		
		form = new Form("Enter details");
	}
	
	public void commandAction(Command c, Displayable s){
		if (c == List.SELECT_COMMAND){
			System.err.println("Select pressed.");
		}else{
			System.err.println("Command not understood: " + c);
		}
	}
}