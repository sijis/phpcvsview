<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide File View Page.
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2004 Brian A Cheeseman
 **/

function DisplayFileContents($File, $Revision = "") {
	global $ModPath, $CVSROOT, $PServer, $UserName, $Password, $ScriptName, 
	       $HTMLTitle, $HTMLHeading, $HTMLTblHdBg, $HTMLTblCell1, $HTMLTblCell2;

	// Calculate the path from the $ScriptName variable.
	$ScriptPath = substr($ScriptName, 0, strrpos($ScriptName, "/"));
	if ($ScriptPath == "") {
	    $ScriptPath = "/";
	}
		  
	// Create our CVS connection object and set the required properties.
	$CVSServer = new CVS_PServer($CVSROOT, $PServer, $UserName, $Password);
	
	// Start the output process.
	echo GetPageHeader($HTMLTitle, $HTMLHeading);
	
	// Connect to the CVS server.
	if ($CVSServer->Connect() === true) {
	
		// Authenticate against the server.
		$Response = $CVSServer->Authenticate();
		if ($Response !== true) {
			return;
		}
		
		// Get a RLOG of the module path specified in $ModPath.
		$CVSServer->RLog($ModPath);
		
		// "Export" the file.
		$Response = $CVSServer->ExportFile($File, $Revision);
		if ($Response !== true) {
		    return;
		}
		
		// Add the quick link navigation bar.
		echo GetQuickLinkBar($ModPath, "Code view for: ", true, true, $Revision);
		
		// Display the file contents.
		echo "<hr />\n";
		$search = array('<', '>', '\n', '\t'); 
		$replace = array("&lt;", "&gt;", "", "    "); 
		echo "<pre>\n";
		echo str_replace($search, $replace, $CVSServer->FILECONTENTS)."\n";
		echo "</pre>\n";
		
		// Close the connection.
		$CVSServer->Disconnect();
	}
	else
	{
		echo "ERROR: Could not connect to the PServer.<br>\n";
	}
	echo GetPageFooter();
}


?>