<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide the main entry point in accessing a CVS repository
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2005 Brian A Cheeseman
 **/

require_once 'config.php';
require_once 'utils.php';

global $config, $env;

$REPOS = "";
$env['script_name'] = $_SERVER['PHP_SELF'];
$env['script_path'] = substr($env['script_name'], 0, strrpos($env['script_name'], "/"));
$env['script_path'] = (empty($env['script_path']))? '/' : $env['script_path'];
$env['language_path'] = 'languages/';
$env['theme_path'] = 'Themes/';

// check if cookie exist, if so use cookie, otherwise use config value
// then verify that config value is a valid entry
$config['language'] = (empty($_COOKIE['config']['lang'])) ? $config['language'] : $_COOKIE['config']['lang'];
$config['language'] = (in_array($config['language'], GetLanguagesList(), false)) ? $config['language'] : 'en';

$config['theme'] = (empty($_COOKIE['config']['theme'])) ? $config['theme'] : $_COOKIE['config']['theme'];
$config['theme'] = (in_array($config['theme'], GetThemeList(), false)) ? $config['theme'] : 'Default';

if (isset($_GET["tm"])) {
	$config['theme'] = $_GET["tm"];
	// Set cookie with theme info. This cookie is set to expire 1 year from today.
	setcookie("config[theme]", $config['theme'], time()+31536000, "/");
}

if(isset($_GET["lg"])){
	$config['language'] = $_GET["lg"];
	// Set cookie with language info. This cookie is set to expire 1 year from today.
	setcookie('config[lang]', $config['language'], time()+31536000, "/");
}

require_once $env['theme_path'] . $config['theme']."/theme.php";
require_once $env['language_path'] . $config['language'] .'.php';
require_once 'phpcvs.php';
require_once 'phpcvsmime.php';
require_once 'func_DirListing.php';
require_once 'func_FileHistory.php';
require_once 'func_FileAnnotation.php';
require_once 'func_FileView.php';
require_once 'func_FileDownload.php';


// Check for a module path
$env['mod_path'] = (isset($_GET["mp"])) ? $_GET["mp"] : "/";
$env['mod_path'] = str_replace("//", "/", $env['mod_path']);

if (isset($_GET["fh"])) {
	DisplayFileHistory();
} else {
	if (isset($_GET["fa"])) {
		 DisplayFileAnnotation($env['mod_path'], $_GET["fa"]);
	} else {
		if (isset($_GET["fv"])) {
			DisplayFileContents($env['mod_path'], $_GET["dt"]);
		} else {
			if (isset($_GET["fd"])) {
				DownloadFile($env['mod_path'], $_GET["dt"]);
			}
			else
			{
				DisplayDirListing();
			}
		}
	}
}

?>
