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

function DisplayDirListing()
{
	global $config, $env;

	// Create our CVS connection object and set the required properties.
	$CVSServer = new CVS_PServer($config['cvsroot'], $config['pserver'], $config['username'], $config['password']);

	// Start the output process.
	echo GetPageHeader($config['html_title'], $config['html_header']);

	// Connect to the CVS server.
	if ($CVSServer->Connect() === true) {

		// Authenticate against the server.
		$Response = $CVSServer->Authenticate();
		if ($Response !== true) {
			return;
		}

		// Get a RLOG of the module path specified in $config['mod_path'].
		$CVSServer->RLog($env['mod_path']);
		
		// If we are in the Root of the CVS Repository then lets get the Module list.
		if (strlen($env['mod_path']) < 2) {
		    $Modules = $CVSServer->getModuleList();
		}
		else
		{
			$Modules = false;
		}

		// Add the quick link navigation bar.
		echo GetQuickLinkBar($env['mod_path']);

		// Start the output for the table.
		startDirList();

		// Do we need the "Back" operation.
		if (strlen($env['mod_path']) > 2) {
			addParentDirectory($env['mod_path']);
		}

		// Display the folders within the table.
		addFolders($env['mod_path'], $CVSServer->FOLDERS);
		
		// Display the Modules if we have them.
		if ($Modules !== false) {
		    addModules($env['mod_path'], $Modules);
		}

		// Display the files within the table.
		addFiles($env['mod_path'], $CVSServer->FILES);

		// Close off our HTML table.
		endDirList();

		$CVSServer->Disconnect();
	} else {
		echo "Connection Failed.";
	}
	echo GetPageFooter();
}

?>
