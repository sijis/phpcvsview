<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide the main entry point in accessing a CVS repository
 *
 * @author Brian A Cheeseman <brian@bcheese.homeip.net>
 * @version $Id$
 * @copyright 2003 Brian A Cheeseman
 **/

/**
 * 
 * phpCVSView Configuration Parameters.
 * 
 **/
 
// The CVSROOT path to access. For sourceforge you need the usual expansion 
// of the path based on the project name.
$CVSROOT = "/cvsroot/phpcvsview";
//$CVSROOT = "/cvsroot/CHASE";

// The hostname (or IP Address) of the server providing the PServer services.
$PServer = "cvs.sourceforge.net";
//$PServer = "192.168.0.1";

// The username to pass to the PServer for authentication purposes.
$UserName = "anonymous";

// The password associated with the username above for authentication process.
$Password = "";

// The HTMLTitle and HTMLHeading are used purely for the generation of the 
// resultant web pages.
$HTMLTitle = "phpCVSView Source Code Library";
$HTMLHeading = "phpCVSView Source Code Library";

$HTMLTblHdBg  = "#CCCCCC";
$HTMLTblCell1 = "#FFFFFF";
$HTMLTblCell2 = "#CCCCEE";

/**
 * 
 * End of phpCVSView Configuration Parameters.
 * 
 **/

$REPOS = "";
$ScriptName = $_SERVER['PHP_SELF'];
 
require_once 'phpcvs.php';
require_once 'phpcvsmime.php';
require_once 'header.php';
require_once 'footer.php';

function microtime_diff($a, $b) {
   list($a_dec, $a_sec) = explode(" ", $a);
   list($b_dec, $b_sec) = explode(" ", $b);
   return $b_sec - $a_sec + $b_dec - $a_dec;
} // End of function microtime_diff($a, $b)

function CalculateDateDiff($DateEarlier, $DateLater)
{
	$DateDiff = $DateLater - $DateEarlier;
	$Seconds = $DateDiff;
	$Minutes = floor($Seconds/60);
	$Hours = floor($Minutes/60);
	$Days = floor($Hours/24);
	$Weeks = floor($Days/7);
	$Years = floor($Days/365);

	if ($Seconds > 0) {
	    $Result = $Seconds . " Second";
		if ($DateDiff > 1) {
		    $Result .= "s";
		}
	}
	if ($Minutes > 0) {
	    $Result = $Minutes . " Minute";
		if ($Minutes > 1) {
		    $Result .= "s";
		}
	}
	if ($Hours > 0) {
	    $Result = $Hours . " Hour";
		if ($Hours > 1) {
		    $Result .= "s";
		}
	}
	if ($Days > 0) {
	    $Result = $Days . " Day";
		if ($Days > 1) {
		    $Result .= "s";
		}
	}
	if ($Weeks > 0) {
	    $Result = $Weeks . " Week";
		if ($Days > 1) {
		    $Result .= "s";
		}
	}
	if ($Years > 0) {
		$Result = $Years . " Year";
		if ($Years > 1) {
		    $Result .= "s";
		}
	}
	return $Result;
}

function DisplayDirListing() {
	global $ModPath, $CVSROOT, $PServer, $UserName, $Password, $ScriptName, 
	       $HTMLTitle, $HTMLHeading, $HTMLTblHdBg, $HTMLTblCell1, $HTMLTblCell2;

	// Calculate the path from the $ScriptName variable.
	$ScriptPath = substr($ScriptName, 0, strrpos($ScriptName, "/"));
	if ($ScriptPath == "") {
	    $ScriptPath = "/";
	}
		  
	// Create our CVS connection object and set the required properties.
	$CVSServer = new CVS_PServer();
	$CVSServer->set_PServer($PServer);
	$CVSServer->set_Repository($CVSROOT);
	$CVSServer->set_UserName($UserName);
	$CVSServer->set_Password($Password);
	
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
			$AGE = CalculateDateDiff(strtotime($File["Revisions"][$File["Head"]]["date"]), time());
			echo "  <tr bgcolor=\"$BGColor\" valign=\"top\">\n";
			echo "    <td align=\"center\" valign=\"center\"><a href=\"$HREF&fh\"><img border=\"0\" src=\"$ScriptPath/images/file.png\"></a></td>\n";
			echo "    <td><a href=\"$HREF&fh\">".$File["Name"]."</a></td>\n";
			echo "    <td align=\"center\"><a href=\"$HREF&fv=1&fr=".$File["Head"]."\">".$File["Head"]."</td>\n";
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
		
//		$Output = print_r($CVSServer->FILES, true);
//		$Output2 = str_replace("\n", "<br>", $Output);
//		echo "<hr><h1>Files Present</h1><pre>$Output2</pre><br><hr>";
		
		$CVSServer->Disconnect();
		
		// Close off our HTML table.
		echo "  </table>\n";
		echo "<hr>";

	} else { // Else of if ($CVSServer->ConnectTcpAndLogon())
		echo "Connection Failed.";
	} // End of if ($CVSServer->ConnectTcpAndLogon())
	echo GetPageFooter();
} // End of function DisplayDirListing()

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
	$CVSServer = new CVS_PServer();
	$CVSServer->set_PServer($PServer);
	$CVSServer->set_Repository($CVSROOT);
	$CVSServer->set_UserName($UserName);
	$CVSServer->set_Password($Password);
	
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
			echo "<hr>\n";
			echo "Revision <b>".$Revision["Revision"]."</b> - (view) (download)<br>\n";
			echo "Last Checkin: ".strftime("%A %d %b %Y %T %Z", strtotime($Revision["date"]))." (".CalculateDateDiff(strtotime($Revision["date"]), time())." ago) by ".$Revision["author"]."<br>\n";
		}
		
		echo "<hr>\n";
		
		$CVSServer->Disconnect();
	} else { // Else of if ($CVSServer->ConnectTcpAndLogon())
		echo "Connection Failed.";
	} // End of if ($CVSServer->ConnectTcpAndLogon())
	echo GetPageFooter();
}

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
DisplayDirListing();
}
?>
