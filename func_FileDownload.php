<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide File Download capability.
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id
 * @copyright 2003-2005 Brian A Cheeseman
 **/
 
function DownloadFile($File, $Revision = "")
{
	global $config, $env, $lang, $MIME_TYPES;

	// Calculate the path from the $env['script_name'] variable.
	$env['script_path'] = substr($env['script_name'], 0, strrpos($env['script_name'], '/'));
	$env['script_path'] = (empty($env['script_path']))? '/' : $env['script_path'];

	// Create our CVS connection object and set the required properties.
	$CVSServer = new CVS_PServer($config['cvsroot'], $config['pserver'], $config['username'], $config['password']);

	// Connect to the CVS server.
	if ($CVSServer->Connect() === true) {

		// Authenticate against the server.
		$Response = $CVSServer->Authenticate();
		if ($Response !== true) {
			return;
		}

		// Get a RLOG of the module path specified in $env['mod_path'].
		$CVSServer->RLog($env['mod_path']);

		// "Export" the file.
		$Response = $CVSServer->ExportFile($File, $Revision);
		if ($Response !== true) {
			return;
		}
		
		// Get the mime type for the file.
		$FileExt = substr($File, strrpos($File, '.')+1);
		$MimeType = $MIME_TYPES[$FileExt];
		if ($MimeType == '') {
			$MimeType = 'text/plain';
		}
		
		// Send the appropriate http header.
		header('Content-Type: '.$MimeType);
		
		// Send the file contents.
		echo $CVSServer->FILECONTENTS;
		echo '<br />'.$lang['file_ext'].' '.$FileExt;
		echo '<br />'.$lang['mime_type'].' '. $MimeType;

		// Close the connection.
		$CVSServer->Disconnect();
	}
}

?>
