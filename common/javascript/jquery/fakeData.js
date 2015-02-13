var PopUpDayTimeJson = {
	"sheduleArray": [
        {
            "weekdays": "",
            "timefrom": "0",
            "timetill": "0"
        }
	]
};

$(document).ready(function(){
// Create a hidden input element, and append it to the form:
    function addHidden(theForm, key, value) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        theForm.appendChild(input);
    }
// Shows JSON in nice popup
    function showFormJson(json,pt) {
        var jsonText = JSON.stringify(json, null, "    ");
        $('#popup')
            .empty()
            .append(pt)
            .append( $('<pre></pre>').append(jsonText) )
            .dialog({
                title: "JSON representation of the form",
                width: 600,
                height: 500
            });
    }
// Simple form demo
	// Let's create the iFrame used to send our data
//	var iframe = document.createElement("iframe");
//	    iframe.name = "myTarget";
	// Next, attach the iFrame to the main document
//	window.addEventListener("load", function () {
//	    iframe.style.display = "none";
//	    document.body.appendChild(iframe);
//	});
    $('#myForm').jqDynaForm();
    $('#myForm').jqDynaForm('set', PopUpDayTimeJson);
    $('#myForm').submit(function(event){
	var json = $('#myForm').jqDynaForm('get');
	var postTo = $('#myForm').attr('action');
//	json = /^([^\\"]|\\.)*/.match(json);
//	json.replace('/(["\'\])/g', "\\$1");
	var data = {json_data:JSON.stringify(json)};
//	document.myForm.elements[].length = 0;
//	addHidden(document.myForm, 'test', 'rrrrrrrrrrrrrrrrrrrrrrrrr');
//	document.myForm.submit();
//	document.myForm.elements["dowdow"].remove();
//	document.myForm.elements["timeshiftfirst"].remove();
//	document.myForm.elements["timeshiftlast"].remove();
//	$.post( postTo, { json_data : json }, function(response){ $(document).append(response); }, "html");
//	var formData = new FormData();
//	formData.append('json_data', json);
//	formData.form_action.value = postTo;
//	addHidden(formData, 'test', 'rrrrrrrrrrrrrrrrrrrrrrrrr');
//	formData.submit();
	
	var name,
		form = document.createElement("form"),
		node = document.createElement("input");
	// Define what should happen when the response is loaded
//	iframe.addEventListener("load", function () {
//		alert("Yeah! Data sent.");
//	});
	form.action = postTo;
//	form.target = "_blank";
	form.method = "POST";
	for(name in data) {
		node.name  = name;
		node.value = data[name].toString();
		form.appendChild(node.cloneNode());
	}
	// To be sent, the form needs to be attached to the main document.
	form.style.display = "none";
	document.body.appendChild(form);
//	document.myForm = form;
	form.submit();
	// But once the form is sent, it's useless to keep it.
	document.body.removeChild(form);

//	var xhr = new XMLHttpRequest();
//	xhr.open('POST', postTo, true);
//	xhr.send(formData);
//	var posting = $.post( postTo, { json_data: json });
//	posting.done(function( data ) {
//		var content = $( data ).find( "#content" );
//		window.empty().append( content );
//	}
//	, $('#page-wrap').html(data); );
//	showFormJson(json,postTo);
//	return false;
//	event.preventDefault();
	return false;
    });

});