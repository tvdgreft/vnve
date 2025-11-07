// javascripts voor docman
//
function ValFormGrenzenloos()
{
	if(buttonclicked == "cancel")
	{
		return true;			// don't validate if cancel was clicked
	}
	if(buttonclicked == "store")
	{
		return(ValidateRecord());
	}
	return true;
}
function ValidateRecord()
{
	return true;
}
function basename (path) 
{
	return path.substring(path.lastIndexOf('\\') + 1)
}