function getHTTPObject()
{
	var requester;
	try
	{
		 requester = new XMLHttpRequest();
	}
	catch (error)
	{
		try
		{
			requester = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (error)
		{

			try
			{
				requester = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (error)
			{
				
			   requester = false;
			}

		}
		

	}
	return requester;
}

var http; //variable to hold the http connection object
var action = ""; //options are: replace, append, prepend.  
				//Describes what happens to data returned from server call
				
var redirect = ""; //javascript functions saved as strings to call after the
					// server response to the current request
								
var dest = ""; // id of the destination element into which the returned content will
				// be delivered
var html = true;

function handleHttpResponse() {

	if (http.readyState == "4") {
		if(http.responseText != "")
		{
			var response = http.responseText;
		}
		else
		{
			var response = "";
		}
		/*
		if(cancelCode != "")
		{
			alert(cancelCode);
		}
		*/		
		//var canceled = false;

		//	alert(response);
		//alert(response);
		
		/*
		if(response.indexOf("|") != -1)
		{
			//error message of some sort, no action taken
			//so we don't need to refresh the page in order to show the results
			//because nothing happened.
		 	redirect = "";
		 	//before the pipe should be the error message,
		 	//after the pipe should be the ID of where the message should go
		 	//error_field by default
			var temp = response.split("|");
			var msg = temp[0];
			displayResults(msg);
			//registerError(msg, temp[1]);
		}
		*/
		
		if (redirect != "")
		{
			displayResults(response);
			if(redirect.indexOf(";") != -1)
			{
				
				var temp = redirect.split(";");
				redirect = "";			
				
				for(x = 0; x < temp.length; x++)
				{
					setTimeout(eval(temp[x]), 2000);
				}
			}
			else
			{
				//alert(redirect);
				var temp = redirect;
				redirect = "";
				eval(temp);				
			}
		}
		else
		{
			displayResults(response);
		}
  	}
}


function displayResults(response)
{

	//alert("inside display");
	if(dest != "" && html == true)
	{
		
		if(document.getElementById(dest))
		{
			var oldHTML = document.getElementById(dest).innerHTML;
			var newHTML = "";
			
			if(action == "append")
				newHTML = oldHTML + response;
			else if(action == "prepend")
				newHTML = response + oldHTML;
			else if(action == "replace")
				newHTML = response;
			
			document.getElementById(dest).innerHTML = newHTML;
		}
	}
	else
	{
		//alert("response: "+response);
		if(response == "")
			response = "false";
	
		if(document.getElementById(dest))
			document.getElementById(dest).value = response;
	}
	
	var the_match = new RegExp('(?:<script.*?>)((\n|.)*?)(?:<\/script>)', 'img');
	var scripts = response.match(the_match);
	if(scripts != null)
	{
		var new_match = new RegExp('(?:<script.*?>)((\n|.)*?)(?:<\/script>)', 'im');
		
		for (var i = 0; i < scripts.length; i++)
		{
			var tmp = scripts[i].match(new_match)[1];
			eval(tmp);
		}
	}
}
/*
	//example POST AJAX call
	//the variables are passed via a string which is sent to http.send() below
	//these are set up in a key = value format
	var str = "action=contents&id="+id;
	http.open("POST", "library.php", true);
    
	//attach the function which will watch the http object
	//and take action when the server sends back its response
	http.onreadystatechange = handleHttpResponse;	
	
	http.send(str);	
*/



var ids; //array of form txt field ids to save
var values; //values of those txt fields	


function saveValues(memberIDS){
	var arrayCount;
	//alert(memberIDS);
	ids = new Array();
	ids = memberIDS.split("|");
	arrayCount = ids.length;
	
	values = new Array(arrayCount);
	//alert(ids[0]+" "+ids[1]+" "+ids[2]+" "+ arrayCount);
	
	for(i=0;i<arrayCount-1;i++){
		values[i] = document.getElementById(ids[i]).value;
	}

}

function restoreValues(){

	var arrayCount;
	arrayCount = ids.length;
	
	for(i=0;i<arrayCount-1;i++){
		document.getElementById(ids[i]).value = values[i];
	}

}