var lasttimeout = null;

$("#ui_language").change(function () {
    self.location.href= "?ui_language="+$("#ui_language option:selected").val();
});

$(function() {
    $(".btn_in").click(function() {
	$(".forms").removeClass("forms-right");
	$(".forms").removeClass("forms-left");
//	$(".frame").removeClass("frame-long");
	$(".signin-active").removeClass("signin-inactive");
	$(".signup-inactive").removeClass("signup-active");
	$(".signup-inactive a").css('color','rgba(255,255,255,.3)');
	scrnum = 2;
    });
});

$(function() {
    $(".btn_up").click(function() {
	$(".forms").removeClass("forms-right");
	$(".forms").addClass("forms-left");
//	$(".frame").addClass("frame-long");
	$(".signup-inactive a").removeAttr('style');
	$(".signup-inactive").addClass("signup-active");
	$(".signin-active").addClass("signin-inactive");
	scrnum = 3;
    });
});

$(function() {
    $(".forgot a").click(function() {
	$(".forms").addClass("forms-right");
	$(".signin-active").addClass("signin-inactive");
	$(".signup-inactive").removeClass("signup-active");
	scrnum = 1;
    });
});

$('.select-styling').mouseenter(function () {
    $("input:focus,select:focus").blur();
});

$('.nav').mouseover(function () {
    $('.nav select:focus').blur();
});

$('body').on('click', '.countryselect', function () {
    $('#countrylist').css('display','none');
    $('#pr_country').val($(this).html());
    $('#pr_country').attr('dataval',$(this).attr('dataval'));
    $('.select-styling').mouseover();
    $('.select-styling').mouseleave();
    $("input").blur();
    setTimeout(function(){
	$('#countrylist').removeAttr('style');
    },350);
});
$('body').on('click', '.zoneselect', function () {
    $('#zonelist').css('display','none');
    $('#timezone').val($(this).html());
    $('#timezone').attr('dataval',$(this).attr('dataval'));
    $('.select-styling').mouseover();
    $('.select-styling').mouseleave();
    $("input").blur();
    setTimeout(function(){
	$('#zonelist').removeAttr('style');
    },350);
});

$(function() {
    $(".btn-signup").click(function() {
	$(".nav").toggleClass("nav-up");
	$('#country').val($('#pr_country').attr('dataval'));
	$('#id_timezone').val($('#timezone').attr('dataval'));
//	$(".form-signup-left").toggleClass("form-signup-down");
//	$(".success").toggleClass("success-left"); 
//	$(".frame").toggleClass("frame-short");
    });
});

function validateEmail(email) {
  var pattern = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
  return pattern.test(email);
}

$('#pr_email').keydown(function(e){
    if(e.keyCode == 13) {
            $('.btn-forgot').click();
            return false;
    }
    return true;
});

$('body').on('click', '.btn-forgot', function () {
	var warning = $("#warningforgot");
	if(pr_email.value=="" || !validateEmail(pr_email.value))
	{
/*	    var shrinktimer = setInterval(function(){
		if (warning.html() === '')
		    warning.html(emptyemail);
		else
		    warning.html('');
	    },100);
	    setTimeout(function(){
		clearInterval(shrinktimer);
		warning.html(emptyemail);
	    },2000);
*/
	    warning.html(emptyemail);
	    lasttimeout = setTimeout(function(){
		warning.html('');
	    },5000);
	    return false;
	}
	warning.html('');
	$(".nav").addClass("nav-up");
//	$(".btn-animate").toggleClass("btn-animate-grow");
//	$(".form-forgot").submit();

	var url = location.origin+location.pathname;
	url = url.replace(".php","");
	url = url.replace("/index","");
	url = url.replace(/^\/+|\/+$/g, '');
	var body = 'action=email&pr_email='+pr_email.value;
	var xhttp = new XMLHttpRequest();
	xhttp.open("POST",url,true);
	xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhttp.timeout = 5000;
	xhttp.onreadystatechange = function() {
	    if (xhttp.readyState == 4)
	    if (xhttp.status == 200) {
		var errorForgot = xhttp.responseXML.getElementsByTagName('response')[0].getElementsByTagName('error')[0].childNodes[0].nodeValue;
		var answerForgot = xhttp.responseXML.getElementsByTagName('response')[0].getElementsByTagName('forgotString')[0].childNodes[0].nodeValue;
		if (lasttimeout) {
		    clearTimeout(lasttimeout);
		    lasttimeout = null;
		}
		if (errorForgot==5) {
		    $('.form-forgot').html('<div class="login-title"></div><div class="login-title">'+answerForgot+'</div>');
		    lasttimeout = setTimeout(function(){
			$(".nav").removeClass("nav-up");
		    },5000);
		} else {
		    warning.html(answerForgot);
		    setTimeout(function(){
			$(".nav").removeClass("nav-up");
		    },5000);
		    lasttimeout = setTimeout(function(){
			warning.html('');
		    },10000);
		}
	    } else {
		warning.html(noservice);
		lasttimeout = setTimeout(function(){
		    warning.html('');
		    $(".nav").removeClass("nav-up");
		},5000);
	    }
	};
	xhttp.send(body);
	return false;
});

$('#pr_login,#pr_password').keydown(function(e){
    if(e.keyCode == 13) {
            $('.btn-signin').click();
            return false;
    }
    return true;
});

$('body').on('click', '.btn-signin', function () {
	var warning = $("#warningsignin");
	if(pr_password.value.length<6 || pr_login.value.length<6)
	{
	    warning.html(emptylogin);
	    setTimeout(function(){
		warning.html('');
	    },10000);
	    return false;
	}
	warning.html('');
	$(".nav").addClass("nav-up");

	var url = location.origin+location.pathname;
	url = url.replace(".php","");
	url = url.replace("/index","");
	url = url.replace(/^\/+|\/+$/g, '');
	url += '/userinfo';
	var body = 'done=submit_sig&pr_login='+pr_login.value+'&pr_password='+pr_password.value;
	var xhttp = new XMLHttpRequest();
	xhttp.open("POST",url,true);
	xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhttp.timeout = 5000;
	xhttp.onreadystatechange = function() {
	    if (xhttp.readyState == 4)
	    if (xhttp.status == 200) {
		var errorForgot = xhttp.responseXML.getElementsByTagName('response')[0].getElementsByTagName('error')[0].childNodes[0].nodeValue;
		var answerForgot = xhttp.responseXML.getElementsByTagName('response')[0].getElementsByTagName('signinString')[0].childNodes[0].nodeValue;
		if (lasttimeout) {
		    clearTimeout(lasttimeout);
		    lasttimeout = null;
		}
		if (errorForgot==0) {
		    location.assign(url);
		    return false;
		} else {
		    warning.html(answerForgot);
		    setTimeout(function(){
			$(".nav").removeClass("nav-up");
		    },5000);
		    lasttimeout = setTimeout(function(){
			warning.html('');
		    },10000);
		}
	    } else {
		warning.html(noservice);
		lasttimeout = setTimeout(function(){
		    warning.html('');
		    $(".nav").removeClass("nav-up");
		},5000);
	    }
	};
	xhttp.send(body);
	return false;
});

var touch_position; // Координата нажатия
var scrnum = 2;

function turn_start(event) {
    // При начальном нажатии получить координаты
    touch_position = event.touches[0].pageX;
}
function turn_page(event) {
    // При движении нажатия отслеживать направление движения
    if (touch_position==null) { return false; }
    var tmp_move = touch_position-event.touches[0].pageX;
    touch_position = null;
    // Сдвиг достаточный?
    if (Math.abs(tmp_move)<10) { return false; }
    if (tmp_move<0) {
        // Листаем вправо
	scrnum--;
    }
    else {
        // Листаем влево
	scrnum++;
    }
    if (scrnum<=1) $(".forgot a").click();
    else if (scrnum==2) $(".btn_in").click();
    else if (scrnum>=3) $(".btn_up").click();
    return false;
}
