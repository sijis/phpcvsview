<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide the HTML page header code
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2005 Brian A Cheeseman
 **/

$FolderIcon = "Themes/".$config['theme']."/Images/folder.png";
$FileIcon = "Themes/".$config['theme']."/Images/file.png";
$ParentIcon = "Themes/".$config['theme']."/Images/parent.png";
$ModuleIcon = "Themes/".$config['theme']."/Images/module.png";
$DownloadIcon = "Themes/".$config['theme']."/Images/download.png";

function GetPageHeader($Title="", $Heading="") {
	global $StartTime, $config, $env, $lang;
	$StartTime = microtime();
	$PageHead = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
	$PageHead .= '<html><head>';
	if ($Title != "") {
		$PageHead .= '<title>'.$Title.'</title>';
	}
	$PageHead .= '<link href="Themes/'.$config['theme'].'/theme.css" rel="stylesheet" type="text/css" />';
	// Add JavaScript to postback the change in theme selection.
	$PageHead .= '<script src="./phpcvsview.js"></script>';
	$PageHead .= '</head>';
	$PageHead .= '<body>';
	if ($Heading != "") {
		$PageHead .= '<div class="title">'.$Heading.'</div>';
	}
	$PageHead .= $lang['message'];
	$PageHead .= '<form class="themechanger">';
	$PageHead .= ' '.$lang['change_cvsroot'].' <select name="reposSelect" class="reposchanger" onchange="postBackReposChange(this.form)">';
	foreach($config['cvs'] as $key => $value){
		$PageHead .= '<option value="'.$key.'"';
		if ($key == $env['CVSROOT']) {
			$PageHead .= ' selected="selected"';
		}
		$PageHead .= '>'.$value['description'].'</option>';
	}
	$PageHead .= '</select><br />';

	$PageHead .= $lang['change_theme'].' <select name="ThemeSelect" class="themechanger" onchange="postBackThemeChange(this.form)">';
	foreach (GetThemeList() as $key=>$value)
	{
		$PageHead .= '<option value="'.$value.'"';
		if ($value == $config['theme']) {
			$PageHead .= ' selected="selected"';
		}
		$PageHead .= '>'.$value.'</option>';
	}
	$PageHead .= '</select>';
	$PageHead .= ' '.$lang['change_lang'].' <select name="langSelect" class="langchanger" onchange="postBackLangChange(this.form)">';
	foreach(getLanguagesList() as $key => $value){
		$PageHead .= '<option value="'.$value.'"';
		if ($value == $config['language']) {
			$PageHead .= ' selected="selected"';
		}
		$PageHead .= '>'.$value.'</option>';
	}
	$PageHead .= '</select>';

	$PageHead .= '<input type="hidden" name="URLRequest" value="'.$env['script_name'].'"';
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
	$PageHead .= '"></form>';
	return $PageHead;
}

function GetPageFooter() {
	global $StartTime, $lang;
	$EndTime = microtime();
	$PageFoot = '<div class="footer">'.$lang['generated'].' '.number_format(microtime_diff($StartTime, $EndTime), 3).' '.$lang['seconds'].'<br />';
	$PageFoot .= $lang['created_by'];
	$PageFoot .= '<p><a href="http://validator.w3.org/check?uri=referer"><img src="http://www.w3.org/Icons/valid-xhtml11" alt="Valid XHTML 1.1!" height="31" width="88" /></a>&nbsp;&nbsp;';
	$PageFoot .= '<a href="http://jigsaw.w3.org/css-validator/check/referer"><img style="border:0;width:88px;height:31px" src="http://www.w3c.org/Icons/valid-css" alt="Valid CSS!" /></a></p>';
	$PageFoot .= '</div>';
	$PageFoot .= '</body></html>';
	return $PageFoot;
}

function GetQuickLinkBar($Prefix = "", $LinkLast = false, $LastIsFile = false, $Revision = "")
{
	global $env, $lang;

	if(empty($Prefix)){ $Prefix = $lang['navigate_to'];}
	// Add the quick link navigation bar.
	$Dirs = explode("/", $env['mod_path']);
	$QLOut = '<div class="quicknav">'.$Prefix.'<a href="'.$env['script_name'].'">'.$lang['root'].'</a>&nbsp;';
	$intCount = 1;
	$OffSet = 2;
	if ($LastIsFile) {
		$OffSet = 1;
	}

	while($intCount < count($Dirs)-$OffSet) {
		if (($intCount != count($Dirs)-$OffSet)) {
			$QLOut .= '/&nbsp;<a href="'.$env['script_name'].'?mp='.ImplodeToPath($Dirs, "/", $intCount).'/">'.$Dirs[$intCount].'</a>&nbsp;';
		} else {
			$QLOut .= '/&nbsp;'.$Dirs[$intCount].'&nbsp;';
		}
		$intCount++;
	}

	$QLOut .= '/&nbsp;';
	if ($LinkLast) {
		$QLOut .= '<a href="'.$env['script_name'].'?mp='.ImplodeToPath($Dirs, "/", $intCount);
		if ($LastIsFile) {
			$QLOut .= '&amp;fh#rd'.$Revision.'">';
		} else {
			$QLOut .= '/';
		}
	}

	$QLOut .= $Dirs[$intCount];
	if ($LinkLast) {
		$QLOut .= '</a>';
	}
	$QLOut .= '</div>';
	return $QLOut;
}

function startDirList()
{
	global $RowClass, $lang;
	echo '<hr />';
	echo '<table>';
	echo '<tr class="head"><th>&nbsp;</th><th>&nbsp;</th><th>'.$lang['file'].'</th><th>'.$lang['rev'].'</th><th>'.$lang['age'].'</th><th>'.$lang['author'].'</th><th>'.$lang['last_log'].'</th></tr>';
	$RowClass = "row1";
}

function endDirList()
{
	echo '</table>';
	echo '<hr />';
}

function addParentDirectory()
{
	global $RowClass, $ParentIcon, $env, $lang;

	$HREF = str_replace("//", "/", $env['script_name']."?mp=".substr($env['mod_path'], 0, strrpos(substr($env['mod_path'], 0, -1), "/"))."/");
	echo '<tr class="'.$RowClass.'">';
	echo '<td class="min">&nbsp;</td>';
	echo '<td class="min"><a href="'.$HREF.'"><img alt="parent" src="'.$env['script_path'].'/'.$ParentIcon.'" /></a></td>';
	echo '<td class="min"><a href="'.$HREF.'">'.$lang['up_folder'].'</a></td>';
	echo '<td>&nbsp;</td>';
	echo '<td>&nbsp;</td>';
	echo '<td>&nbsp;</td>';
	echo '<td>&nbsp;</td>';
	echo '</tr>';
	$RowClass = "row2";
}

function addFolders($Folders)
{
	global $RowClass, $DownloadIcon, $FolderIcon, $env;
	foreach ($Folders as $Folder) {
		$HREF = str_replace("//", "/", $env['script_name']."?mp=".$env['mod_path']."/".$Folder["Name"]."/");
		echo '<tr class="'.$RowClass.'">';
		if ($Folder["Name"] != "CVSROOT" && $Folder["Name"] != "Attic") {
			echo '<td class="min"><a href="'.$HREF.'&dp"><img alt="D/L" src="'.$env['script_path'].'/'.$DownloadIcon.'" /></a></td>';
		} else {
			echo '<td class="min">&nbsp;</td>';
		}
		echo '<td class="min"><a href="'.$HREF.'"><img alt="DIR" src="'.$env['script_path'].'/'.$FolderIcon.'" /></a></td>';
		echo '<td class="min"><a href="'.$HREF.'">'.$Folder["Name"].'</a></td>';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '</tr>';
		if ($RowClass == "row1") {
			$RowClass = "row2";
		} else {
			$RowClass = "row1";
		}
	}
}

function addModules($Modules)
{
	global $RowClass, $DownloadIcon, $ModuleIcon, $env;

	foreach ($Modules as $Key => $Val) {
		// Add the row data here.
		$HREF = str_replace("//", "/", $env['script_name']."?mp=".$env['mod_path'].$Val."/");
		echo '<tr class="'.$RowClass.'">';
		if ($Val != "CVSROOT" && $Val != "Attic") {
			echo '<td class="min"><a href="'.$HREF.'&dp"><img alt="D/L" src="'.$env['script_path'].'/'.$DownloadIcon.'" /></a></td>';
		} else {
			echo '<td class="min">&nbsp;</td>';
		}
		echo '<td class="min"><a href="'.$HREF.'"><img alt="MOD" src="'.$env['script_path'].'/'.$ModuleIcon.'" /></a></td>';
		echo '<td class="min"><a href="'.$HREF.'">'.$Key.'</a></td>';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '</tr>';
		if ($RowClass == "row1") {
			$RowClass = "row2";
		} else {
			$RowClass = "row1";
		}
	}
}

function addFiles($Files)
{
	global $RowClass, $FileIcon, $env, $lang;

	foreach ($Files as $File) {
		$HREF = str_replace("//", "/", $env['script_name']."?mp=".$env['mod_path'].$File["Name"]);
		$DateTime = strtotime($File["Revisions"][$File["Head"]]["date"]);
		$AGE = CalculateDateDiff($DateTime, strtotime(gmdate("M d Y H:i:s")));
		echo '<tr class="'.$RowClass.'">';
		echo '<td class="min">&nbsp;</td>';
		echo '<td align="center"><a href="'.$HREF.'&amp;fh"><img alt="FILE" src="'.$env['script_path'].'/'.$FileIcon.'" /></a></td>';
		echo '<td><a href="'.$HREF.'&amp;fh">'.$File["Name"].'</a></td>';
		echo '<td align="center"><a href="'.$HREF.'&amp;fv&amp;dt='.$DateTime.'">'.$File["Head"].'</a></td>';
		echo '<td align="center">'.str_replace(" ", "&nbsp;", $AGE).'&nbsp;'.$lang['ago'].'</td>';
		echo '<td align="center">'.$File["Revisions"][$File["Head"]]["author"].'</td>';
		echo '<td>'.str_replace(array("\n", " "), array("<br />", "&nbsp;"), $File["Revisions"][$File["Head"]]["LogMessage"]).'</td>';
		echo '</tr>';
		if ($RowClass == "row1") {
			$RowClass = "row2";
		} else {
			$RowClass = "row1";
		}
	}
}

function GetDiffForm()
{
	global $CVSServer, $env;

	$HREF = str_replace("//", "/", $env['script_name']."?mp=".$env['mod_path']);
	
	$DiffForm = '<form class="diffform">';
	$DiffForm .= 'Diff between: <select name="DiffRev1" class="diffform">';
	foreach ($CVSServer->FILES[0]["Revisions"] as $Revision)
	{
		$DiffForm .= '<option value="'.$Revision["Revision"].'">'.$Revision["Revision"].'</option>';
	}
	$DiffForm .= '</select> and <select name="DiffRev2" class="diffform">';
	foreach ($CVSServer->FILES[0]["Revisions"] as $Revision)
	{
		$DiffForm .= '<option value="'.$Revision["Revision"].'">'.$Revision["Revision"].'</option>';
	}
	$DiffForm .= '</select><input type="hidden" name="URLDiffReq" value="'.$HREF.'">';
	$DiffForm .= '<input type="button" value="Get Diff" onclick="postBackDiffRequest(this.form);">';
	$DiffForm .= '</form>';
	
	return $DiffForm;
}

?>
