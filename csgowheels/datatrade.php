<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Untitled Document</title>
		
		<script>
			function createRequestObject() {
			    var ro;
			    var browser = navigator.appName;
			    if(browser == "Microsoft Internet Explorer"){
			        ro = new ActiveXObject("Microsoft.XMLHTTP");
			    }else{
			        ro = new XMLHttpRequest();
			    }
			    return ro;
			}
			
			var http = createRequestObject();
			
			function sndReq() {
			    http.open('get', 'inventory1.php');
			    http.onreadystatechange = handleResponse;
			    http.send(null);
			    setTimeout("sndReq()", 2000); // Recursive JavaScript function calls sndReq() every 2 seconds
			}
			
			function handleResponse() {
			    if(http.readyState == 4){
			        var response = http.responseText;
			        if (response != responseold || responsecheck != 1) {
			            var responsecheck = 1;
			            document.getElementById("mydata").innerHTML = http.responseText;
			            var responseold = response;
			        }
			    }
			}
		</script>
	</head>
	
	<body onLoad="javascript:sndReq();">
		<div id="mydata" name="mydata">
			abc
		</div>
	</body>
	
</html>