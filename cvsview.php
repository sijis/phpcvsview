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

global $config, $env;

$REPOS = "";
$env['script_name'] = $_SERVER['PHP_SELF'];
$env['script_path'] = substr($env['script_name'], 0, strrpos($env['script_name'], "/"));

if ($env['script_path'] == "") {
    $env['script_path'] = "/";
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
    $env['mod_path'] = $_GET["mp"];
} else {
	$env['mod_path'] = "/";
}

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
			DisplayDirListing();
		}
	}
}

?>
