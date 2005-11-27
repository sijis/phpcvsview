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

$FolderIcon		= "Themes/".$config['theme']."/Images/folder.png";
$FileIcon		= "Themes/".$config['theme']."/Images/file.png";
$ParentIcon		= "Themes/".$config['theme']."/Images/parent.png";
$ModuleIcon		= "Themes/".$config['theme']."/Images/module.png";
$DownloadIcon	= "Themes/".$config['theme']."/Images/download.png";

function GetPageHeader($Title="phpCVSView Source Code Library", $Heading="phpCVSView Source Code Library")
{
	global $StartTime, $config, $env, $lang;

	$StartTime = microtime();
	$PageHead = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">'."\n";
	$PageHead .= '<html xmlns="http://www.w3.org/1999/xhtml">'."\n";
	$PageHead .= '<head>'."\n";
	$PageHead .= '	<meta http-equiv="content-type" content="text/html; charset='.$lang['encoding'].'" />'."\n";
	$PageHead .= '	<title>'.$Title.'</title>'."\n";
	$PageHead .= '	<link href="'.$env['theme_path'].$config['theme'].'/theme.css" rel="stylesheet" type="text/css" />'."\n";
	$PageHead .= '	<script src="./phpcvsview.js" type="text/javascript"></script>'."\n";
	$PageHead .= '</head>'."\n";
	$PageHead .= '<body>'."\n";
	$PageHead .= '<div id="header">'."\n";
	$PageHead .= '	<div class="title">'.$Heading.'</div>'."\n";
	$PageHead .= '	<div class="headmsg">'.$lang['message'].'</div>'."\n";
	$PageHead .= '</div>'."\n";
	$PageHead .= '<div id="userOptions">'."\n";
	$PageHead .= '	<form class="themechanger" action="'.$_SERVER['PHP_SELF'].'">'."\n";
	$PageHead .= '		<p class="reposchanger" ><label for="reposSelect">'.$lang['change_cvsroot'].'</label>'."\n";
	$PageHead .= '		<select name="reposSelect" id="reposSelect" class="reposchanger" onchange="postBackReposChange(this.form);">'."\n";
	foreach($config['cvs'] as $key => $value){
		$PageHead .= '			<option value="'.$key.'"';
		if ($key == $env['CVSROOT']) {
			$PageHead .= ' selected="selected"';
		}
		$PageHead .= '>'.$value['description'].'</option>'."\n";
	}
	$PageHead .= '		</select></p>'."\n";

	$PageHead .= '		<p class="themechanger"><label for="ThemeSelect">'.$lang['change_theme'].'</label>'."\n";
	$PageHead .= '		<select name="ThemeSelect" id="ThemeSelect" class="themechanger" onchange="postBackThemeChange(this.form);">'."\n";
	foreach (GetThemeList() as $key=>$value){
		$PageHead .= '			<option value="'.$value.'"';
		if ($value == $config['theme']) {
			$PageHead .= ' selected="selected"';
		}
		$PageHead .= '>'.$value.'</option>'."\n";
	}
	$PageHead .= '		</select>'."\n";
	$PageHead .= '		<label for="langSelect">'.$lang['change_lang'].'</label>'."\n";
	$PageHead .= '		<select name="langSelect" id="langSelect" class="langchanger" onchange="postBackLangChange(this.form);">'."\n";
	foreach(getLanguagesList() as $key => $value){
		$PageHead .= '			<option value="'.$value.'"';
		if ($value == $config['language']) {
			$PageHead .= ' selected="selected"';
		}
		$PageHead .= '>'.$value.'</option>'."\n";
	}
	$PageHead .= '		</select>'."\n";

	$PageHead .= '		<input type="hidden" name="URLRequest" value="'.$env['script_name'];
	$first = true;
	foreach ($_GET as $key=>$value) {
		if ($key != "tm") {
			if ($first != true) {
				$PageHead .= "&amp;";
			} else {
				$PageHead .= "?";
			}
			$first = false;
			$PageHead .= $key."=".$value;
		}
	}
	$PageHead .= '" /></p>'."\n";
	$PageHead .= '	</form>'."\n";
	$PageHead .= '</div>'."\n";

	return $PageHead;
}

function GetPageFooter()
{
	global $StartTime, $lang;

	$EndTime = microtime();
	$PageFoot = '<div class="footer">'.$lang['generated'].' '.number_format(microtime_diff($StartTime, $EndTime), 3).' '.$lang['seconds'].'<br />'."\n";
	$PageFoot .= '	'.$lang['created_by']."\n";
	$PageFoot .= '	<p><a href="http://validator.w3.org/check?uri=referer"><img src="http://www.w3.org/Icons/valid-xhtml11" alt="'.$lang['icon_xhtml'].'" height="31" width="88" /></a>&nbsp;&nbsp;'."\n";
	$PageFoot .= '	<a href="http://jigsaw.w3.org/css-validator/check/referer"><img style="border:0;width:88px;height:31px" src="http://www.w3c.org/Icons/valid-css" alt="'.$lang['icon_css'].'" /></a></p>'."\n";
	$PageFoot .= '</div>'."\n";
	$PageFoot .= '</body>'."\n";
	$PageFoot .= '</html>'."\n";

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
	$QLOut .= '</div>'."\n";

	return $QLOut;
}

function startDirList()
{
	global $RowClass, $lang;
	echo '<hr />'."\n";
	echo '	<table>'."\n";
	echo '		<tr class="head">'."\n";
	echo '			<th>&nbsp;</th>'."\n";
	echo '			<th>&nbsp;</th>'."\n";
	echo '			<th>'.$lang['file'].'</th>'."\n";
	echo '			<th>'.$lang['rev'].'</th>'."\n";
	echo '			<th>'.$lang['age'].'</th>'."\n";
	echo '			<th>'.$lang['author'].'</th>'."\n";
	echo '			<th>'.$lang['last_log'].'</th>'."\n";
	echo '		</tr>'."\n";

	$RowClass = "row1";
}

function endDirList()
{
	echo '	</table>'."\n";
	echo '<hr />'."\n";
}

function addParentDirectory()
{
	global $RowClass, $ParentIcon, $env, $lang;

	$HREF = str_replace("//", "/", $env['script_name']."?mp=".substr($env['mod_path'], 0, strrpos(substr($env['mod_path'], 0, -1), "/"))."/");
	echo '		<tr class="'.$RowClass.'">'."\n";
	echo '			<td class="min">&nbsp;</td>'."\n";
	echo '			<td class="min"><a href="'.$HREF.'"><img alt="'.$lang['icon_parent'].'" src="'.$env['script_path'].'/'.$ParentIcon.'" /></a></td>'."\n";
	echo '			<td class="min"><a href="'.$HREF.'">'.$lang['up_folder'].'</a></td>'."\n";
	echo '			<td>&nbsp;</td>'."\n";
	echo '			<td>&nbsp;</td>'."\n";
	echo '			<td>&nbsp;</td>'."\n";
	echo '			<td>&nbsp;</td>'."\n";
	echo '		</tr>'."\n";

	$RowClass = "row2";
}

function addFolders($Folders)
{
	global $RowClass, $DownloadIcon, $FolderIcon, $env, $lang;

	foreach ($Folders as $Folder) {
		$HREF = str_replace("//", "/", $env['script_name']."?mp=".$env['mod_path']."/".$Folder["Name"]."/");
		echo '		<tr class="'.$RowClass.'">'."\n";
		if ($Folder["Name"] != "CVSROOT" && $Folder["Name"] != "Attic") {
			echo '			<td class="min"><a href="'.$HREF.'&amp;dp"><img alt="'.$lang['icon_dl'].'" src="'.$env['script_path'].'/'.$DownloadIcon.'" /></a></td>'."\n";
		} else {
			echo '			<td class="min">&nbsp;</td>'."\n";
		}
		echo '			<td class="min"><a href="'.$HREF.'"><img alt="'.$lang['icon_dir'].'" src="'.$env['script_path'].'/'.$FolderIcon.'" /></a></td>'."\n";
		echo '			<td class="min"><a href="'.$HREF.'">'.$Folder["Name"].'</a></td>'."\n";
		echo '			<td>&nbsp;</td>'."\n";
		echo '			<td>&nbsp;</td>'."\n";
		echo '			<td>&nbsp;</td>'."\n";
		echo '			<td>&nbsp;</td>'."\n";
		echo '		</tr>'."\n";

		$RowClass = ($RowClass == "row1")? "row2" : "row1";
	}
}

function addModules($Modules)
{
	global $RowClass, $DownloadIcon, $ModuleIcon, $env, $lang;

	foreach ($Modules as $Key => $Val) {
		// Add the row data here.
		$HREF = str_replace("//", "/", $env['script_name']."?mp=".$env['mod_path'].$Val."/");
		echo '		<tr class="'.$RowClass.'">'."\n";
		if ($Val != "CVSROOT" && $Val != "Attic") {
			echo '			<td class="min"><a href="'.$HREF.'&amp;dp"><img alt="'.$lang['icon_dl'].'" src="'.$env['script_path'].'/'.$DownloadIcon.'" /></a></td>'."\n";
		} else {
			echo '			<td class="min">&nbsp;</td>'."\n";
		}
		echo '			<td class="min"><a href="'.$HREF.'"><img alt="'.$lang['icon_mod'].'" src="'.$env['script_path'].'/'.$ModuleIcon.'" /></a></td>'."\n";
		echo '			<td class="min"><a href="'.$HREF.'">'.$Key.'</a></td>'."\n";
		echo '			<td>&nbsp;</td>'."\n";
		echo '			<td>&nbsp;</td>'."\n";
		echo '			<td>&nbsp;</td>'."\n";
		echo '			<td>&nbsp;</td>'."\n";
		echo '		</tr>'."\n";

		$RowClass = ($RowClass == "row1")? "row2" : "row1";
	}
}

function addFiles($Files)
{
	global $RowClass, $FileIcon, $env, $lang;

	foreach ($Files as $File) {
		$HREF = str_replace("//", "/", $env['script_name']."?mp=".$env['mod_path'].$File["Name"]);
		$DateTime = strtotime($File["Revisions"][$File["Head"]]["date"]);
		$AGE = CalculateDateDiff($DateTime, strtotime(gmdate("M d Y H:i:s")));
		echo '		<tr class="'.$RowClass.'">'."\n";
		echo '			<td class="min">&nbsp;</td>'."\n";
		echo '			<td align="center"><a href="'.$HREF.'&amp;fh"><img alt="'.$lang['icon_file'].'" src="'.$env['script_path'].'/'.$FileIcon.'" /></a></td>'."\n";
		echo '			<td><a href="'.$HREF.'&amp;fh">'.$File["Name"].'</a></td>'."\n";
		echo '			<td align="center"><a href="'.$HREF.'&amp;fv&amp;dt='.$DateTime.'">'.$File["Head"].'</a></td>'."\n";
		echo '			<td align="center">'.str_replace(" ", "&nbsp;", $AGE).'&nbsp;'.$lang['ago'].'</td>'."\n";
		echo '			<td align="center">'.$File["Revisions"][$File["Head"]]["author"].'</td>'."\n";
		echo '			<td>'.str_replace(array(" ", "\n"), array("&nbsp;", "<br />"), $File["Revisions"][$File["Head"]]["LogMessage"]).'</td>'."\n";
		echo '		</tr>'."\n";

		$RowClass = ($RowClass == "row1")? "row2" : "row1";
	}
}

function GetDiffForm()
{
	global $CVSServer, $env;

	$HREF = str_replace("//", "/", $env['script_name']."?mp=".$env['mod_path']);

	$DiffForm = '<form class="diffform" action="'.$_SERVER['PHP_SELF'].'">'."\n";
	$DiffForm .= '	Diff between: <select name="DiffRev1" class="diffform">'."\n";
	foreach ($CVSServer->FILES[0]["Revisions"] as $Revision){
		$DiffForm .= '		<option value="'.$Revision["Revision"].'">'.$Revision["Revision"].'</option>'."\n";
	}
	$DiffForm .= '	</select> and '."\n";
	$DiffForm .= '	<select name="DiffRev2" class="diffform">'."\n";
	foreach ($CVSServer->FILES[0]["Revisions"] as $Revision){
		$DiffForm .= '		<option value="'.$Revision["Revision"].'">'.$Revision["Revision"].'</option>'."\n";
	}
	$DiffForm .= '	</select>'."\n";
	$DiffForm .= '	<input type="hidden" name="URLDiffReq" value="'.$HREF.'" />'."\n";
	$DiffForm .= '	<input type="button" value="Get Diff" onclick="postBackDiffRequest(this.form);" />'."\n";
	$DiffForm .= '</form>'."\n";

	return $DiffForm;
}

?>
