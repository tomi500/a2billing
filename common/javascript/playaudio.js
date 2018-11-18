var prevel,
    Audio1 = $("#sound1")[0];

Audio1.addEventListener("ended",  function(){prevel.src="./templates/default/images/control_play.png";},true);
Audio1.addEventListener("playing",function(){prevel.src="./templates/default/images/control_pause.png";},true);
Audio1.addEventListener("pause",  function(){prevel.src="./templates/default/images/control_play.png";},true);

function GreetPlay(el, soundpath)
{
	if (prevel != el || Audio1.paused)
	{
	    Audio1.pause();
	    if (prevel) {
		if (prevel != el) Audio1.currentTime = 0;
		prevel.src = "./templates/default/images/flv.gif";
	    }
	    el.src = "./templates/default/images/control_pause.png";
	    if (prevel != el) Audio1.src = soundpath;
	    prevel = el;
	    Audio1.play();
	} else {
	    Audio1.pause();
	    el.src = "./templates/default/images/control_play.png";
	}
	return;
}
