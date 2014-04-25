<?php

/*
 * Eqiat
 * Easy QTI Item Authoring Tool
 */

/*------------------------------------------------------------------------------
(c) 2010 JISC-funded EASiHE project, University of Southampton
Licensed under the Creative Commons 'Attribution non-commercial share alike' 
licence -- see the LICENCE file for more details
------------------------------------------------------------------------------*/

class PreviewAssessmentItemAction extends ItemAction {
	public function name() {
		return "preview";
	}

	public function description() {
		return "Preview the assessment item in QTIEngine";
	}

	public function getLogic() {
		$ai = QTIAssessmentItem::fromQTIID($_REQUEST["qtiid"]);
		$Qtixml=$ai->getQTIIndentedString();
	
		$myFile = "E:/wamp/www/Eqiat/xml_tmp/".$_REQUEST["qtiid"].".xml";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh,$Qtixml);
		fclose($fh);
		
		$myFile2 = "E:/wamp/www/Eqiat/xml_tmp/Item.txt";
		$fh = fopen($myFile2, 'w') or die("can't open file");
		fwrite($fh,$_REQUEST["qtiid"]);
		fclose($fh);
		
		$xslDoc = new DOMDocument();
		$xslDoc->load("PreviewAssessmentItem.xsl");
			
		$proc = new XSLTProcessor();
		$proc->importStylesheet($xslDoc);
		echo $proc->transformToXML(simplexml_load_string($Qtixml));
	
		
	}

	public function available(QTIAssessmentItem $ai) {
		return $ai->itemOK();
	}
}

?>
