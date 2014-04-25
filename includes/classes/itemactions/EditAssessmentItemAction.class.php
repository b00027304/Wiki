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

class EditAssessmentItemAction extends ItemAction {
	private $ai;

	public function name() {
		return "edit";
	}

	public function description() {
		return "Edit the assessment item";
	}

	public function beforeLogic() {
		$this->ai = QTIAssessmentItem::fromQTIID($_REQUEST["qtiid"]);
	}

	public function getLogic() {
		// nothing posted -- show form with data as is (possibly empty)
		$this->ai->showForm();
	}

	public function postLogic($data,$ai) {

		// form submitted -- try to build QTI		

		$ai->getQTI($data); }
}
?>
