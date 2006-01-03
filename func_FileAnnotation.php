<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide File Annotation Page.
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2006 Brian A Cheeseman
 **/

function DisplayFileAnnotation($File, $Revision = "")
{
	global $config, $env, $lang;

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

		// Annotate the file.
		$Response = $CVSServer->Annotate($File, $Revision);
		if ($Response !== true) {
			return;
		}

		echo GetQuickLinkBar($lang['annotate_history'], false, true);

		// Start the output for the table.
		echo '<hr />'."\n";
		echo '<div id="fileannotation">'."\n";
		echo '	<table>' ."\n";
		$RowClass = 'row1';

		$search = array('<', '>', '\n');
		$replace = array('&lt;', '&gt;', '');
		$PrevRev = "";
		$FirstLine = true;
		foreach ($CVSServer->ANNOTATION as $Annotation)	{
			if (strcmp($PrevRev, $Annotation["Revision"]) != 0) {
				if (!$FirstLine) {
					echo '</pre></td>'."\n";
					echo '		</tr>'."\n";
				} else {
					$FirstLine = false;
				}
				echo '		<tr class="'.$RowClass.'">'."\n";
				echo '			<td>'.$Annotation["Revision"].'</td>'."\n";
				echo '			<td>'.$Annotation["Author"].'</td>'."\n";
				echo '			<td>'.$Annotation["Date"].'</td>'."\n";
				echo '			<td><pre>'.str_replace($search, $replace, $Annotation["Line"]);

				$RowClass = ($RowClass == "row1")? "row2" : "row1";

			} else{
				echo "\n".str_replace($search, $replace, $Annotation["Line"]);
			}
			$PrevRev = $Annotation["Revision"];
		}
		echo '</pre></td>'."\n";
		echo '		</tr>'."\n";
		echo '	</table>'."\n";
		echo '</div>'."\n";
		echo '<hr />'."\n";

		// Close the connection.
		$CVSServer->Disconnect();
	} else{
		echo $lang['err_connect'];
	}
	echo GetPageFooter();
}

?>
