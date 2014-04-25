<?php
/**
 * Implements Special:DownloadQuestions
 *
 * Copyright © 2004 Brion Vibber <brion@pobox.com>
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
 * implements Special:DownloadQuestions - create an assessement based on chosen learning outcomes
 * @ingroup SpecialPage
 */

class SpecialDownloadQuestions extends SpecialPage {
 
		public function __construct() {
		parent::__construct( 'DownloadQuestions' );
	}

	/**
	 * Entry point
	 */
	public function execute($par) {
		global $wgRequest, $wgUser, $wgOut;

		$this->setHeaders();
		$this->outputHeader();
		$wgOut->allowClickjacking();
		$wgOut->addModuleStyles( 'mediawiki.special' );
			
		$action="/wiki/index.php?action=DownloadQuestions";
		
		$txt="This section allows teachers to download questions based on the learning outcomes of 
		their choice. To choose a learning outcome just highlight the learning outcome of interest in the learning outcomes selection box.
		For the selected learning outcomes the teacher will have to specify the number of questions to download.
		The system will generate random questions in the number specified by the teacher. If the number of questions specified by the teacher 
		is greater than the available number of approved questions the system will download all the available
		questions. If the chosen learning outcome has no questions available then no questions will be downloaded";
		
		$wgOut-> addWikiText($txt);
		
		$wgOut->addHTML("<br/>");
		
		$wgOut->addHTML( <<<HTML
<form id="DownloadQuestionsform" name="DownloadQuestionsform" method="post" action="$action" enctype="multipart/form-data">

HTML
);
			
		$wgOut->addHTML("<fieldset>");
		$wgOut->addHTML("<legend><h5>Select Learning Outcomes</h5></legend>");
		
		$wgOut->addHTML("<table> \n");
		$wgOut->addHTML("<tr>\n");
		$wgOut->addHTML("<th><h5> Learning Outcomes Selection Box </h5></th>");
		$wgOut->addHTML("<th><h5> Number of Questions to include </h5></th>");
		$wgOut->addHTML("</tr>\n");
		
		$wgOut->addHTML("<tr>\n");
		$wgOut->addHTML("<td>\n");
		$wgOut->addHTML($this->addCategorylist());
		$wgOut->addHTML("</td>\n");
		
		$wgOut->addHTML("<td>\n");
		$wgOut->addHTML("<input id=\"Ques_count\" type=\"text\" name=\"Ques_count\" /> \n");
		$wgOut->addHTML("</td>\n");
		
		$wgOut->addHTML("</tr> \n");
		$wgOut->addHTML("</table>\n");
		
		
		$wgOut->addHTML("</br>");
		$wgOut->addHTML('<input type="submit" value="Download Questions" id="mw_DwnldQues"/> </form>');	
	
}// end of execute

/**
* returns HTML code for a list Box for all the categories
* */
protected function addCategorylist(){
		
		$db = wfGetDB( DB_SLAVE );
	
		$category_table = $db->tableName( 'page' );
		$db_result = $db->query( "select page_title FROM ${category_table} where page_namespace=14 " );
	
		$listBox=" <select size=\"1\" name=\"CategoryListBox\" id=\"mw_CatlistBox\">";
	
		while ( $row = $db->fetchObject( $db_result ) ) {
	
			$listBox.=" <option value=\"".str_replace('_',' ',trim($row->page_title))."\"";
			$listBox.= ">";
			$listBox.=strtr(trim($row->page_title),'_',' ')." </option> ";
		}
	
		$listBox.=" </select> ";
	
		return $listBox;
	
	}// end of addCategorylist
	
public function DownloadQuestions($Request){
global $wgOut,$IP,$wgUser,$wgRequest;
    
$LearningOutcomes=array();

   //$myFile = "E:/Documents and Settings/Toshiba/Desktop/Faten.txt";
   //$fh = fopen($myFile, 'w') or die("can't open file"); 
   
   $Catlist="";
   $Qcount=0;
   
    if($Request->getBool('CategoryListBox')==true || $Request->wasposted())
    {
	   $Catlist=$Request->getval('CategoryListBox');
	   $Qcount=$wgRequest->getval('Ques_count');

   if($Qcount>0) {
   	   
		$db = wfGetDB( DB_SLAVE );
		
		$db_result = $db->query( "select approved_revs.Page_id,
										 approved_revs.Rev_id,
										 Revision.rev_text_id,
										 text.old_text,
										 REPLACE(categorylinks.cl_to,'_',' ') as category
								  from   approved_revs,Revision,text,categorylinks where
								         approved_revs.Rev_id=Revision.Rev_id and 
									     Revision.rev_text_id=text.old_id and 
									     approved_revs.Page_id=categorylinks.cl_from and 
									     REPLACE(categorylinks.cl_to,'_',' ') ='$Catlist' order by category" );
	
		$datarows = $db->numRows( $db_result );
		
		if ($datarows > 0 )
		
		{
				
				$MasterArray=array();
					
				while ($row = $db->fetchObject( $db_result ))
				{
					$MasterArray[trim($row->category)][]=$row->old_text;
				}
				
				$db_Cat_Count = $db->query("select REPLACE(categorylinks.cl_to,'_',' ') as category,
			                                       count(approved_revs.Page_id) as counter
										  from     approved_revs, categorylinks where
											       approved_revs.Page_id=categorylinks.cl_from and 
						  						   REPLACE(categorylinks.cl_to,'_',' ') ='$Catlist'
											       group by REPLACE(categorylinks.cl_to,'_',' ')  order by category");
				
				$CountArray=array();
				
				while ($row2 = $db->fetchObject( $db_Cat_Count ))
				{ $CountArray[trim($row2->category)]=$row2->counter; }
				
				
				$AssessemtItems=array();
				
				// For each Category generate 3 Random indexes and use them to retrive the questions
				//if a learning outcomes has no pages listed remove from the list and
				// remove it from the question count array 
				
				$LOwithPages=array();
				
				if (array_key_exists($Catlist, $CountArray))
					array_push($LOwithPages,$Catlist);
						
				$random=array();
				
				foreach($LOwithPages as $LO )
				{    
					//number of pages under each category
					$Count=$CountArray[$LO];
								
					// loop counter 3 questions from each category
					$y=1;
					
					if ($Count >= $Qcount){
						
			          // generate 3 random indexes 
						
								while($y <=$Qcount)
							{   
								$x=rand(0,$Count-1);
				
								
								if(!in_array($x, $random))
								{	array_push($random,$x);
								    $y++;
								}
								
							}//end of while
												
					}//end of if count >3 
					
					else // still there is not so many question added under the category
					{
						
					    while($Count >=1)	
					    {
						array_push($random,$Count-1);
						$Count--;
					
						 }
						
					}
							
					foreach ($random as $pageIdx)
					$AssessemtItems[]=$MasterArray[$LO][$pageIdx];
					
					foreach ($random as $value)
					array_pop($random);
					
				}// end of for each category
				
			//fwrite($fh,print_r($AssessemtItems,true));
			
			$this->Generate_Zip_xml($LOwithPages,$AssessemtItems,$Catlist);
		
		}// if $datarows > 0
		
			
			else 
			{  $wgOut->addWikiText('It seems that there are no questions listed under the chosen  Learning outcomes Yet. Try another set of learning outcomes or start adding questions.');
			   $wgOut->addHTML("<a href=\"index.php/Special:DownloadQuestions\">Try other Learning Outcomes </a>");                                                              
			}	
			
   } //if Qcount >0
   
   else {$wgOut->addWikitext('Please make Sure to specify number of questions');
   		 $wgOut->addHTML("<a href=\"index.php/Special:DownloadQuestions\">Try Again </a>");
   }
			
 }//if posted
 
	else { 
	
	$wgOut->setTitle($this-> getTitle());
	$wgOut->addWikiText('Please make Sure you have chosen some Learning outcomes. Try to regenerate the assessment');
	$wgOut->addHTML("<a href=\"index.php/Special:GenerateAnAssessment\">Try Again</a>");
	}
	//fclose($fh);
	
}// end of Generate_An_Assessment



protected function Generate_Zip_xml($LOwithPages,$AssessemtItems,$Cat){
global $IP,$wgUser;	

//$myFile = "E:/Documents and Settings/Toshiba/Desktop/NOHA.txt";
//$fh = fopen($myFile, 'w') or die("can't open file");

$date = getdate();

//File Name
$fname='Xml_Questions_';
$curdate='_Date_'.$date['mday'].$date['mon'].$date['year'];
$TimeStamp=$date['mday'].$date['mon'].$date['year'].'_'.$date['hours'].$date['minutes'].$date['seconds'];
$fname.=$TimeStamp;
$fname.='_'.$Cat;

$zip = new ZipArchive();
$zipname="$fname.zip";
$zip->open($zipname, ZipArchive::CREATE);

$test='';
$files=array();

  		for($i=0; $i < sizeof($AssessemtItems);$i++)
			
			{   $Question=$AssessemtItems[$i];
			    $files[$i]="qti_xml_".time().rand().".xml";
			    $handle = fopen($files[$i], "w");
				//strip the Category from the xml
				$Colonpos=strrpos($Question,':');
				$SecArgpos=strrpos($Question,']');
				$catxt="[[Category:".trim(substr($Question,$Colonpos+1,$SecArgpos-$Colonpos-2))."]]";
				$trans = array("$catxt" => " ");
				$outputxml=trim(strtr($Question,$trans));
				$test.='	'.$outputxml;
				fwrite($handle,$outputxml);
				fclose($handle);									
			} 
			
			foreach ($files as $file) 
			{$zip->addFile($file,$file);}
			
			$zip->close();		
			header('Content-Type: application/zip');
			header("Content-disposition: attachment; filename=$zipname");
			header('Content-Length: ' .filesize($zipname));
		    echo readfile($zipname);
							
			$this->LOG_An_Assessment($test,$Catlist,$TimeStamp,'xmlQuestions');
			
			//deleting tmp files on the server files
			unlink($zipname);
			foreach ($files as $file)
			{ unlink($file);}
	
	//fclose($fh);
}


protected function LOG_An_Assessment($text,$list,$TimeStamp,$type){
	global $wgUser;

	$dbr = wfGetDB( DB_MASTER );
	$dbr->insert('Assessments',array('Test_Date'=>$TimeStamp,'user'=>$wgUser,'Test_Type'=>$type,'List_of_LO'=>$list,'Test_txt'=>$text));
	$dbr->commit('Assessments');
}

}// end of CLass
