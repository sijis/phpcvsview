<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide File View Page.
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2005 Brian A Cheeseman
 * 
 * Thanks To:
 * 		Nigel McNie - Suggestion of Caching of source code from repository, hence improving efficiency.
 **/

if ($config['GeSHi']['Enable']) {
	include_once($config['GeSHi']['Path'].'/geshi.php');
}

function DisplayFileContents($File, $Revision = "")
{
	global $config, $env, $lang;

	// Create our CVS connection object and set the required properties.
	$CVSServer = new CVS_PServer($env['CVSSettings']['cvsroot'], $env['CVSSettings']['server'], $env['CVSSettings']['username'], $env['CVSSettings']['password']);

	// Start the output process.
	echo GetPageHeader($env['CVSSettings']['html_title'], $env['CVSSettings']['html_header']);

	// Add the quick link navigation bar.
	echo GetQuickLinkBar($lang['code_view'], true, true, $Revision)."<hr />\n";
	
	// Check and see if this file and version has already been viewed and exists in the cache.
	if ($config['Cache']['Enable']) {
		$CachedFileName = $config['Cache']['Location'];
		if (!file_exists($CachedFileName)) {
		    mkdir($CachedFileName, 0750);
		}
		$CachedFileName .= "/".str_replace("/", "_", $File).",$Revision";
	}
	if (file_exists($CachedFileName) && $config['Cache']['Enable']) {
		$fd = fopen($CachedFileName, "r");
		if ($fd !== false) {
			fpassthru($fd);	    
			fclose($fd);
		}
	}
	else
	{
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
	
			if ($config['GeSHi']['Enable']) {
				// Create the GeSHi instance and parse the output.
				// TODO: setup code to auto identify the highlighting class to use for current file.
				$FileExt = substr($File, strrpos($File, '.')+1);
				$Language = guess_highlighter($FileExt);
				if (is_array($Language)) {
					$Language = $Language[0];
				}
				
				$geshi = new GeSHi($CVSServer->FILECONTENTS, $Language, $config['GeSHi']['HighlightersPath']);
				$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
				$geshi->set_line_style('background: #fcfcfc;'); 
				$geshi->set_tab_width(4);
				$hlcontent = $geshi->parse_code();
	
				// Store in the current cache.		
				if ($config['Cache']['Enable']) {
					$fd = fopen($CachedFileName, "w");
					if ($fd !== false) {
						fwrite($fd, '<table class="source"><tr><td>'.$hlcontent.'</td></tr></table>');
						fclose($fd);
					}
				}		

				// Display the file contents.
				echo '<table class="source"><tr><td>';
				echo $hlcontent;
				echo '</td></tr></table>';
			}
			else
			{
				$search = array('<', '>', '\n', '\t');
				$replace = array('&lt;', '&gt;', '', ' ');
				$content = str_replace($search, $replace, $CVSServer->FILECONTENTS);
				$source = explode('\n', $content);
				$soure_size = sizeof($source);
				
				if ($config['Cache']['Enable']) {
					$fd = fopen($CachedFileName, "w");
					if ($fd !== false) {
						fwrite($fd, "<pre>\n");
					}
				}
				else
				{
					$fd = false;
				}
				echo "<pre>\n";
				for($i = 1; $i <= $soure_size; $i++) {
					$line = '<a name="'.$i.'" class="numberedLine">&nbsp;'.str_repeat('&nbsp;', strlen($soure_size) - strlen($i)). $i.'.</a> ' . $source[$i-1] . "\n";
					if ($fd !== false) {
						fwrite($fd, $line);
					}
					echo $line;
				}
				if ($fd !== false) {
					fwrite($fd, "</pre>\n");
				}
				echo "</pre>\n";
			}
			// Close the connection.
			$CVSServer->Disconnect();
		} else {
			echo $lang['err_connect'];
		}
	}
	echo "<hr />";
	echo GetPageFooter();
}

?>
