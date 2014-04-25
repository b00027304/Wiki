RemoveAllLO= function() {
    
	$('#mw_LOlistBox').empty();
	
	var Parent = document.getElementById('Qcounts');
	while(Parent.hasChildNodes())
	{
	   Parent.removeChild(Parent.firstChild);
	}
	
	     			
};
RemoveLO= function() {
    
var selectedIndex= document.getElementById('mw_LOlistBox').selectedIndex;
	
	if(selectedIndex >=0) {
    var list=document.getElementById('mw_LOlistBox');
	var selectedIndex= list.selectedIndex;
	document.getElementById("mw_LOlistBox").options.remove(selectedIndex);
	removeRow(selectedIndex);
	}
	
};
MoveLO= function() {

  // Create an Option object 
	var selectedIndex= document.getElementById('mw_CatlistBox').selectedIndex;
	
	if(selectedIndex >=0) {
	
		var opt = document.createElement("option");
		opt.value =  document.getElementById('mw_CatlistBox').value;
		opt.text =  document.getElementById('mw_CatlistBox').value.replace(/_/g,' ');
		document.getElementById("mw_LOlistBox").options.add(opt);
		var list=document.getElementById("mw_LOlistBox");
		var j=document.getElementById("mw_LOlistBox").options.length; 
		var i=0;
		for (i=0; i<j;i++ ){
			list.options[i].selected = true;	
		}
		addRow(document.getElementById('mw_CatlistBox').value);
  }
      			
};

CheckForSelection= function() {

	  // Create an Option object 
		var j= document.getElementById('mw_LOlistBox').length;
		var list= document.getElementById('mw_LOlistBox');
		
		if(j> 0 )
		{
			var i=0;
			for (i=0; i<j;i++ )
			{ list.options[i].selected = true;}
			
			var qcount=true;
			
			var i=0;
			for (i=0; i<j;i++ )
			    {   var tmpid='Ques_count_'+i;
				   if(document.getElementById(tmpid).value!="")
				   {qcount=true;}
				   else{qcount=false;}
				}
					
			if(!qcount)
			
			{alert("Can't Generate an Assessment. Please Make Sure to Specify the number of question for each learning outcome");  return false;}			
			else{return true;}
			 
		}
		else
		{alert("Can't Generate an Assessment. Please Make Sure to Select Some Learning Outcomes");  return false;}	
	  			
	};
	
function addRow(txt)
	{
	  var ptable = document.getElementById('Qcounts');
	  var lastElement = ptable.rows.length;
	  var index = lastElement;
	  
	  if (lastElement > 0)
	  {var row = ptable.insertRow(lastElement);}
	  else {var row = ptable.insertRow(0);}

      
	  var cellLeft = row.insertCell(0);
	  textNode = document.createTextNode(txt.replace(/_/g,' '));
	  cellLeft.appendChild(textNode);
	  
	  var input = document.createElement("input");
	  input.setAttribute("type", "hidden");
	  input.setAttribute("name", "QCat_"+lastElement);
	  input.setAttribute("id", "QCat_"+lastElement);
	  input.setAttribute("value",txt);
	  cellLeft.appendChild(input);
	   
	  var cellText = row.insertCell(1);
	  var element = document.createElement('input');
	  element.type = 'text';
	  element.name = 'Ques_count_' + index;
	  element.id = 'Ques_count_' + index;
	  element.size = 10;
	 
	  cellText.appendChild(element);
	  document.getElementById("psize").value=index;
	  }	
	
function removeRow(indx)
{
  var ptable = document.getElementById('Qcounts');
  var lastElement = ptable.rows.length;
  if (lastElement > 0) ptable.deleteRow(indx);
  if(document.getElementById("psize").value>1)
	{
	   document.getElementById("psize").value = 
           document.getElementById("psize").value-1;
	}
}	

$(document).ready(function() {$("#mw_move_LO").click(MoveLO);});
$(document).ready(function() {$("#mw_remove_LO").click(RemoveLO);});
$(document).ready(function() {$("#mw_removeAll_LO").click(RemoveAllLO);});
$(document).ready(function() {$("#mw_GnrtAssmnt").click(CheckForSelection);});