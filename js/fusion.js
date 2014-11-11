// Encoding RegularExpressions in JS
function encodeRE(s) { 
	return s.replace(/[.*+#&?^${}()|[\]\/\\]/g, '\\$0'); 
}

/*
String.prototype.trim = function() {
	a = this.replace(/^\s+/g, '');
	return a.replace(/\s+$/g, '');
};*/

String.prototype.trim = function() {
// Strip leading and trailing white-space + to *
//return this.replace(/^\s+|\s+$/g, "");
return this.replace(/^\s+/,'').replace(/\s+$/,'');
}
 
// Removes leading whitespaces
function LTrim( value ) {
	
	var re = /\s*((\S+\s*)*)/;
	return value.replace(re, "$1");
	
}

//sets focus on login page
function setFocus(){
	document.loginForm.loginEmail.focus();
}
// Removes ending whitespaces
function RTrim( value ) {
	
	var re = /((\s*\S+)*)\s*/;
	return value.replace(re, "$1");
	
}

// Removes leading and ending whitespaces
function trim( value ) {
	
	return LTrim(RTrim(value));
	
}

function replaceSpecial(s) {
	// do we include "&" ?
	return s.replace(/[#\\]/g,"");
}

function validate(object, valType, errorType)
{   	
	var value;
		
	value = replaceSpecial(object.value);

	var name = object.name;
	name = name.replace(/\[\]/,"");	
	
	var type = object.type;
	if (type == "text") {
		name = object.id;
	}
	
	dest = name+"Error";
   	action = "replace";
	html = true;
	
	if (errorType == 1) {
		redirect = "showError('"+name+"')";
	}
	if (errorType == 2) {
		redirect = "showErrorSummary('"+name+"')";
	}
	
	if (name == "password2") {
		var password1 = document.getElementById("password1").value;
		value = value+"|"+password1;
	}
	
   	http = getHTTPObject();
   	http.open("GET", "includes/lib/CommonValidate.php?value="+value+"&valType="+valType+"&name="+name, true);
   	http.onreadystatechange = handleHttpResponse;
   	http.send(null);
   		
}

function showError(name) {

	var error;
	var errorID;
	
	errorID = name+"Error";
	error = document.getElementById(errorID).innerHTML.trim();
	
	if (error != "") {
		document.getElementById(errorID).className = "visible";	
	}
	else {
		document.getElementById(errorID).className = "invisible";
	}
	
}

function showErrorSummary(name) {

	var show = false;
	var children = document.getElementById("errorSummary").childNodes;
	var guy;

	for (i=0;i<children.length;i++) {
		
		guy = children[i].innerHTML.trim();

		if (guy != "" && guy != "Error Summary" && guy != "---------------" && guy != "              "
			&& guy != null) {		
			show = true;
		}
	}
	
	if (show) {
		document.getElementById("errorSummary").className = "errorSummaryBox";
		document.getElementById("extraSpace").className = "visible";
	}
	else {
		document.getElementById("errorSummary").className = "errorSummaryBox invisible";
		document.getElementById("extraSpace").className = "invisible";
	}

	if (name == "exceptionDate") {
		checkValidation(name);
	}   	
	
	if (name == "userDate") {
		selectedDay();
	}
}

function checkValidation(name) {

	var errorName = name+"Error";
	var error = document.getElementById(errorName).innerHTML;	

	if (error.trim() == "") {	
		if (name == "exceptionDate") {
			addExceptionPlease();
		}
	}
}

/* Inserts the date widget that is
 * used in several areas throughout
 * the site
 */
function insertDateWidget(name, summary)
{
	dest = "untilDateDiv";
	action = "replace";
	
	if (summary) 
		redirect = "addErrorToSummary('"+name+"')";
	
	html = true;
	http = getHTTPObject();
	http.open("GET", "includes/lib/EventWrapper.php?name="+name, true);
	http.onreadystatechange = handleHttpResponse;
   	http.send(null);
	
}

function addErrorToSummary(name) {

	dest = "errorSummary";
	action = "append";
	
	html = true;
	http = getHTTPObject();
	http.open("GET", "includes/lib/CommonValidate.php?addError="+name, true);
	http.onreadystatechange = handleHttpResponse;
   	http.send(null);

}

function registerError(msg, id)
{
	document.getElementById(id).innerHTML = msg;
}

//function called on click to move the main calendar to the next week period
//increments the end of the week timestamp (1 second before midnight)
//in order to switch the day over to the following Sunday
function forwardWeek(end_time)
{
	var new_start = end_time + 1;
	setVisibleWeek(new_start, false);
}

//function called on click to move the main calendar to the previous week period
//increments the start of the week timestamp (midnight, sunday morning (12:00 AM))
//in order to switch the day over to the preceeding Saturday
function backWeek(start_time)
{
	var new_start = start_time - 1;
	setVisibleWeek(new_start, false);
}


function changeGroup(pid)
{
	var time = document.getElementById("start_time").value;
	var id = (document.getElementById("groups")[document.getElementById("groups").selectedIndex]).value;
	document.getElementById("person_ids").value = "";
	action = "replace";
	html = false;
	dest = "person_ids";
	
	redirect = "setVisibleWeek("+time+", true)";
	
	http = getHTTPObject();
	http.open("GET", "includes/lib/ScheduleWrapper.php?getMembers="+id+"&pid="+pid, true);
   	http.onreadystatechange = handleHttpResponse;
	http.send(null);

}

function changeUsers(obj)
{
	var value = document.getElementById("person_ids").value;
	var time = document.getElementById("start_time").value;
	var selectAll = document.getElementById("selectAllCheckbox");
	//alert(value);
	//alert(time);
	//alert(obj.innerHTML);
	var old = value;
	
	var stuff = obj.innerHTML.split("/");
	var img = stuff[stuff.length-1];
	img = img.substring(0,img.length-2);
	//alert(img);
	
	if(img == "check.gif")
	{
		//alert("was checked");
		obj.innerHTML = "&nbsp;";
		selectAll.innerHTML = "&nbsp;";
		value = value.replace("|"+obj.id+"|","|");
	//	alert("removing. "+value);
		if (value == old)
		{
			//we're trying to get the first item, which doesn't
			//have a preceeding pipe
			value = value.replace(obj.id+"|","");
			//alert("removing. "+value);				
		}
	}
	else
	{
		obj.innerHTML = "<img src=\"images/check.gif\">";		
		value += obj.id + "|";
		//alert("adding "+value);
	}
	
	time = eval(Number(time)+1000);
	
	document.getElementById("person_ids").value = value;
	setVisibleWeek(time, false);
}

function selectAllMembers(obj)
{	
	var personIDs = document.getElementById("person_ids");
	var time = document.getElementById("start_time").value;
	var members = document.getElementById("memberList").childNodes;
	var guy;
	var value = "";
	
	var stuff = obj.innerHTML.split("/");
	var img = stuff[stuff.length-1];
	img = img.substring(0,img.length-2);

	// uncheck all
	if(img == "check.gif")
	{
		obj.innerHTML = "&nbsp;";
		
		for (i=0;i<members.length;i++) {
			guy = members[i].firstChild;	
			guy.innerHTML = "&nbsp;";
		}
	}
	else
	{
		obj.innerHTML = "<img src=\"images/check.gif\">";
		for (i=0;i<members.length;i++) {
			guy = members[i].firstChild;
			guy.innerHTML = "<img src=\"images/check.gif\">";
			if (guy.id != "selectAllCheckbox")
			     value += guy.id + "|";
		}
	}
		
	time = eval(Number(time)+1000);
	
	personIDs.value = value;
	setVisibleWeek(time, false);	
}

//AJAX function call to actually get the new calendaring information
//determines the group or person variables present in the current calendar object
//and preserves them in the new instance that is created and referenced.
function setVisibleWeek(date, refresh_members)
{

	if(date.toString().indexOf(",") != -1)
	{
		var info = date.split(",");
		
//		alert(info[1] + " " + info[2] + " " + info[0]);
		
		var ut = datetounixtime(info[1], info[2], info[0]);

	//	alert(ut);

		var start = document.getElementById("start_time").value;
		
		//alert("start was "+start+" ut clicked was "+ut + " end of week is "+ Number(Number(start)+604799));
		
		if(Number(ut) >= Number(start) && Number(ut) < Number(Number(start) + 604800))
		{
			//alert(date);
			//alert("clicked in this week");
			return false;
		}
		
	
	}

	var gid = "";

	if(document.getElementById("group_id"))
	{
		gid = document.getElementById("group_id").value;
	}
	
	var pid = document.getElementById("person_ids").value;

	dest = "panel";
	action = "replace";
	html = true;
	
	if(refresh_members == true)
		redirect = "refreshMembers("+gid+")";
	else if(refresh_members == "refresh")
		redirect = "showEventForm()";

	addLoadingImage();
	http = getHTTPObject();

	//alert(date);
	var str = "includes/lib/ScheduleWrapper.php?start="+date+"&ids="+pid;
	
	if (gid != "")
		str += "&gid="+gid;
	
	http.open("GET", str, true);
   	http.onreadystatechange = handleHttpResponse;
   	http.send(null);

}


function refreshMembers(group_id)
{
	dest = "member_selector";
	action = "replace";
	html = true;
	http = getHTTPObject();
	http.open("GET", "includes/lib/ScheduleWrapper.php?ListMembers="+group_id, true);
   	http.onreadystatechange = handleHttpResponse;
   	http.send(null);
	
}

//this function will eventually help users to delete the event that they have clicked on.
//it will need to confirm if users want to delete just this instance of an event, 
//or all instances of the event
//maybe we should also inform them that if they want all $day_click_on (Wednesdays) to be
//exceptions they can do it through the event form?
function delPopup(id, target)
{

	 var info = id.split("_");
	 
	 var event_id = info[1];
	 var display_start = info[2];
	 var event_day = info[3];
	 
	//alert(event_id + " " + display_start + " " + event_day + " " + target);

	window.currentlyVisiblePopup = target;
	//alert(window.currentlyVisiblePopup);
	
	dest = target;
	action = "replace";
	html = true;
	http = getHTTPObject();
	//alert("includes/lib/EventWrapper.php?del_id="+event_id+"&date="+display_start+"&day="+event_day);
	http.open("GET", "includes/lib/EventWrapper.php?del_id="+event_id+"&date="+display_start+"&day="+event_day, true);
   	http.onreadystatechange = handleHttpResponse;
   	http.send(null);
   	
   	
}				
					 
/* function that takes an object ID as a parameter
 * hides or shows that object based on its current status
 * if the object display is set to visible it will become hidden
 * and vice versa
 */
function hideShow(id)
{
    var el = document.getElementById(id);
    if (el.style.display != 'none' ) {
        el.style.display = 'none';
    }
    else {
        el.style.display = '';
    }	
}

/* function similiar to hideShow
 * but only with the hide functionality
 */
function hide(id)
{
	var el = document.getElementById(id);
	el.style.display = 'none';

}
/* function similiar to hideShow
 * but only with the show functionality
 */
function show(id)
{
	var el = document.getElementById(id);
	el.style.display = '';

}

/* enables and disables an element
 * on the form
 */
function enableDisable(id) {
	var el = document.getElementById(id);
	if(document.getElementById('count'))
		var count = document.getElementById('count').value;
	
	if(count){	
		if(count!=0){
		
		    if(el.disabled == false) {
		    	el.disabled = true;
		    	el.value = "false";
		    	el.checked = false;
		    }
		    else {
		    	if(el.disabled==true &&el.checked == true){
			    	el.disabled = false;
			    	el.value = "true";
			    	el.checked = true;
			    }
		    }
		}
	}
	if(!count){
		if(el.disabled == false) {
		    	el.disabled = "disabled";
		    }
		    else {
		    	el.disabled = false;
		    }
	}
    //alert(id + " " + document.getElementById(id).disabled+" "+count);
}

/* enables and disables an element
 * on the form
 */
function enableDisabled(name) {
	
	for(num=1;num<8;num++){
		if(document.getElementById(name+num).checked==true && document.getElementById(name+num).disabled == true){
			document.getElementById(name+num).disabled = false;
		}
	}
}
/* Displays the secret question form
 * on the login page
 */
function secretQuestion()
{
	if(document.getElementById("loginEmail").value != "")
	{
		if(document.getElementById("loginEmailError").innerHTML.trim() == "")
		{
			var id = document.getElementById("loginEmail").value;
			dest = "secret";
			action = "replace";
			html = true;
			http = getHTTPObject();
			http.open("GET", "includes/lib/PersonWrapper.php?getSecret="+id, true);

   			http.onreadystatechange = handleHttpResponse;
   			http.send(null);
		}
	
	}
	else
	{
		document.getElementById("loginEmailError").innerHTML = "Please enter your email";
		showErrorSummary('');
	}

}


function editGroup(groupID)
{
	
	var id = groupID;
	
	document.getElementById("editMSG").innerHTML = "";
	document.getElementById("createGroupButton").innerHTML="<a href = 'http://rook.hss.cmu.edu/~team04s06/groups.php'><div id=\"back\" class=\"group\" onmouseover=\"document.getElementById('back').className='groupOn';\" onmouseout=\"document.getElementById('back').className='groupOff'\";>:: Create Group form</div></a>";
	
	dest = "data";
	action = "replace";
	html = true;
	http = getHTTPObject();
	http.open("GET", "includes/lib/GroupWrapper.php?editGroupID="+id, true);
   	http.onreadystatechange = handleHttpResponse;
   	http.send(null);
}

function edit(groupID)
{

	var id = groupID;
	var groupName;
	var endDate;
	var count;
	var str = "";
	
	groupName = document.getElementById("groupName").value;
	endDate = document.getElementById("until").value;

	if (endDate == "DATE") {
		endDate = document.getElementById("untilDate").value;
	}
	
	count = document.getElementById('newMemberCount').value;
	
	for (i=1; i<=count; i++) { 
		str += "&newMember"+i+"=";
		str += document.getElementById("newMember"+i).value;
		
		if(document.getElementById("newMember"+i+"Error").innerHTML.trim() != "")
		{
			return;
		}
	}

	
   	redirect = "displayTeamList(true)";	

	dest = "data";
	action = "replace";
	
	
	html = true;
	http = getHTTPObject();
	http.open("GET", "includes/lib/GroupWrapper.php?editID="+id+"&groupName="+groupName+"&endDate="+endDate+str, true);
   	http.onreadystatechange = handleHttpResponse;
   	http.send(null);

}

function createGroup(groupID)
{
	
	var id = groupID;
	
	document.getElementById("createGroupButton").innerHTML="";
	
	redirect = "displayTeamList(false)";
	
	dest = "data";
	action = "replace";
	html = true;
	http = getHTTPObject();
	http.open("GET", "includes/lib/GroupWrapper.php?createGroup", true);
   	http.onreadystatechange = handleHttpResponse;
   	http.send(null);
}

function displayTeamList(banner)
{

	//alert("inside list with " + banner);

	if(document.getElementById("group_errors") && document.getElementById("group_errors").innerHTML != "")
		return;

	if(banner == true)
		document.getElementById("editMSG").innerHTML = "<div class=\"roundcont\"><div class='roundtop'><img src='images/tl.gif' width='15' height='15' class='corner' style='display: none' /></div> <span class='bigTitle'>Your changes have been saved!</span><div class='roundbottom'><img src='images/bl.gif' width='15' height='15' class='corner' style='display: none' /></div></div>";
	
	dest = "teamList";
	action = "replace";
	html = true;
	http = getHTTPObject();
	http.open("GET", "includes/lib/GroupWrapper.php?relistTeams", true);
   	http.onreadystatechange = handleHttpResponse;
   	http.send(null);
   	
   	
}

function joinGroup(groupID, personID)
{
	
	var id = groupID;
	var pid = personID;
	redirect = "JSredirect('groups.php')";
	//alert("wtf? " + id +" "+pid);
	document.getElementById("div_name_based_on_"+id).innerHTML = "";
	
	dest = "teamList";
	action = "replace";
	html = true;
	http = getHTTPObject();
	http.open("GET", "includes/lib/GroupWrapper.php?joinGroupID="+id+"&personID="+pid, true);
   	http.onreadystatechange = handleHttpResponse;
   	http.send(null);
}

function rejectGroup(groupID, personID)
{
	
	var id = groupID;
	var pid = personID;
	
	//alert("wtf? " + id +" "+pid);
	document.getElementById("div_name_based_on_"+id).innerHTML = "";
	
	dest = "editMSG";
	action = "replace";
	html = true;
	http = getHTTPObject();
	http.open("GET", "includes/lib/GroupWrapper.php?rejectGroupID="+id+"&personID="+pid, true);
   	http.onreadystatechange = handleHttpResponse;
   	http.send(null);
}

function leaveGroup(groupID, personID)
{
	
	var id = groupID;
	var pid = personID;
	
	
	//document.getElementById("data").innerHTML = "You have left this group" + id;
	redirect = "JSredirect('groups.php')";
	
	addLoadingImage();
	dest = "";
	//action = "replace";
	//html = true;
	http = getHTTPObject();
	//alert("wtf? " + id +" "+pid);
	http.open("GET", "includes/lib/GroupWrapper.php?leaveGroupID="+id+"&personID="+pid, true);
   	http.onreadystatechange = handleHttpResponse;
   	http.send(null);
   	
   	
}

function JSredirect(page)
{
	var dest = "http://rook.hss.cmu.edu/~team04s06/"+page;
	window.location = dest;
}

function createExceptionBox() 
{
	document.getElementById("exceptionsBox").innerHTML += "<input type='textbox'>";
}

function validateCourse(obj)
{

	var course_id = obj.value;
	var drop = obj.id;
	var sem = document.getElementById("Semester").value
	
	
	if(course_id != "" && course_id.length > 1)
	{
		dest = drop+"Results";
		
		window.currentlyVisiblePopup = dest;
		action = "replace";
		
		html = true;
		http = getHTTPObject();
		http.open("GET", "includes/lib/CourseFunctions.php?course_id="+course_id+"&semester="+sem+"&source="+drop, true);
	   	http.onreadystatechange = handleHttpResponse;
	   	http.send(null);
	
	}
}

/* function used in request meeting.
 * Displays each alternative time and
 * stores the information to be accessed
 * upon submit
 */
function listMeeting()
{

	var title;
	var desc;
	var responseHH;
	var date;
	var fromHH;
	var fromMM;
	var fromAMPM;
	var durationHH;
	var durationMM;
	
	var recurrence;
	var frequency;
	var weeklyRecurrence;
	var weekly;
	var weekdays="";
	var until;
	var exceptionDate;
	
	var count;
	var checkValue;

	document.getElementById("count").value = eval(Number(document.getElementById("count").value) + 1);
	count = document.getElementById("count").value;
	//alert (count);
	if (count == 1 ){
		//saving information that is only inputted once
		
		if(document.getElementById("veil"))
		{
		
			document.getElementById("veil").className = "opaque";
			
			if (inAgent("MSIE")) {
				veilWidth = getElementWidth("member_selector") - 5;	
			}
			else {
				veilWidth = getElementWidth("member_selector") + 10;
			}
			document.getElementById("veil").style.width = veilWidth+"px";
			veilHeight = getElementHeight("member_selector") + 20;
			document.getElementById("veil").style.height = veilHeight+"px";
			
		}

		title = document.getElementById("title").value;
		desc = document.getElementById("desc").value;

		responseHH = document.getElementById("responseHH").value;
		
		document.getElementById("recurrence").disabled = true;
		

		
		if(desc != "") {
			document.getElementById("confirmed_info").innerHTML = "<div class=\"chunk\"><div class=\"requestLabel\">Meeting Details</div><span class=\"requested\">Title:</span> " + title + "<br/><span class=\"requested\">Description:</span><div class=\"indent\">" + desc + "</div><span class=\"requested\">Response In:</span> " + responseHH + " hours</div>";
		}
		else {
			document.getElementById("confirmed_info").innerHTML = "<div class=\"chunk\"><div class=\"requestLabel\">Meeting Details</div><span class=\"requested\">Title:</span> " + title + "<br/><span class=\"requested\">Response In:</span> " + responseHH + " hours</div>";
		}
		
		document.getElementById("confirmed_info").innerHTML+="<input type='hidden' name = 'title' id = 'titleVal' value = ''>";
		document.getElementById("titleVal").value = title;
		document.getElementById("confirmed_info").innerHTML+="<input type='hidden' name = 'desc' id = 'descVal' value = ' '>";
		document.getElementById("descVal").value = desc;
		document.getElementById("confirmed_info").innerHTML+="<input type='hidden' name = 'responseHH' value = " + responseHH + ">";
		} //end if count == 1
		
		recurrence = document.getElementById("recurrence").checked;
		date = document.getElementById("userDate").value;
		fromHH = document.getElementById("fromHH").value;
		fromMM = document.getElementById("fromMM").value;
		fromAMPM = document.getElementById("fromAMPM").value;
		durationHH = document.getElementById("durationHH").value;
		durationMM = document.getElementById("durationMM").value;
		exception = document.getElementById("exceptions").checked;
		
		/***************
		TREY
		I moved the creation of str up to here because it was only being build if recurrence happened
		this way it actually exists, and we stop getting undefined values.
		****************/
		var str = date+"%"+fromHH+"%"+fromMM+"%"+fromAMPM+"%"+durationHH+"%"+durationMM+"%"+recurrence;

    
	fromMMtemp = fromMM;
	if(fromMM == 0){
		fromMMtemp = "00";
	}
	if(fromMM == 5){
		fromMMtemp = "05";
	}
	
	if(count == 1) {
		var times = "<div class=\"requestLabel\">Time "+count+"</div><span class=\"requested\">Date:</span> "+date+"<br/><span class=\"requested\">Time:</span> "+fromHH+":"+fromMMtemp+" "+fromAMPM+"<br/><span class=\"requested\">Duration:</span> "+durationHH+"hr "+durationMM+"mins";
	}
	else {
		var times = "<span class=\"requested\">Date:</span> "+date+"<br/><span class=\"requested\">Time:</span> "+fromHH+":"+fromMMtemp+" "+fromAMPM+"<br/><span class=\"requested\">Duration:</span> "+durationHH+"hr "+durationMM+"mins";
	}
	
	if(recurrence == true) {
		//alert("inside recurrence");
		//handling reccurence
	    	for(j=1;j<3;j++){
	    		if(document.getElementById("frequency"+j).checked==true){
	    			frequency = document.getElementById("frequency"+j).value;
	    			}
	    	}
	    	//alert("build the recurrence days");
			weekly = document.getElementById("weekly").value;
			var weekdaycounter = 0;
			for(i=1;i<=7;i++){
				
				if(document.getElementById("weekdays"+i).checked==true){
					checkValue = document.getElementById("weekdays"+i).value;
						//alert(weekdaycounter);
						if(weekdaycounter==0)
							weekdays=checkValue;
						else
							weekdays+="|"+checkValue;
						weekdaycounter++;	
					}
				
			}	
			
			//alert("and up here?");
			
			until = document.getElementById("until").value;
			if(until == "DATE") {
				untilDate = document.getElementById("untilDate").value;
			}
			exceptionDate = document.getElementById("exceptionDate").value;
			
			//alert("before the swarm");
			//document.getElementById("setTimes").innerHTML += "This reccurs";
			document.getElementById("confirmed_info").innerHTML+="<input type='hidden' name = 'frequency' value = " + frequency+ ">";
			document.getElementById("confirmed_info").innerHTML+="<input type='hidden' name = 'weekly' value = " + weekly+ ">";
			document.getElementById("confirmed_info").innerHTML+="<input type='hidden' name = 'weekdays' value = " + weekdays+ ">";
			document.getElementById("confirmed_info").innerHTML+="<input type='hidden' name = 'until' value = " + until+ ">";
			document.getElementById("confirmed_info").innerHTML+="<input type='hidden' name = 'exceptionDate' value = " + exceptionDate+ ">";
					
		/***************
		TREY
		This is where the rest of the hidden field is built for dealing with recurrence and exceptions and whatnot
		****************/
			str += "%"+frequency+"%"+weeklyRecurrence+"%"+weekly+"%"+weekdays+"%"+until+"%"+exceptionDate;
			//alert(str);
			//alert('recurrence ' + recurrence);
			
			times += "<div class=\"recurrenceLabel\">Recurrence:</div>";
			times += "<div class=\"indent\">";
			
			if(frequency == "ed"){
				weekdays = "U|M|T|W|R|F|S";
				times += "-Every day";
			}
			else {
				times += "-Every ";
				if(weekdays.match("U")) {
					times += " Sun";
				}
				if(weekdays.match("M")) {
					times += " Mon";
				}
				if(weekdays.match("T")) {
					times += " Tue";
				}
				if(weekdays.match("W")) {
					times += " Wed";
				}
				if(weekdays.match("R")) {
					times += " Thu";
				}
				if(weekdays.match("F")) {
					times += " Fri";
				}
				if(weekdays.match("S")) {
					times += " Sat";
				}
			}	
			
			if(weekly == 02) {
				times += "<br/>-Every other week";
			}
			else if (weekly == 03) {
				times += "<br/>-Every 3 weeks";
			}
			else if (weekly == 04) {
				times += "<br/>-Every 4 weeks";
			}
			
			if(until == "SEMESTER") {
				times += "<br/>-Until end of semester";
			}
			else if(until == "MINI") {
				times += "<br/>-Until end of mini session";
			}
			else if(until == "DATE") {
				times += "<br/>-Until " + untilDate;
			}
			
			times += "</div>";
			
			document.getElementById("recurrence").checked = true;
			
			
			if(exception == true) {
				times += "<div class=\"recurrenceLabel\">Exceptions:</div>";
				times += "<div class=\"indent\">";
				numExceptions = document.getElementById("exceptionsListBox").length;
				for(var e = 0; e < numExceptions; e++) {
					times += "-" + document.getElementById("exceptionsListBox").options[e].value + "<br/>";
				}
				times += "</div>";
			}
		}//if recurrence
		else {
			document.getElementById("recurrence").checked = false;
			document.getElementById("recurrence").disabled = true.
			frequency = "";
			weeklyRecurrence = "";
			weekly = "";
			weedays = "";
			until="";
			exceptionDate = "";
		}
		
		
	var next = eval(count) + 1;
	document.getElementById("setTimes").innerHTML += "<div id = visibleDiv"+count+" class=\"chunk\">"+times+"</div><div class=\"requestLabel\">Time " + next + "</div>";
	
	document.getElementById("hidden_info").innerHTML += "<div id = hiddenDiv"+count+"><input type='hidden' name = hiddenInput"+count+" value =" + str + "><div/>";
	
	if (count == 1 ){
		document.getElementById("setTime").value = "Add Time";
	}
	
	//grey out the recurrence after first request
	
	document.getElementById("recurrence").disabled = true;
	document.getElementById("recurrenceCheckbox").className = "disabled";
	document.getElementById("recurrence").className = "disabled";
	
}


function hoverEffects() {
	//get all elements (text inputs, passwords inputs, textareas)
	var elements = document.getElementsByTagName('input');
	var j = 0;
	for (var i4 = 0; i4 < elements.length; i4++) {
		if((elements[i4].type=='text')||(elements[i4].type=='password')) {
			hovers[j] = elements[i4];
			++j;
		}
	}
	elements = document.getElementsByTagName('textarea');
	for (var i4 = 0; i4 < elements.length; i4++) {
		hovers[j] = elements[i4];
		++j;
	}
	
	//add focus effects
	for (var i4 = 0; i4 < hovers.length; i4++) {
		hovers[i4].onfocus = function() {this.className += "Hovered";}
		hovers[i4].onblur = function() {this.className = this.className.replace(/Hovered/g, "");}
	}
}

function buttonHovers() {
	//get all buttons
	var elements = document.getElementsByTagName('input');
	var j = 0;
	for (var i5 = 0; i5 < elements.length; i5++) {
		if(elements[i5].type=='submit') {
			buttons[j] = elements[i5];
			++j;
		}
	}
	
	//add hover effects
	for (var i5 = 0; i5 < buttons.length; i5++) {
		buttons[i5].onmouseover = function() {this.className += "Hovered";}
		buttons[i5].onmouseout = function() {this.className = this.className.replace(/Hovered/g, "");}
	}
}
/* function that calls the meeting wrapper
 * when a user rejects/accepts meeting times
 */
function rejectMeeting(person_id, event_id, type, priority) {

	
	dest = event_id+"response";
	action = "replace";
	html = true;
	
	var date = Number(document.getElementById("start_time").value);
	
	redirect = "setVisibleWeek("+date+", false)";
	
	http = getHTTPObject();
	http.open("GET", "includes/lib/MeetingWrapper.php?person_id="+person_id+"&event_id="+event_id+"&type="+type+"&priority="+priority, true);
	http.onreadystatechange = handleHttpResponse;
  	http.send(null);
}

/* function that displays course form
 * when user selects 'course' from the
 * category dropdown on the add event
 * page
 */
function showCourseForm()
{

		dest = "eventForm";
		action = "replace";
		html = true;
		http = getHTTPObject();
		http.open("GET", "includes/views/course_form.php", true);
	   	http.onreadystatechange = handleHttpResponse;
	   	http.send(null);

}

/* function that shows event form
 * when user selects 'work' or
 * 'personal' from the course form
 * category dropdown, or when user
 * selects 'add another event' from
 * the edit event form
 */
function showEventForm()
{
		if(document.getElementById('category'))
			var index = document.getElementById('category').selectedIndex;
		else
			var index = 1;

		if(document.getElementById("meetingTop"))
			document.getElementById("meetingTop").id = "addEventTop";
		
		if(document.getElementById("editEventTop"))
			document.getElementById("editEventTop").id = "addEventTop";
		
		
		dest = "eventForm";
		action = "replace";
		html = true;
		http = getHTTPObject();
		http.open("GET", "includes/views/event_form.php?index="+index, true);
	   	http.onreadystatechange = handleHttpResponse;
	   	http.send(null);
		
		
		
}

/* this function will eventually populate
 * the event/meeting form below the calendar
 * with the appropriate information
 */
function populateForm(event_id)
{
	//alert("inside populate form");
		
	dest = "eventDiv";
	action = "replace";
	html = true;
	http = getHTTPObject();
	http.open("GET", "includes/lib/EventWrapper.php?event_id="+event_id, true);
   	http.onreadystatechange = handleHttpResponse;
   	http.send(null);
   	
}   

/* Function that selects and disables
 * a certain day in the weekdays 
 * checkbox group depending on the 
 * date that is entered in the date
 * widget
 */
function selectedDay()
{
	var curDate;
	var year;
	var month;
	var numDay;
	var dayError;
	
	dayError = document.getElementById("userDateError").innerHTML;
	curDate = document.getElementById("userDate").value;

	if (curDate != "" && dayError.trim() == "") {
		fullDateArray = curDate.split("/");
		var month = fullDateArray[0];
		var numDay = fullDateArray[1];
		var year = fullDateArray[2];
		var newYear = year.substring(2,4);
		month = month-1;
		var d = new Date(newYear,month,numDay)
	
		thisDay = d.getDay();
		
		if(thisDay==0)
			thisDay=7;	
	
		for(num=1;num<8;num++){
			if(num==thisDay){
				document.getElementById("weekdays"+num).checked = true;
				document.getElementById("weekdays"+num).disabled = true;				
			}
			else {
					document.getElementById("weekdays"+num).checked = false;
					document.getElementById("weekdays"+num).disabled = false;		
			}//end of else				
		}
	}
	else {
		for(num=1;num<8;num++) {
			if (document.getElementById("weekdays"+num).disabled == true) {
				document.getElementById("weekdays"+num).disabled = false;
				document.getElementById("weekdays"+num).checked = false;
			}
		}
	}
}

/* Function that adds an exception
 * into the exception list box on
 * the add/edit event forms and the
 * request form
 */
function addExceptionPlease()
{

	var exceptionDate;
	var listBox;
	var intCount;
	
	exceptionDate = document.getElementById("exceptionDate").value;
	if(exceptionDate!= ""){	
		listBox = document.getElementById("exceptionsListBox");
		
		intCount = listBox.options.length;
	
		listBox.options[intCount] = new Option(exceptionDate);
		
		document.getElementById("exceptionsList").innerHTML+="<input type = 'hidden' name = 'exception"+intCount+"' id = 'exception"+intCount+"' value = '"+exceptionDate+"'>";
		var count = document.getElementById("exCount").value;
		document.getElementById("exCount").value = eval(count)+1;
		
	}
	
}

/* Function that removes an exception
 * from the exception list box on
 * the add/edit event forms and the
 * request form
 */
function removeExceptionPlease()
{
	var exceptionDate;
	var intCount;
	var listBox;
	
	
	listBox = document.getElementById("exceptionsListBox");
	intCount = listBox.options.length;
	
	for(i=0;i<intCount;i++){
		//alert(i + " " + intCount);
		if(listBox.options[i].selected)
			exceptionDate = listBox.options[i].text;
		if(listBox.options[i].text == exceptionDate){
			listBox.options[i]= null;
			for(j=i;j<intCount;j++){
				if(document.getElementById("exception"+eval(j+1)))
					document.getElementById("exception"+j).value=document.getElementById("exception"+eval(j+1)).value;
			}
			//alert("exception"+eval(intCount-1));
			node = document.getElementById("exception"+eval(intCount-1));
			node.parentNode.removeChild(node);
			var count = document.getElementById("exCount").value;
			document.getElementById("exCount").value = eval(count)-1;
			break;
			}
	}

}

function addGroupMember()
{
		var count = document.getElementById('memberCount').value;
		document.getElementById('memberCount').value = eval(count)+1;
		count = document.getElementById('memberCount').value;
		
		var members;
		members = "member1|member2";
		for(i=3;i<=count;i++){
			members+="|member"+i;
		}
		
		saveValues(members);
		
		redirect = "restoreValues()";
		
		dest = "members";
		action = "append";
		html = true;
		http = getHTTPObject();
		http.open("GET", "includes/lib/GroupWrapper.php?addMember=yes&count="+count, true);
	   	http.onreadystatechange = handleHttpResponse;
	   	http.send(null); 

}

function addNewGroupMember()
{
		var count = document.getElementById('newMemberCount').value;
		document.getElementById('newMemberCount').value = eval(count)+1;
		count = document.getElementById('newMemberCount').value;
		
		var newMembers;
		//newMembers = "newMember1|newMember2";
		
		
			for(i=1;i<=count;i++){
				if(i==1)
					newMembers="newMember"+i;
				else
					newMembers+="|newMember"+i;
			}
	
		
		if(count>1){
			saveValues(newMembers);
			
			redirect = "restoreValues()";
		}
		//alert(count);
		dest = "newMembers";
		action = "append";
		html = true;
		http = getHTTPObject();
		http.open("GET", "includes/lib/GroupWrapper.php?addNewMember=yes&newCount="+count, true);
	   	http.onreadystatechange = handleHttpResponse;
	   	http.send(null); 

}

function hideCurrentPopup()
{


	if(window.currentlyVisiblePopup)
	{
		if(document.getElementById(window.currentlyVisiblePopup))
		{
			document.getElementById(window.currentlyVisiblePopup).innerHTML = "";
		}
		
		window.currentlyVisiblePopup = false;
	}
		


}
/* Function that deletes an event.
 * This is accessed when a user clicks 
 * on the 'X' in the upper right hand
 * corner of an event on the schedule
 */
function delEvent(eid, date)
{
	redirect = "setVisibleWeek("+date+", 'refresh')";
	dest = "";
	
	http = getHTTPObject();
	http.open("GET", "includes/lib/EventWrapper.php?delete="+eid+"&date="+date, true);
	http.onreadystatechange = handleHttpResponse;
   	http.send(null);

}

/* Function that adds an exception
 * for a specified event. This is 
 * accessed when a user chooses to 
 * delete one instance of an event
 */
function addException(eid, date)
{
	redirect = "setVisibleWeek("+date+", 'refresh')";
	
	http = getHTTPObject();
	http.open("GET", "includes/lib/EventWrapper.php?exception="+eid+"&date="+date, true);
	http.onreadystatechange = handleHttpResponse;
   	http.send(null);

}

/* Function that displays the request form.
 * This is used after a meeting has been 
 * requested and the user wants to select
 * another meeting
 */
function showRequest()
{
	dest = "requestDiv";
	action = "replace";
	html = true;
	http = getHTTPObject();
	http.open("GET", "includes/lib/MeetingWrapper.php?showRequest=true", true);
   	http.onreadystatechange = handleHttpResponse;
   	http.send(null);

}

function nozeros(input) {
	return input;
	if((input.length > 1) && (input.substr(0,1) == "0")) {
		return input.substr(1);
	} else {
		return input;
	}
}

function datetounixtime(mm, dd, yy) {
	var humDate = new Date(nozeros(yy), (eval(mm-1)), dd, 0, 0, 0);
	return (humDate.getTime()/1000.0);
}

function activatePerson(id){



    dest = ""
    action = "";
	html = true;
	http = getHTTPObject();
	http.open("GET", "includes/lib/PersonWrapper.php?person_id="+id, true);
   	http.onreadystatechange = handleHttpResponse;
   	http.send(null);

}

function getElementHeight(Elem) {
	if(document.getElementById) {
		var elem = document.getElementById(Elem);
	}
	else
		return -1;
	
	xPos = elem.offsetHeight;
	
	return xPos;
}

function getElementWidth(Elem) {
	if(document.getElementById) {
		var elem = document.getElementById(Elem);
	}
	else
		return -1;
	
	xPos = elem.offsetWidth;
	
	return xPos;
}

function getLeft(ll) {
	if (ll.offsetParent)
		return (ll.offsetLeft + getLeft(ll.offsetParent));
	else
		return (ll.offsetLeft);
}
function getTop(obj) {
	/*if (ll.offsetParent)
		return (ll.offsetTop + getTop(ll.offsetParent));
	else
		return (ll.offsetTop);*/
	xTop = obj.offsetTop;	
	return xTop;
}

function addLoadingImage()
{

	loadingWidth = getElementWidth("panel") - 48;
	loadingHeight = getElementHeight("panel") + 13;
	bufferHeight = getElementHeight("panel") / 2;
	document.getElementById("panel").innerHTML = "<div id='loading'></div><div id='buffer'><div id='loading_image'><img src=\"images/loading.gif\"></div></div>"+document.getElementById("panel").innerHTML;
	document.getElementById("loading").style.width = loadingWidth+"px";
	document.getElementById("loading").style.height = loadingHeight+"px";
	if(getElementHeight("panel") > 300)
		bufferHeight = getElementHeight("panel") / 3;
	else
		bufferHeight = getElementHeight("panel") / 2;
	document.getElementById("buffer").style.height = bufferHeight+"px";
	
}

function inAgent(agent)
{
	var detect = navigator.userAgent.toLowerCase();
	var place = detect.indexOf(agent.toLowerCase()) ;
	if(place != -1)
		return true;
	else
		return false;
}


function checkDuration()
{
	if(document.getElementById("durationHH").value == 0 && document.getElementById("durationMM").value == 0)
	{
		if(!document.getElementById("duration_error"))
		{
			document.getElementById("errorSummary").innerHTML = document.getElementById("errorSummary").innerHTML + "<div class=\"error\" id=\"duration_error\"> The duration of a meeting cannot be zero </div>";
		}

		return false;
	}

	if(document.getElementById("duration_error"))
	{
		var node = document.getElementById("duration_error");
		node.parentNode.removeChild(node);
	}
	return true;
}