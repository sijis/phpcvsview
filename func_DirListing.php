<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide Directory Listing Page.
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2004 Brian A Cheeseman
 **/

function DisplayDirListing() {
	global $ModPath, $CVSROOT, $PServer, $UserName, $Password, $ScriptName, 
	       $HTMLTitle, $HTMLHeading, $FolderIcon, $FileIcon, $RowClass;

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
		
		// Add the quick link navigation bar.
		echo GetQuickLinkBar($ModPath);
		
		// Start the output for the table.
		startDirList();
		
		// Do we need the "Back" operation.
		if (strlen($ModPath) > 1) {
			addParentDirectory($ScriptName, $ScriptPath, $ModPath);
		}

		// Display the folders within the table.
		addFolders($ModPath, $CVSServer->FOLDERS);

		// Display the files within the table.
		addFiles($ModPath, $CVSServer->FILES);
		
		// Close off our HTML table.
		endDirList();

		$CVSServer->Disconnect();
	} else { // Else of if ($Response !== true)
		echo "Connection Failed.";
	} // End of if ($Response !== true)
	echo GetPageFooter();
} // End of function DisplayDirListing()



?>