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
		
		// Add the quick link navigation bar.
		$Dirs = explode("/", $ModPath);
		echo "Navigate to: <a href=\"$ScriptName\">Root</a>&nbsp;";
		$intCount = 1;
		while($intCount < count($Dirs)-2){
			echo "/&nbsp;<a href=\"$ScriptName?mp=".ImplodeToPath($Dirs, "/", $intCount)."/\">".$Dirs[$intCount]."</a>&nbsp;";
			$intCount++;
		} // while
		echo "/&nbsp;".$Dirs[$intCount]."<br>\n";
		
		// Start the output for the table.
		echo "<hr>\n";
		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"100%\">\n";
		echo "  <tr bgcolor=\"$HTMLTblHdBg\">\n    <th width=\"30\">&nbsp;</th>\n    <th>File</th>\n    <th>Rev.</th>\n    <th>Age</th>\n    <th>Author</th>\n    <th>Last Log Entry</th>\n  </tr>\n";
		$BGColor = $HTMLTblCell1;
		
		// Do we need the "Back" operation.
		if (strlen($ModPath) > 1) {
			$HREF = str_replace("//", "/", "$ScriptName?mp=".substr($ModPath, 0, strrpos(substr($ModPath, 0, -1), "/"))."/"); 
			echo "  <tr bgcolor=\"$BGColor\">\n";
			echo "    <td align=\"center\" valign=\"center\"><a href=\"$HREF\"><img border=\"0\" src=\"$ScriptPath/images/parent.png\"></a></td>\n";
			echo "    <td><a href=\"$HREF\">Previous Level</a></td>\n";
			echo "    <td>&nbsp;</td>\n";
			echo "    <td>&nbsp;</td>\n";
			echo "    <td>&nbsp;</td>\n";
			echo "    <td>&nbsp;</td>\n";
			echo "  </tr>\n";
			$BGColor = $HTMLTblCell2;
		}

		// Process each folder and display a single row in a table.		
		foreach ($CVSServer->FOLDERS as $Folder)
		{
			$HREF = str_replace("//", "/", "$ScriptName?mp=$ModPath/".$Folder["Name"]."/"); 
			echo "  <tr bgcolor=\"$BGColor\">\n";
			echo "    <td align=\"center\" valign=\"center\"><a href=\"$HREF\"><img border=\"0\" src=\"$ScriptPath/images/folder.png\"></a></td>\n";
			echo "    <td><a href=\"$HREF\">".$Folder["Name"]."</a></td>\n";
			echo "    <td>&nbsp;</td>\n";
			echo "    <td>&nbsp;</td>\n";
			echo "    <td>&nbsp;</td>\n";
			echo "    <td>&nbsp;</td>\n";
			echo "  </tr>\n";
			if ($BGColor == $HTMLTblCell1) {
			    $BGColor = $HTMLTblCell2;
			}
			else
			{
				$BGColor = $HTMLTblCell1;
			}
		}

		foreach ($CVSServer->FILES as $File)
		{
			$HREF = str_replace("//", "/", "$ScriptName?mp=$ModPath/".$File["Name"]); 
			$DateTime = strtotime($File["Revisions"][$File["Head"]]["date"]);
			$AGE = CalculateDateDiff($DateTime, time());
			echo "  <tr bgcolor=\"$BGColor\" valign=\"top\">\n";
			echo "    <td align=\"center\" valign=\"center\"><a href=\"$HREF&fh\"><img border=\"0\" src=\"$ScriptPath/images/file.png\"></a></td>\n";
			echo "    <td><a href=\"$HREF&fh\">".$File["Name"]."</a></td>\n";
			echo "    <td align=\"center\"><a href=\"$HREF&fv&dt=$DateTime\">".$File["Head"]."</td>\n";
			echo "    <td align=\"center\">".$AGE." ago</td>\n";
			echo "    <td align=\"center\">".$File["Revisions"][$File["Head"]]["author"]."</td>\n";
			echo "    <td>".str_replace("\n", "<br>", $File["Revisions"][$File["Head"]]["LogMessage"])."</td>\n";
			echo "  </tr>\n";
			if ($BGColor == $HTMLTblCell1) {
			    $BGColor = $HTMLTblCell2;
			}
			else
			{
				$BGColor = $HTMLTblCell1;
			}
		}
		
		$CVSServer->Disconnect();
		
		// Close off our HTML table.
		echo "  </table>\n";
		echo "<hr>";

	} else { // Else of if ($Response !== true)
		echo "Connection Failed.";
	} // End of if ($Response !== true)
	echo GetPageFooter();
} // End of function DisplayDirListing()



?>