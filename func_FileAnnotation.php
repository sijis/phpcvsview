<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide File Annotation Page.
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2004 Brian A Cheeseman
 **/

function DisplayFileAnnotation($File, $Revision = "")
{
	global $config;

	// Calculate the path from the $ScriptName variable.
	$ScriptPath = substr($ScriptName, 0, strrpos($ScriptName, "/"));
	if ($ScriptPath == "") {
	    $ScriptPath = "/";
	}

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

		// Annotate the file.
		$Response = $CVSServer->Annotate($File, $Revision);
		if ($Response !== true) {
		    return;
		}

		// Start the output for the table.
		echo "<hr />\n";
		echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">\n";
		$RowClass = "row1";

		$search = array('<', '>', '\n');
		$replace = array("&lt;", "&gt;", "");
		$PrevRev = "";
		$FirstLine = true;
		foreach ($CVSServer->ANNOTATION as $Annotation)	{
			if (strcmp($PrevRev, $Annotation["Revision"]) != 0) {
				if (!$FirstLine) {
				    "</pre></td></tr>\n";
				} else {
					$FirstLine = false;
				}
				echo "<tr class=\"$RowClass\"><td>".$Annotation["Revision"]."</td><td>".$Annotation["Author"];
				echo "</td><td>".$Annotation["Date"]."</td><td><pre>".str_replace($search, $replace, $Annotation["Line"]);
				if ($RowClass == "row1") {
				    $RowClass = "row2";
				} else {
					$RowClass = "row1";
				}
			} else{
				echo "\n".str_replace($search, $replace, $Annotation["Line"]);
			}
			$PrevRev = $Annotation["Revision"];
		}
		echo "</table><hr />\n";

		// Close the connection.
		$CVSServer->Disconnect();
	} else{
		echo "ERROR: Could not connect to the PServer.<br />\n";
	}
	echo GetPageFooter();
}

?>
