// JavaScript Document
function isBlank(objname,msg,divname)
{		
	if(objname.value == "")
	{	
		_show(msg,divname);	
		return 1;
	}
	else
	{
		_hide(divname);
		return 0;
	}	
}

function isCompare(objname,value,msg,divname)
{	
	if(objname.value == value)
	{	
		_show(msg,divname);	
		return 1;
	}
	else
	{
		_hide(divname);
		return 0;
	}	
}

function ispwdcompare(objname1,objname2,msg,divname)
{
	if(objname1.value != objname2.value)
	{
		_show_msg(msg,divname);
		return 1;
	}
	else
	{
		_hide_msg(divname);
		return 0;
	}
}

function isEmail(objname,msg,divname)
{		
	if (objname.value != "")
	{			
		var valEmail = objname.value;
		var reg = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		//var reg = /^[a-z][a-z_0-9\-\.]+@[a-z_0-9\-\.]+\.[a-z]{2,4}$/i
		if(!reg.test(valEmail))
		{			
			_show_msg(msg,divname);
			return 1;
		}
		else
		{				
			_hide_msg(divname);		
			return 0;
		}	
	}
	else
	{
		_show_msg(msg,divname);
		return 1;	
	}
}

function ischeckpwdlength(objname,msg,divname)
{	
	var newpass = trimAll(objname.value);
	if(newpass.length < 6)
	{		
		_show(msg,divname);
		return 1;
	}
	else
	{
		_hide(divname);
		return 0;
	}
}

function iscomboselect(objname,compare,msg,divname)
{		
	if(objname.value == compare)
	{			
		_show_msg(msg,divname);		
		return 1;
	}
	else
	{
		_hide_msg(divname);	
		return 0;
	}
}

function isselect(objname,name,msg,divname)
{	
	var fav_count = objname.elements[name].length;
	var is_checked = 0;	

	for (var i = 0; i < fav_count; i++) 
	{
		if(objname.elements[name][i].checked)
		{	
			is_checked++;
		}		
	}
	if(is_checked==0)
	{
		_show(msg,divname);	
		return 1;
	}
	else
	{		
		_hide(divname);
		return 0;
	}	
}

function isPhone(objname,msg,divname)
{
	if (trimAll(objname.value) != "")
	{
		if (isNaN(parseInt(trimAll(objname.value))) || parseInt(trimAll(objname.value)) < 0)
		{			
			_show(msg,divname);	
			return 1;
		}
		else
		{
			_hide(divname);
			return 0;
		}
	}
	else
	{
		return 0;
	}
}

function isZip(objname,msg,divname)
{
	if (objname.value != "")
	{
		var valZip = objname.value;
		var reg = /^\d{5}[- ]\d{4}|\d{5,6}$/;
		if(reg.test(valZip))	
		{
			_show(msg,divname);	
			return 1;
		}
		else
		{		
			_hide(divname);
			return 0;
		}
	}
	else
	{
		_show(msg,divname);	
		return 1;
	}
}

function datecompare(date1,date2)
{
	_a = datefrom = date1.split("/");
	_c = datefrom = date2.split("/");
	
	var date1 = new Date(_a[2],_a[1],_a[0]);
	var date2 = new Date(_c[2],_c[1],_c[0]);
	
	var date1Comp = date1.getTime(); // milliseconds
	var date2Comp = date2.getTime();	
	
	if (date1Comp > date2Comp)
	{	  
	  return false;
		
	}
	else
	{	
		return true;
	}
}

function isimage(obj)
{
	if (obj.value.length>0)
	{
		if (obj.value.length>4)
		{
			var ext = obj.value.substring(obj.value.length-3,obj.value.length);
			if (ext == 'jpg' || ext == 'JPG' || ext == 'jpeg' || ext == 'JPEG' || ext == 'gif' || ext == 'GIF' || ext == 'png' || ext == 'PNG')
			{
				return true;
			}
			else
			{
				alert('- Upload only .jpg,.gif Or .png File')
				obj.value="";								
				return false;
			}
		}
		else
		{
			alert('- Upload only .jpg,.gif Or .png file')
			obj.value="";			
			return false;
		}
	}
}

// updated
function isDate(IsItReal,msg,divid)
{
	if (IsItReal.value != "")
	{
		var valDate = IsItReal.value;
		/*var reg = /^(0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])[- /.]((19|20)[0-9][0-9]+)$/;*/
		var reg = /^((19|20)[0-9][0-9]+)[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/;
		if(reg.test(valDate))
		{
			_hide(divid);
			return 0;
		}
		else
		{
			_show(msg,divid);	
			return 1;
		}
	}
	else
	{	
		_hide(divid);
		return 0;
	}
}

// updated
function IsNumeric(sText,msg,divid)
{
   var ValidChars = "0123456789";
   var IsNumber=true;
   var Char;
   for (i = 0; i < sText.length && IsNumber == true; i++)
   {
      Char = sText.charAt(i);
      if (ValidChars.indexOf(Char) == -1)
	  {
       	_show(msg,divid);	
		return 1;
	  }
   }
   _hide(divid);
   return 0;
}

function _show(msg,objdiv)
{
	if(document.getElementById(objdiv))
	{
		document.getElementById(objdiv).innerHTML = msg;	
		//document.getElementById(objdiv).style.color = "red";
	}
}

function _hide(objdiv)
{
	if(document.getElementById(objdiv))
	{
		document.getElementById(objdiv).innerHTML = '';
		//document.getElementById(objdiv).style.color = "#939393";
	}
}

function _show_msg(msg,objdiv)
{
	if(document.getElementById(objdiv))
	{
		document.getElementById(objdiv).innerHTML = msg;			
	}
}

function _hide_msg(objdiv)
{
	if(document.getElementById(objdiv))
	{
		document.getElementById(objdiv).innerHTML = '';		
	}
}

function trimAll(sString)
{
	while (sString.substring(0,1) == ' ')
	{
		sString = sString.substring(1, sString.length);
	}
	
	while (sString.substring(sString.length-1, sString.length) == ' ')
	{
		sString = sString.substring(0,sString.length-1);
	}
	return sString;
}

function verifycode(code,value,msg,divname)
{
	if(trimAll(value) != code)
	{	
		_show(msg,divname);	
		return 1;
	}
	else
	{
		_hide(divname);
		return 0;
	}	
}

function selectradiocheck(frmObj,msg,divname)
{
	var flag = 0;
	var strid = "0";
	
	for(i = 0; i < frmObj.elements.length; i++) 
	{		
		elm = frmObj.elements[i]
		if (elm.type == "radio"  && !isNaN(elm.id)) 
		{			
			if (elm.checked)
			{				
				if (flag==0)
				{					
					flag = 1;					
				}			
			}
		}		
	}
	if (flag == 1)
	{
		//return true;
		_hide(divname);
		return 0;
	}
	else
	{
		//alert("- Select at least one record.");
		//return false;
		_show(msg,divname);	
		return 1;
	}
} 
function opensearch()
{
	document.getElementById('search_div').style.display = '';
}
function closesearch()
{
	document.getElementById('search_div').style.display = 'none';
}

function opencomment(mid)
{
	document.getElementById('comment'+mid).style.display = '';
}
function closecomment(mid)
{
	document.getElementById('comment'+mid).style.display = 'none';
}

