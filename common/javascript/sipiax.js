
// Function to validate is a string is numeric
function IsNumeric(sText)
{
	var ValidChars = "0123456789";
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

function clear_textbox()
{
	if (!IsNumeric(document.theForm.startnumber.value))
	document.theForm.startnumber.value = "";
}

function clear_textbox2()
{
	if (!IsNumeric(document.theForm.quantity.value))
		document.theForm.quantity.value = "";
}

function keytoDownNumber(e,id_el)
{
	if (e.keyCode!=13) {
		var key = (typeof e.charCode == 'undefined' ? e.keyCode : e.charCode);
		if (e.ctrlKey || e.altKey || (key>47 && key<58) || key==0)  {
			document.getElementById(id_el).style.color = "blue";
			return true;
		}
		else  return false;
	}
	else  return true;
}
