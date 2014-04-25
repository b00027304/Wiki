<?php
/**
 * Implements Special:GenerateAnAssessment
 *
 * Copyright آ© 2004 Brion Vibber <brion@pobox.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @ingroup SpecialPage
 */

/**
 * implements Special:GenerateAnAssessment - create an assessement based on chosen learning outcomes
 * @ingroup SpecialPage
 */

/*For GPL V2. 

AssessmentWiki (extension to MediaWiki) version 1.2.1, Copyright (C)
2013 Faten Qutaifan, Imran Zualkernan and American University of Sharjah, UAE. 


For Creative Commons Share alike Non-Commercial V4. 

AssessmentWiki by Faten Qutaifan, Imran Zualkernan and American University of Sharjah,
UAE <http://creativecommons.org/choose/www.assessmentwiki.org> 
is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0
International License<http://creativecommons.org/licenses/by-nc-sa/4.0/>**/



class SpecialGenerateAnAssessment extends SpecialPage {

		public function __construct() {
		parent::__construct( 'Generateanassessment' );
	}

	/**
	 * Entry point
	 */
	public function execute($par) {
		global $wgRequest, $wgUser, $wgOut;
		
		# Check permissions
		global $wgGroupPermissions;
		$permissionRequired = $this->isAllowed( $wgUser );
		
		if( $permissionRequired !== true ) {
			if( !$wgUser->isLoggedIn() && $wgGroupPermissions['user']['GenerateAssessment'] ) {
				// Custom message if logged-in users without any special rights can upload
				$wgOut->showErrorPage( 'createbulknologin', 'createbulknologintext' );
			} else {
				$wgOut->permissionRequired( $permissionRequired );
			}
			return;
		}
				
		$this->setHeaders();
		$this->outputHeader();
		$wgOut->allowClickjacking();
		$wgOut->addModuleStyles( 'mediawiki.special' );
		$wgOut->addModules('MoveLO');
		$action="/wiki/index.php?action=GenerateAssessment";
		
		$txt="This section allows teachers to create assessments based on the learning outcomes of 
		their choice. To choose a learning outcome just highlight the learning outcome of interest in the learning outcomes selection box.
		After that, click on the \"Copy >>\" button to move it to the Selected learning outcomes list.
		To generate the assessment click on the \"Generate An Assessment button\". The Assessment will be generated in a PDF format.
		For each of the selected learning outcomes the teacher will have to specify the number of questions.
		The system will generate two versions of the assessment one with Answer Key and one without.
		The system will include the same number of specified questions by the teacher as long as enough approved questions are available.
		if the number of questions specified by the teacher is greater than the available number of approved questions the system will include all the available
		questions. If the chosen learning outcome has no questions available then no questions will be included";
		
		$wgOut-> addWikiText($txt);
		
		$wgOut->addHTML("<br/>");
		
		$wgOut->addHTML( <<<HTML
<form id="GenerateAnAssessmentform" name="GenerateAnAssessmentform" method="post" action="$action" enctype="multipart/form-data">

HTML
);

		/*<fieldset>
		<legend><h5>Assessment Type</h5></legend>
		<input type="radio" name="AnswerKey" value="1"  checked="checked"/> With Answer Keys <br/>
		<input type="radio" name="AnswerKey" value="0" /> Without Answer Keys <br/>
		</fieldset>*/
				
		$wgOut->addHTML("<fieldset>");
		$wgOut->addHTML("<legend><h5>Select Learning Outcomes</h5></legend>");
		
		$wgOut->addHTML("<table> \n");
		$wgOut->addHTML("<tr>\n");
		$wgOut->addHTML("<th><h5> Learning Outcomes Selection Box </h5></th>");
		$wgOut->addHTML("<th><h5> Selected Learning Outcomes List </h5></th>");
		$wgOut->addHTML("<th><h5> Number of Questions to include </h5></th>");
		$wgOut->addHTML("</tr>\n");
		
		$wgOut->addHTML("<tr>\n");
		$wgOut->addHTML("<td>\n");
		$wgOut->addHTML($this->addCategorylist());
		$wgOut->addHTML("</td>\n");
		
		$wgOut->addHTML("<td>\n");
		$wgOut->addHTML(<<< HTML
		<select size="4" name="LOlistBox[]" id="mw_LOlistBox" style="width:400px; height:250px" multiple="multiple"> </select>
HTML
);
		$wgOut->addHTML("</td>\n");
		
		$wgOut->addHTML("<td>\n");
		$wgOut->addHTML("<input type=\"hidden\" name=\"psize\" id=\"psize\">");
			$wgOut->addHTML("<table id=\"Qcounts\" nmae=\"Qcounts\">\n");
			//$wgOut->addHTML("<tr> \n");
			//$wgOut->addHTML("<td>\n");
			//$wgOut->addHTML("<input id=\"Ques_count_0\" type=\"text\" name=\"Ques_count_0\" /> \n");
			//$wgOut->addHTML("</td> \n");
			//$wgOut->addHTML("</tr> \n");
			$wgOut->addHTML("</table>\n");
		$wgOut->addHTML("</td>\n");
		
		$wgOut->addHTML("</tr> \n");
		$wgOut->addHTML("</table>\n");
		
		
		$wgOut->addHTML("</br>");
		
		$wgOut->addHTML( <<<HTML
	   <div id="mw_LO_controls" style="margin-left:350px; margin-right:350px; width:100%;">
	   <button type="button" id="mw_move_LO"> Copy >> </button>
	   <button type="button" id="mw_remove_LO"> Remove << </button>	
	   <button type="button" id="mw_removeAll_LO"> Remove All<< </button>	
	   </div>	
	   </br>
	   </fieldset>		
	   <input type="submit" value="Generate As Assessment" id="mw_GnrtAssmnt"/> </form>
HTML
);	
		
		
		
		

}// end of execute

/**
* returns HTML code for a list Box for all the categories
* */
protected function addCategorylist(){
		
		/*$db = wfGetDB( DB_SLAVE );*/
	
		$db = wfGetDB( DB_MASTER );
		
		$category_table = $db->tableName( 'page' );
		$db_result = $db->query( "select page_title FROM ${category_table} where page_namespace=14 " );
	
		$listBox=" <select size=\"30\" name=\"CategoryListBox\" id=\"mw_CatlistBox\" style=\"width:400px;  height:250px;\">";
	
		while ( $row = $db->fetchObject( $db_result ) ) {
	
			$listBox.=" <option value=\"".trim($row->page_title)."\"";
			$listBox.= ">";
			$listBox.=strtr(trim($row->page_title),'_',' ')." </option> ";
		}
	
		$listBox.=" </select> ";
	
		return $listBox;
	
	}// end of addCategorylist
	
public function Generate_An_Assessment($Request){


$LearningOutcomes=array();

global $wgOut,$IP,$wgUser,$wgRequest;
    
    
   //$myFile = "D:/Hosting/9845962/html/Wiki/includes/specials/Doa.txt";
   //$fh = fopen($myFile, 'w') or die("can't open file");   
    
    if($Request->getBool('LOlistBox')==true || $Request->wasposted())
    {
	   $LearningOutcomes=$Request->getArray('LOlistBox');
	   
	   for($i=0; $i< sizeof($LearningOutcomes);$i++)
	   {$LearningOutcomes[$i]=str_replace('_',' ',$LearningOutcomes[$i]);}
	   
	   $Catlist="";
	   $Cat_list="";
	   $Qcount=array();
	      
	 
	for($i=0; $i< sizeof($LearningOutcomes);$i++)
	{     
		//$Qcount[$i]=$wgRequest->getval('Ques_count_'.$i);
		$Qcount[str_replace('_',' ',$wgRequest->getval('QCat_'.$i))]=$wgRequest->getval('Ques_count_'.$i);
		
		     if ($i != sizeof($LearningOutcomes)-1)
		     {  $Catlist=$Catlist."'".str_replace('_',' ',$LearningOutcomes[$i])."',";
		     	$Cat_list=$Cat_list.str_replace('_',' ',$LearningOutcomes[$i]).",";
		     }
		      
			else 
			{
			  $Catlist=$Catlist."'".str_replace('_',' ',$LearningOutcomes[$i])."'";
			  $Cat_list=$Cat_list.str_replace('_',' ',$LearningOutcomes[$i]);
			}
	}	

	//fwrite($fh,print_r($Catlist,true));

	//$db = wfGetDB( DB_SLAVE );
	$db = wfGetDB( DB_MASTER);
	
	$db_result = $db->query( "CREATE TEMPORARY TABLE tmp as (select distinct b.rev_page,b.rev_user_text as user,
							  REPLACE(a.page_title,'_',' ') as page_title FROM page a, revision b where
							  a.page_id=b.rev_page and b.rev_timestamp in(select MIN(rev_timestamp)as rev_timestamp FROM revision c WHERE c.rev_page=b.rev_page) and
							  a.page_title like '%.Q%' AND b.rev_user_text like 'Teacher%')" );
	
	
	
	$db_result = $db->query( "SELECT a.rev_page,MAX(a.rev_id)rev_id,a.rev_user_text,REPLACE(d.page_title,'_',' ') as page_title,
			                  MAX(a.rev_timestamp),b.old_text FROM  revision a, page d, text b 
			                  WHERE a.rev_text_id=b.old_id AND a.rev_page=d.page_id AND a.rev_user_text =
                              (SELECT c.user FROM tmp c WHERE a.rev_page=c.rev_page)
			                  GROUP BY a.rev_page,a.rev_user_text,d.page_title order by a.rev_page" );
	

	$datarows = $db->numRows( $db_result );
	
	
	
	if ($datarows > 0 )
	
	{
			
			$MasterArray=array();
			$Pages=array();
				
			while ($row = $db->fetchObject( $db_result ))
			{
				$MasterArray[trim($row->rev_page)]=$row->old_text;
				$Pages[]=trim($row->rev_page);
			}
			
			//fwrite($fh,print_r($MasterArray,true));
			
		
			
		$this->Generate_PDF($Pages,$MasterArray);
	
	}// if $datarows > 0
	
		
		else 
		{  $wgOut->addWikiText('It seems that there are no questions listed under the chosen  Learning outcomes Yet. Try another set of learning outcomes or start adding questions.');
		   $wgOut->addHTML("<a href=\"index.php?title=Special%3AGenerateAnAssessment\">Try other Learning Outcomes </a>");                                                              
		}	
			
			
			
    }
	else { 
	
	$wgOut->setTitle($this-> getTitle());
	$wgOut->addWikiText('Please make Sure you have chosen some Learning outcomes. Try to regenerate the assessment');
	$wgOut->addHTML("<a href=\"index.php?title=Special%3AGenerateAnAssessment\">Try Again</a>");
	}
	//fclose($fh);
}// end of Generate_An_Assessment

protected function LOG_An_Assessment($text,$list,$TimeStamp,$type){
global $wgUser;
	
	$dbr = wfGetDB( DB_MASTER );
	$dbr->insert('Assessments',array('Test_Date'=>$TimeStamp,'user'=>$wgUser,'Test_Type'=>$type,'List_of_LO'=>$list,'Test_txt'=>$text));
	$dbr->commit('Assessments');
}

protected function Generate_PDF($Pages,$AssessemtItems){
global $IP,$wgUser;	

	//$myFile = "D:/Hosting/9845962/html/Wiki/includes/specials/Faten.txt";
	//$fh = fopen($myFile, 'w') or die("can't open file");

	//fwrite($fh,print_r($AssessemtItems,true));
	
require_once('includes/tcpdf/config/lang/eng.php');
require_once('includes/tcpdf/tcpdf.php');
	
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$counter_count=1;
	

			$pdf->AddPage('P','A4');
			$pdf->SetLeftMargin(20);
			$pdf->SetRightMargin(20);
			
			$pdf->SetFont('times','B',15);
			
			$pdf-> SetFillColor(51,51,53);
			$pdf-> SetTextColor(255,255,255);
			
			$pdf->Cell(0,10,'This is a Math Assessment for Grade 6',1,1,'C',true);
			$pdf->Ln(12);
			
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('Courier','',9);
			
			$pdf->Write(5,'This Assessment was generated using Curriculum Wiki; an Educational Crowd sourcing application for collecting questions which are collaboratively written by volunteer teachers.This Assessment contains questions for each of the following learning outcomes:-');
						
			$pdf->Ln(5);
			
						
			$pdf->SetFont('Courier','B',20);
			
			$pdf->SetAutoPageBreak(true,5);
			
			$pdf->SetFont('Courier','',9);
			
			//set header & Title
			$pdf->SetCreator($wgUser);
			$pdf->SetAuthor('This Assessment was created using Curriculum Wiki an Educational tool for Crowd Sourcing Assessments');
			$pdf->SetTitle('An Assessment');
					
			
			$pdf->sety(220);
			
			//Body
			$date = getdate();
	
			$label="	School   Name: ----------------------------------------------------------------\n";
			$label.="	Techer   Name: ----------------------------------------------------------------\n";
			$label.="	Student  Name: ----------------------------------------------------------------\n";
			$label.="	Semester Name: ----------------------------------------------------------------\n";
			$label.="	Date: ".$date['mday']."-".$date['mon']."-".$date['year'];
			
			$pdf->MultiCell(0,30,$label,1,'C',false,1,'','',true,0,false,true,30,'M',true);
									
			//Set the footer
			$pdf->Line(20,280, 190, 280);
			$pdf->SetFont('Times','',7);
			$pdf->sety(283);
			$footer='Copyright '.chr(169).' Curriculum Wiki '.$date['year'];
			$footer.=". Generated {$date['month']} {$date['year']}. May reproduce for instructional and educational purposes only; not for personal or financial gain.";
			
			$pdf->cell(0,5,$footer,0,'C');
			
			$xslDoc = new DOMDocument();
										
			$xslDoc->load("$IP/includes/PdfWAnswers.xsl");
			
			$proc = new XSLTProcessor();
			$proc->importStylesheet($xslDoc);
				
			$pdf->SetFont('freeserif', '', 9);
			$pdf->AddPage();
			$text='';
			$test='';
			$x=0;
			$y=0;
			
	
			for($i=0; $i <=400;$i++)
			{   	
				$Question=$AssessemtItems[$Pages[$i]];
				//fwrite($fh,$Pages[$i]."\n");
				//strip the Category from the xml
				$Colonpos=strrpos($Question,':');
				$SecArgpos=strrpos($Question,']');
				$catxt="[[Category:".trim(substr($Question,$Colonpos+1,$SecArgpos-$Colonpos-2))."]]";
				$trans = array("$catxt" => " ");
				$outputxml=trim(strtr($Question,$trans));
				
				libxml_use_internal_errors(true);
				
				if(!simplexml_load_string($outputxml))
				{  
					$Question = gzinflate( $outputxml );				
					$start=strpos($Question,'<');
					$end=strrpos($Question,'>');
					$outputxml=trim(substr($Question,$start,($end-$start)+1));
					
				}			

				if (!simplexml_load_string($outputxml))
				{//fwrite($fh,' We are zipped '."\n");
				
				//fwrite($fh,$Question."\n");
				//fwrite($fh,$i." ".$outputxml."\n");
				
				}
				
				else{
				//fwrite($fh,$i." ".$outputxml."\n");
          
				$text=$proc->transformToXML(simplexml_load_string($outputxml));
				$test.=$text."\n";
				}
							

				if ($x!=0 && $y!=0)
					
				$pdf->SetXY($x,$y);
				
				$pdf->SetFont('Times','B',12);
				
				$pdf-> SetFillColor(51,51,53);
				$pdf-> SetTextColor(255,255,255);
				$pdf->cell(0,5,' Question ID '.$Pages[$i].': ',1,1,'L',True);
				$pdf->Ln(2);
				
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFont('freeserif', '', 9);
				
				$pdf->Write(5,$text);
				
				$pdf->Write(5,'Quality Scale 5 means Excellent and 1 means poor');
				$pdf->Ln(5);
				$pdf->cell(10,5,'5','TBL',0,'C',False);
				$pdf->cell(10,5,'4','TBL',0,'C',False);
				$pdf->cell(10,5,'3','TBL',0,'C',False);
				$pdf->cell(10,5,'2','TBL',0,'C',False);
				$pdf->cell(10,5,'1','TBLR',0,'C',False);
				$pdf->Ln(5);
				
				$pdf->Write(5,'Difficulty Scale 5 means Extremely Difficult and 1 means Super Easy');
				$pdf->Ln(5);
				$pdf->cell(10,5,'5','TBL',0,'C',False);
				$pdf->cell(10,5,'4','TBL',0,'C',False);
				$pdf->cell(10,5,'3','TBL',0,'C',False);
				$pdf->cell(10,5,'2','TBL',0,'C',False);
				$pdf->cell(10,5,'1','TBLR',0,'C',False);
				$pdf->Ln(10);
			
				$x=$pdf->GetX();
				$y=$pdf->GetY();
						    
			}   

				
			$pdf->SetDisplayMode(100);
			
			//File Name
			$fname='Assessment_No_'.rand().'_';
			$curdate='_Date_'.$date['mday'].$date['mon'].$date['year'];
			$TimeStamp=$date['mday'].'-'.$date['mon'].'-'.$date['year'].'_'.$date['hours'].':'.$date['minutes'].':'.$date['seconds'];
			$fname.=$TimeStamp;
		
						
			$pdf->Output($fname.'.pdf','D');
			
			//$this->LOG_An_Assessment($test,$Cat_list,$TimeStamp,'BOTH');
			
			//fclose($fh);
	
		  
}

public static function isAllowed( $user ) {
	foreach ( array( 'GenerateAssessment' ) as $permission ) {
		if ( !$user->isAllowed( $permission ) ) {
			return $permission;
		}
	}
	return true;
}

}// end of CLass
