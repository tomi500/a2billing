function hidez(s)
 {
 show(s);
 var theLayer = getElement(s);
 if (layerobject)
  theLayer.display = 'block';
 else
  theLayer.style.display = 'block';
 }
function rehidez(s)
 {
 hide(s);
 var theLayer = getElement(s);
 if (layerobject)
  theLayer.display = 'none';
 else
  theLayer.style.display = 'none';
 }
function dede(s,ss)
 {
  var im = getElement(ss);
  if (!im.v11uivis)
   { 
    im.v11uivis = true;
    hidez(s);
    im.src='/images/arru.jpg';
   }
 else {
   im.v11uivis = false;
   rehidez(s);
   im.src = '/images/arrd.jpg';
  }
 }

var aup = new Image();
    aup.src ="/images/arru.jpg";
var ado = new Image();
    ado.src ="/images/arrd.jpg";

