<?php

/**
 * Purpose: To provide the HTML page header code
 *
 * @author Brian A Cheeseman <brian@bcheese.homeip.net>
 * @version $Id$
 * @copyright 2003 Brian A Cheeseman
 **/

function GetPageHeader($Title="", $Heading="") {
	global $StartTime;
	$StartTime = microtime();
	// Here we will generate the HTML page header.
	$PageHead = "<HTML>";
	if (isset($Title)) {
	    $PageHead .= "<HEAD><TITLE>$Title</TITLE></HEAD>";
	}
	$PageHead .= "<BODY>";
	if (isset($Heading)) {
	    $PageHead .= "<DIV ALIGN=\"center\"><H1>$Heading</H1></DIV>";
	}
	$PageHead .= "Welcome to our CVS Repository viewer. This page has been dynamically";
	$PageHead .= " created with '<a href=\"http://phpcvsview.sourceforge.net/\">phpCVS";
	$PageHead .= "Viewer</a>' created by <a href=\"mailto:bcheesem@us";
	$PageHead .= "ers.sourceforge.net\">Brian Cheeseman</a>. <BR><BR>Please feel free";
	$PageHead .= " to browse our source code.<BR><BR>";
	
	return $PageHead;
}

?>