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

/**
 * 
 * phpCVSView Configuration Parameters.
 * 
 **/
require_once 'config.php';

global $CVSROOT, $PServer, $UserName, $Password, $HTMLTitle, $HTMLHeading, $HTMLTblHdBg, $HTMLTblCell1, $HTMLTblCell2;

/**
 *  * End of phpCVSView Configuration Parameters.
 * 
 **/

$REPOS = "";
$ScriptName = $_SERVER['PHP_SELF'];
 
require_once 'phpcvs.php';
require_once 'phpcvsmime.php';
require_once 'header.php';
require_once 'footer.php';
require_once 'utils.php';
require_once 'func_DirListing.php';
require_once 'func_FileHistory.php';
require_once 'func_FileAnnotation.php';
require_once 'func_FileView.php';

		
// Check for a module path
if (isset($_GET["mp"])) {
    $ModPath = $_GET["mp"];
} else { // Else of if (isset($_GET["CVSROOT"]))
	$ModPath = "/";
} // End of if (isset($_GET["CVSROOT"]))
$ModPath = str_replace("//", "/", $ModPath);

if (isset($_GET["fh"])) {
    DisplayFileHistory();
}
else
{
	if (isset($_GET["fa"])) {
	    DisplayFileAnnotation($ModPath, $_GET["fa"]);
	}
	else
	{
		if (isset($_GET["fv"])) {
		    DisplayFileContents($ModPath, $_GET["dt"]);
		}
		else
		{
			DisplayDirListing();
		}
	}
}

?>
