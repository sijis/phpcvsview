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
 
//global $ThemeName;

$FolderIcon = "Themes/".$ThemeName."/Images/folder.png";
$FileIcon = "Themes/".$ThemeName."/Images/file.png";
$ParentIcon = "Themes/".$ThemeName."/Images/parent.png";
$ModuleIcon = "Themes/".$ThemeName."/Images/module.png";

function GetPageHeader($Title="", $Heading="") {
	global $StartTime, $ThemeName;
	$StartTime = microtime();
	$PageHead = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">";
	$PageHead .= "<html><head>";
	if ($Title != "") {
	    $PageHead .= "<title>$Title</title>";
	}
	$PageHead .= "<link href=\"Themes/".$ThemeName."/theme.css\" rel=\"stylesheet\" type=\"text/css\" />";
	// Add JavaScript to postback the change in theme selection.
	$PageHead .= "<script src=\"./phpcvsview.js\"></script>";
	$PageHead .= "</head>";
	$PageHead .= "<body>";
	if ($Heading != "") {
	    $PageHead .= "<div class=\"title\">$Heading</div>";
	}
	$PageHead .= "<p>Welcome to the CVS Repository viewing system for the phpCVSView project ";
	$PageHead .= "hosted at SourceForge.net</p><p>The goal of this project is simply to ";
	$PageHead .= "build a php application/class to provide access to a CVS based source ";
	$PageHead .= "control repository over the various connectivity mechanisms available for ";
	$PageHead .= "CVS in general. There are also some extensions to this goal planned for ";
	$PageHead .= "future releases such as a full web-based CVS client utilising the core of ";
	$PageHead .= "this project.</p><p>So please feel free to look at our code, suggest ";
	$PageHead .= "features, test the code in your own environment, submit bugs, and most of ";
	$PageHead .= "all support the commitment of the open source developers by using the many ";
	$PageHead .= "wonderful products available.</p><p>Kindest Regards,<br />Brian Cheeseman.";
	$PageHead .= "<br />phpCVSView Project Leader.</p>";
	$PageHead .= "<form class=\"themechanger\">Change Theme: <select name=\"ThemeSelect\" class=\"themechanger\" onchange=\"postBackThemeChange(this.form)\">";
	foreach (GetThemeList() as $key=>$value)
	{
		$PageHead .= "<option value=\"$value\"";
		if ($value == $ThemeName) {
		    $PageHead .= " selected";
		}
		$PageHead .= ">$value</option>";
	}
	$PageHead .= "</select>";
	$PageHead .= "<input type=\"hidden\" name=\"URLRequest\" value=\"".$env['script_name']."";
	$first = true;
	foreach ($_GET as $key=>$value)
	{
		if ($key != "tm") {
			if ($first != true) {
				$PageHead .= "&";
			}
			else
			{
				$PageHead .= "?";
			}
		    $first = false;
			$PageHead .= $key."=".$value;
		}
	}
	$PageHead .= "\"></form>";
	return $PageHead;
}

function GetPageFooter() {
	global $StartTime;
	$EndTime = microtime();
	$PageFoot = "<div class=\"footer\">This page was generated by <a href=\"http://phpcvsview.sourceforge.net/\">phpCVSView</a> in ".number_format(microtime_diff($StartTime, $EndTime), 3)." seconds.<br />";
	$PageFoot .= "phpCVSView created by <a href=\"mailto:bcheesem@users.sourceforge.net";
	$PageFoot .= "\">Brian Cheeseman</a> and <a href=\"mailto:sijis@users.sourceforge.net\">";
	$PageFoot .= "Sijis Aviles</a>.";
	$PageFoot .= "<p><a href=\"http://validator.w3.org/check?uri=referer\"><img src=\"http://www.w3.org/Icons/valid-xhtml11\" alt=\"Valid XHTML 1.1!\" height=\"31\" width=\"88\" /></a>&nbsp;&nbsp;";
	$PageFoot .= "<a href=\"http://jigsaw.w3.org/css-validator/check/referer\"><img style=\"border:0;width:88px;height:31px\" src=\"http://www.w3c.org/Icons/valid-css\" alt=\"Valid CSS!\" /></a></p>";
	$PageFoot .= "</div>";
	$PageFoot .= "</body></html>";
	return $PageFoot;
}

function GetQuickLinkBar($ModPath = "/", $Prefix = "Navigate to: ", $LinkLast = false, $LastIsFile = false, $Revision = "")
{
	global $env;

	// Add the quick link navigation bar.
	$Dirs = explode("/", $ModPath);
	$QLOut = "<div class=\"quicknav\">$Prefix<a href=\"".$env['script_name']."\">Root</a>&nbsp;";
	$intCount = 1;
	$OffSet = 2;
	if ($LastIsFile) {
	    $OffSet = 1;
	}

	while($intCount < count($Dirs)-$OffSet) {
		if (($intCount != count($Dirs)-$OffSet)) {
			$QLOut .= "/&nbsp;<a href=\"".$env['script_name']."?mp=".ImplodeToPath($Dirs, "/", $intCount)."/\">".$Dirs[$intCount]."</a>&nbsp;";
		} else {
			$QLOut .= "/&nbsp;".$Dirs[$intCount]."&nbsp;";
		}
		$intCount++;
	}

	$QLOut .= "/&nbsp;";
	if ($LinkLast) {
	    $QLOut .= "<a href=\"".$env['script_name']."?mp=".ImplodeToPath($Dirs, "/", $intCount);
		if ($LastIsFile) {
		    $QLOut .= "&amp;fh#rd$Revision\">";
		} else {
			$QLOut .= "/";
		}
	}

	$QLOut .= $Dirs[$intCount];
	if ($LinkLast) {
	    $QLOut .= "</a>";
	}
	$QLOut .= "</div>";
	return $QLOut;
}

function startDirList()
{
	global $RowClass;
	echo "<hr />";
	echo "<table>";
	echo "<tr class=\"head\"><th>&nbsp;</th><th>File</th><th>Rev.</th><th>Age</th><th>Author</th><th>Last Log Entry</th></tr>";
	$RowClass = "row1";
}

function endDirList()
{
	echo "</table>";
	echo "<hr />";
}

function addParentDirectory($ModPath)
{
	global $RowClass, $ParentIcon, $env;
	$HREF = str_replace("//", "/", $env['script_name']."?mp=".substr($ModPath, 0, strrpos(substr($ModPath, 0, -1), "/"))."/");
	echo "<tr class=\"$RowClass\">";
	echo "<td class=\"min\"><a href=\"$HREF\"><img alt=\"parent\" src=\"".$env['script_path']."/$ParentIcon\" /></a></td>";
	echo "<td class=\"min\"><a href=\"$HREF\">Up&nbsp;one&nbsp;folder</a></td>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "</tr>";
	$RowClass = "row2";
}

function addFolders($ModPath, $Folders)
{
	global $RowClass, $FolderIcon, $env;
	foreach ($Folders as $Folder) {
		$HREF = str_replace("//", "/", $env['script_name']."?mp=$ModPath/".$Folder["Name"]."/");
		echo "<tr class=\"$RowClass\">";
		echo "<td class=\"min\"><a href=\"$HREF\"><img alt=\"DIR\" src=\"".$env['script_path']."/$FolderIcon\" /></a></td>";
		echo "<td class=\"min\"><a href=\"$HREF\">".$Folder["Name"]."</a></td>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";
		if ($RowClass == "row1") {
		    $RowClass = "row2";
		} else {
			$RowClass = "row1";
		}
	}
}

function addModules($ModPath, $Modules)
{
	global $RowClass, $ModuleIcon, $env;
	foreach ($Modules as $Key => $Val) {
		// Add the row data here.
		$HREF = str_replace("//", "/", $env['script_name']."?mp=$ModPath/".$Val."/");
		echo "<tr class=\"$RowClass\">";
		echo "<td class=\"min\"><a href=\"$HREF\"><img alt=\"MOD\" src=\"".$env['script_path']."/$ModuleIcon\" /></a></td>";
		echo "<td class=\"min\"><a href=\"$HREF\">".$Key."</a></td>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";
		if ($RowClass == "row1") {
		    $RowClass = "row2";
		} else {
			$RowClass = "row1";
		}
	}
}

function addFiles($ModPath, $Files)
{
	global $RowClass, $FileIcon, $env;
	foreach ($Files as $File) {
		$HREF = str_replace("//", "/", $env['script_name']."?mp=$ModPath/".$File["Name"]);
		$DateTime = strtotime($File["Revisions"][$File["Head"]]["date"]);
		$AGE = CalculateDateDiff($DateTime, strtotime(gmdate("M d Y H:i:s")));
		echo "<tr class=\"$RowClass\">";
		echo "<td align=\"center\"><a href=\"$HREF&amp;fh\"><img alt=\"FILE\" src=\"".$env['script_path']."/$FileIcon\" /></a></td>";
		echo "<td><a href=\"$HREF&amp;fh\">".$File["Name"]."</a></td>";
		echo "<td align=\"center\"><a href=\"$HREF&amp;fv&amp;dt=$DateTime\">".$File["Head"]."</a></td>";
		echo "<td align=\"center\">".str_replace(" ", "&nbsp;", $AGE)."&nbsp;ago</td>";
		echo "<td align=\"center\">".$File["Revisions"][$File["Head"]]["author"]."</td>";
		echo "<td>".str_replace(array("\n", " "), array("<br />", "&nbsp;"), $File["Revisions"][$File["Head"]]["LogMessage"])."</td>";
		echo "</tr>";
		if ($RowClass == "row1") {
		    $RowClass = "row2";
		} else {
			$RowClass = "row1";
		}
	}
}

?>
