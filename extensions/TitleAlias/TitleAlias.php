<?php
/*
 * Copyright 2008,2009 Surf Chen <http://www.surfchen.org>
 *
 *
 * This source code is under the terms of the
 * GNU General Public License. 
 * see <http://www.gnu.org/licenses/gpl.txt>
 */

$wgExtensionMessagesFiles['TitleAlias'] = dirname(__FILE__) . '/TitleAlias.i18n.php';
$wgHooks['ArticleViewHeader'][] = 'TitleAlias::changeTitle';
$wgHooks['EditPage::showEditForm:fields'][] = 'TitleAlias::addField';
$wgHooks['EditPage::attemptSave'][] = 'TitleAlias::saveAlias';
$wgHooks['LoadExtensionSchemaUpdates'][] = 'TitleAlias::buildTable';
class TitleAlias {
    public function __construct($title) {
        wfLoadExtensionMessages('TitleAlias');
        $this->mTitle=$title;
        $this->db=wfGetDB( DB_SLAVE );
    }
    public function TryToUseAlias() {
    }
    public function getAlias() {
        $res=$this->db->select( 'titlealias', array('alias'), array( 'title' => $this->mTitle ),'Database::select',array('LIMIT'=>1));
        if ($row=$this->db->fetchRow($res)) {
            return $row['alias'];
        }
        return false;
    }
    function save($alias) {
        $this->db->replace(
                    'titlealias',
                    'title',
                     array(
                         'title' => $this->mTitle,
                         'alias'    => $alias),
                     'Database::replace'
                      );
    }
    static function changeTitle(&$out, &$text) {
        $ta=new TitleAlias($out->mTitle->mDbkeyform);
        //I hate one-line-code
        if ($alias=$ta->getAlias()) {
            $out->mTitle->mPrefixedText=$alias;
        }
        return true;
    }
    static function addField(&$editpage,&$out) {
        $ta=new TitleAlias($editpage->mTitle->mDbkeyform);
        $out->addHtml(wfMsg('alias').': <input type="text" name="alias" size="50" value="'.$ta->getAlias().'" />');
        return true;
    }
    static function saveAlias(&$editpage) {
        global $wgRequest;
        if (isset($wgRequest->data['alias'])) {
            $ta=new TitleAlias($editpage->mTitle->mDbkeyform);
            $ta->save($wgRequest->data['alias']);
        }
        return true;
    }
    static function buildTable() {
        global $wgDBtype;
        $db = wfGetDB( DB_MASTER );
        if ($db->tableExists('titlealias')) {
            echo "...titlealias table exists.\n";
        } else {
            if ($wgDBtype=='postgres') {
                echo "...sorry,TitleAlias does not support postgres currently...";
            } else {
                echo "...creating titlealias table...\n";
                $err = $db->sourceFile(dirname(__FILE__).'/ta.sql');
                if( $err !== true ) {
                    throw new MWException( $err );
                }
            }
        }
        return true;
    }
}
