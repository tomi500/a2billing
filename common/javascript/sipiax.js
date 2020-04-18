
// Function to validate is a string is numeric
function IsNumeric(sText)
{
	var ValidChars = "0123456789*#";
	var IsNumber=true;
	var Char;
	var len=sText.length;
	
	if (len == 0) IsNumber=false;
	else
	for (i = 0; i < len && IsNumber == true; i++)
	{
		Char = sText.charAt(i);
		if (ValidChars.indexOf(Char) == -1)
		{
			IsNumber = false;
		}
	}
	return IsNumber;
}


function openURL(theLINK,maxQuantity,QuantityAlert,NumericAlert,min,max)
{
	startnumber = document.theForm.startnumber.value;

	quantity = document.theForm.quantity.value;

	if ( (!IsNumeric(quantity)) || (!IsNumeric(startnumber)) || (quantity == '0') || (startnumber == '0') ){
		alert(NumericAlert);
		return;
	}
	if (quantity > maxQuantity){
		alert(QuantityAlert);
		return;
	}
	if ( (startnumber < min) || (max < parseInt(startnumber) + parseInt(quantity) -1) ){
		alert('values beyond the boundaries of permissible');
		return;
	}

	goURL = startnumber + "&quantity=" + quantity;
	
	self.location.href = theLINK + goURL;
	
	return false;
}

function clear_textbox(id_el)
{
	if (!IsNumeric(id_el.value))
		id_el.value = "";
}

function keytoDownNumber(e,id_el)
{
	if (e.keyCode!=13) {
		if (e.ctrlKey || e.altKey || (e.key >= '0' && e.key <= '9') || e.key == '*' || e.key == '#')  {
			clear_textbox(id_el);
			id_el.style.color = "blue";
		} else if (e.key == 'Delete' || e.key == 'Backspace') {
			clear_textbox(id_el);
		} else  return false;
		return true;
	} else	return true;
}
