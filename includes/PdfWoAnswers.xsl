<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet 
 version="1.0" 
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns:x="http://www.imsglobal.org/xsd/imsqti_v2p1" 
 xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1 http://www.imsglobal.org/xsd/imsqti_v2p1.xsd">
<xsl:output method="text" encoding="UTF-8" indent="no"/>

<xsl:template match="/x:assessmentItem">
    <xsl:text>Title: </xsl:text><xsl:value-of select="@title"/> 
    <xsl:text>&#10;</xsl:text>
    <xsl:variable name="Stimulus">
	<xsl:value-of select="//x:div/text()"/>
	</xsl:variable>
	
	<xsl:if test="normalize-space($Stimulus)!=''"> 
	<xsl:value-of select="//x:div/text()"/>
	<xsl:text>&#10;</xsl:text>
	</xsl:if> 
 	 
 	 <xsl:variable name="class">
     <xsl:value-of select="//@class"/>
     </xsl:variable>	
 		
 		
 		
  <xsl:choose>
  <xsl:when test="$class!='eqiat-te'">
  
        <xsl:for-each select="//x:ol">
		    <xsl:text>Option List:</xsl:text>
			   <xsl:if test="@class='emioptions'"> 
				<xsl:for-each select="//x:li">
				 &#x09;&#42;&#9;<xsl:value-of select="."/>
			   </xsl:for-each>
			  </xsl:if>
			  <xsl:text>&#10;</xsl:text>  
		  </xsl:for-each>
		  
  		<xsl:for-each select="//x:choiceInteraction">	
				<xsl:variable name="correctAnswerid">
			     <xsl:value-of select="./@responseIdentifier"/>
			  	</xsl:variable>	  
			  	<xsl:variable name="Question">
			    <xsl:value-of select="x:prompt"/>
			  	</xsl:variable>
			  <xsl:if test="normalize-space($Question)!=''"> 
				  <xsl:text>Question:</xsl:text>
				  <xsl:text>&#10;</xsl:text>			  	  
				  <xsl:value-of select="x:prompt"/>
				  <xsl:text>&#10;</xsl:text>
	          </xsl:if> 
			  				   
			<xsl:text>Choices:</xsl:text>
				<xsl:text>&#10;</xsl:text>		    
			   <xsl:for-each select="x:simpleChoice">
			  	&#x09;<xsl:text>*</xsl:text>&#9;<xsl:value-of select="."/>
			  	<xsl:text>&#10;</xsl:text>	
			  </xsl:for-each>
		    </xsl:for-each>
  </xsl:when>
  <xsl:otherwise>
  
  <xsl:variable name="Question">
  <xsl:value-of select="//x:div[@class='textentrytextbody']"/>
  </xsl:variable>
  
 <xsl:if test="normalize-space($Question)!=''"> 
 <xsl:text>Question:</xsl:text>
 <xsl:text>&#10;</xsl:text>			  	  
 <xsl:value-of select="normalize-space(string(//x:div[@class='textentrytextbody']))"/>
 <xsl:text>&#10;</xsl:text>
 </xsl:if> 
  </xsl:otherwise>
  </xsl:choose>
</xsl:template>
</xsl:stylesheet>