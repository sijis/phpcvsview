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

		$Files = $CVSServer->FILES;
		
		echo "<h1>History for ".$ModPath."</h1>\n";
		foreach ($CVSServer->FILES[0]["Revisions"] as $Revision)
		{
			$HREF = str_replace("//", "/", "$ScriptName?mp=$ModPath");
			$DateTime = strtotime($Revision["date"]);
			echo "<hr>\n";
			echo "<b>Revision</b> ".$Revision["Revision"]." -";
			echo " (<a href=\"$HREF&fv&dt=$DateTime\">view</a>)";
			echo " (<a href=\"$HREF&fd&dt=$DateTime\">download</a>)";
			echo " (<a href=\"$HREF&df&r1=".strtotime($Revision["date"])."&r2=";
			echo strtotime($CVSServer->FILES[0]["Revisions"][$Revision["PrevRevision"]]["date"])."\">diff to previous</a>)";
			echo " (<a href=\"$HREF&fa=".$Revision["Revision"]."\">annotate</a>)<br>\n";
			echo "<b>Last Checkin:</b> ".strftime("%A %d %b %Y %T -0000", strtotime($Revision["date"]))." (".CalculateDateDiff(strtotime($Revision["date"]), time())." ago)<br>\n";
			echo "<b>Branch:</b> ".$Revision["Branches"]."<br>\n";
			echo "<b>Date:</b> ".strftime("%B %d, %Y", $DateTime)."<br>\n";
			echo "<b>Time:</b> ".strftime("%H:%M:%S", $DateTime)."<br>\n";
			echo "<b>Author:</b> ".$Revision["author"]."<br>\n";
			echo "<b>State:</b> ".$Revision["state"]."<br>\n";
			if ($Revision["PrevRevision"] != "") {
			    echo "<b>Changes since ".$Revision["PrevRevision"].":</b> ".$Revision["lines"]."<br>";
			}
			echo "<b>Log Message:</b><pre>".$Revision["LogMessage"]."</pre>\n";
		}
		
		echo "<hr>\n";
		
		$CVSServer->Disconnect();
	} else { // Else of if ($CVSServer->Connect() === true)
		echo "Connection Failed.";
	} // End of if ($CVSServer->Connect() === true)
	echo GetPageFooter();
}



?>