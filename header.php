<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide the HTML page header code
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2004 Brian A Cheeseman
 **/

function GetPageHeader($Title="", $Heading="") {
	global $StartTime;
	$StartTime = microtime();
	$PageHead = "<HTML>";
	if ($Title != "") {
	    $PageHead .= "<HEAD><TITLE>$Title</TITLE></HEAD>";
	} // End of if ($Title != "")
	$PageHead .= "<BODY>";
	if ($Heading != "") {
	    $PageHead .= "<DIV ALIGN=\"center\"><H1>$Heading</H1></DIV>";
	} // End of if ($Header != "")
	$PageHead .= "Welcome to our CVS Repository viewer. This page has been dynamically";
	$PageHead .= " created with '<a href=\"http://phpcvsview.sourceforge.net/\">phpCVS";
	$PageHead .= "Viewer</a>' created by <a href=\"mailto:bcheesem@us";
	$PageHead .= "ers.sourceforge.net\">Brian Cheeseman</a>. <BR><BR>Please feel free";
	$PageHead .= " to browse our source code.<BR><BR>";
	return $PageHead;
} // End of function GetPageHeader($Title="", $Heading="")

?>