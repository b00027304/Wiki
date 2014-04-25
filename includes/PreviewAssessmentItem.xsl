<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet 
 version="1.0" 
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns:x="http://www.imsglobal.org/xsd/imsqti_v2p1" 
 xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1 http://www.imsglobal.org/xsd/imsqti_v2p1.xsd">

<xsl:template match="/x:assessmentItem">
 
 <html>
 <head>
	 <link rel="stylesheet" href="styles.css" type="text/css" media="screen"/>
 </head>
 
  <body>
  
   <div id="title">
    <h5>Title: <xsl:value-of select="@title"/> </h5>
   </div>
   
   
   <xsl:variable name="Stimulus">
	<xsl:value-of select="//x:div/text()"/>
	</xsl:variable>
	
	<xsl:if test="normalize-space($Stimulus)!=''"> 
	 <div id="stimulus">
    <xsl:value-of select="//x:div/text()"/>
    </div>
	</xsl:if>
  
  <xsl:variable name="class">
    <xsl:value-of select="//@class"/>
  </xsl:variable>
   
   
  <xsl:choose>   
  <xsl:when test="$class!='eqiat-te'">
  
     <xsl:for-each select="//x:ol">
		   <h5>Option List:</h5>
			   <xsl:if test="@class='emioptions'"> 
					  <div id="EMIOptions">
					  <ol type="A">
					  <xsl:for-each select="//x:li">
					  	<li><xsl:value-of select="."/></li><br/>
					  </xsl:for-each>
					  </ol>
					  </div>
					  <br/>
			  </xsl:if>  
		  </xsl:for-each>
  
		  <div id="Questions">
			  <xsl:for-each select="//x:choiceInteraction">
		
				<xsl:variable name="correctAnswerid">
			     <xsl:value-of select="./@responseIdentifier"/>
			  	</xsl:variable>
		
		       <xsl:variable name="Question">
			    <xsl:value-of select="x:prompt"/>
			   </xsl:variable>
			  
			  <xsl:if test="normalize-space($Question)!=''"> 
				 <div id="Question">
			  	  <b>Question:</b>&#160;
			  	  <xsl:value-of select="x:prompt"/> <br/><br/>
			  	</div>
	          </xsl:if> 
					  	
			  	<div id="Option">
			    <b>Choices:</b><br/>
			    <ul>
			    <xsl:for-each select="x:simpleChoice">
			  	  <li> <xsl:value-of select="."/> <br/></li>
			  	</xsl:for-each>
			  	</ul><br/>	
			    </div>
			    
			    <xsl:for-each select="//x:responseDeclaration[@identifier=normalize-space(string($correctAnswerid))]">
			  		
			  		<xsl:variable name="correctAnswer"> <xsl:value-of select="x:correctResponse"/> </xsl:variable>
		   	         
			        <div id="correctAnswer">
			            <b>Correct Answer(s):</b>&#160;
			            
			            <xsl:for-each select="//x:choiceInteraction[@responseIdentifier=normalize-space(string($correctAnswerid))]">
				        
				             <xsl:for-each select="x:simpleChoice">
				             <ul>
				            	    <xsl:variable name="id"> <xsl:value-of select="@identifier"/> </xsl:variable>
							        <xsl:if test="contains(normalize-space(string($correctAnswer)),$id)"> 
							        <xsl:value-of select="//x:simpleChoice[normalize-space(string(@identifier))=$id]"/> &#160;
								  	</xsl:if>
							</ul> 
					  	     </xsl:for-each>  
					   </xsl:for-each>
					   
					</div>  
			    	
			    </xsl:for-each>
			    
		    </xsl:for-each>
		  </div>
  </xsl:when>
  <xsl:otherwise>
  
  
  <xsl:variable name="Question">
    <xsl:value-of select="//x:div[@class='textentrytextbody']"/>
  </xsl:variable>
  
 <xsl:if test="normalize-space($Question)!=''"> 
<div id="Question">
      <b>Question:</b>&#160;	  
	  <xsl:value-of select="//x:div[@class='textentrytextbody']"/>  	
</div>
 </xsl:if> 
  
	<xsl:for-each select="//x:textEntryInteraction">
	  	
	  	<xsl:variable name="GabId"> 
	  	<xsl:value-of select="@responseIdentifier"/>
	  	</xsl:variable>
	  	
	  	 <xsl:for-each select="//x:responseDeclaration[@identifier=normalize-space(string($GabId))]">
	  	  
		  	<div id="correctAnswer">
		  	<xsl:for-each select="x:mapping">
		  	   <b>Correct Answer(s) for gab <xsl:value-of select="number(substring($GabId,14,2))+1"/>:</b>&#160;
		  	     
		  	    <xsl:for-each select="x:mapEntry">
		  	    <ul>
				  <xsl:value-of select="@mapKey"/> &#160;
				</ul> 
				 </xsl:for-each>
			   	
		    </xsl:for-each>	
		  	</div>
	  	
	    </xsl:for-each>	
	  
  </xsl:for-each>
	
	
	
  </xsl:otherwise>
  </xsl:choose>
  
 </body>
</html>
</xsl:template>

<xsl:template name="AddingGabs">
   <xsl:param name="GabCount"/>
   <xsl:param name="txtbody"/>
   <xsl:choose>
   <xsl:when test="$GabCount=0">
    <xsl:value-of select="$txtbody"/>
   </xsl:when>
   <xsl:otherwise>> 
   
   <xsl:value-of select="translate(concat(substring-before($txtbody,'--'),' [] '),'-',' ')"/>
     <xsl:call-template name="AddingGabs">
     <xsl:with-param name="GabCount" select="number($GabCount)-1"/>
     <xsl:with-param name="txtbody" select="substring-after($txtbody,'--')"/>
   </xsl:call-template>
   
   </xsl:otherwise>
   
 </xsl:choose>
 
</xsl:template>
</xsl:stylesheet>