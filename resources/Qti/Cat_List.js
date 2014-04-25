CheckCategory= function() {

var catxt = document.getElementById("mw_CategorylistBox").value;
		
   if (!catxt || catxt.length() <= 0 || catxt ==="")
   {    alert('Please Choose A Category');
        return false;
    }
    else { 
    	//document.forms["AddQSp"].submit();		
    	return true;  
    }
};

CheckCategory2= function() {

	var catxt = document.getElementById("mw_Categorylist").value;
			
	   if (!catxt || catxt.length() <= 0 || catxt ==="")
	   {    alert('Please Choose A Category');
	        return false;
	    }
	    else { return true;  }
	};
	
$(document).ready(function() {$("#mw-AddQuesButton").click(CheckCategory);});
$(document).ready(function() {$("#wpSave").click(CheckCategory2);});