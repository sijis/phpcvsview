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
	$PageHead = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">";
	$PageHead .= "<html>";
	if ($Title != "") {
	    $PageHead .= "<head><title>$Title</title>";
		$PageHead .= "<link href=\"Themes/Default/theme.css\" rel=\"stylesheet\" type=\"text/css\" />";
		$PageHead .= "</head>";
	} // End of if ($Title != "")
	$PageHead .= "<body>";
	if ($Heading != "") {
	    $PageHead .= "<div class=\"title\">$Heading</div>";
	} // End of if ($Header != "")
	$PageHead .= "<p>Welcome to our CVS Repository viewer. This page has been dynamically";
	$PageHead .= " created with '<a href=\"http://phpcvsview.sourceforge.net/\">phpCVS";
	$PageHead .= "Viewer</a>' created by <a href=\"mailto:bcheesem@users.sourceforge.net";
	$PageHead .= "\">Brian Cheeseman</a>.</p><p>Please feel free to browse our source code.</p>";
	return $PageHead;
} // End of function GetPageHeader($Title="", $Heading="")

function GetPageFooter() {
	global $StartTime;
	$EndTime = microtime();
	$PageFoot = "<div class=\"footer\">This page was created by <a href=\"http://phpcvsview.sourceforge.net/\">phpCVSView</a> in ".number_format(microtime_diff($StartTime, $EndTime), 3)." seconds.";
	$PageFoot .= "<p><a href=\"http://validator.w3.org/check?uri=referer\"><img src=\"http://www.w3.org/Icons/valid-xhtml11\" alt=\"Valid XHTML 1.1!\" height=\"31\" width=\"88\" /></a>&nbsp;&nbsp;";
	$PageFoot .= "<a href=\"http://jigsaw.w3.org/css-validator/check/referer\"><img style=\"border:0;width:88px;height:31px\" src=\"http://jigsaw.w3.org/css-validator/images/vcss\" alt=\"Valid CSS!\" /></a></p>";
	$PageFoot .= "</div>";
	$PageFoot .= "</body></html>";
	return $PageFoot;
} // End of function GetPageFooter()

?>