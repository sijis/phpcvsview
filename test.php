<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2002 ARECOM International
 **/

$REPOS = "";
$CVSROOT = "/cvsroot/d/de/denet/";
//$CVSROOT = "/cvsroot/p/ph/phpcvsview/";
$PServer = "cvs.sourceforge.net";
$UserName = "anonymous";
$Password = "";
 
include("phpcvs.php");
include("phpcvsmime.php");

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

function DisplayFileHistory($FileName) {
	global $REPOS, $CVSROOT, $PServer, $UserName, $Password;

	$CVSServer = new phpcvs($CVSROOT, $PServer, $UserName, $Password);
	
	if ($CVSServer->ConnectTcpAndLogon()) {
		$CVSServer->SendRoot();
		$CVSServer->SendValidResponses();
		$CVSServer->SendValidRequests();
		$Elements = $CVSServer->RLOGFile($FileName, $REPOS);

		echo "<code>".str_replace("\n", "<BR>", $Elements["CODE"])."</code>";
		
		// List each revision with a HorizRule between them.
		for ($i = 1; $i <= $Elements[0]["TotalRevisions"]; $i++) {
			echo "<HR>\n";
		    echo "Revision: ".$Elements[$i]["Revision"]."&nbsp;&nbsp;";
			echo "[<A HREF=\"/test.php?CVSROOT=$REPOS&ShowFile=".$FileName."&Rev=".$Elements[$i]["Revision"]."\">View";
			echo "</a>]&nbsp;&nbsp;";

			echo "[<A HREF=\"/test.php?CVSROOT=$REPOS&DownloadFile=".$FileName."&Rev=".$Elements[$i]["Revision"]."\">Download";
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
			}
			echo "<pre>".str_replace("\n", "<BR>", $Elements[$i]["Log"])."</pre>";
		}
		echo "<HR>\n";
		
		
		$CVSServer->DisconnectTcp();
	} else {
		echo "ERROR: Unable to connect to the CVS PServer.<BR>";
	} // End of if ($CVSServer->ConnectTcpAndLogon())
} // End of function DisplayFileHistory()

function DisplayFile() {
	global $REPOS, $CVSROOT, $PServer, $UserName, $Password, $FileToView, $FileRev;

	$CVSServer = new phpcvs($CVSROOT, $PServer, $UserName, $Password);
	
	if ($CVSServer->ConnectTcpAndLogon()) {
		$CVSServer->SendRoot();
		$CVSServer->SendValidResponses();
		$CVSServer->SendValidRequests();
		echo "<H1>File Contents for Revision ".$FileRev." of '".$REPOS.$FileToView."'</H1>";
		$Elements = $CVSServer->ViewFile($FileToView, $FileRev, $REPOS);
		
		// Format and Display the output.
		if (strpos($FileToView, ".php")) {
		    $OutText = highlight_string($Elements["CONTENT"], true);
			$OutText = str_replace("<code>", "<pre>", $OutText);
			$OutText = str_replace("</code>", "</pre>", $OutText);
			echo $OutText;
		} else {
			$Find = array("\r", "\n", " ", "\t");
			$Repl = array("", "<BR>", "&nbsp;", "&nbsp;&nbsp;&nbsp;&nbsp;");
			echo "<pre>".str_replace($Find, $Repl, $Elements["CONTENT"])."</pre>";
		}
//		echo "<code>".ereg_replace(" ", "&nbsp;", ereg_replace("\n", "<BR>", $Elements["CONTENT"]))."</code>";

		$CVSServer->DisconnectTcp();
	}
}

function DownloadFile() {
	global $REPOS, $CVSROOT, $PServer, $UserName, $Password, $FileToDownload, $FileRev, $MIME_TYPE;

	$CVSServer = new phpcvs($CVSROOT, $PServer, $UserName, $Password);
	
	if ($CVSServer->ConnectTcpAndLogon()) {
		$CVSServer->SendRoot();
		$CVSServer->SendValidResponses();
		$CVSServer->SendValidRequests();
		$Elements = $CVSServer->ViewFile($FileToDownload, $FileRev, $REPOS);
		
		// Send the file to the client.
//		$Elements["CONTENT"];
		$PeriodPos = strrchr($FileToDownload, ".");
		$FileExt = substr($FileToDownload, $PeriodPos, strlen($FileToDownload)-$PeriodPos);
		if (isset($MIME_TYPE["$FileExt"])) {
		    $ContentType = $MIME_TYPE["$FileExt"];
		} else {
			$ContentType = "text/plain";
		}
		header("content-type: ".$ContentType);
		echo $Elements["CONTENT"];

		$CVSServer->DisconnectTcp();
	}
}

if (isset($_GET["CVSROOT"])) {
    $REPOS = $_GET["CVSROOT"];
} else {
	$REPOS = "/";
}

$REPOS = str_replace("//", "/", $REPOS);

if (isset($_GET["ShowFile"])) {
    // Here we will show the contents of a file.
	$FileToView = $_GET["ShowFile"];
	$FileRev = $_GET["Rev"];
	DisplayFile();
} else {
	if (isset($_GET["ShowHist"])) {
	    // Here we will show the Revision History of a given file.
		echo "<H1>Revision History for '".$REPOS.$_GET["ShowHist"]."'</H1>";
		DisplayFileHistory($_GET["ShowHist"]);
	} else {
		if (isset($_GET["DownloadFile"])) {
			$FileToDownload = $_GET["DownloadFile"];
			$FileRev = $_GET["Rev"];
		    DownloadFile();
		} else {
			// Here we will just show the current file listing.
			DisplayDirListing();
		}
	}
}




?>