    var background = $('<div>').css({
      'backgroundColor': '#000',
      'width': '100%',
      'height': '100%',
      'position': 'fixed',
      'z-index': '1',
      'top': '0',
      'left': '0',
      'opacity': '0.2'
    });

    var image = $('<img>').attr({
      'src': 'javascript/jquery/images/telephone.gif'
    });

    var loading = $('<div>').css({
      'z-index': '100',
      'position': 'fixed',
      'top': '40%',
      'left': '50%',
      'margin': '-64px 0 0 -64px',
      'text-align': 'center'
    }).html(image);

function mysubmit() { $('body').prepend(loading).prepend(background);document.myForm.submit(); }

$(document).ready(function(){
    $('#submit4').click(function () {
	$('body').prepend(loading).prepend(background);
    });
});
