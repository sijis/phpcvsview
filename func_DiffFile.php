<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide File Diff Page.
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2005 Brian A Cheeseman
 **/

function DisplayFileDiff($Rev1, $Rev2)
{
	global $config, $env;

	// Calculate the path from the $env['script_name'] variable.
	$env['script_path'] = substr($env['script_name'], 0, strrpos($env['script_name'], "/"));
	if ($env['script_path'] == "") {
	    $env['script_path'] = "/";
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

		// Add the quick link navigation bar.
		echo GetQuickLinkBar($env['mod_path'], "Revision Diff for: ", true, true, "");
		echo "<hr />";

		// Get the DIFF from the server.
		$DiffLines = explode("\n", $CVSServer->getFileDiff($env['mod_path'], $Rev1, $Rev2));
//		echo "<pre>".implode("\n", $DiffLines)."</pre>";

		// Get a RLOG of the module path specified in $env['mod_path'].
		$CVSServer->RLog($env['mod_path']);
		$DateOfFile = 0;
		foreach ($CVSServer->FILES[0]["Revisions"] as $Revision) {
			if ($Revision["Revision"] == $Rev1) {
			    $DateOfFile = strtotime($Revision["date"]);
			}
		}
		
		$CVSServer->Disconnect();
		$CVSServer = new CVS_PServer($config['cvsroot'], $config['pserver'], $config['username'], $config['password']);
		$CVSServer->Connect();
		$CVSServer->Authenticate();
		
		if ($CVSServer->ExportFile($env['mod_path'], $DateOfFile) !== true) {
		    return;
		}
		$FilePatching = array();
		$FileContents = $CVSServer->FILECONTENTS;
		if ($FileContents === false) {
		    echo "<pre>ERROR Getting Revision $Rev1 of file</pre>";
		}
		$Lines = explode("\n", $FileContents);
		foreach ($Lines as $Line) 
		{
			$FilePatching[] = array('mode' => "o", 'text' => $Line);
		}
		
		$linenumber = 0;
		while(strpos($DiffLines[$linenumber], "diff") === false)
		{
			$linenumber++;
		} // while
		
		$linenumber++;
		// We now have the line which starts the diff output.
		$LineOffset = 0;
		while($linenumber < count($DiffLines))
		{
			if (strpos($DiffLines[$linenumber], "a") !== false) 
			{
			    // We have added a line or lines.
				$Parts = explode("a", $DiffLines[$linenumber]);
				$InsertLocation = explode(",", $Parts[0]);
				$NewLineLocation = explode(",", $Parts[1]);
				$InsertLength = count($NewLineLocation) == 2 ? $NewLineLocation[1]-$NewLineLocation[0]+1 : 1;
				for ($LineCounter = 0; $LineCounter < $InsertLength; $LineCounter++)
				{
					$TempLine = array('mode' => "+", 'text' => substr($DiffLines[++$linenumber], 2));
					InsertIntoArray(&$FilePatching, $TempLine, $InsertLocation[0]+$LineCounter+1);
					$LineOffset++;
				}
			}
			else if (strpos($DiffLines[$linenumber], "c") !== false) 
			{
			    // We have changed a line or lines.
				$Parts = explode("c", $DiffLines[$linenumber]);
				$InsertLocation = explode(",", $Parts[0]);
				$InsertLength = count($InsertLocation) == 2 ? $InsertLocation[1]-$InsertLocation[0]+1 : 1;
				$linenumber += $InsertLength + 1;
				$NewFileLocation = explode(",", $Parts[1]);
				$NewLineLength = count($NewFileLocation) == 2 ? $NewFileLocation[1]-$NewFileLocation[0]+1 : 1;
				for ($LineCounter = 0; $LineCounter < $InsertLength; $LineCounter++)
				{
					if ($LineCounter < $NewLineLength) {
						$linenumber++;
    					$TempLine = array('mode' => "+", 'text' => substr($DiffLines[$linenumber], 2));
						InsertIntoArray(&$FilePatching, $TempLine, $InsertLocation[0]+$LineCounter+$LineOffset);
						$LineOffset++;
					}
					$FilePatching[$InsertLocation[0]-2+$LineCounter+$LineOffset]['mode'] = '-';
				}
				for ($LineCounter = $InsertLength; $LineCounter < $NewLineLength; $LineCounter++)
				{
					$linenumber++;
					$TempLine = array('mode' => "+", 'text' => substr($DiffLines[$linenumber], 2));
					InsertIntoArray(&$FilePatching, $TempLine, $InsertLocation[0]+$LineCounter+$LineOffset);
				}
			}
			else if (strpos($DiffLines[$linenumber], "d") !== false) 
			{
			    // we have removed a line or lines.
				$Parts = explode("d", $DiffLines[$linenumber]);
				$DeleteLocation = explode(",", $Parts[0]);
				$OldLineLocation = explode(",", $Parts[1]);
				$DeleteLength = count($DeleteLocation) == 2 ? $DeleteLocation[1]-$DeleteLocation[0]+1 : 1;
				for ($LineCounter = 0; $LineCounter < $DeleteLength; $LineCounter++)
				{
					$FilePatching[$OldLineLocation[0]+$LineCounter+$LineOffset]['mode'] = '-';
					$LineOffset++;
					$linenumber++;
				}
			}
			$linenumber++;
		} // while
		
		echo "<table><tr><td>";
		$search = array("<", ">", "\n", "\t", " ");
		$replace = array("&lt;", "&gt;", "", "&nbsp;&nbsp;&nbsp;&nbsp;", "&nbsp;");
		foreach ($FilePatching as $Line)
		{
			echo "<div class=\"";
			switch($Line['mode']){
				case '+': 
					echo "added";
					break;
				case '-': 
					echo "removed";
					break;
				default:
					echo "normal";
			} // switch
			echo "\">".str_replace($search, $replace, $Line['text'])."&nbsp;</div>\n";
		}
		echo "</td></tr></table><hr />";

		$CVSServer->Disconnect();
	} else {
		echo "Connection Failed.";
	}
	echo GetPageFooter();
}

?>
