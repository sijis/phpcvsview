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
	$PageHead = "<html>";
	if ($Title != "") {
	    $PageHead .= "<head><title>$Title</title>";
		$PageHead .= "<link rel=StyleSheet href=\"Themes/Default/theme.css\" type=\"text/css\">";
		$PageHead .= "</head>";
	} // End of if ($Title != "")
	$PageHead .= "<body>";
	if ($Heading != "") {
	    $PageHead .= "<div class=\"title\">$Heading</div>";
	} // End of if ($Header != "")
	$PageHead .= "Welcome to our CVS Repository viewer. This page has been dynamically";
	$PageHead .= " created with '<a href=\"http://phpcvsview.sourceforge.net/\">phpCVS";
	$PageHead .= "Viewer</a>' created by <a href=\"mailto:bcheesem@users.sourceforge.net";
	$PageHead .= "\">Brian Cheeseman</a>. <br><br>Please feel free to browse our source code.<br><br>";
	return $PageHead;
} // End of function GetPageHeader($Title="", $Heading="")

function GetPageFooter() {
	global $StartTime;
	$EndTime = microtime();
	$PageFoot = "<div class=\"footer\">This page was created by <a href=\"http://phpcvsview.sourceforge.net/\">phpCVSView</a> in ".number_format(microtime_diff($StartTime, $EndTime), 3)." seconds.</div>";
	$PageFoot .= "</body></html>";
	return $PageFoot;
} // End of function GetPageFooter()

?>