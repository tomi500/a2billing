
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

function allowOnlyDigits(id_el) {
  var tval = id_el.value.replace(/[^\d]/g,'');
  if (id_el.value==tval) id_el.style.color = "blue";
  id_el.value = tval;
}

function allowOnlyNumberPad(id_el) {
  var tval = id_el.value.replace(/[^\d*#]/g,'');
  if (id_el.value==tval) id_el.style.color = "blue";
  if (document.querySelector("#"+id_el.getAttribute("list")+" option[value='"+id_el.value+"']")===null) id_el.value = tval;
}
