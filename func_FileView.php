<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide File View Page.
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2004 Brian A Cheeseman
 **/

if ($config['GeSHi']['Enable']) {
    include_once($config['GeSHi']['Path'].'/geshi.php');
}

function DisplayFileContents($File, $Revision = "")
{
	global $config, $env;

	// Calculate the path from the $env['script_name'] variable.
	$env['script_path'] = substr($env['script_name'], 0, strrpos($env['script_name'], "/"));
	if ($env['script_path'] == ""){
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

		// Get a RLOG of the module path specified in $env['mod_path'].
		$CVSServer->RLog($env['mod_path']);

		// "Export" the file.
		$Response = $CVSServer->ExportFile($File, $Revision);
		if ($Response !== true) {
		    return;
		}

		// Add the quick link navigation bar.
		echo GetQuickLinkBar($env['mod_path'], "Code view for: ", true, true, $Revision);

		echo "<hr />\n";

		if ($config['GeSHi']['Enable']) {
    		// Create the GeSHi instance and parse the output.
			// TODO: setup code to auto identify the highlighting class to use for current file.
			$FileExt = substr($File, strrpos($File, ".")+1);
			$Language = guess_highlighter($FileExt);
			if (is_array($Language)) {
			    $Language = $Language[0];
			}
			
			$geshi = new GeSHi($CVSServer->FILECONTENTS, $Language, $config['GeSHi']['HighlightersPath']);
			$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
			$geshi->set_line_style('background: #fcfcfc;'); 
			$geshi->set_tab_width(4);
			$hlcontent = $geshi->parse_code();

			// Display the file contents.
			echo "<table class=\"source\"><tr><td>";
			echo $hlcontent;
			echo "</td></tr></table>";
		}
		else
		{
			$search = array('<', '>', '\n', '\t');
			$replace = array("&lt;", "&gt;", "", " ");
			$content = str_replace($search, $replace, $CVSServer->FILECONTENTS);
			$source = explode("\n", $content);
			$soure_size = sizeof($source);
			
			echo "<pre>\n";
			for($i = 1; $i <= $soure_size; $i++) {
				echo '<a name="'.$i.'" class="numberedLine">&nbsp;'.str_repeat('&nbsp;', strlen($soure_size) - strlen($i)). $i.'.</a> ' . $source[$i-1] . "\n";
			}
			echo "</pre>\n";
		}
		echo "<hr />";

		// Close the connection.
		$CVSServer->Disconnect();
	} else {
		echo "ERROR: Could not connect to the PServer.<br>\n";
	}
	echo GetPageFooter();
}

?>
