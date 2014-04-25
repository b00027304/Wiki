<?php
/**
 * Implements Special:GenerateAnAssessment
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
 * implements Special:CreatBulkCategories - create an assessement based on chosen learning outcomes
 * @ingroup SpecialPage
 */

class SpecialCreatBulkCategories extends SpecialPage {
     
	public $wgMaxCategories = 10;
	
		public function __construct() {
		parent::__construct( 'CreatBulkCategories' );
	}

	/**
	 * Entry point
	 */
	public function execute($par) {
		global $wgRequest, $wgUser, $wgOut;

		//$myFile = "E:/Documents and Settings/Toshiba/Desktop/Kim.txt";
		//$fh = fopen($myFile, 'w') or die("can't open file");
		
		
		# Check permissions
		global $wgGroupPermissions;
		$permissionRequired = $this->isAllowed( $wgUser );
		
		if( $permissionRequired !== true ) {
			if( !$wgUser->isLoggedIn() && $wgGroupPermissions['sysop']['bulkcreate'] ) {
				// Custom message if logged-in users without any special rights can upload
				$wgOut->showErrorPage( 'createbulknologin', 'createbulknologintext' );
			} else {
				$wgOut->permissionRequired( $permissionRequired );
			}
			return;
		}
			
		
		
		//fclose($fh);
		
		$this->setHeaders();
		$this->outputHeader();
		$wgOut->allowClickjacking();
		$wgOut->addModuleStyles( 'mediawiki.special' );
		
		//$wgOut->addModules('MoveLO');
		$action="/wiki/index.php?action=CreatBulkCategories";
		
		$txt="This section allows administrators to create category pages in bulk. The main purpose is to help the administrator
		to build the category tree which consits of the learning outcomes as stated in Common Core Curriculum standards.";
		
		$wgOut-> addWikiText($txt);
		
		$wgOut->addHTML("<br/>");
		
		$wgOut->addHTML( <<<HTML
<form id="CreateBulkCategoriesform" name="CreateBulkCategoriesform" method="post" action="$action" enctype="multipart/form-data">
HTML
);
		$wgOut->addHTML("<fieldset>");
		$wgOut->addHTML("<legend><h5>Creating Category Pages</h5></legend>");
		
		$wgOut->addHTML("<table> \n");
		$wgOut->addHTML("<tr>\n");
		$wgOut->addHTML("<th style=\"width=50% height=50%\">Learning Outcome Page title (Child)</th>");
		$wgOut->addHTML("<th style=\"width=50% height=50%\">Learning outcome description</th>");
		$wgOut->addHTML("<th style=\"width=50% height=50%\">Category (Parent)</th>");
		$wgOut->addHTML("</tr>\n");
		
		for($i=0; $i<$this->wgMaxCategories;$i++)
			
		{
		
		$wgOut->addHTML(<<<HTML
		<tr>\n		
		<td style="width=100% height=30%">\n
		<input type="text" name="PgTitle_{$i}" size="30" id="PgTitle_{$i}"/>
		</td>\n
				
		<td style="width=100% height=30%">\n
		<textarea name="Pgdsc_{$i}" id="Pgdsc_{$i}" rows="3" cols="30"></textarea> 
		</td>\n
		
		<td style="width=100% height=30%">\n
		<input type="text" name="CatTitle_{$i}" size="30" id="CatTitle_{$i}"/>
		</td>\n
		</tr>\n
						
HTML
);	
		}
		$wgOut->addHTML("</table>\n");
		$wgOut->addHTML("</br>");
		
		$wgOut->addHTML( <<<HTML
				
	   <input type="submit" value="Create Categories" id="mw_CreateCategories"/> </form>
	   </fieldset>		
	   
HTML
);				

}// end of execute

/**
* returns HTML code for a list Box for all the categories
* */

public function Create_Cat_Pages($Request)

{ global $wgUser,$wgRequest,$wgOut;
	
//$myFile = "E:/Documents and Settings/Toshiba/Desktop/Khloe.txt";
//$fh = fopen($myFile, 'w') or die("can't open file");


	if ($Request->wasposted()){
	
	
		
		$ok=array();
		$pages=array();
		
	for ( $i = 0; $i < $this->wgMaxCategories; $i++ ) {
	
		     
			$pageTitle=$Request->getval("PgTitle_{$i}");
			
		    $pageBody=$Request->getval("Pgdsc_{$i}",'');
		    $pageCategory=$Request->getVal("CatTitle_{$i}",'');
		    
		      
		    if($pageCategory!='')
		       $pageBody.="[[Category:".$pageCategory."]]";
		    
		    $t = Title::newFromText( "Category:".$pageTitle );
		    
		    
		    if( !is_null( $t ) ) 
		    
		    {   
			    $article = new Article($t);
			    
			    $res = $article->doEdit($pageBody, '',0,false,$wgUser);
			    
			    if ($res->isOK())
			    { $ok[$i]=true; $pages[$i]=$pageTitle;}
			    else 
		    	{ $ok[$i]=false; $pages[$i]=$pageTitle;}
			    
		    } // if it is a suitable title
	
	}// end of foor loop
	
	
	$msg="Status Summary of the created pages:";
	$wgOut->addWikiText($msg);
	
	$wgOut->addHTML("<table>\n");
	
	$wgOut->addHTML("<tr>\n");
	$wgOut->addHTML("<th> Number</th>");
	$wgOut->addHTML("<th> Page Title</th>");
	$wgOut->addHTML("<th> Status </th>");
	$wgOut->addHTML("</tr>\n");
	
	for ( $i = 0; $i < sizeof($ok); $i++ ) 
	{    $status=$ok[$i]? "Sucessful\n":"Failed\n";
	     $j=$i+1;
		$wgOut->addHTML(<<<HTML
		<tr>\n
				
				<td>\n
				{$j}
				</td>\n
				
				<td>\n
				{$pages[$i]}
				</td>\n

				<td>\n
				{$status}
				</td>\n
		</tr>\n
HTML
);
		//$msg.="Page: ".$pages[$i];
		
		//$msg.=" status: ".$status; 	
	}
	
  $wgOut->addHTML("</table>\n");
  
  //fclose($fh);
	
	
	
}// if page was posted 
}// end of function 


public static function isAllowed( $user ) {
	foreach ( array( 'bulkcreate', 'edit' ) as $permission ) {
		if ( !$user->isAllowed( $permission ) ) {
			return $permission;
		}
	}
	return true;
}

}// end of CLass
