<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide utility functions for the phpCVSViewer.
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2004 Brian A Cheeseman
 **/

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
	    $Result = "$Seconds Second";
		if ($DateDiff > 1) {
		    $Result .= "s";
		}
	}
	if ($Minutes > 0) {
	    $Result = "$Minutes Minute";
		if ($Minutes > 1) {
		    $Result .= "s";
		}
	}
	if ($Hours > 0) {
	    $Result = "$Hours Hour";
		if ($Hours > 1) {
		    $Result .= "s";
		}
		$Minutes = $Minutes % 60;
		if ($Minutes > 0) {
		    $Result .= ", $Minutes Minute";
			if ($Minutes > 1) {
			    $Result .= "s";
			}
		}
	}
	if ($Days > 0) {
	    $Result = $Days . " Day";
		if ($Days > 1) {
		    $Result .= "s";
		}
		$Hours = $Hours % 24;
		if ($Hours > 0) {
		    $Result .= ", $Hours Hour";
			if ($Hours > 1) {
			    $Result .= "s";
			}
		}
	}
	if ($Weeks > 0) {
	    $Result = $Weeks . " Week";
		if ($Days > 1) {
		    $Result .= "s";
		}
		$Days = $Days % 7;
		if ($Days > 0) {
		    $Result .= ", $Days Day";
			if ($Days > 1) {
			    $Result .= "s";
			}
		}
	}
	if ($Years > 0) {
		$Result = $Years . " Year";
		if ($Years > 1) {
		    $Result .= "s";
		}
		$Weeks = $Weeks % 52;
		if ($Weeks > 0) {
		    $Result .= ", $Weeks Week";
			if ($Weeks > 1) {
			    $Result .= "s";
			}
		}
	}
	return $Result;
}

function ImplodeToPath($Dirs, $Seperator, $Number)
{
//	echo "<br><br>In ImplodeToPath()...<br>\n";
	$RetVal = "";
	for ($Counter = 0; $Counter <= $Number; $Counter++)
	{
//		echo "Counter is at $Counter of $Number. Value is '".$Dirs[$Counter]."'.<br>\n";
		if ($Dirs[$Counter] != "") {
		    $RetVal .= $Seperator . $Dirs[$Counter];
		}
//		echo "RetVal is '".$RetVal."'.<br>\n";
	}
//	echo "Out ImplodeToPath()...<br><br>\n";
	return $RetVal;
}

?>