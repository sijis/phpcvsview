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

function DisplayFileAnnotation($File, $Revision = "") {
	global $ModPath, $CVSROOT, $PServer, $UserName, $Password, $ScriptName, 
	       $HTMLTitle, $HTMLHeading, $HTMLTblHdBg, $HTMLTblCell1, $HTMLTblCell2;

	// Calculate the path from the $ScriptName variable.
	$ScriptPath = substr($ScriptName, 0, strrpos($ScriptName, "/"));
	if ($ScriptPath == "") {
	    $ScriptPath = "/";
	}
		  
	// Create our CVS connection object and set the required properties.
	$CVSServer = new CVS_PServer($CVSROOT, $PServer, $UserName, $Password);
	
	// Start the output process.
	echo GetPageHeader($HTMLTitle, $HTMLHeading);
	
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
		foreach ($CVSServer->ANNOTATION as $Annotation)
		{
			echo "<tr class=\"$RowClass\"><td><pre>".$Annotation["Revision"]."</pre></td><td><pre>".$Annotation["Author"];
			echo "</pre></td><td><pre>".$Annotation["Date"]."</pre></td><td><pre>".str_replace($search, $replace, $Annotation["Line"])."</pre></td></tr>\n";
			if ($RowClass == "row1") {
			    $RowClass = "row2";
			}
			else
			{
				$RowClass = "row1";
			}
		}
		echo "</table><hr />\n";
		
		// Close the connection.
		$CVSServer->Disconnect();
	}
	else
	{
		echo "ERROR: Could not connect to the PServer.<br />\n";
	}
	echo GetPageFooter();
}


?>