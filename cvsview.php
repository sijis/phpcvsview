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
$CVSROOT = "/cvsroot/p/ph/phpcvsview/";

// The hostname (or IP Address) of the server providing the PServer services.
$PServer = "cvs.sourceforge.net";

// The username to pass to the PServer for authentication purposes.
$UserName = "anonymous";

// The password associated with the username above for authentication process.
$Password = "";

/**
 * 
 * End of phpCVSView Configuration Parameters.
 * 
 **/

$REPOS = "";
$ScriptName = $_SERVER['PHP_SELF'];
 
include("phpcvs.php");
include("phpcvsmime.php");
include("header.php");
include("footer.php");

function microtime_diff($a, $b) {
   list($a_dec, $a_sec) = explode(" ", $a);
   list($b_dec, $b_sec) = explode(" ", $b);
   return $b_sec - $a_sec + $b_dec - $a_dec;
} // End of function microtime_diff($a, $b)

function DisplayDirListing() {
	global $REPOS, $CVSROOT, $PServer, $UserName, $Password, $ScriptName;
	$CVSServer = new phpcvs($CVSROOT, $PServer, $UserName, $Password);
	echo GetPageHeader("phpCVSView CVS Repository", "phpCVSView CVS Repository");
	if ($CVSServer->ConnectTcpAndLogon()) {
		$CVSServer->SendRoot();
		$CVSServer->SendValidResponses();
		$CVSServer->SendValidRequests();
		$Elements = $CVSServer->RLOGDir($REPOS);
		$FileOut = "";
		echo "<HR>\n";
		echo "<TABLE BORDER=\"0\" CELLPADDING=\"2\" CELLSPACING=\"1\" WIDTH=\"100%\">\n";
		echo "  <TR BGCOLOR=\"#CCFFCC\">\n    <TH>File</TH>\n    <TH>Rev.</TH>\n    <TH>Age</TH>\n    <TH>Author</TH>\n    <TH>Last Log Entry</TH>\n  </TR>\n";
		$BGColor = "#FFFFFF";
		if (strcmp($REPOS, "/") != 0) {
		    echo "  <TR BGCOLOR=\"$BGColor\" COLSPAN=\"5\">\n    <TD><A HREF=\"$ScriptName?CVSROOT=";
			$UpDirPath = substr($REPOS, 0, strlen($REPOS)-1);
			echo strrev(strchr(strrev($UpDirPath), "/"));
			echo "\"><IMG SRC=\"/icons/back.gif\" border=\"0\">&nbsp;Parent&nbsp;Directory</A>&nbsp;</TD>\n";
			$BGColor="#CCCCFF";
		} // End of if (strcmp($REPOS, "/") != 0)
		while(list($key, $val) = each($Elements)){
			if ($val == "DIR") {
				echo "  <TR BGCOLOR=\"$BGColor\">\n";
				echo "    <TD><A HREF=\"$ScriptName?CVSROOT=$REPOS".substr($key, 0, strlen($key))."\">";
				echo "<IMG SRC=\"/icons/dir.gif\" WIDTH=\"20\" HEIGHT=\"22\" border=\"0\">&nbsp;".$key."</A></TD>\n";
				echo "    <TD>&nbsp;</TD>\n    <TD>&nbsp;</TD>\n    <TD>&nbsp;</TD>\n    <TD>&nbsp;</TD>\n";
				echo "  </TR>\n";
				if (strcmp($BGColor, "#FFFFFF") == 0) {
				    $BGColor = "#CCCCFF";
				} else { // Else of if (strcmp($BGColor, "#FFFFFF") == 0)
					$BGColor = "#FFFFFF";
				} // End of if (strcmp($BGColor, "#FFFFFF") == 0)
			} // End of if ($val == "DIR")
		} // End of while(list($key, $val) = each($Elements))
		reset($Elements);
		while(list($key, $val) = each($Elements)){
			if ($val != "DIR") {
				$FileOut .= "  <TR BGCOLOR=\"$BGColor\">\n";
				$FileOut .= "    <TD><A HREF=\"$ScriptName?CVSROOT=$REPOS&ShowHist=".$val["FILENAME"]."\">";
				$FileOut .= "<IMG SRC=\"/icons/text.gif\" WIDTH=\"20\" HEIGHT=\"22\" border=\"0\">&nbsp;".$val["FILENAME"]."</A>&nbsp;</TD>\n";
				$FileOut .= "    <TD>&nbsp;<A HREF=\"$ScriptName?CVSROOT=$REPOS&ShowFile=".$val["FILENAME"]."&Rev=".$val["HEAD"]."\">".$val["HEAD"]."</A>&nbsp;</TD>\n";
				$FileOut .= "    <TD>&nbsp;".str_replace(" ", "&nbsp;", strftime("%d %b %Y %H:%M:%S", $val["DATE"]))."&nbsp;</TD>\n";
				$FileOut .= "    <TD>&nbsp;".$val["AUTHOR"]."&nbsp;</TD>\n";
				$FileOut .= "    <TD>".str_replace("\n", "<BR>", substr($val["LOG"], 0, strlen($val["LOG"])-1))."</TD>\n";
				$FileOut .= "  </TR>\n";
				if (strcmp($BGColor, "#FFFFFF") == 0) {
				    $BGColor = "#CCCCFF";
				} else { // End of if (strcmp($BGColor, "#FFFFFF") == 0)
					$BGColor = "#FFFFFF";
				} // End of if (strcmp($BGColor, "#FFFFFF") == 0)
			} // End of if ($val != "DIR"
		} // End of while(list($key, $val) = each($Elements))
		echo $FileOut."  </TABLE>\n";
		echo "<HR>";
		$CVSServer->DisconnectTcp();
	} else { // Else of if ($CVSServer->ConnectTcpAndLogon())
		echo "Connection Failed.";
	} // End of if ($CVSServer->ConnectTcpAndLogon())
	echo GetPageFooter();
} // End of function DisplayDirListing()

function DisplayFileHistory($FileName) {
	global $REPOS, $CVSROOT, $PServer, $UserName, $Password, $ScriptName;
	$CVSServer = new phpcvs($CVSROOT, $PServer, $UserName, $Password);
	echo GetPageHeader("phpCVSView CVS Repository", "phpCVSView CVS Repository");
	if ($CVSServer->ConnectTcpAndLogon()) {
		$CVSServer->SendRoot();
		$CVSServer->SendValidResponses();
		$CVSServer->SendValidRequests();
		$Elements = $CVSServer->RLOGFile($FileName, $REPOS);
		echo "<H3>Revision History for '".$REPOS.$_GET["ShowHist"]."'</H3>";
		for ($i = 1; $i <= $Elements[0]["TotalRevisions"]; $i++) {
			echo "<HR>\n";
		    echo "Revision: ".$Elements[$i]["Revision"]."&nbsp;&nbsp;";
			echo "[<A HREF=\"$ScriptName?CVSROOT=$REPOS&ShowFile=".$FileName."&Rev=".$Elements[$i]["Revision"]."\">View";
			echo "</a>]&nbsp;&nbsp;";
			echo "[<A HREF=\"$ScriptName?CVSROOT=$REPOS&DownloadFile=".$FileName."&Rev=".$Elements[$i]["Revision"]."\">Download";
			echo "</a>]";
			echo "<BR>\n";
			echo "Branch: Yet to identify.<BR>\n";
			echo "Date: ".$Elements[$i]["Date"]."<BR>\n";
			echo "Time: ".$Elements[$i]["Time"]."<BR>\n";
			echo "Author: ".$Elements[$i]["Author"]."<BR>\n";
			echo "State: ".$Elements[$i]["State"]."<BR>\n";
			if (($i + 1) < $Elements[0]["TotalRevisions"]) {
				echo "Changes since ".$Elements[$i+1]["Revision"].": ";
			    echo "+".$Elements[$i]["LinesAdd"]." -".$Elements[$i]["LinesSub"]."<br>\n";
			} // End of if (($i + 1) < $Elements[0]["TotalRevisions"])
			echo "<pre>".str_replace("\n", "<BR>", $Elements[$i]["Log"])."</pre>";
		} // End of for ($i = 1; $i <= $Elements[0]["TotalRevisions"]; $i++)
		echo "<HR>\n";
		$CVSServer->DisconnectTcp();
	} else { // Else of if ($CVSServer->ConnectTcpAndLogon())
		echo "ERROR: Unable to connect to the CVS PServer.<BR>";
	} // End of if ($CVSServer->ConnectTcpAndLogon())
	echo GetPageFooter();
} // End of function DisplayFileHistory()

function DisplayFile() {
	global $REPOS, $CVSROOT, $PServer, $UserName, $Password, $FileToView, $FileRev;
	$CVSServer = new phpcvs($CVSROOT, $PServer, $UserName, $Password);
	echo GetPageHeader("phpCVSView CVS Repository", "phpCVSView CVS Repository");
	if ($CVSServer->ConnectTcpAndLogon()) {
		$CVSServer->SendRoot();
		$CVSServer->SendValidResponses();
		$CVSServer->SendValidRequests();
		echo "<H3>File Contents for Revision ".$FileRev." of '".$REPOS.$FileToView."'</H3>";
		$Elements = $CVSServer->ViewFile($FileToView, $FileRev, $REPOS);
		if (strpos($FileToView, ".php")) {
		    $OutText = highlight_string($Elements["CONTENT"], true);
			$OutText = str_replace("<code>", "<pre>", $OutText);
			$OutText = str_replace("</code>", "</pre>", $OutText);
			echo $OutText;
		} else { // Else of if (strpos($FileToView, ".php"))
			$Find = array("\r", "\n", " ", "\t");
			$Repl = array("", "<BR>", "&nbsp;", "&nbsp;&nbsp;&nbsp;&nbsp;");
			echo "<pre>".str_replace($Find, $Repl, $Elements["CONTENT"])."</pre>";
		} // End of if (strpos($FileToView, ".php"))
		$CVSServer->DisconnectTcp();
	} // End of if ($CVSServer->ConnectTcpAndLogon())
	echo GetPageFooter();
} // End of function DisplayFile()

function DownloadFile() {
	global $REPOS, $CVSROOT, $PServer, $UserName, $Password, $FileToDownload, $FileRev, $MIME_TYPE;
	$CVSServer = new phpcvs($CVSROOT, $PServer, $UserName, $Password);
	if ($CVSServer->ConnectTcpAndLogon()) {
		$CVSServer->SendRoot();
		$CVSServer->SendValidResponses();
		$CVSServer->SendValidRequests();
		$Elements = $CVSServer->ViewFile($FileToDownload, $FileRev, $REPOS);
		$PeriodPos = strrchr($FileToDownload, ".");
		$FileExt = substr($FileToDownload, $PeriodPos, strlen($FileToDownload)-$PeriodPos);
		if (isset($MIME_TYPE["$FileExt"])) {
		    $ContentType = $MIME_TYPE["$FileExt"];
		} else { // Else of if (isset($MIME_TYPE["$FileExt"]))
			$ContentType = "text/plain";
		} // End of if (isset($MIME_TYPE["$FileExt"]))
		header("content-type: ".$ContentType);
		echo $Elements["CONTENT"];
		$CVSServer->DisconnectTcp();
	} // End of if ($CVSServer->ConnectTcpAndLogon())
} // End of function DownloadFile()

if (isset($_GET["CVSROOT"])) {
    $REPOS = $_GET["CVSROOT"];
} else { // Else of if (isset($_GET["CVSROOT"]))
	$REPOS = "/";
} // End of if (isset($_GET["CVSROOT"]))
$REPOS = str_replace("//", "/", $REPOS);
if (isset($_GET["ShowFile"])) {
	$FileToView = $_GET["ShowFile"];
	$FileRev = $_GET["Rev"];
	DisplayFile();
} else { // Else of if (isset($_GET["ShowFile"]))
	if (isset($_GET["ShowHist"])) {
		DisplayFileHistory($_GET["ShowHist"]);
	} else { // Else of if (isset($_GET["ShowHist"]))
		if (isset($_GET["DownloadFile"])) {
			$FileToDownload = $_GET["DownloadFile"];
			$FileRev = $_GET["Rev"];
		    DownloadFile();
		} else { // Else of if (isset($_GET["DownloadFile"]))
			// Here we will just show the current file listing.
			DisplayDirListing();
		} // End of if (isset($_GET["DownloadFile"]))
	} // End of if (isset($_GET["ShowHist"]))
} // End of if (isset($_GET["ShowFile"]))




?>