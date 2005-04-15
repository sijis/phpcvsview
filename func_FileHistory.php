<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide File Listing Page.
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2005 Brian A Cheeseman
 **/

function DisplayFileHistory()
{
	global $config, $env, $lang, $CVSServer;

	// Create our CVS connection object and set the required properties.
	$CVSServer = new CVS_PServer($env['CVSSettings']['cvsroot'], $env['CVSSettings']['server'], $env['CVSSettings']['username'], $env['CVSSettings']['password']);

	// Start the output process.
	echo GetPageHeader($env['CVSSettings']['html_title'], $env['CVSSettings']['html_header']);

	// Connect to the CVS server.
	if ($CVSServer->Connect() === true) {

		// Authenticate against the server.
		$Response = $CVSServer->Authenticate();
		if ($Response !== true) {
			return;
		}

		// Get a RLOG of the module path specified in $config['mod_path'].
		$CVSServer->RLog($env['mod_path']);

		$Files = $CVSServer->FILES;

		// Add the quick link navigation bar.
		echo GetQuickLinkBar($lang['rev_history'], false, true, "");
		
		echo '<div id="filehistory">'."\n";

		foreach ($CVSServer->FILES[0]["Revisions"] as $Revision) {
			$HREF = str_replace('//', '/', $env['script_name'].'?mp='.$env['mod_path']);
			$DateTime = strtotime($Revision["date"]);
			echo '<hr />'."\n";
			//echo '<a name="rd'.$DateTime.'" />&nbsp;</a>'."\n";
			echo '<div class="filerevision">'."\n";
			echo '	<p><b>'.$lang['revision'].'</b> '.$Revision["Revision"].' -';
			echo ' (<a href="'.$HREF.'&amp;fv&amp;dt='.$DateTime.'">'.$lang['view'].'</a>)';
			echo ' (<a href="'.$HREF.'&amp;fd&amp;dt='.$DateTime.'">'.$lang['download'].'</a>)';
			if ($Revision["PrevRevision"] != '') {
				echo ' (<a href="'.$HREF.'&amp;df&amp;r1='.$Revision["PrevRevision"].'&amp;r2=';
				echo $Revision["Revision"].'">'.$lang['diff'].'</a>)';
			}
			echo ' (<a href="'.$HREF.'&amp;fa='.$Revision["Revision"].'">'.$lang['annotate'].'</a>)</p>'."\n";
			echo '	<p><b>'.$lang['last_checkin'].'</b> '.strftime("%A %d %b %Y %T -0000", $DateTime).' ('.CalculateDateDiff($DateTime, strtotime(gmdate("M d Y H:i:s"))).' '.$lang['ago'].')</p>'."\n";
			echo '	<p><b>'.$lang['branch'].'</b> '.$Revision["Branches"].'</p>'."\n";
			echo '	<p><b>'.$lang['date'].'</b> '.strftime("%B %d, %Y", $DateTime).'</p>'."\n";
			echo '	<p><b>'.$lang['time'].'</b> '.strftime("%H:%M:%S", $DateTime).'</p>'."\n";
			echo '	<p><b>'.$lang['author'].'</b> '.$Revision["author"].'</p>'."\n";
			echo '	<p><b>'.$lang['state'].'</b> '.$Revision["state"].'</p>'."\n";
			if ($Revision["PrevRevision"] != '') {
				echo '	<p><b>'.$lang['changes'].$Revision["PrevRevision"].':</b> '.$Revision["lines"].'</p>'."\n";
			}
			echo '	<p><b>'.$lang['log_message'].'</b></p>'."\n";
			echo '	<p class="logmsg">'.$Revision["LogMessage"].'</p>'."\n";
			echo '</div>'."\n";
		}

		echo '</div>'."\n";
		echo '<hr />'."\n";

		echo GetDiffForm();
		$CVSServer->Disconnect();
	} else {
		echo $lang['err_connect'];
	}
	echo GetPageFooter();
}

?>
