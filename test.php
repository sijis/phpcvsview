<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2002 ARECOM International
 **/

$REPOS = "";
$CVSROOT = "/cvsroot/d/de/denet";
$PServer = "cvs.sourceforge.net";
$UserName = "anonymous";
$Password = "";
 
include("phpcvs.php");

function DisplayDirListing () {
	global $REPOS, $CVSROOT, $PServer, $UserName, $Password;

	$CVSServer = new phpcvs($CVSROOT, $PServer, $UserName, $Password);
	
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
		    echo "  <TR BGCOLOR=\"$BGColor\" COLSPAN=\"5\">\n    <TD><A HREF=\"/test.php?CVSROOT=";
			$UpDirPath = substr($REPOS, 0, strlen($REPOS)-1);
			echo strrev(strchr(strrev($UpDirPath), "/"));
			echo "\"><IMG SRC=\"/icons/back.gif\" border=\"0\">&nbsp;Parent&nbsp;Directory</A>&nbsp;</TD>\n";
			$BGColor="#CCCCFF";
		} // End of if (strcmp($REPOS, "/") != 0)
		while(list($key, $val) = each($Elements)){
			if ($val == "DIR") {
				echo "  <TR BGCOLOR=\"$BGColor\">\n";
				echo "    <TD><A HREF=\"/test.php?CVSROOT=$REPOS".substr($key, 0, strlen($key))."\">";
				echo "<IMG SRC=\"/icons/dir.gif\" WIDTH=\"20\" HEIGHT=\"22\" border=\"0\">&nbsp;".$key."</A></TD>\n";
				echo "    <TD>&nbsp;</TD>\n    <TD>&nbsp;</TD>\n    <TD>&nbsp;</TD>\n    <TD>&nbsp;</TD>\n";
				echo "  </TR>\n";
				if (strcmp($BGColor, "#FFFFFF") == 0) {
				    $BGColor = "#CCCCFF";
				} else {
					$BGColor = "#FFFFFF";
				} // End of if (strcmp($BGColor, "#FFFFFF") == 0)
			} // End of if ($val == "DIR")
		} // End of while(list($key, $val) = each($Elements))
		reset($Elements);
		while(list($key, $val) = each($Elements)){
			if ($val != "DIR") {
				$FileOut .= "  <TR BGCOLOR=\"$BGColor\">\n";
				$FileOut .= "    <TD><A HREF=\"/test.php?CVSROOT=$REPOS&ShowHist=".$val["FILENAME"]."\">";
				$FileOut .= "<IMG SRC=\"/icons/text.gif\" WIDTH=\"20\" HEIGHT=\"22\" border=\"0\">&nbsp;".$val["FILENAME"]."</A>&nbsp;</TD>\n";
				$FileOut .= "    <TD>&nbsp;<A HREF=\"/test.php?CVSROOT=$REPOS&ShowFile=".$val["FILENAME"]."&Rev=".$val["HEAD"]."\">".$val["HEAD"]."</A>&nbsp;</TD>\n";
				$FileOut .= "    <TD>&nbsp;".str_replace(" ", "&nbsp;", strftime("%d %b %Y %H:%M:%S", $val["DATE"]))."&nbsp;</TD>\n";
				$FileOut .= "    <TD>&nbsp;".$val["AUTHOR"]."&nbsp;</TD>\n";
				$FileOut .= "    <TD>".str_replace("\n", "<BR>", substr($val["LOG"], 0, strlen($val["LOG"])-1))."</TD>\n";
				$FileOut .= "  </TR>\n";
				if (strcmp($BGColor, "#FFFFFF") == 0) {
				    $BGColor = "#CCCCFF";
				} else {
					$BGColor = "#FFFFFF";
				} // End of if (strcmp($BGColor, "#FFFFFF") == 0)
			} // End of if ($val != "DIR"
		} // End of while(list($key, $val) = each($Elements))
		echo $FileOut."  </TABLE>\n";
		echo "<HR>";
		$CVSServer->DisconnectTcp();
	} else {
		echo "Connection Failed.";
	} // End of if ($CVSServer->ConnectTcpAndLogon())
} // End of function DisplayDirListing()

function DisplayFileHistory() {
	global $REPOS, $CVSROOT, $PServer, $UserName, $Password;

	$CVSServer = new phpcvs($CVSROOT, $PServer, $UserName, $Password);
	
	if ($CVSServer->ConnectTcpAndLogon()) {
		$CVSServer->SendRoot();
		$CVSServer->SendValidResponses();
		$CVSServer->SendValidRequests();
		$Elements = $CVSServer->RLOG ($REPOS);
	} // End of if ($CVSServer->ConnectTcpAndLogon())
} // End of function DisplayFileHistory()

if (isset($_GET["CVSROOT"])) {
    $REPOS = $_GET["CVSROOT"];
} else {
	$REPOS = "/";
}

if (isset($_GET["ShowFile"])) {
    // Here we will show the contents of a file.
} else {
	if (isset($_GET["ShowHist"])) {
	    // Here we will show the Revision History of a given file.
		echo "<H1>Revision History for '".$_GET["CVSROOT"].$_GET["ShowHist"]."'</H1>";
	} else {
		// Here we will just show the current file listing.
		DisplayDirListing();
	}
}

$REPOS = str_replace("//", "/", $REPOS);



?>