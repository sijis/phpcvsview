<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide utility functions for the phpCVSViewer.
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2005 Brian A Cheeseman
 **/

function microtime_diff($a, $b)
{
	list($a_dec, $a_sec) = explode(" ", $a);
	list($b_dec, $b_sec) = explode(" ", $b);
	return $b_sec - $a_sec + $b_dec - $a_dec;
}

function CalculateDateDiff($DateEarlier, $DateLater)
{
	global $lang;

	$date['date_diff'] = $DateLater - $DateEarlier;
	$date['seconds'] = $date['date_diff'];
	$date['minutes'] = floor($date['seconds']/60);
	$date['hours'] = floor($date['minutes']/60);
	$date['days'] = floor($date['hours']/24);
	$date['weeks'] = floor($date['days']/7);
	$date['years'] = floor($date['days']/365);

	// displays seconds
	if ($date['seconds'] > 0) {
	    $Result = $date['seconds'] .' ';
		$Result .= ($date['date_diff'] > 1)? $lang['seconds'] : $lang['second'];
	}

	// displays minutes
	if ($date['minutes'] > 0) {
	    $Result = $date['minutes'].' ';
		$Result .= ($date['minutes'] > 1)? $lang['minutes'] : $lang['minute'];
	}

	// displays hours, then minutes
	if ($date['hours'] > 0) {
	    $Result = $date['hours'].' ';
		$Result .= ($date['hours'] > 1)? $lang['hours'] : $lang['hour'];
		$date['minutes'] = $date['minutes'] % 60;
		if ($date['minutes'] > 0) {
		    $Result .= ', '.$date['minutes'].' ';
			$Result .= ($date['minutes'] > 1)? $lang['minutes'] : $lang['minute'];
		}
	}

	// displays days, then hours
	if ($date['days'] > 0) {
	    $Result = $date['days'] . ' ';
		$Result .= ($date['days'] > 1)? $lang['days'] : $lang['day'];
		$date['hours'] = $date['hours'] % 24;
		if ($date['hours'] > 0) {
		    $Result .= ', '.$date['hours'].' ';
			$Result .= ($date['hours'] > 1)? $lang['hours'] : $lang['hour'];
		}
	}

	// displays weeks, then days
	if ($date['weeks'] > 0) {
	    $Result = $date['weeks'] . ' ';
		$Result .= ($date['weeks'] > 1)? $lang['weeks'] : $lang['week'];
		$date['days'] = $date['days'] % 7;
		if ($date['days'] > 0) {
		    $Result .= ', '.$date['days'].' ';
			$Result .= ($date['days'] > 1)? $lang['days'] : $lang['day'];
		}
	}

	// displays years, then weeks
	if ($date['years'] > 0) {
		$Result = $date['years'] . ' ';
		$Result .= ($date['years'] > 1)? $lang['years'] : $lang['year'];
		$date['weeks'] = $date['weeks'] % 52;
		if ($date['weeks'] > 0) {
		    $Result .= ', '.$date['weeks'].' ';
			$Result .= ($date['weeks'] > 1)? $lang['weeks'] : $lang['week'];
		}
	}
	return $Result;
}

function ImplodeToPath($Dirs, $Seperator, $Number)
{
	$RetVal = "";
	for ($Counter = 0; $Counter <= $Number; $Counter++)
	{
		if ($Dirs[$Counter] != "") {
			$RetVal .= $Seperator . $Dirs[$Counter];
		}
	}
	return $RetVal;
}

function GetThemeList()
{
	global $env;
	$theme = array();

	// open theme directory
	if ($handle = opendir($env['theme_path'])) {

		// list all directories
		while (false !== ($file = readdir($handle))) {
			// do not list . and ..
			if ($file != "." && $file != ".." && $file != "CVS") {
				// add directory to an array
				array_push($theme, $file);
			}
		}
		closedir($handle);
	}
	sort($theme, SORT_STRING);
	return $theme;
}

function GetLanguagesList()
{
	global $env;
	$lang = array();

	// open language directory
	if ($handle = opendir($env['language_path'])) {

		// list all directory files
		while (false !== ($file = readdir($handle))) {
			// do not list . and ..
			if ($file != "." && $file != ".." && $file != "CVS") {
				// strip filename and add to an array
				array_push($lang, rtrim($file, '.php'));
			}
		}
		closedir($handle);
	}
	return $lang;
}

function SetDefaultLanguage()
{
	global $env, $config, $_ENVIRON;
	// en_AU:en_GB:en
	$PreferredLangs = explode(":", $_ENVIRON['LANGUAGE']);
	foreach ($PreferredLangs as $Lang) {
		$FileToCheck = $env['language_path']."$Lang.php";
		if (file_exists($FileToCheck)) {
		    return $Lang;
		}
	}
	return $config['language'];
}

function InsertIntoArray(&$Array, $Value, $Position)
{
	if (!is_array($Array)) return false;
	$Last = array_splice($Array, $Position);
	$Array[] = $Value;
	$Array = array_merge($Array, $Last);
}

?>
