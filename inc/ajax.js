// JavaScript Document
var XMLHttpRequestObject = false;
var XMLHttpRequestObject2 = false;

if(window.XMLHttpRequest) {
	XMLHttpRequestObject = new XMLHttpRequest();
	XMLHttpRequestObject2 = new XMLHttpRequest();
}
else if (window.ActiveXObject) {
	XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
	XMLHttpRequestObject2 = new ActiveXObject("Microsoft.XMLHTTP");
}
	
function getData(dataSource, divID) {
		//dataSource is a URL that will get data by GET and print an output
		//divID is the name of an element that will have its innerHTML udpated by this function
		//getData('calendar.php?this=that', 'some_div');
		
	// substitute + symbols by %2B so that they are passed by $_GET
	dataSource = dataSource.replace('+', '%2B');
		
	if(XMLHttpRequestObject) {
		var obj = document.getElementById(divID);
		XMLHttpRequestObject.open("GET", dataSource);
		
		XMLHttpRequestObject.onreadystatechange = function()
		{
			if(XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
				obj.innerHTML = XMLHttpRequestObject.responseText;
			}
		}
		
		XMLHttpRequestObject.send(null);
	}
}

function getData2(dataSource, divID) {
		//dataSource is a URL that will get data by GET and print an output
		//divID is the name of an element that will have its innerHTML udpated by this function
		//getData('calendar.php?this=that', 'some_div');
		
	// substitute + symbols by %2B so that they are passed by $_GET
	dataSource = dataSource.replace('+', '%2B');

	if(XMLHttpRequestObject2) {
		var obj = document.getElementById(divID);
		XMLHttpRequestObject2.open("GET", dataSource);
		
		XMLHttpRequestObject2.onreadystatechange = function()
		{
			if(XMLHttpRequestObject2.readyState == 4 && XMLHttpRequestObject2.status == 200) {
				obj.innerHTML = XMLHttpRequestObject2.responseText;
			}
		}
		
		XMLHttpRequestObject2.send(null);
	}
}

function getData_param(dataSource, divID, out_func) {
		//dataSource is a URL that will get data by GET and print an output
		//divID is the name of an element that will have its innerHTML udpated by this function
		//getData('calendar.php?this=that', 'some_div');
	// substitute + symbols by %2B so that they are passed by $_GET
	dataSource = dataSource.replace('+', '%2B');
		
	if(XMLHttpRequestObject) {
		var obj = document.getElementById(divID);
		XMLHttpRequestObject.open("GET", dataSource);
		
		XMLHttpRequestObject.onreadystatechange = function()
		{
			if(XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
				obj.innerHTML = XMLHttpRequestObject.responseText;
				eval(out_func);
			}
		}
		
		XMLHttpRequestObject.send(null);
	}
}


function getDataPOST(dataSource, divID, params, out_func) {
	if(XMLHttpRequestObject) {
		var obj = document.getElementById(divID);
		XMLHttpRequestObject.open("POST", dataSource);
		XMLHttpRequestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
//		XMLHttpRequestObject.setRequestHeader('Content-Type', 'charset=iso-8859-1');
				 
		XMLHttpRequestObject.onreadystatechange = function()
		{
			if(XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
				obj.innerHTML = XMLHttpRequestObject.responseText;
				eval(out_func +'();');
			}
		}
		
		XMLHttpRequestObject.send(params);
	}
}