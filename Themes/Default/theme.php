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

$FolderIcon = "Themes/Default/Images/folder.gif";
$FileIcon = "Themes/Default/Images/file.gif";
$ParentIcon = "Themes/Default/Images/parent.gif";

function GetPageHeader($Title="", $Heading="") {
	global $StartTime;
	$StartTime = microtime();
	$PageHead = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">";
	$PageHead .= "<html>";
	if ($Title != "") {
	    $PageHead .= "<head><title>$Title</title>";
		$PageHead .= "<link href=\"Themes/Default/theme.css\" rel=\"stylesheet\" type=\"text/css\" />";
		$PageHead .= "</head>";
	}

	$PageHead .= "<body>";
	if ($Heading != "") {
	    $PageHead .= "<div class=\"title\">$Heading</div>";
	}

	$PageHead .= "<p>Welcome to our CVS Repository viewer. This page has been dynamically";
	$PageHead .= " created with '<a href=\"http://phpcvsview.sourceforge.net/\">phpCVS";
	$PageHead .= "Viewer</a>' created by <a href=\"mailto:bcheesem@users.sourceforge.net";
	$PageHead .= "\">Brian Cheeseman</a>.</p><p>Please feel free to browse our source code.</p>";
	return $PageHead;
}

function GetPageFooter() {
	global $StartTime;
	$EndTime = microtime();
	$PageFoot = "<div class=\"footer\">This page was created by <a href=\"http://phpcvsview.sourceforge.net/\">phpCVSView</a> in ".number_format(microtime_diff($StartTime, $EndTime), 3)." seconds.";
	$PageFoot .= "<p><a href=\"http://validator.w3.org/check?uri=referer\"><img src=\"http://www.w3.org/Icons/valid-xhtml11\" alt=\"Valid XHTML 1.1!\" height=\"31\" width=\"88\" /></a>&nbsp;&nbsp;";
	$PageFoot .= "<a href=\"http://jigsaw.w3.org/css-validator/check/referer\"><img style=\"border:0;width:88px;height:31px\" src=\"http://jigsaw.w3.org/css-validator/images/vcss\" alt=\"Valid CSS!\" /></a></p>";
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
	$QLOut .= "</div>\n";
	return $QLOut;
}

function startDirList()
{
	global $RowClass;
	echo "<hr />\n";
	echo "<table>\n";
	echo "  <tr class=\"head\">\n    <th>&nbsp;</th>\n    <th>File</th>\n    <th>Rev.</th>\n    <th>Age</th>\n    <th>Author</th>\n    <th>Last Log Entry</th>\n  </tr>\n";
	$RowClass = "row1";
}

function endDirList()
{
	echo "  </table>\n";
	echo "<hr />";
}

function addParentDirectory($ModPath)
{
	global $RowClass, $ParentIcon, $env;
	$HREF = str_replace("//", "/", $env['script_name']."?mp=".substr($ModPath, 0, strrpos(substr($ModPath, 0, -1), "/"))."/");
	echo "  <tr class=\"$RowClass\">\n";
	echo "    <td align=\"center\"><a href=\"$HREF\"><img alt=\"parent\" src=\"".$env['script_path']."/$ParentIcon\" /></a></td>\n";
	echo "    <td><a href=\"$HREF\">Up one folder</a></td>\n";
	echo "    <td>&nbsp;</td>\n";
	echo "    <td>&nbsp;</td>\n";
	echo "    <td>&nbsp;</td>\n";
	echo "    <td>&nbsp;</td>\n";
	echo "  </tr>\n";
	$RowClass = "row2";
}

function addFolders($ModPath, $Folders)
{
	global $RowClass, $FolderIcon, $env;
	foreach ($Folders as $Folder) {
		$HREF = str_replace("//", "/", $env['script_name']."?mp=$ModPath/".$Folder["Name"]."/");
		echo "  <tr class=\"$RowClass\">\n";
		echo "    <td align=\"center\"><a href=\"$HREF\"><img alt=\"DIR\" src=\"".$env['script_path']."/$FolderIcon\" /></a></td>\n";
		echo "    <td><a href=\"$HREF\">".$Folder["Name"]."</a></td>\n";
		echo "    <td>&nbsp;</td>\n";
		echo "    <td>&nbsp;</td>\n";
		echo "    <td>&nbsp;</td>\n";
		echo "    <td>&nbsp;</td>\n";
		echo "  </tr>\n";
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
		echo "  <tr class=\"$RowClass\" valign=\"top\">\n";
		echo "    <td align=\"center\"><a href=\"$HREF&amp;fh\"><img alt=\"FILE\" src=\"".$env['script_path']."/$FileIcon\" /></a></td>\n";
		echo "    <td><a href=\"$HREF&amp;fh\">".$File["Name"]."</a></td>\n";
		echo "    <td align=\"center\"><a href=\"$HREF&amp;fv&amp;dt=$DateTime\">".$File["Head"]."</a></td>\n";
		echo "    <td align=\"center\">".$AGE." ago</td>\n";
		echo "    <td align=\"center\">".$File["Revisions"][$File["Head"]]["author"]."</td>\n";
		echo "    <td>".str_replace("\n", "<br />", $File["Revisions"][$File["Head"]]["LogMessage"])."</td>\n";
		echo "  </tr>\n";
		if ($RowClass == "row1") {
		    $RowClass = "row2";
		} else {
			$RowClass = "row1";
		}
	}
}

?>
