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

	// Calculate the path from the $env['script_name'] variable.
	$env['script_path'] = substr($env['script_name'], 0, strrpos($env['script_name'], '/'));
	$env['script_path'] = (empty($env['script_path']))? '/' : $env['script_path'];

	// Create our CVS connection object and set the required properties.
	$CVSServer = new CVS_PServer($config['cvsroot'], $config['pserver'], $config['username'], $config['password']);

	// Start the output process.
	echo GetPageHeader($config['html_title'], $config['html_header']);

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
		echo GetQuickLinkBar($env['mod_path'], $lang['rev_history'], false, true, "");

		foreach ($CVSServer->FILES[0]["Revisions"] as $Revision) {
			$HREF = str_replace('//', '/', $env['script_name'].'?mp='.$env['mod_path']);
			$DateTime = strtotime($Revision["date"]);
			echo '<hr /><p><a id="rd'.$DateTime.'" />'."\n";
			echo '<b>'.$lang['revision'].'</b> '.$Revision["Revision"].' -';
			echo ' (<a href="'.$HREF.'&amp;fv&amp;dt='.$DateTime.'">'.$lang['view'].'</a>)';
			echo ' (<a href="'.$HREF.'&amp;fd&amp;dt='.$DateTime.'">'.$lang['download'].'</a>)';
			if ($Revision["PrevRevision"] != '') {
				echo ' (<a href="'.$HREF.'&amp;df&amp;r1='.$Revision["PrevRevision"].'&amp;r2=';
				echo $Revision["Revision"].'">'.$lang['diff'].'</a>)';
			}
			echo ' (<a href="'.$HREF.'&amp;fa='.$Revision["Revision"].'">'.$lang['annotate'].'</a>)<br />'."\n";
			echo '<b>'.$lang['last_checkin'].'</b> '.strftime("%A %d %b %Y %T -0000", $DateTime).' ('.CalculateDateDiff($DateTime, strtotime(gmdate("M d Y H:i:s"))).' '.$lang['ago'].')<br />'."\n";
			echo '<b>'.$lang['branch'].'</b> '.$Revision["Branches"].'<br />'."\n";
			echo '<b>'.$lang['date'].'</b> '.strftime("%B %d, %Y", $DateTime).'<br />'."\n";
			echo '<b>'.$lang['time'].'</b> '.strftime("%H:%M:%S", $DateTime).'<br />'."\n";
			echo '<b>'.$lang['author'].'</b> '.$Revision["author"].'<br />'."\n";
			echo '<b>'.$lang['state'].'</b> '.$Revision["state"].'<br />'."\n";
			if ($Revision["PrevRevision"] != '') {
				echo '<b>'.$lang['changes'].$Revision["PrevRevision"].':</b> '.$Revision["lines"].'<br />'."\n";
			}
			echo '<b>'.$lang['log_message'].'</b></p><pre>'.$Revision["LogMessage"].'</pre>'."\n";
		}

		echo '<hr />'."\n";
		
		echo GetDiffForm();
		$CVSServer->Disconnect();
	} else {
		echo $lang['err_connect'];
	}
	echo GetPageFooter();
}

?>
