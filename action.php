<?php
/**
 * DokuWiki Action Plugin SidebarNG
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Michael Klier <chi@chimeric.de>
 * @author     Samuel Fischer <sf@notomorrow.de>
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC.'lib/plugins/');
if(!defined('DOKU_LF')) define('DOKU_LF', "\n");

require_once(DOKU_PLUGIN.'action.php');

$sb_done = array( );

/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
class action_plugin_sidebarng extends DokuWiki_Action_Plugin {

    // register hook
    function register(&$controller) {
        $controller->register_hook('TPL_ACT_RENDER', 'BEFORE', $this, '_before');
        $controller->register_hook('TPL_CONTENT_DISPLAY', 'AFTER', $this, '_after');
    }

    function _before(&$event, $param) {
        global $sb_done;
        $pos = $this->getConf('pos');
        if( in_array( $pos, $sb_done )) { return true; }
	if( $pos == "off" ) { return true; }
        print '<div class="sidebarng '.$pos.'_sidebar">'.DOKU_LF;
        $this->p_sidebar($pos);
        print '</div>'. DOKU_LF;
        print '<div class="pageng">'.DOKU_LF;
    }

    function _after(&$event, $param) {
        $pos = $this->getConf('pos');
	if( $pos == "off" ) { return true; }
        print '</div>'.DOKU_LF;
    }

    /**
     * Displays the sidebar
     *
     * Michael Klier <chi@chimeric.de>
     */
    function p_sidebar($pos) {
        global $ACT, $ID, $sb_done;
        $sb_done[] = $pos;

        $sb_order   = explode(',', $this->getConf('order'));
        $sb_content = explode(',', $this->getConf('content'));
        $this->sb_actions = explode(',', $this->getConf('actions'));
        $this->sb_order_actions = explode(',', $this->getConf('order_actions'));

        if( !in_array( $ACT, array( 'show' ))) {
            if( in_array( 'showpage', $this->sb_actions )) {
                print '<div class="sidebarng '.$pos.'_sidebar">'.DOKU_LF;
                print '<a class="showpage" href="'.wl( $ID ).'">'.$this->getLang( 'showpage' ).'</a>';
                print '</div>'. DOKU_LF;
            }
        } else {

            // process contents by given order
            foreach($sb_order as $sb) {
                if(in_array($sb,$sb_content)) {
                    $key = array_search($sb,$sb_content);
                    unset($sb_content[$key]);
                    $this->_sidebar_dispatch($sb,$pos);
                }
            }

            // check for left content not specified by order
            if(is_array($sb_content) && !empty($sb_content) > 0) {
                foreach($sb_content as $sb) {
                    $this->_sidebar_dispatch($sb,$pos);
                }
            }
        }
    }

    /**
     * Prints given sidebar box
     *
     * @author Michael Klier <chi@chimeric.de>
     */
    function _sidebar_dispatch($sb, $pos) {
        global $lang;
        global $conf;
        global $ID;
        global $REV;
        global $INFO;

        $svID  = $ID;   // save current ID
        $svREV = $REV;  // save current REV 

        $pname = $this->getConf('pagename');
        $sb_actions = $this->sb_actions;

        switch($sb) {

            case 'main':
                $main_sb = $pname;
                if(@page_exists($main_sb)) {
                    if(auth_quickaclcheck($main_sb) >= AUTH_READ) {
                        $always = $this->getConf('main_always');
                        if($always or (!$always && !getNS($ID))) {
                            print '<div class="sbhead">'.$this->p_sidebar_title( $main_sb ).'</div>' . DOKU_LF;
                            print '<div class="main_sidebar sbbox">' . DOKU_LF;
                            print $this->p_sidebar_xhtml($main_sb,$pos) . DOKU_LF;
                            print '</div>' . DOKU_LF;
                        }
                    }
                } else {
                    $out = $this->locale_xhtml('nosidebar');
                    $link = '<a href="' . wl($pname) . '" class="wikilink2">' . $pname . '</a>' . DOKU_LF;
                    print '<div class="main_sidebar sbbox">' . DOKU_LF;
                    print str_replace('LINK', $link, $out);
                    print '</div>' . DOKU_LF;
                }
                break;

            case 'namespace':
                $user_ns  = $this->getConf('user_ns');
                $group_ns = $this->getConf('group_ns');
                if(!preg_match("/^".$user_ns.":.*?$|^".$group_ns.":.*?$/", $svID)) { // skip group/user sidebars and current ID
                    $ns_sb = $this->_getNsSb($svID);
                    if($ns_sb && auth_quickaclcheck($ns_sb) >= AUTH_READ) {
                        print '<div class="sbhead">'.$this->p_sidebar_title( $ns_sb ).'</div>' . DOKU_LF;
                        print '<div class="namespace_sidebar sbbox">' . DOKU_LF;
                        print $this->p_sidebar_xhtml($ns_sb,$pos) . DOKU_LF;
                        print '</div>' . DOKU_LF;
                    }
                }
                break;

            case 'user':
                $user_ns = $this->getConf('user_ns');
                if(isset($INFO['userinfo']['name'])) {
                    $user = $_SERVER['REMOTE_USER'];
                    $user_sb = $user_ns . ':' . $user . ':' . $pname;
                    if(@page_exists($user_sb)) {
                        $subst = array('pattern' => array('/@USER@/'), 'replace' => array($user));
                        print '<div class="sbhead">'.$this->p_sidebar_title( $user_sb ).'</div>' . DOKU_LF;
                        print '<div class="user_sidebar sbbox">' . DOKU_LF;
                        print $this->p_sidebar_xhtml($user_sb,$pos,$subst) . DOKU_LF;
                        print '</div>';
                    }
                    // check for namespace sidebars in user namespace too
                    if(preg_match('/'.$user_ns.':'.$user.':.*/', $svID)) {
                        $ns_sb = $this->_getNsSb($svID); 
                        if($ns_sb && $ns_sb != $user_sb && auth_quickaclcheck($ns_sb) >= AUTH_READ) {
                            print '<div class="sbhead">'.$this->p_sidebar_title( $ns_sb ).'</div>' . DOKU_LF;
                            print '<div class="namespace_sidebar sbbox">' . DOKU_LF;
                            print $this->p_sidebar_xhtml($ns_sb,$pos) . DOKU_LF;
                            print '</div>' . DOKU_LF;
                        }
                    }

                }
                break;

            case 'group':
                $group_ns = $this->getConf('group_ns');
                if(isset($INFO['userinfo']['grps'])) {
                    foreach($INFO['userinfo']['grps'] as $grp) {
                        $group_sb = $group_ns.':'.$grp.':'.$pname;
                        if(@page_exists($group_sb) && auth_quickaclcheck(cleanID($group_sb)) >= AUTH_READ) {
                            $subst = array('pattern' => array('/@GROUP@/'), 'replace' => array($grp));
                            print '<div class="sbhead">'.$this->p_sidebar_title( $group_sb ).'</div>' . DOKU_LF;
                            print '<div class="group_sidebar sbbox">' . DOKU_LF;
                            print $this->p_sidebar_xhtml($group_sb,$pos,$subst) . DOKU_LF;
                            print '</div>' . DOKU_LF;
                        }
                    }
                } else {
                    $group_sb = $group_ns.':all:'.$pname;
                    if(@page_exists($group_sb) && auth_quickaclcheck(cleanID($group_sb)) >= AUTH_READ) {
                        print '<div class="sbhead">'.$this->p_sidebar_title( $group_sb ).'</div>' . DOKU_LF;
                        print '<div class="group_sidebar sbbox">' . DOKU_LF;
                        print $this->p_sidebar_xhtml($group_sb,$pos,$subst) . DOKU_LF;
                        print '</div>' . DOKU_LF;
                    }
                }
                break;

        case 'toc':
            if(auth_quickaclcheck($svID) >= AUTH_READ) {
                $toc = tpl_toc(true);
                if(!empty($toc)) {
                    print ($toc);
                }
            }
            $INFO['prependTOC'] = 0;
            break;

        case 'index':
            if($this->getConf('closedwiki') && !isset($_SERVER['REMOTE_USER'])) return;
            print '<div class="sbhead">'.$this->getLang( $sb ).'</div>' . DOKU_LF;
            print '<div class="index_sidebar sbbox">' . DOKU_LF;
            print '  ' . $this->p_index_xhtml($svID,$pos) . DOKU_LF;
            print '</div>' . DOKU_LF;
            break;

        
        case 'toolbox':
            if($this->getConf('closedwiki') && !isset($_SERVER['REMOTE_USER'])) {
                if( in_array( $login, $sb_actions )) {
                    print '    <ul>' . DOKU_LF;
                    print '      <li><div class="li">';
                    tpl_actionlink('login');
                    print '      </div></li>' . DOKU_LF;
                    print '    </ul>' . DOKU_LF;
                }
            } else {
                print '<div class="sbhead">'.$this->getLang( $sb ).'</div>' . DOKU_LF;
                $actions = array( );
                // process contents by given order
                foreach( $this->sb_order_actions as $act) {
                    if(in_array($act,$sb_actions)) {
                        $key = array_search($act,$sb_actions);
                        unset($sb_actions[$key]);
                        $actions[] = $act;

                    }
                }

                // check for left content not specified by order
                if(is_array($sb_actions) && !empty($sb_actions) > 0) {
                    foreach($sb_actions as $act) {
                        $actions[] = $act;
                    }
                }

                print '<div class="toolbox_sidebar sbbox">' . DOKU_LF;
                print '    <ul>' . DOKU_LF;

                foreach($actions as $action) {
                    if(!actionOK($action)) continue;
                    // start output buffering
                    if($action == 'edit') {
                        // check if new page button plugin is available
                        if(!plugin_isdisabled('npd') && ($npd =& plugin_load('helper', 'npd'))) {
                            $npb = $npd->html_new_page_button(true);
                            if($npb) {
                                print '    <li><div class="li">';
                                print $npb;
                                print '</div></li>' . DOKU_LF;
                            }
                        }
                    }
                    ob_start();
                    print '   <li><div class="li">';
                    if(tpl_actionlink($action)) {
                        print '</div></li>' . DOKU_LF;
                        ob_end_flush();
                    } else {
                        ob_end_clean();
                    }
                }

                print '  </ul>' . DOKU_LF;
                print '</div>' . DOKU_LF;
            }

            break;

        case 'trace':
            if($this->getConf('closedwiki') && !isset($_SERVER['REMOTE_USER'])) return;
            print '<div class="sbhead">'.$this->getLang( $sb ).'</div>' . DOKU_LF;
            print '<div class="trace_sidebar sbbox">' . DOKU_LF;
            #print '  <div class="sb_label">'.$lang['breadcrumb'].'</div>' . DOKU_LF;
            print '  <div class="breadcrumbsng">' . DOKU_LF;
            ($conf['youarehere'] != 1) ? tpl_breadcrumbs() : tpl_youarehere();
            print '  </div>' . DOKU_LF;
            print '</div>' . DOKU_LF;
            break;

        case 'extra':
            if($this->getConf('closedwiki') && !isset($_SERVER['REMOTE_USER'])) return;
            print '<div class="sbhead">'.$this->getLang( $sb ).'</div>' . DOKU_LF;
            print '<div class="extra_sidebar sbbox">' . DOKU_LF;
            @include(dirname(__FILE__).'/sidebar.html');
            print '</div>' . DOKU_LF;
            break;

        default:
            if($this->getConf('closedwiki') && !isset($_SERVER['REMOTE_USER'])) return;

            if(@file_exists(DOKU_PLUGIN.'sidebarng/sidebars/'.$sb.'/sidebar.php')) {
                print '<div class="sbhead">'.$this->getLang( $sb ).'</div>' . DOKU_LF;
                print '<div class="'.$sb.'_sidebar sbbox">' . DOKU_LF;
                @require_once(DOKU_PLUGIN.'sidebarng/sidebars/'.$sb.'/sidebar.php');
                print '</div>' . DOKU_LF;
            }
            break;
        }

        // restore ID and REV
        $ID  = $svID;
        $REV = $svREV;
        $TOC = $svTOC;
    }

    /**
     * Removes the TOC of the sidebar pages and 
     * shows a edit button if the user has enough rights
 *
 * TODO sidebar caching
 * 
     *
     * @author Michael Klier <chi@chimeric.de>
     */
    function p_sidebar_xhtml($sb,$pos,$subst=array()) {
        $data = p_wiki_xhtml($sb,'',false);
        if(!empty($subst)) {
            $data = preg_replace($subst['pattern'], $subst['replace'], $data);
        }
        if(auth_quickaclcheck($sb) >= AUTH_EDIT) {
            $data .= '<div class="secedit">'.html_btn('secedit',$sb,'',array('do'=>'edit','rev'=>'','post')).'</div>';
        }
        // strip TOC
        $data = preg_replace('/<div class="toc">.*?(<\/div>\n<\/div>)/s', '', $data);
        // replace headline ids for XHTML compliance
        $data = preg_replace('/(<h.*?><a.*?name=")(.*?)(".*?id=")(.*?)(">.*?<\/a><\/h.*?>)/','\1sb_'.$pos.'_\2\3sb_'.$pos.'_\4\5', $data);
        return ($data);
    }
    function p_sidebar_title($sb) {
        if( !$title = p_get_first_heading($sb,METADATA_RENDER_USING_SIMPLE_CACHE)) {
            return $sb;
        }
    }
/**
 * Renders the Index
 *
 * copy of html_index located in /inc/html.php
 *
 * TODO update to new AJAX index possible?
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 * @author Michael Klier <chi@chimeric.de>
 */
function p_index_xhtml($ns,$pos) {
  require_once(DOKU_INC.'inc/search.php');
  global $conf;
  global $ID;
  $dir = $conf['datadir'];
  $ns  = cleanID($ns);
  #fixme use appropriate function
  if(empty($ns)){
    $ns = dirname(str_replace(':','/',$ID));
    if($ns == '.') $ns ='';
  }
  $ns  = utf8_encodeFN(str_replace(':','/',$ns));

  // extract only the headline
  preg_match('/<h1>.*?<\/h1>/', p_locale_xhtml('index'), $match);
  print preg_replace('#<h1(.*?id=")(.*?)(".*?)h1>#', '<h1\1sidebar_'.$pos.'_\2\3h1>', $match[0]);

  $data = array();
  search($data,$conf['datadir'],'search_index',array('ns' => $ns));

  print '<div id="' . $pos . '__index__tree">' . DOKU_LF;
  print html_buildlist($data,'idx','html_list_index','html_li_index');
  print '</div>' . DOKU_LF;
}

/**
 * Searches for namespace sidebars
 *
 * @author Michael Klier <chi@chimeric.de>
 */
function _getNsSb($id) {
     $pname = $this->getConf('pagename');
     $ns_sb = '';
     $path  = explode(':', $id);
     $found = false;

     while(count($path) > 0) {
         $ns_sb = implode(':', $path).':'.$pname;
         if(@page_exists($ns_sb)) return $ns_sb;
         array_pop($path);
     }
     
     // nothing found
     return false;
 }
  /**
   * Checks wether the sidebar should be hidden or not
   *
   * @author Michael Klier <chi@chimeric.de>
   */
  function tpl_sidebar_hide() {
    global $ACT;
    $act_hide = array( 'edit', 'preview', 'admin', 'conflict', 'draft', 'recover', 'media' );
    if(in_array($ACT, $act_hide)) {
        return true;
    } else {
        return false;
    }
  }
}
