<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide the main entry point in accessing a CVS repository
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2004 Brian A Cheeseman
 **/

require_once 'config.php';

global $config;

$REPOS = "";
$ScriptName = $_SERVER['PHP_SELF'];
$ScriptPath = substr($ScriptName, 0, strrpos($ScriptName, "/"));

if ($ScriptPath == "") {
    $ScriptPath = "/";
}

if (isset($_GET["tm"])) {
    $ThemeName = $_GET["tm"];
} else {
	$ThemeName = "Default";
}

require_once "Themes/$ThemeName/theme.php";
require_once 'phpcvs.php';
require_once 'phpcvsmime.php';
require_once 'utils.php';
require_once 'func_DirListing.php';
require_once 'func_FileHistory.php';
require_once 'func_FileAnnotation.php';
require_once 'func_FileView.php';


// Check for a module path
if (isset($_GET["mp"])) {
    $config['mod_path'] = $_GET["mp"];
} else {
	$config['mod_path'] = "/";
}

$config['mod_path'] = str_replace("//", "/", $config['mod_path']);

if (isset($_GET["fh"])) {
    DisplayFileHistory();
} else {
	if (isset($_GET["fa"])) {
	    DisplayFileAnnotation($config['mod_path'], $_GET["fa"]);
	} else {
		if (isset($_GET["fv"])) {
		    DisplayFileContents($config['mod_path'], $_GET["dt"]);
		} else {
			DisplayDirListing();
		}
	}
}

?>
