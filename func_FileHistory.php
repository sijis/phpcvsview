<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide File Listing Page.
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2004 Brian A Cheeseman
 **/

function DisplayFileHistory()
{
	global $config;

	// Calculate the path from the $ScriptName variable.
	$ScriptPath = substr($ScriptName, 0, strrpos($ScriptName, "/"));
	if ($ScriptPath == "") {
	    $ScriptPath = "/";
	}

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
		$CVSServer->RLog($config['mod_path']);

		$Files = $CVSServer->FILES;

		// Add the quick link navigation bar.
		echo GetQuickLinkBar($config['mod_path'], "Revision History for: ", false, true, "");

		foreach ($CVSServer->FILES[0]["Revisions"] as $Revision) {
			$HREF = str_replace("//", "/", "$ScriptName?mp=".$config['mod_path']);
			$DateTime = strtotime($Revision["date"]);
			echo "<hr /><p><a id=\"rd$DateTime\" />\n";
			echo "<b>Revision</b> ".$Revision["Revision"]." -";
			echo " (<a href=\"$HREF&amp;fv&amp;dt=$DateTime\">view</a>)";
			echo " (<a href=\"$HREF&amp;fd&amp;dt=$DateTime\">download</a>)";
			echo " (<a href=\"$HREF&amp;df&amp;r1=".strtotime($Revision["date"])."&amp;r2=";
			echo strtotime($CVSServer->FILES[0]["Revisions"][$Revision["PrevRevision"]]["date"])."\">diff to previous</a>)";
			echo " (<a href=\"$HREF&amp;fa=".$Revision["Revision"]."\">annotate</a>)<br />\n";
			echo "<b>Last Checkin:</b> ".strftime("%A %d %b %Y %T -0000", strtotime($Revision["date"]))." (".CalculateDateDiff(strtotime($Revision["date"]), strtotime(gmdate("M d Y H:i:s")))." ago)<br />\n";
			echo "<b>Branch:</b> ".$Revision["Branches"]."<br />\n";
			echo "<b>Date:</b> ".strftime("%B %d, %Y", $DateTime)."<br />\n";
			echo "<b>Time:</b> ".strftime("%H:%M:%S", $DateTime)."<br />\n";
			echo "<b>Author:</b> ".$Revision["author"]."<br />\n";
			echo "<b>State:</b> ".$Revision["state"]."<br />\n";
			if ($Revision["PrevRevision"] != "") {
			    echo "<b>Changes since ".$Revision["PrevRevision"].":</b> ".$Revision["lines"]."<br />";
			}
			echo "<b>Log Message:</b></p><pre>".$Revision["LogMessage"]."</pre>\n";
		}

		echo "<hr />\n";

		$CVSServer->Disconnect();
	} else {
		echo "Connection Failed.";
	}
	echo GetPageFooter();
}

?>
