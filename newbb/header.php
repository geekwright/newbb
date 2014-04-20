<?php
/**
 * CBB 4.0, or newbb, the forum module for XOOPS project
 *
 * @copyright	The XOOPS Project http://xoops.sf.net
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Taiwen Jiang (phppp or D.J.) <phppp@users.sourceforge.net>
 * @since		4.00
 * @version		$Id$
 * @package		module::newbb
 */
include_once '../../mainfile.php';
defined('XOOPS_ROOT_PATH') or die('Restricted access');
$xoopsLogger->startTime('newBB_Header');
// irmtfan assign newbb dirname then replace all. include xoops header.php (now commented and removed)
$dirname = $xoopsModule->getVar("dirname");
//include_once XOOPS_ROOT_PATH . "/header.php";

if (!empty($xoopsModuleConfig['do_rewrite'])) {
	include_once "seo_url.php"; 
	/* for seo */
	$toseo_url = array("index.php","viewforum.php","viewtopic.php","rss.php");

	if(((strpos(getenv('REQUEST_URI'), '.html') === false) && !empty($xoopsModuleConfig['do_rewrite'])  && (!isset($_POST) || count($_POST) <= 0))) 
	{
		$redir = false;
		if ( strpos(getenv("REQUEST_URI"),"mark_read=") == true  || strpos(getenv("REQUEST_URI"),"mark=") == true) {
			// Mark Forums
		} else {
			if ( in_array(basename(getenv('SCRIPT_NAME')),$toseo_url) ) {
				//rewrite only for files

				if ( trim(getenv("SCRIPT_NAME")) != ''  ) {
					if ( strpos(getenv("REQUEST_URI") , "/".SEO_MODULE_NAME."/") === false ) {
						$redir = true;
					} else if ( getenv("QUERY_STRING") ) {
						$redir = true;
					}
				}			
			}
		}

		if ($redir == true) {	
			$s = "http://" . getenv('HTTP_HOST') . getenv("REQUEST_URI");
			$s = str_replace("/".REAL_MODULE_NAME."/","/".SEO_MODULE_NAME."/",$s);
			$newurl = seo_urls('<a href="'.$s.'"></a>');
			$newurl = str_replace('<a href="','',$newurl);
			$newurl = str_replace('"></a>','',$newurl); 
			if ( !headers_sent() ) {
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: $newurl");	
				exit();
			}
		} 
	}		
}

include_once XOOPS_ROOT_PATH."/modules/".$dirname."/include/vars.php";

mod_loadFunctions("user", $dirname);
mod_loadFunctions("topic", $dirname);

require_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
require_once XOOPS_ROOT_PATH."/class/module.textsanitizer.php";
$myts = MyTextSanitizer::getInstance();

$menumode = 0;
$menumode_other = array();
$menu_url = htmlSpecialChars(preg_replace("/&menumode=[^&]/", "", $_SERVER[ 'REQUEST_URI' ]));
$menu_url .= ( false === strpos($menu_url, "?") ) ? "?menumode=" : "&amp;menumode=";
foreach ($xoopsModuleConfig["valid_menumodes"] as $key => $val) {
	if ($key != $menumode) $menumode_other[] = array("title"=>$val, "link"=>$menu_url.$key);
}
// irmtfan new method for add js scripts - commented and move to footer.php
//global $xoopsTpl;
//$xoopsTpl->assign("xoops_module_header",'
	//<script type="text/javascript">var toggle_cookie="'.$forumCookie['prefix'].'G'.'";</script>
	//'. @$xoopsTpl->get_template_vars("xoops_module_header"));

/* START irmtfan remove and move to newbb/footer.php */
/*
$newbb_module_header = '';
$newbb_module_header .= '<link rel="alternate" type="application/rss+xml" title="'.$xoopsModule->getVar("name").'" href="'.XOOPS_URL.'/modules/'.$dirname.'/rss.php" />';
if (!empty($xoopsModuleConfig['pngforie_enabled'])) {
	$newbb_module_header .= '<style type="text/css">img {behavior:url("include/pngbehavior.htc");}</style>';
}
// START hacked by irmtfan to add localization/customization for newbb style.css
mod_loadFunctions("render", $dirname);
$icon_handler = newbb_getIconHandler();
//  get from setted language
$rel_path=$icon_handler->getPath("language/" . $xoopsConfig['language'], $dirname ,"language/english");
if (!file_exists(XOOPS_ROOT_PATH . $rel_path . '/style.css')) {
    // for backward compatibility - as before
    $rel_path="/modules/" . $dirname . "/templates";
}
$newbb_module_header .= '
	<link rel="stylesheet" type="text/css" href="'.XOOPS_URL . $rel_path . '/style.css" />
	<script type="text/javascript">var toggle_cookie="'.$forumCookie['prefix'].'G'.'";</script>
	<script src="'.XOOPS_URL.'/modules/'.$dirname.'/include/js/newbb_toggle.js" type="text/javascript"></script>
	';
// END hacked by irmtfan to add localization/customization for newbb style.css
if ($menumode == 2) {
	$newbb_module_header .= '
	<link rel="stylesheet" type="text/css" href="'.XOOPS_URL.'/modules/'.$dirname.'templates/newbb_menu_hover.css" />
	<style type="text/css">body {behavior:url("include/newbb.htc");}</style>
	';
}

if ($menumode == 1) {
	$newbb_module_header .= '
	<link rel="stylesheet" type="text/css" href="'.XOOPS_URL.'/modules/'.$dirname.'templates/newbb_menu_click.css" />
	<script src="include/js/newbb_menu_click.js" type="text/javascript"></script>
	';
}

$xoops_module_header = $newbb_module_header; // for cache hack
*/
/* END irmtfan remove and move to newbb/footer.php */

if (!empty($xoopsModuleConfig["welcome_forum"]) && is_object($xoopsUser) && !$xoopsUser->getVar('posts')) {
	mod_loadFunctions("welcome", $dirname);
}
// irmtfan for backward compatibility 
$pollmodules = $xoopsModuleConfig["poll_module"];

//$module_handler = &xoops_gethandler('module');
$xoopspoll = &$module_handler->getByDirname($pollmodules);
/*
if (is_object($xoopspoll) && $xoopspoll->getVar('isactive')) {
        $pollmodules = 'xoopspoll';
} else {
    //Umfrage
    $xoopspoll = &$module_handler->getByDirname('umfrage');
    if (is_object($xoopspoll) && $xoopspoll->getVar('isactive')) 
        $pollmodules = 'umfrage';
}
*/
$xoopsLogger->stopTime('newBB_Header');
?>