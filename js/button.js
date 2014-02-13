/* 
   Float Submit Button To Right Edge Of Window
   Version 1.0
   April 11, 2010

   Will Bontrager
   http://www.willmaster.com/
   Copyright 2010 Bontrager Connection, LLC

   Generated with customizations on February 06, 2014 at
   http://www.willmaster.com/library/manage-forms/floating-submit-button.php

   Bontrager Connection, LLC grants you 
   a royalty free license to use or modify 
   this software provided this notice appears 
   on all copies. This software is provided 
   "AS IS," without a warranty of any kind.
*/

//*****************************//

/** Five places to customize **/

// Place 1:
// The id value of the button.

var ButtonId = "Update";


// Place 2:
// The width of the button.

var ButtonWidth = 100;


// Place 3:
// Left/Right location of button (specify "left" or "right").

var ButtonLocation = "right";


// Place 4:
// How much space (in pixels) between button and window left/right edge.

var SpaceBetweenButtonAndEdge = 10;


// Place 5:
// How much space (in pixels) between button and window top edge.
var SpaceBetweenButtonAndTop = 73;



/** No other customization required. **/

//************************************//

TotalWidth = parseInt(ButtonWidth) + parseInt(SpaceBetweenButtonAndEdge);
ButtonLocation = ButtonLocation.toLowerCase();
ButtonLocation = ButtonLocation.substr(0,1);
var ButtonOnLeftEdge = (ButtonLocation=='l') ? true : false;

function AddButtonPlacementEvents(f)
{
   var cache = window.onload;
   if(typeof window.onload != 'function') { window.onload = f; }
   else { window.onload = function() { if(cache) { cache(); } f(); }; }
   cache = window.onresize;
   if(typeof window.onresize != 'function') { window.onresize = f; }
   else { window.onresize = function() { if(cache) { cache(); } f(); }; }
}

function WindowHasScrollbar() {
var ht = 0;
if(document.all) {
   if(document.documentElement) { ht = document.documentElement.clientHeight; }
   else { ht = document.body.clientHeight; }
   } 
else { ht = window.innerHeight; }
if (document.body.offsetHeight > ht) { return true; }
else { return false; }
}

function GlueButton(ledge) {
var did = document.getElementById(ButtonId);
did.style.top = SpaceBetweenButtonAndTop + "px";
did.style.width = ButtonWidth + "px";
did.style.left = ledge + "px";
did.style.display = "block";
did.style.zIndex = "9999";
did.style.position = "fixed";
}

function PlaceTheButton() {
if(ButtonOnLeftEdge) {
   GlueButton(SpaceBetweenButtonAndEdge);
   return;
   }
if(document.documentElement && document.documentElement.clientWidth) { GlueButton(document.documentElement.clientWidth-TotalWidth); }
else {
   if(navigator.userAgent.indexOf('MSIE') > 0) { GlueButton(document.body.clientWidth-TotalWidth+19); }
   else {
      var scroll = WindowHasScrollbar() ? 0 : 15;
      if(typeof window.innerWidth == 'number') { GlueButton(window.innerWidth-TotalWidth-15+scroll); }
      else { GlueButton(document.body.clientWidth-TotalWidth+15); }
      }
   }
}

AddButtonPlacementEvents(PlaceTheButton);
