$(document).ready(CheckInput);
$(document).ready(DatePicker);  //datepicker not found?????
//$(document).ready(TimePicker);
$(document).ready(DisableEnterKey);	// disable enter key for forms
$(document).ready(ShowImage);
function CheckInput()
{
	$(".checkint").on("change",function()
	{ 
		var regex = new RegExp(/^\d+$/);
		PranaWarning(this,regex);
	});
	$(".checkemail").on("change",function()
	{ 
		var regex = /(^\S+@\S+\.\S+$)/i;
		PranaWarning(this,regex);
	});
	$(".checkphone").on("change",function()
	{ 
		var regex = /(^\+[0-9]{2}|^\+[0-9]{2}\(0\)|^\(\+[0-9]{2}\)\(0\)|^00[0-9]{2}|^0)([0-9]{9}$|[0-9\-\s]{10}$)/i;
		PranaWarning(this,regex);
	});
	$(".checkbankrekening").on("change",function()
	{ 
		var regex = /(^[a-zA-Z]{2}[0-9]{2}[a-zA-Z0-9]{4}[0-9]{7}([a-zA-Z0-9]?){0,16}$)/i;
		PranaWarning(this,regex);
	});
	// calcfield should be 9
	$(".checkcalcfield").on("change",function()
	{ 
		var regex = /(^acht$)/i;
		PranaWarning(this,regex);
	});
}
function PranaWarning(element,regex)
{
	var cssborder = { 'borderColor': 'red' };
	var cssoldborder = { 'borderColor': '' };
	var error = $(element).siblings("span").text();
	if(regex.test($(element).val()) == false)
	{
		alert (error);
		$(element).css(cssborder);
		$(element).addClass("invalid");
		$(element).removeClass("valid");
	}
	else
	{
		$(element).addClass("valid");
		$(element).removeClass("invalid");
		$(element).css(cssoldborder);
	}
}
//
// ValForm - general validate form function
//
function ValForm()
{
	if(buttonclicked == "cancel")
	{
		return true;			// don't validate if cancel was clicked
	}
	if(buttonclicked == "store")
	{
		return(Validate());
	}
	return true;
}
function Validate()
{
	//
	// check email
	//
	if(document.getElementById("email"))
	{
		var value = document.getElementById("email");
		var regex = /\S+@\S+\.\S+/;
		if(regex.test(value.value) == false)
		{
			document.getElementById("email").style.borderColor = "red";
			alert('fout emailadres');
			return false;
		}
	}
	//
	// check calcfield
	//
	if(document.getElementById("calcfield"))
	{
		var value = document.getElementById("calcfield");
		var regex = /(^acht$)/i;
		if(regex.test(value.value) == false)
		{
			document.getElementById("calcfield").style.borderColor = "red";
			alert('fout controleveld');
			return false;
		}
	}
		//
	// is terms and conditions checked?
	//
	if(document.getElementById("terms"))
	{
		if (document.getElementById('terms').checked == false) 
		{
			alert("check algemene voorwaarden");
			document.getElementById("terms").style.borderColor = "red";
            return false;
        } 
		return true;
	}

}

function DatePicker()
{
	$('.datepicker').datepicker(
	
	{
		dateFormat : 'dd-mm-yy',
		changeYear : true,
		yearRange: '1930:2024',
		monthNames : ['januari', 'februari', 'maart', 'april', 'mei', 'juni','juli', 'augustus', 'september', 'oktober', 'november', 'december']
	}
	);
}

function TimePicker()
{
	$('#fromtime').timepicker(
	{
		timeFormat: 'HH:mm',
			interval: 60,
			defaultTime: '08:00',
			startTime: '08:00',
			minTime: '08:00',
			maxTime: '17:00',
			dynamic: false,
			dropdown: true,
			scrollbar: true
	}
	);
	$('#tilltime').timepicker(
		{
			timeFormat: 'HH:mm',
			interval: 60,
			defaultTime: '17:00',
			startTime: '08:00',
			minTime: '08:00',
			maxTime: '17:00',
			dynamic: false,
			dropdown: true,
			scrollbar: true
		}
	);
}
/*
function TimePicker()
{
	$('.timepicker').pDatepicker();
}
*/


function SetPopover() 
{
	$('[data-toggle="popover"]').popover();
}
/**
 * Disable the enter key for submitting a form
 */
function DisableEnterKey()
{
	$("form").keypress(function(e) 
	{
		//Enter key
		if (e.which == 13) {
	 	 return false;
		}
  	});
}

function DragCrop()
{
	$( "#crop_div" ).draggable({ containment: "parent" });
}
 
function crop()
{
	var posi = document.getElementById('crop_div');
	document.getElementById("top").value=posi.offsetTop;
	document.getElementById("left").value=posi.offsetLeft;
	document.getElementById("right").value=posi.offsetWidth;
	document.getElementById("bottom").value=posi.offsetHeight;
	return true;
}
//
// change the src of the image with id = showphoto to the file which is choosen by file element with class showfile
//
function ShowImage(event)
{
	$(".showimage").on("change",function(event)
	{
		$(this).siblings("img").attr('src',URL.createObjectURL(event.target.files[0]));
	});
}
//
// display next div element
//
function DisplayElement(id)
{
	document.getElementById(id).style.display = 'block';
}
function HideElement(id)
{
	document.getElementById(id).style.display = 'none';
}