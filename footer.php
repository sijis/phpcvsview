<?php

/**
 * Purpose: To provide the HTML page footer code
 *
 * @author Brian A Cheeseman <brian@bcheese.homeip.net>
 * @version $Id$
 * @copyright 2003 Brian A Cheeseman
 **/

function GetPageFooter() {
	// Here we will generate the HTML page footer.
	global $StartTime;
	$EndTime = microtime();
	$PageFoot = "This page was created in ".number_format(microtime_diff($StartTime, $EndTime), 3)." seconds.";
	$PageFoot .= "</BODY></HTML>";
	
	return $PageFoot;
}

?>