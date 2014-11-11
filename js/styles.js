
/*
Functionality for dynamically adding and removing classes from the CSS stylesheets
These functions dynamically add and remove CSS stylings in order to make events
light up when moused over, return to their normal state when moused out of, and change color
when clicked.  Clearly right now, this functionality is a mess, and needs to be abstracted.
*/


/*
Keep track of the last event that was clicked, so that when you click on another event the 
system knows to revert the last event back to its original color.
*/
var last_event = "";
var last_type = "";
var styles = new Array();

function resetStyles() {
	if (last_event != "") {
		var start = "." + last_event + "Start";
		var mid = "." + last_event + "Middle";
		var end = "." + last_event + "End";
		var fadeIn = "." + last_event + "FadeIn";
		var fadeOut = "." + last_event + "FadeOut";
		var halfHour = "." + last_event + "HalfHour";
		
		/*This handles IE*/
		if (!document.styleSheets) return;
		var ss = document.styleSheets;
		if(ss[3].rules){
			var i = ss[3].rules.length;
			ss[3].removeRule(i-1);
			
			i = ss[3].rules.length;
			ss[3].removeRule(i-1);
			
			i = ss[3].rules.length;
			ss[3].removeRule(i-1);
			
			i = ss[3].rules.length;
			ss[3].removeRule(i-1);
			
			i = ss[3].rules.length;
			ss[3].removeRule(i-1);
			
			i = ss[3].rules.length;
			ss[3].removeRule(i-1);
			
			ss[3].addRule(start, styles[1]);
			ss[3].addRule(mid, styles[2]);
			ss[3].addRule(end, styles[3]);
			ss[3].addRule(fadeIn, styles[4]);
			ss[3].addRule(fadeOut, styles[5]);
			ss[3].addRule(halfHour, styles[6]);
		}
		else{ /*This handles all other browsers (Firefox)*/
			var i = ss[3].cssRules.length;
			var style = start + "{" + styles[1] + "}";
			ss[3].insertRule(style, i);
			
			i = ss[3].cssRules.length;
			style = mid + "{" + styles[2] + "}";
			ss[3].insertRule(style, i);
			
			i = ss[3].cssRules.length;
			style = end + "{" + styles[3] + "}";
			ss[3].insertRule(style, i);
			
			i = ss[3].cssRules.length;
			style = fadeIn + "{" + styles[4] + "}";
			ss[3].insertRule(style, i);
			
			i = ss[3].cssRules.length;
			style = fadeOut + "{" + styles[5] + "}";
			ss[3].insertRule(style, i);
			
			i = ss[3].cssRules.length;
			style = halfHour + "{" + styles[6] + "}";
			ss[3].insertRule(style, i);
		}
	}
}

function resetStyles2() {
	if (last_event != "") {
		var ident = "." + last_event;
		
		/*This handles IE*/
		if (!document.styleSheets) return;
		var ss = document.styleSheets;
		if(ss[3].rules){
			var i = ss[3].rules.length;
			ss[3].removeRule(i-1);
			ss[3].addRule(ident, styles[1]);
		}
		else{ /*This handles all other browsers (Firefox)*/
			var i = ss[3].cssRules.length;
			var style = ident + "{" + styles[1] + "}";
			ss[3].insertRule(style, i);
		}
	}
}

/*
This is the function that changes the color of an event when a user clicks on it.
The color is changed to a bolder/darker color to signify that it is selected.
The function passes the id of the event, so it knows which event to change, 
as well as the type (work, personal, etc.) so it knows what color to change it to.
*/
function eventOnClick(id, type)
{
	var start = "." + id + "Start";
	var mid = "." + id + "Middle";
	var end = "." + id + "End";
	var fadeIn = "." + id + "FadeIn";
	var fadeOut = "." + id + "FadeOut";
	var halfHour = "." + id + "HalfHour";
	
	resetStyles();
	last_event = id;
	
		switch(type)
		{
			case '1': 
				styles[1] = "border-left: 1px solid #993333; border-top: 1px solid #993333; border-right: 1px solid #993333; padding-left: 3px; padding-top: 2px; background-color: #efadab;";
				styles[2] = "border-left: 1px solid #993333; border-right: 1px solid #993333; background-color: #efadab;";
				styles[3] = "border-left: 1px solid #993333; border-right: 1px solid #993333; border-bottom: 1px solid #993333; height: 17px; background-color: #efadab;";
				styles[4] = "background-image: url('../images/classIn.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 15px; padding-left: 3px;";
				styles[5] = "background-image: url('../images/classOut.gif'); background-color: white; background-repeat: no-repeat; border: none;";
				styles[6] = "border: 1px solid #993333; padding-left: 3px; padding-top: 2px; background-color: #efadab; height: 14px;";
				break;
				
			case '2': 
				styles[1] = "border-left: 1px solid #333366; border-top: 1px solid #333366; border-right: 1px solid #333366; padding-left: 3px; padding-top: 2px; background-color: #9c9ccd;";
				styles[2] = "border-left: 1px solid #333366; border-right: 1px solid #333366; background-color: #9c9ccd;";
				styles[3] = "border-left: 1px solid #333366; border-right: 1px solid #333366; border-bottom: 1px solid #333366; height: 17px; background-color: #9c9ccd;";
				styles[4] = "background-image: url('../images/workIn.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 15px; padding-left: 3px;";
				styles[5] = "background-image: url('../images/workOut.gif'); background-color: white; background-repeat: no-repeat; border: none;";
				styles[6] = "border: 1px solid #333366; padding-left: 3px; padding-top: 2px; background-color: #9c9ccd; height: 14px;";
				break;
				
			case '3': 
				styles[1] = "border-left: 1px solid #006600; border-top: 1px solid #006600; border-right: 1px solid #006600; padding-left: 3px; padding-top: 2px; background-color: #8aceac;";
				styles[2] = "border-left: 1px solid #006600; border-right: 1px solid #006600; background-color: #8aceac;";
				styles[3] = "border-left: 1px solid #006600; border-right: 1px solid #006600; border-bottom: 1px solid #006600; height: 17px; background-color: #8aceac;";
				styles[4] = "background-image: url('../images/personalIn.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 15px; padding-left: 3px;";
				styles[5] = "background-image: url('../images/personalOut.gif'); background-color: white; background-repeat: no-repeat; border: none;";
				styles[6] = "border: 1px solid #006600; padding-left: 3px; padding-top: 2px; background-color: #8aceac; height: 14px;";
				break;
				
			case '4': 
				styles[1] = "border-left: 1px solid #CC6600; border-top: 1px solid #CC6600; border-right: 1px solid #CC6600; padding-left: 3px; padding-top: 2px; background-color: #f7be76;";
				styles[2] = "border-left: 1px solid #CC6600; border-right: 1px solid #CC6600; background-color: #f7be76;";
				styles[3] = "border-left: 1px solid #CC6600; border-right: 1px solid #CC6600; border-bottom: 1px solid #CC6600; height: 17px; background-color: #f7be76;";
				styles[4] = "background-image: url('../images/meetingIn.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 15px; padding-left: 3px;";
				styles[5] = "background-image: url('../images/meetingOut.gif'); background-color: white; background-repeat: no-repeat; border: none;";
				styles[6] = "border: 1px solid #CC6600; padding-left: 3px; padding-top: 2px; background-color: #f7be76; height: 14px;";
				break;
	
			case '5': 
				styles[1] = "border-left: 1px solid #fbcd13; border-top: 1px solid #fbcd13; border-right: 1px solid #fbcd13; padding-left: 3px; padding-top: 2px; background-color: #FFFF99;";
				styles[2] = "border-left: 1px solid #fbcd13; border-right: 1px solid #fbcd13; background-color: #FFFF99;";
				styles[3] = "border-left: 1px solid #fbcd13; border-right: 1px solid #fbcd13; border-bottom: 1px solid #fbcd13; height: 17px; background-color: #FFFF99;";
				styles[4] = "background-image: url('../images/tempIn.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 15px; padding-left: 3px;";
				styles[5] = "background-image: url('../images/tempOut.gif'); background-color: white; background-repeat: no-repeat; border: none;";
				styles[6] = "border: 1px solid #fbcd13; padding-left: 3px; padding-top: 2px; background-color: #FFFF99; height: 14px;";
				break;
		}
	
	
	last_type = type;
	
	switch(type)
	{
		case '1': 
			var startClass = "border-left: 3px solid #993333; border-top: 3px solid #993333; border-right: 3px solid #993333; padding-left: 1px; padding-top: 0px; background-color: red;";
			var midClass = "border-left: 3px solid #993333; border-right: 3px solid #993333; background-color: red;";
			var endClass = "border-left: 3px solid #993333; border-right: 3px solid #993333; border-bottom: 3px solid #993333; height: 15px; background-color: red;";
			var fadeInClass = "background-image: url('../images/classInHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; padding-left: 3px; height: 17px;";
			var fadeOutClass = "background-image: url('../images/classOutHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
			var halfHourClass = "border: 3px solid #993333; padding-left: 1px; padding-top: 0px; background-color: red; height: 12px;";
			break;
			
		case '2': 
			var startClass = "border-left: 3px solid #333366; border-top: 3px solid #333366; border-right: 3px solid #333366; padding-left: 1px; padding-top: 0px; background-color: blue;";
			var midClass = "border-left: 3px solid #333366; border-right: 3px solid #333366; background-color: blue;";
			var endClass = "border-left: 3px solid #333366; border-right: 3px solid #333366; border-bottom: 3px solid #333366; height: 15px; background-color: blue;";
			var fadeInClass = "background-image: url('../images/workInHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; padding-left: 3px; height: 17px;";
			var fadeOutClass = "background-image: url('../images/workOutHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
			var halfHourClass = "border: 3px solid #333366; padding-left: 1px; padding-top: 0px; background-color: blue; height: 12px;";
			break;
			
		case '3': 
			var startClass = "border-left: 3px solid #006600; border-top: 3px solid #006600; border-right: 3px solid #006600; padding-left: 1px; padding-top: 0px; background-color: green;";
			var midClass = "border-left: 3px solid #006600; border-right: 3px solid #006600; background-color: green;";
			var endClass = "border-left: 3px solid #006600; border-right: 3px solid #006600; border-bottom: 3px solid #006600; height: 15px; background-color: green;";
			var fadeInClass = "background-image: url('../images/personalInHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; padding-left: 3px; height: 17px;";
			var fadeOutClass = "background-image: url('../images/personalOutHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
			var halfHourClass = "border: 3px solid #006600; padding-left: 1px; padding-top: 0px; background-color: green; height: 12px;";
			break;
			
		case '4': 
			var startClass = "border-left: 3px solid #CC6600; border-top: 3px solid #CC6600; border-right: 3px solid #CC6600; padding-left: 1px; padding-top: 0px; background-color: orange;";
			var midClass = "border-left: 3px solid #CC6600; border-right: 3px solid #CC6600; background-color: orange;";
			var endClass = "border-left: 3px solid #CC6600; border-right: 3px solid #CC6600; border-bottom: 3px solid #CC6600; height: 15px; background-color: orange;";
			var fadeInClass = "background-image: url('../images/meetingInHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; padding-left: 3px; height: 17px;";
			var fadeOutClass = "background-image: url('../images/meetingOutHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
			var halfHourClass = "border: 3px solid #CC6600; padding-left: 1px; padding-top: 0px; background-color: orange; height: 12px;";
			break;

		case '5': 
			var startClass = "border-left: 3px solid #fbcd13; border-top: 3px solid #fbcd13; border-right: 3px solid #fbcd13; padding-left: 1px; padding-top: 0px; background-color: #FFD700;";
			var midClass = "border-left: 3px solid #fbcd13; border-right: 3px solid #fbcd13; background-color: #FFD700;";
			var endClass = "border-left: 3px solid #fbcd13; border-right: 3px solid #fbcd13; border-bottom: 3px solid #fbcd13; height: 15px; background-color: #FFD700;";
			var fadeInClass = "background-image: url('../images/tempInHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; padding-left: 3px; height: 17px;";
			var fadeOutClass = "background-image: url('../images/tempOutHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
			var halfHourClass = "border: 3px solid #fbcd13; padding-left: 1px; padding-top: 0px; background-color: #FFD700; height: 12px;";
			break;
	}

	if (!document.styleSheets) return;
	var ss = document.styleSheets;
	if(ss[3].rules){
		var i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		
		i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		
		i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		
		i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		
		i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		
		i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		
		ss[3].addRule(start, startClass);
		ss[3].addRule(mid, midClass);
		ss[3].addRule(end, endClass);
		ss[3].addRule(fadeIn, fadeInClass);
		ss[3].addRule(fadeOut, fadeOutClass);
		ss[3].addRule(halfHour, halfHourClass);
	}
	else{
		//alert("ff");
		var i = ss[3].cssRules.length;
		var style = start + "{" + startClass + "}";
		ss[3].insertRule(style, i);
		
		i = ss[3].cssRules.length;
		style = mid + "{" + midClass + "}";
		ss[3].insertRule(style, i);
		
		i = ss[3].cssRules.length;
		style = end + "{" + endClass + "}";
		ss[3].insertRule(style, i);
		
		i = ss[3].cssRules.length;
		style = fadeIn + "{" + fadeInClass + "}";
		ss[3].insertRule(style, i);
		
		i = ss[3].cssRules.length;
		style = fadeOut + "{" + fadeOutClass + "}";
		ss[3].insertRule(style, i);
		
		i = ss[3].cssRules.length;
		style = halfHour + "{" + halfHourClass + "}";
		ss[3].insertRule(style, i);
	}
}
function eventOnClick2(id, type)
{
	var ident = "." + id;
	
	resetStyles2();
	last_event = id;
	
		switch(type)
		{
			case '1': 
				styles[1] = "border: 1px solid #993333; background-color: #efadab; padding-left: 3px; padding-top: 2px;"
				break;
				
			case '2': 
				styles[1] = "border: 1px solid #333366; background-color: #9c9ccd; padding-left: 3px; padding-top: 2px;";
				break;
				
			case '3': 
				styles[1] = "border: 1px solid #006600; background-color: #8aceac; padding-left: 3px; padding-top: 2px;";
				break;
				
			case '4': 
			case '6':
				styles[1] = "border: 1px solid #CC6600; background-color: #f7be76; padding-left: 3px; padding-top: 2px;";
				break;
	
			case '5': 
				styles[1] = "border: 1px solid #fbcd13; background-color: #FFFF99; padding-left: 3px; padding-top: 2px;";
				break;

		}
	
	
	last_type = type;
	
	switch(type)
	{
		case '1': 
			var newClass = "border: 3px solid #993333; background-color: red; padding-left: 1px; padding-top: 0px;";
			break;
			
		case '2': 
			var newClass = "border: 3px solid #333366; background-color: blue; padding-left: 1px; padding-top: 0px;";
			break;
			
		case '3': 
			var newClass = "border: 3px solid #006600; background-color: green; padding-left: 1px; padding-top: 0px;";
			break;
			
		case '4': 
		case '6':
			var newClass = "border: 3px solid #CC6600; background-color: orange; padding-left: 1px; padding-top: 0px;";
			break;

		case '5': 
			var newClass = "border: 3px solid #fbcd13; background-color: #FFD700; padding-left: 1px; padding-top: 0px;";
			break;

	}

	if (!document.styleSheets) return;
	var ss = document.styleSheets;
	if(ss[3].rules){
		var i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		ss[3].addRule(ident, newClass);
	}
	else{
		//alert("ff");
		var i = ss[3].cssRules.length;
		var style = ident + "{" + newClass + "}";
		ss[3].insertRule(style, i);
	}
}
/*
When users mouse over an event, the border of the event becomes thicker
to signify that the event can be clicked.
*/
function eventOnMouseOver(id, type)
{
	var start = "." + id + "Start";
	var mid = "." + id + "Middle";
	var end = "." + id + "End";
	var fadeIn = "." + id + "FadeIn";
	var fadeOut = "." + id + "FadeOut";
	var halfHour = "." + id + "HalfHour";
	
	if(last_event == id) {
		switch(type)
		{
			case '1': 
				var startClass = "border-left: 3px solid #993333; border-top: 3px solid #993333; border-right: 3px solid #993333; padding-left: 1px; padding-top: 0px;";
				var midClass = "border-left: 3px solid #993333; border-right: 3px solid #993333;";
				var endClass = "border-left: 3px solid #993333; border-right: 3px solid #993333; border-bottom: 3px solid #993333; height: 15px;";
				var fadeInClass = "background-image: url('../images/classInHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; padding-left: 3px; height: 17px;";
				var fadeOutClass = "background-image: url('../images/classOutHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
				var halfHourClass = "border: 3px solid #993333; padding-left: 1px; padding-top: 0px; height: 12px;";
				break;
				
			case '2': 
				var startClass = "border-left: 3px solid #333366; border-top: 3px solid #333366; border-right: 3px solid #333366; padding-left: 1px; padding-top: 0px; ";
				var midClass = "border-left: 3px solid #333366; border-right: 3px solid #333366;";
				var endClass = "border-left: 3px solid #333366; border-right: 3px solid #333366; border-bottom: 3px solid #333366; height: 15px;";
				var fadeInClass = "background-image: url('../images/workInHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; padding-left: 3px; height: 17px;";
				var fadeOutClass = "background-image: url('../images/workOutHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
				var halfHourClass = "border: 3px solid #333366; padding-left: 1px; padding-top: 0px; height: 12px;";
				break;
				
			case '3': 
				var startClass = "border-left: 3px solid #006600; border-top: 3px solid #006600; border-right: 3px solid #006600; padding-left: 1px; padding-top: 0px; ";
				var midClass = "border-left: 3px solid #006600; border-right: 3px solid #006600;";
				var endClass = "border-left: 3px solid #006600; border-right: 3px solid #006600; border-bottom: 3px solid #006600; height: 15px;";
				var fadeInClass = "background-image: url('../images/personalInHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; padding-left: 3px; height: 17px;";
				var fadeOutClass = "background-image: url('../images/personalOutHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
				var halfHourClass = "border: 3px solid #006600; padding-left: 1px; padding-top: 0px; height: 12px;";
				break;
				
			case '4': 
				var startClass = "border-left: 3px solid #CC6600; border-top: 3px solid #CC6600; border-right: 3px solid #CC6600; padding-left: 1px; padding-top: 0px; ";
				var midClass = "border-left: 3px solid #CC6600; border-right: 3px solid #CC6600;";
				var endClass = "border-left: 3px solid #CC6600; border-right: 3px solid #CC6600; border-bottom: 3px solid #CC6600; height: 15px;";
				var fadeInClass = "background-image: url('../images/meetingInHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; padding-left: 3px; height: 17px;";
				var fadeOutClass = "background-image: url('../images/meetingOutHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
				var halfHourClass = "border: 3px solid #CC6600; padding-left: 1px; padding-top: 0px; height: 12px;";
				break;
	
			case '5': 
				var startClass = "border-left: 3px solid #fbcd13; border-top: 3px solid #fbcd13; border-right: 3px solid #fbcd13; padding-left: 1px; padding-top: 0px; ";
				var midClass = "border-left: 3px solid #fbcd13; border-right: 3px solid #fbcd13;";
				var endClass = "border-left: 3px solid #fbcd13; border-right: 3px solid #fbcd13; border-bottom: 3px solid #fbcd13; height: 15px;";
				var fadeInClass = "background-image: url('../images/tempInHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; padding-left: 3px; height: 17px;";
				var fadeOutClass = "background-image: url('../images/tempOutHoverClick.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
				var halfHourClass = "border: 3px solid #fbcd13; padding-left: 1px; padding-top: 0px; height: 12px;";
				break;
		}
	}
	else {
		switch(type)
		{
			case '1': 
				var startClass = "border-left: 3px solid #993333; border-top: 3px solid #993333; border-right: 3px solid #993333; padding-left: 1px; padding-top: 0px;";
				var midClass = "border-left: 3px solid #993333; border-right: 3px solid #993333;";
				var endClass = "border-left: 3px solid #993333; border-right: 3px solid #993333; border-bottom: 3px solid #993333; height: 15px;";
				var fadeInClass = "background-image: url('../images/classInHover.gif'); background-color: white; background-repeat: no-repeat; border: none; padding-left: 3px; height: 17px;";
				var fadeOutClass = "background-image: url('../images/classOutHover.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
				var halfHourClass = "border: 3px solid #993333; padding-left: 1px; padding-top: 0px; height: 12px;";
				break;
				
			case '2': 
				var startClass = "border-left: 3px solid #333366; border-top: 3px solid #333366; border-right: 3px solid #333366; padding-left: 1px; padding-top: 0px; ";
				var midClass = "border-left: 3px solid #333366; border-right: 3px solid #333366;";
				var endClass = "border-left: 3px solid #333366; border-right: 3px solid #333366; border-bottom: 3px solid #333366; height: 15px;";
				var fadeInClass = "background-image: url('../images/workInHover.gif'); background-color: white; background-repeat: no-repeat; border: none; padding-left: 3px; height: 17px;";
				var fadeOutClass = "background-image: url('../images/workOutHover.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
				var halfHourClass = "border: 3px solid #333366; padding-left: 1px; padding-top: 0px; height: 12px;";
				break;
				
			case '3': 
				var startClass = "border-left: 3px solid #006600; border-top: 3px solid #006600; border-right: 3px solid #006600; padding-left: 1px; padding-top: 0px; ";
				var midClass = "border-left: 3px solid #006600; border-right: 3px solid #006600;";
				var endClass = "border-left: 3px solid #006600; border-right: 3px solid #006600; border-bottom: 3px solid #006600; height: 15px;";
				var fadeInClass = "background-image: url('../images/personalInHover.gif'); background-color: white; background-repeat: no-repeat; border: none; padding-left: 3px; height: 17px;";
				var fadeOutClass = "background-image: url('../images/personalOutHover.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
				var halfHourClass = "border: 3px solid #006600; padding-left: 1px; padding-top: 0px; height: 12px;";
				break;
				
			case '4': 
				var startClass = "border-left: 3px solid #CC6600; border-top: 3px solid #CC6600; border-right: 3px solid #CC6600; padding-left: 1px; padding-top: 0px; ";
				var midClass = "border-left: 3px solid #CC6600; border-right: 3px solid #CC6600;";
				var endClass = "border-left: 3px solid #CC6600; border-right: 3px solid #CC6600; border-bottom: 3px solid #CC6600; height: 15px;";
				var fadeInClass = "background-image: url('../images/meetingInHover.gif'); background-color: white; background-repeat: no-repeat; border: none; padding-left: 3px; height: 17px;";
				var fadeOutClass = "background-image: url('../images/meetingOutHover.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
				var halfHourClass = "border: 3px solid #CC6600; padding-left: 1px; padding-top: 0px; height: 12px;";
				break;
	
			case '5': 
				var startClass = "border-left: 3px solid #fbcd13; border-top: 3px solid #fbcd13; border-right: 3px solid #fbcd13; padding-left: 1px; padding-top: 0px; ";
				var midClass = "border-left: 3px solid #fbcd13; border-right: 3px solid #fbcd13;";
				var endClass = "border-left: 3px solid #fbcd13; border-right: 3px solid #fbcd13; border-bottom: 3px solid #fbcd13; height: 15px;";
				var fadeInClass = "background-image: url('../images/tempInHover.gif'); background-color: white; background-repeat: no-repeat; border: none; padding-left: 3px; height: 17px;";
				var fadeOutClass = "background-image: url('../images/tempOutHover.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
				var halfHourClass = "border: 3px solid #fbcd13; padding-left: 1px; padding-top: 0px; height: 12px;";
				break;
		}
	}

	if (!document.styleSheets) return;
	var ss = document.styleSheets;
	if(ss[3].rules){
		var i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		
		i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		
		i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		
		i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		
		i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		
		i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		
		ss[3].addRule(start, startClass);
		ss[3].addRule(mid, midClass);
		ss[3].addRule(end, endClass);
		ss[3].addRule(fadeIn, fadeInClass);
		ss[3].addRule(fadeOut, fadeOutClass);
		ss[3].addRule(halfHour, halfHourClass);
	}
	else{
		//alert("ff");
		var i = ss[3].cssRules.length;
		var style = start + "{" + startClass + "}";
		ss[3].insertRule(style, i);
		
		i = ss[3].cssRules.length;
		style = mid + "{" + midClass + "}";
		ss[3].insertRule(style, i);
		
		style = end + "{" + endClass + "}";
		i = ss[3].cssRules.length;
		ss[3].insertRule(style, i);
		
		style = fadeIn + "{" + fadeInClass + "}";
		i = ss[3].cssRules.length;
		ss[3].insertRule(style, i);
		
		style = fadeOut + "{" + fadeOutClass + "}";
		i = ss[3].cssRules.length;
		ss[3].insertRule(style, i);
		
		style = halfHour + "{" + halfHourClass + "}";
		i = ss[3].cssRules.length;
		ss[3].insertRule(style, i);
	}
}

function eventOnMouseOver2(id, type)
{
	var ident = "." + id;
	
	if(last_event == id) {
		switch(type)
		{
			case '1': 
				var newClass = "border: 3px solid #993333; background-color: red; padding-left: 1px; padding-top: 0px;";
				break;
				
			case '2': 
				var newClass = "border: 3px solid #333366; background-color: blue; padding-left: 1px; padding-top: 0px; ";
				break;
				
			case '3': 
				var newClass = "border: 3px solid #006600; background-color: green; padding-left: 1px; padding-top: 0px; ";
				break;
				
			case '4':
			case '6':
				var newClass = "border: 3px solid #CC6600; background-color: orange; padding-left: 1px; padding-top: 0px; ";
				break;
	
			case '5': 
				var startClass = "border: 3px solid #fbcd13; background-color: #FFD700; padding-left: 1px; padding-top: 0px; ";
				break;
		}
	}
	else {
		switch(type)
		{
			case '1': 
				var newClass = "border: 3px solid #993333; background-color: #efadab; padding-left: 1px; padding-top: 0px;"
				break;
				
			case '2': 
				var newClass = "border: 3px solid #333366; background-color: #9c9ccd; padding-left: 1px; padding-top: 0px;";
				break;
				
			case '3': 
				var newClass = "border: 3px solid #006600; background-color: #8aceac; padding-left: 1px; padding-top: 0px;";
				break;
				
			case '4': 
			case '6':
				var newClass = "border: 3px solid #CC6600; background-color: #f7be76; padding-left: 1px; padding-top: 0px;";
				break;
	
			case '5': 
				var newClass = "border: 3px solid #fbcd13; background-color: #FFFF99; padding-left: 1px; padding-top: 0px;";
				break;
		
		}
	}

	if (!document.styleSheets) return;
	var ss = document.styleSheets;
	if(ss[3].rules){
		var i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		ss[3].addRule(ident, newClass);
	}
	else{
		//alert("ff");
		var i = ss[3].cssRules.length;
		var style = ident + "{" + newClass + "}";
		ss[3].insertRule(style, i);
	}
}

/*
When users mouse out, the borders return to normal.
*/
function eventOnMouseOut(id, type)
{

	var start = "." + id + "Start";
	var mid = "." + id + "Middle";
	var end = "." + id + "End";
	var fadeIn = "." + id + "FadeIn";
	var fadeOut = "." + id + "FadeOut";
	var halfHour = "." + id + "HalfHour";
	
	if(last_event == id) {
		switch(type)
		{
			case '1': 
				var startClass = "border-left: 1px solid #993333; border-top: 1px solid #993333; border-right: 1px solid #993333; padding-left: 3px; padding-top: 2px; background-color: red;";
				var midClass = "border-left: 1px solid #993333; border-right: 1px solid #993333; background-color: red;";
				var endClass = "border-left: 1px solid #993333; border-right: 1px solid #993333; border-bottom: 1px solid #993333; height: 17px; background-color: red;";
				var fadeInClass = "background-image: url('../images/classInClick.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 15px; padding-left: 3px;";
				var fadeOutClass = "background-image: url('../images/classOutClick.gif'); background-color: white; background-repeat: no-repeat; border: none;";
				var halfHourClass = "border: 1px solid #993333; padding-left: 3px; padding-top: 2px; background-color: red; height: 14px;";
				break;
				
			case '2': 
				var startClass = "border-left: 1px solid #006600; border-top: 1px solid #006600; border-right: 1px solid #006600; padding-left: 3px; padding-top: 2px; background-color: blue;";
				var midClass = "border-left: 1px solid #006600; border-right: 1px solid #006600; background-color: blue;";
				var endClass = "border-left: 1px solid #006600; border-right: 1px solid #006600; border-bottom: 1px solid #006600; height: 17px; background-color: blue;";
				var fadeInClass = "background-image: url('../images/workInClick.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 15px; padding-left: 3px;";
				var fadeOutClass = "background-image: url('../images/workOutClick.gif'); background-color: white; background-repeat: no-repeat; border: none;";
				var halfHourClass = "border: 1px solid #006600; padding-left: 3px; padding-top: 2px; background-color: blue; height: 14px;";
				break;
				
			case '3': 
				var startClass = "border-left: 1px solid #006600; border-top: 1px solid #006600; border-right: 1px solid #006600; padding-left: 3px; padding-top: 2px; background-color: green;";
				var midClass = "border-left: 1px solid #006600; border-right: 1px solid #006600; background-color: green;";
				var endClass = "border-left: 1px solid #006600; border-right: 1px solid #006600; border-bottom: 1px solid #006600; height: 17px; background-color: green;";
				var fadeInClass = "background-image: url('../images/personalInClick.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 15px; padding-left: 3px;";
				var fadeOutClass = "background-image: url('../images/personalOutClick.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
				var halfHourClass = "border: 1px solid #006600; padding-left: 3px; padding-top: 2px; background-color: green; height: 14px;";
				break;
				
			case '4': 
				var startClass = "border-left: 1px solid #CC6600; border-top: 1px solid #CC6600; border-right: 1px solid #CC6600; padding-left: 3px; padding-top: 2px; background-color: orange;";
				var midClass = "border-left: 1px solid #CC6600; border-right: 1px solid #CC6600; background-color: orange;";
				var endClass = "border-left: 1px solid #CC6600; border-right: 1px solid #CC6600; border-bottom: 1px solid #CC6600; height: 17px; background-color: orange;";
				var fadeInClass = "background-image: url('../images/meetingInClick.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 15px; padding-left: 3px;";
				var fadeOutClass = "background-image: url('../images/meetingOutClick.gif'); background-color: white; background-repeat: no-repeat; border: none;";
				var halfHourClass = "border: 1px solid #CC6600; padding-left: 3px; padding-top: 2px; background-color: orange; height: 14px;";
				break;

			case '5': 
				var startClass = "border-left: 1px solid #fbcd13; border-top: 1px solid #fbcd13; border-right: 1px solid #fbcd13; padding-left: 3px; padding-top: 2px; background-color: #ffd700;";
				var midClass = "border-left: 1px solid #fbcd13; border-right: 1px solid #fbcd13; background-color: #ffd700;";
				var endClass = "border-left: 1px solid #fbcd13; border-right: 1px solid #fbcd13; border-bottom: 1px solid #fbcd13; height: 17px; background-color: #ffd700;";
				var fadeInClass = "background-image: url('../images/tempInClick.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 15px; padding-left: 3px;";
				var fadeOutClass = "background-image: url('../images/tempOutClick.gif'); background-color: white; background-repeat: no-repeat; border: none;";
				var halfHourClass = "border: 1px solid #fbcd13; padding-left: 3px; padding-top: 2px; background-color: #fbcd13; height: 14px;";
				break;
		}
	}
	else {
		switch(type)
		{
			case '1': 
				var startClass = "border-left: 1px solid #993333; border-top: 1px solid #993333; border-right: 1px solid #993333; padding-left: 3px; padding-top: 2px;";
				var midClass = "border-left: 1px solid #993333; border-right: 1px solid #993333;";
				var endClass = "border-left: 1px solid #993333; border-right: 1px solid #993333; border-bottom: 1px solid #993333; height: 17px;";
				var fadeInClass = "background-image: url('../images/classIn.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 15px;";
				var fadeOutClass = "background-image: url('../images/classOut.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
				var halfHourClass = "border: 1px solid #993333; padding-left: 3px; padding-top: 2px; height: 14px;";
				break;
				
			case '2': 
				var startClass = "border-left: 1px solid #333366; border-top: 1px solid #333366; border-right: 1px solid #333366; padding-left: 3px; padding-top: 2px; ";
				var midClass = "border-left: 1px solid #333366; border-right: 1px solid #333366;";
				var endClass = "border-left: 1px solid #333366; border-right: 1px solid #333366; border-bottom: 1px solid #333366; height: 17px;";
				var fadeInClass = "background-image: url('../images/workIn.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 15px;";
				var fadeOutClass = "background-image: url('../images/workOut.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
				var halfHourClass = "border: 1px solid #333366; padding-left: 3px; padding-top: 2px; height: 14px;";
				break;
				
			case '3': 
				var startClass = "border-left: 1px solid #006600; border-top: 1px solid #006600; border-right: 1px solid #006600; padding-left: 3px; padding-top: 2px; ";
				var midClass = "border-left: 1px solid #006600; border-right: 1px solid #006600;";
				var endClass = "border-left: 1px solid #006600; border-right: 1px solid #006600; border-bottom: 1px solid #006600; height: 17px;";
				var fadeInClass = "background-image: url('../images/personalIn.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 15px;";
				var fadeOutClass = "background-image: url('../images/personalOut.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
				var halfHourClass = "border: 1px solid #006600; padding-left: 3px; padding-top: 2px; height: 14px;";
				break;
				
			case '4': 
				var startClass = "border-left: 1px solid #CC6600; border-top: 1px solid #CC6600; border-right: 1px solid #CC6600; padding-left: 3px; padding-top: 2px; ";
				var midClass = "border-left: 1px solid #CC6600; border-right: 1px solid #CC6600;";
				var endClass = "border-left: 1px solid #CC6600; border-right: 1px solid #CC6600; border-bottom: 1px solid #CC6600; height: 17px;";
				var fadeInClass = "background-image: url('../images/meetingIn.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 15px;";
				var fadeOutClass = "background-image: url('../images/meetingOut.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
				var halfHourClass = "border: 1px solid #CC6600; padding-left: 3px; padding-top: 2px; height: 14px;";
				break;
	
			case '5': 
				var startClass = "border-left: 1px solid #fbcd13; border-top: 1px solid #fbcd13; border-right: 1px solid #fbcd13; padding-left: 3px; padding-top: 2px; ";
				var midClass = "border-left: 1px solid #fbcd13; border-right: 1px solid #fbcd13;";
				var endClass = "border-left: 1px solid #fbcd13; border-right: 1px solid #fbcd13; border-bottom: 1px solid #fbcd13; height: 17px;";
				var fadeInClass = "background-image: url('../images/tempIn.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 15px;";
				var fadeOutClass = "background-image: url('../images/tempOut.gif'); background-color: white; background-repeat: no-repeat; border: none; height: 17px;";
				var halfHourClass = "border: 1px solid #fbcd13; padding-left: 3px; padding-top: 2px; height: 14px;";
				break;
		}
	}



	if (!document.styleSheets) return;
	var ss = document.styleSheets;
	//INTERNET EXPLORERERERE.R.
	if(ss[1].rules){
		var i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		
		i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		
		i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		
		i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		
		i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		
		i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		
		ss[3].addRule(start, startClass);
		ss[3].addRule(mid, midClass);
		ss[3].addRule(end, endClass);
		ss[3].addRule(fadeIn, fadeInClass);
		ss[3].addRule(fadeOut, fadeOutClass);
		ss[3].addRule(halfHour, halfHourClass);
		return;
	}
	else{
		var i = ss[3].cssRules.length;
		var style = start + "{"+startClass+"}";
		ss[3].insertRule(style, i);
		
		i = ss[3].cssRules.length;
		style = mid + "{"+midClass+"}";
		ss[3].insertRule(style, i);		
		
		i = ss[3].cssRules.length;
		style = end + "{"+endClass+"}";
		ss[3].insertRule(style, i);
		
		i = ss[3].cssRules.length;
		style = fadeIn + "{"+fadeInClass+"}";
		ss[3].insertRule(style, i);
		
		i = ss[3].cssRules.length;
		style = fadeOut + "{"+fadeOutClass+"}";
		ss[3].insertRule(style, i);
		
		i = ss[3].cssRules.length;
		style = halfHour + "{"+halfHourClass+"}";
		ss[3].insertRule(style, i);
	}
}
function eventOnMouseOut2(id, type)
{

	var ident = "." + id;
	
	if(last_event == id) {
		switch(type)
		{
			case '1': 
				var newClass = "border: 1px solid #993333; padding-left: 3px; padding-top: 2px; background-color: red;";
				break;
				
			case '2': 
				var newClass = "border: 1px solid #006600; padding-left: 3px; padding-top: 2px; background-color: blue;";
				break;
				
			case '3': 
				var newClass = "border: 1px solid #006600; padding-left: 3px; padding-top: 2px; background-color: green;";
				break;
				
			case '4':
			case '6':
				var newClass = "border: 1px solid #CC6600; padding-left: 3px; padding-top: 2px; background-color: orange;";
				break;

			case '5': 
				var newClass = "border: 1px solid #fbcd13; padding-left: 3px; padding-top: 2px; background-color: #ffd700;";
				break;
		}
	}
	else {
		switch(type)
		{
			case '1': 
				var newClass = "border: 1px solid #993333; background-color: #efadab; padding-left: 3px; padding-top: 2px;"
				break;
				
			case '2': 
				var newClass = "border: 1px solid #333366; background-color: #9c9ccd; padding-left: 3px; padding-top: 2px;";
				break;
				
			case '3': 
				var newClass = "border: 1px solid #006600; background-color: #8aceac; padding-left: 3px; padding-top: 2px;";
				break;
				
			case '4': 
			case '6':
				var newClass = "border: 1px solid #CC6600; background-color: #f7be76; padding-left: 3px; padding-top: 2px;";
				break;
	
			case '5': 
				var newClass = "border: 1px solid #fbcd13; background-color: #FFFF99; padding-left: 3px; padding-top: 2px;";
				break;
		
		}
	
	}



	if (!document.styleSheets) return;
	var ss = document.styleSheets;
	//INTERNET EXPLORERERERE.R.
	if(ss[1].rules){
		var i = ss[3].rules.length;
		ss[3].removeRule(i-1);
		ss[3].addRule(ident, newClass);
		return;
	}
	else{
		var i = ss[3].cssRules.length;
		var style = ident + "{"+newClass+"}";
		ss[3].insertRule(style, i);
	}
}
