<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide the HTML page footer code
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2004 Brian A Cheeseman
 **/

function GetPageFooter() {
	global $StartTime;
	$EndTime = microtime();
	$PageFoot = "This page was created in ".number_format(microtime_diff($StartTime, $EndTime), 3)." seconds.";
	$PageFoot .= "</BODY></HTML>";
	return $PageFoot;
} // End of function GetPageFooter()

?>