<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide Archive Downloads.
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2005 Brian A Cheeseman
 **/
 
require_once("Archive/Tar.php");
 
function DAProcessDirectory($ReposLoc, $BasePath)
{
	global $env;
	
	// Create our CVS connection object and set the required properties.
	$CVSServer = new CVS_PServer($env['CVSSettings']['cvsroot'], $env['CVSSettings']['server'], $env['CVSSettings']['username'], $env['CVSSettings']['password']);

	// Connect to the CVS server.
	if ($CVSServer->Connect() === true) {

		// Authenticate against the server.
		$Response = $CVSServer->Authenticate();
		if ($Response !== true) {
			return;
		}
		
		// Create the folder for the BasePath.
		@mkdir($BasePath, 0700);
	
		// Get a RLOG of the module path specified in $ReposLoc.
		$CVSServer->RLog($ReposLoc);
		$Folders = $CVSServer->FOLDERS;
		$Files = $CVSServer->FILES;
		
		foreach ($Folders as $folder)
		{
			if ($folder["Name"] != "Attic") {
			    DAProcessDirectory($ReposLoc.$folder["Name"]."/", $BasePath."/".$folder["Name"]);
			}
		}
		
		foreach ($Files as $file)
		{
			$CVSServer->ExportFile($ReposLoc.$file["Name"], time());
			$filehandle = fopen($BasePath."/".$file["Name"], "wb");
			fwrite($filehandle, $CVSServer->FILECONTENTS);
			fclose($filehandle);
		}
		$CVSServer->Disconnect();

		// When we leave this function the contents should be in the File System.
	}
}

function DownloadArchive()
{
	global $config, $env, $lang;

	// Get a unique string to create a directory for storing this request.
	$jobpath = $config['TempFileLocation']."/".md5(uniqid(rand(), true));
	mkdir($jobpath, 0700);
    $buildpath = $jobpath."/".$config['CVSROOT'];
	mkdir($buildpath, 0700);	
	$ReposFolders = explode("/", $env['mod_path']);
	if (count($ReposFolders) > 0 && $ReposFolders[count($ReposFolders) - 2] != "") {
		$buildpath .= "/".$ReposFolders[count($ReposFolders) - 2];
	}
		
	// Export the source tree.
	DAProcessDirectory($env['mod_path'], $buildpath);
	
	// Create the tar file.
	$FileName = $jobpath."/".$config['CVSROOT'].".tar.gz";
	$tar = new Archive_Tar($FileName, "gz");
	$cwd = getcwd();
	chdir($jobpath);
	$tar->create($config['CVSROOT']);
	chdir($cwd);
	
	header('Content-Type: application/x-tar');
	header("Content-Disposition: attachment; filename=\"".$config['CVSROOT'].".tar.gz\"");
	header("Content-Length: " . filesize($FileName));
	$tarfile = fopen($FileName, "rb");

	// Dump the contents of the file to the client.
	fpassthru($tarfile);

}

?>