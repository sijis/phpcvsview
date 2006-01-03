<?php

/*
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide the main entry point in accessing a CVS repository
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2006 Brian A Cheeseman
 */

require_once 'config.php';
require_once 'utils.php';

global $config, $env;

if (phpversion() <= "4.1.0") { 
	global $HTTP_ENV_VARS;
	$_ENVIRON = $HTTP_ENV_VARS;
} else {
	$_ENVIRON = $_ENV;
}

// set enviroment paths and defaults
$env['script_name'] = $_SERVER['PHP_SELF'];

$env['script_path'] = substr($env['script_name'], 0, strrpos($env['script_name'], "/"));
$env['script_path'] = (empty($env['script_path']))? '/' : $env['script_path'];

$env['mod_path'] = (isset($_GET["mp"])) ? $_GET["mp"] : "/";
$env['mod_path'] = str_replace("//", "/", $env['mod_path']);

$env['language_path']	= 'languages/';
$env['theme_path']		= 'Themes/';

// check if cookie exist, if so use cookie, otherwise use config value
// then verify that config value is a valid entry
$config['language'] = (empty($_COOKIE['config']['lang'])) ? SetDefaultLanguage() : $_COOKIE['config']['lang'];
$config['language'] = (in_array($config['language'], GetLanguagesList(), false)) ? $config['language'] : "en";

$config['theme'] = (empty($_COOKIE['config']['theme'])) ? $config['theme'] : $_COOKIE['config']['theme'];
$config['theme'] = (in_array($config['theme'], GetThemeList(), false)) ? $config['theme'] : 'Default';

// Determine the CVSROOT settings required for this instance.
$env['CVSROOT'] = (empty($_COOKIE['config']['CVSROOT'])) ? $config['default_cvs'] : $_COOKIE['config']['CVSROOT'];
if (isset($_GET["cr"])) {
	$env['mod_path'] = "/";
	unset($_GET["fh"]);
	unset($_GET["fa"]);
	unset($_GET["fv"]);
	unset($_GET["fd"]);
	unset($_GET["df"]);
	unset($_GET["dp"]);
	$env['CVSROOT'] = $_GET["cr"];
	// Set cookie with theme info. This cookie is set to expire 1 year from today.
	setcookie("config[CVSROOT]", $env['CVSROOT'], time()+31536000, "/");
}
$env['CVSSettings'] = $config['cvs'][$env['CVSROOT']];

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

// include required files and functions
require_once $env['theme_path'] . $config['theme']."/theme.php";
require_once $env['language_path'] . $config['language'] .'.php';
require_once 'phpcvs.php';
require_once 'phpcvsmime.php';
require_once 'func_DirListing.php';
require_once 'func_FileHistory.php';
require_once 'func_FileAnnotation.php';
require_once 'func_FileView.php';
require_once 'func_FileDownload.php';
require_once 'func_DiffFile.php';
require_once 'func_ArchiveDownload.php';

// begin display logic
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
			} else {
				if (isset($_GET["df"])) {
				    DisplayFileDiff($_GET["r1"], $_GET["r2"]);
				} else {
					if (isset($_GET["dp"])) {
					    DownloadArchive();
					} else {
						DisplayDirListing();
					}
				}
			}
		}
	}
}

?>
