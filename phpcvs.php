<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To provide the main class required to access a CVS repository
 *
 * @author Brian A Cheeseman <brian@bcheese.homeip.net>
 * @version $Id$
 * @copyright 2003 Brian A Cheeseman
 **/
 
require_once 'Net/Socket.php';

class CVS_PServer {

	var $CVS_REPOSITORY;		// Storage of the CVS Repository file system path.
	var $CVS_USERNAME;			// Username to use when authenticating with the PServer.
	var $CVS_PASSWORD;			// Password for the account above.
	var $CVS_PSERVER;			// Hostname of the server running the PServer.
	var $CVS_PORT;				// Port number the PServer listener is running on.
	var $CVS_TIMEOUT;			// Timeout in seconds for all socket operations.
	var $CVS_VALID_REQUESTS;	// List of valid requests the PServer accepts.
	var $SOCKET;				// The socket handle for communicating with the PServer.
	var $ALLOWED_RESPONSES = array();	// A hashed array of responses that we are capable of 
								// processing and the contents is the name of the function
								// to process it through.
	var $ALLOWED_REQUESTS = array();	// A hashed array of requests we are allowed to send.
	var $FINAL_RESPONSE;		// A state variable for tracking whether the final response 
								// in a chain of lines was a success or failure.
	var $STDERR;				// Standard Error output. (Does not mean that an error occured).
	var $MESSAGE_CONTENT;		// Message contents. (Standard Out)
	var $FOLDERS = array();				// An array of the folders in the current module.
	var $FILES = array();				// An array of the files in the current module.
	var $CURRENT_FOLDER;		// The current folder we are building up.
	var $CURRENT_FILE;			// The current file we are building up.
	
	/**
	* Allowed Response Decoding functions.
	**/
	
	// ***************************************************************************
	//     Function: processOk()
	//       Author: Brian A Cheeseman.
	//   Parameters: string			- Line of response text.
	// Return Value: boolean		- Are we expecting more responses to come in?
	// ***************************************************************************
	function processOk($LineOfText)
	{
		$this->FINAL_RESPONSE = true;
		return false;
	}

	// ***************************************************************************
	//     Function: processError()
	//       Author: Brian A Cheeseman.
	//   Parameters: string			- Line of response text.
	// Return Value: boolean		- Are we expecting more responses to come in?
	// ***************************************************************************
	function processError($LineOfText)
	{
		$this->FINAL_RESPONSE = false;
		return false;
	}

	// ***************************************************************************
	//     Function: processValidRequests()
	//       Author: Brian A Cheeseman.
	//   Parameters: string			- Line of response text.
	// Return Value: boolean		- Are we expecting more responses to come in?
	// ***************************************************************************
	function processValidRequests($LineOfText)
	{
		// Start tokenising the list of Valid Requests.
		$Token = strtok($LineOfText, " ");
		
		// Skip the first token, as it is the response identifier.
		$Token = strtok(" ");
		while($Token !== false){
			$this->ALLOWED_REQUESTS[$Token] = true;
			$Token = strtok(" ");
		} // while
		return true;
	}
	
	// ***************************************************************************
	//     Function: processE()
	//       Author: Brian A Cheeseman.
	//   Parameters: string			- Line of response text.
	// Return Value: boolean		- Are we expecting more responses to come in?
	// ***************************************************************************
	function processE($LineOfText)
	{
		$this->STDERR .= substr($LineOfText, 2) . "\n";
		return true;
	}
	
	// ***************************************************************************
	//     Function: processM()
	//       Author: Brian A Cheeseman.
	//   Parameters: string			- Line of response text.
	// Return Value: boolean		- Are we expecting more responses to come in?
	// ***************************************************************************
	function processM($LineOfText)
	{
		$this->MESSAGE_CONTENT .= substr($LineOfText, 2) . "\n";
		return true;
	}
	
	/**
	* 
	* Class Constructor.
	* 
	**/
	function CVS_PServer() {
					
		$this->CVS_REPOSITORY = '';
		$this->CVS_PSERVER = '';
		$this->CVS_PORT = 2401;
		$this->CVS_USERNAME = '';
		$this->CVS_PASSWORD = '';
		$this->SOCKET = new Net_Socket();
		
		$this->ALLOWED_RESPONSES["ok"] = "processOk";
		$this->ALLOWED_RESPONSES["error"] = "processError";
		$this->ALLOWED_RESPONSES["Valid-requests"] = "processValidRequests";
		$this->ALLOWED_RESPONSES["Checked-in"] = "processCheckedIn";
		$this->ALLOWED_RESPONSES["New-entry"] = "processNewEntry";
		$this->ALLOWED_RESPONSES["Checksum"] = "processChecksum";
		$this->ALLOWED_RESPONSES["Copy-file"] = "processCopyFile";
		$this->ALLOWED_RESPONSES["Updated"] = "processUpdated";
		$this->ALLOWED_RESPONSES["Created"] = "processCreated";
		$this->ALLOWED_RESPONSES["Update-existing"] = "processUpdateExisting";
		$this->ALLOWED_RESPONSES["Merged"] = "processMerged";
		$this->ALLOWED_RESPONSES["Patched"] = "processPatched";
		$this->ALLOWED_RESPONSES["Rcs-diff"] = "processRcsDiff";
		$this->ALLOWED_RESPONSES["Mode"] = "processMode";
		$this->ALLOWED_RESPONSES["Mod-time"] = "processModTime";
		$this->ALLOWED_RESPONSES["Removed"] = "processRemoved";
		$this->ALLOWED_RESPONSES["Remove-entry"] = "processRemoveEntry";
		$this->ALLOWED_RESPONSES["Set-static-directory"] = "processSetStaticDirectory";
		$this->ALLOWED_RESPONSES["Clear-static-directory"] = "processClearStaticDirectory";
		$this->ALLOWED_RESPONSES["Set-sticky"] = "processSetSticky";
		$this->ALLOWED_RESPONSES["Clear-sticky"] = "processClearSticky";
		$this->ALLOWED_RESPONSES["Template"] = "processTemplate";
		$this->ALLOWED_RESPONSES["Set-checkin-prog"] = "processSetCheckinProg";
		$this->ALLOWED_RESPONSES["Set-update-prog"] = "processSetUpdateProg";
		$this->ALLOWED_RESPONSES["Notified"] = "processNotified";
		$this->ALLOWED_RESPONSES["Module-expansion"] = "processModuleExpansion";
		$this->ALLOWED_RESPONSES["Wrapper-rcsOption"] = "processWrapperRcsOption";
		$this->ALLOWED_RESPONSES["M"] = "processM";
		$this->ALLOWED_RESPONSES["Mbinary"] = "processMBinary";
		$this->ALLOWED_RESPONSES["E"] = "processE";
		$this->ALLOWED_RESPONSES["F"] = "processF";
		$this->ALLOWED_RESPONSES["MT"] = "processMT";
	}
	
	/**
	* 
	* Property Retrieval Functions.
	* 
	**/
	function get_Repository() {
		return $this->CVS_REPOSITORY;
	}
	
	function get_UserName() {
		return $this->CVS_USERNAME;
	}
	
	function get_Password() {
		return $this->CVS_PASSWORD;
	}
	
	function get_PServer() {
		return $this->CVS_PSERVER;
	}

	/**
	* 
	* Property Setting Functions.
	* 
	**/
	function set_Repository($NewRepository) {
		$this->CVS_REPOSITORY = $NewRepository;
		return true;
	}
	
	function set_UserName($NewUserName) {
		$this->CVS_USERNAME = $NewUserName;
		return true;
	}
	
	function set_Password($NewPassword) {
		$this->CVS_PASSWORD = $NewPassword;
		return true;
	}
	
	function set_PServer($NewPServer) {
		$this->CVS_PSERVER = $NewPServer;
		return true;
	}
	
	/**
	* 
	* Class Methods.
	* 
	**/
	
	// ***************************************************************************
	//     Function: TransformPW()
	//       Author: Brian A Cheeseman.
	//   Parameters: $ClearPW		- The clear text password to be transformed.
	// Return Value: string			- The cipher text of the clear test password.
	// ***************************************************************************
	function TransformPW($ClearPW) {
	
		// Define our constant array to provide a lookup table for the conversion
		// of the clear password to cipher text.
	   	$NewChars = array(
			'!' => 'x',		'8' => '_',		'N' => '[',		'g' => 'I',
			'"' => '5',		'9' => 'A',		'O' => '#',		'h' => 'c',
			'%' => 'm',		':' => 'p',		'P' => '}',		'i' => '?',
			'&' => 'H',		';' => 'V',		'Q' => '7',		'j' => '^',
			'\'' => 'l',	'<' => 'v',		'R' => '6',		'k' => ']',
			'(' => 'F',		'=' => 'n',		'S' => 'B',		'l' => '\'',
			')' => '@',		'>' => 'z',		'T' => '|',		'm' => '%',
			'*' => 'L',		'?' => 'i',		'U' => '~',		'n' => '=',
			'+' => 'C',		'A' => '9',		'V' => ';',		'o' => '0',
			',' => 't',		'B' => 'S',		'W' => '/',		'p' => ':',
			'-' => 'J',		'C' => '+',		'X' => '\\',	'q' => 'q',
			'.' => 'D',		'D' => '.',		'Y' => 'G',		'r' => ' ',
			'/' => 'W',		'E' => 'f',		'Z' => 's',		's' => 'Z',
			'0' => 'o',		'F' => '(',		'_' => '8',		't' => ',',
			'1' => '4',		'G' => 'Y',		'a' => 'y',		'u' => 'b',
			'2' => 'K',		'H' => '&',		'b' => 'u',		'v' => '<',
			'3' => 'w',		'I' => 'g',		'c' => 'h',		'w' => '3',
			'4' => '1',		'J' => '-',		'd' => 'e',		'x' => '!',
			'5' => '"',		'K' => '2',		'e' => 'd',		'y' => 'a',
			'6' => 'R',		'L' => '*',		'f' => 'E',		'z' => '>',
			'7' => 'Q',		'M' => '{');
			
		// Initialise the cipher text password local storage variable.
		$CryptPW = '';
		
		// Loop through each char in the clear text password and add 
		// the appropriate character from the lookup table to the 
		// cipher text password variable.
		for ($i=0; $i<strlen($ClearPW); $i++) {
			$CryptPW .= $NewChars[substr($ClearPW, $i, 1)];
		}
		
		// Return the cipher text password to the calling code.
		return $CryptPW;
	} // End of function TransformPW

	// ***************************************************************************
	//     Function: Connect()
	//       Author: Brian A Cheeseman.
	//   Parameters: None.
	// Return Value: boolean		- Were we successful in connecting?
	// ***************************************************************************
	function Connect() {
	
		// Do we have the name of the server to connect to?
		if ($this->CVS_PSERVER != "") {
			// Yes, attempt to connect to the server.
			$retval = $this->SOCKET->connect($this->CVS_PSERVER, $this->CVS_PORT, false, $this->CVS_TIMEOUT);
			// Return to the calling code the fact that we are connected.
			return $retval;
		}
		else
		{
			// We need a server name to connect, so return a false.
			return false;
		} // End of if ($this->CVS_PSERVER != "")
	} // End of function Connect
	
	// ***************************************************************************
	//     Function: Disconnect()
	//       Author: Brian A Cheeseman.
	//   Parameters: None.
	// Return Value: boolean		- Were we successful in connecting?
	// ***************************************************************************
	function Disconnect() {
	
		$retval = $this->SOCKET->disconnect();
		return $retval;
	} // End of function Disconnect
	
	// ***************************************************************************
	//     Function: Authenticate()
	//       Author: Brian A Cheeseman.
	//   Parameters: None.
	// Return Value: boolean		- Are we authenticated.
	// ***************************************************************************
	function Authenticate() {
	
		// Send the start of authentication request.
		if ($this->SOCKET->write("BEGIN AUTH REQUEST\n") != true) {
		    return false;
		}
		
		// Send the path to the repository we are attempting to connect to.
		if ($this->SOCKET->write($this->CVS_REPOSITORY."\n") != true) {
		    return false;
		}
		
		// Send the user name to authenticate with.
		if ($this->SOCKET->write($this->CVS_USERNAME."\n") != true) {
		    return false;
		}
		
		// Transform and send the password matching the username above.
		if ($this->SOCKET->write("A".$this->TransformPW($this->CVS_PASSWORD)."\n") != true) {
		    return false;
		}
		
		// Send the terminator for the authentication request.
		if ($this->SOCKET->write("END AUTH REQUEST\n") != true) {
		    return false;
		}
		
		// Read the next line to determine if we were successful.
		$response = $this->SOCKET->readLine();
		if ($response == true && strncmp($response, "I LOVE YOU", 10) == 0) {
	        return true;
		}
		else
		{
			// Retrieve the error message from the PServer.
			$errorMsg = "";
			while(!$this->SOCKET->eof()){
				$line = $this->SOCKET->readLine();
				if (strncmp($line, "E ", 2) == 0) {
				    $errorMsg .= substr($line, 2);
				}
				if (strncmp($line, "error", 5) == 0) {
				    $this->SOCKET->disconnect();
				}				
			} // End of while(!$this->SOCKET->eof())
			if ($errorMsg == "") {
			    return false;
			}
			else
			{
				return $errorMsg;
			} // End of if ($errorMsg == "")
		} // End of if ($response == true && strncmp($response, "I LOVE YOU", 10) == 0)
	} // End of function Authenticate

	// ***************************************************************************
	//     Function: processResponse()
	//       Author: Brian A Cheeseman.
	//   Parameters: None.
	// Return Value: boolean		- Successfully sent.
	// ***************************************************************************
	function processResponse()
	{
		$this->MESSAGE_CONTENT = "";
		$this->STDERR = "";
		$KeepGoing = true;
		while($KeepGoing){
			$ResponseLine = $this->SOCKET->readLine();
			$Response = strtok($ResponseLine, " ");
			if ($Response != "") {
				$Func = $this->ALLOWED_RESPONSES[$Response];
    			if (method_exists($this, $Func)) {
				    $KeepGoing = $this->$Func($ResponseLine);
				}
			}
		} // while
	}

	// ***************************************************************************
	//     Function: sendCVSROOT()
	//       Author: Brian A Cheeseman.
	//   Parameters: None.
	// Return Value: boolean		- Successfully sent.
	// ***************************************************************************
	function sendCVSROOT()
	{
		if ($this->SOCKET->write("Root ".$this->CVS_REPOSITORY."\n") != true) {
		    return false;
		}
		return true;
	}	
	
	// ***************************************************************************
	//     Function: sendValidResponses()
	//       Author: Brian A Cheeseman.
	//   Parameters: None.
	// Return Value: boolean		- Successfully sent.
	// ***************************************************************************
	function sendValidResponses()
	{
		// Build our list of responses we can process into the format required
		// for the cvs pserver.
		$ValidResponses = "";
		foreach ($this->ALLOWED_RESPONSES as $name => $value)
		{
			$ValidResponses .= " ".$name;
		}
		
		// Send the command to the pserver.
		if ($this->SOCKET->write("Valid-responses".$ValidResponses."\n") != true) {
		    return false;
		}
		return true;
	}
	
	// ***************************************************************************
	//     Function: sendValidRequests()
	//       Author: Brian A Cheeseman.
	//   Parameters: None.
	// Return Value: boolean		- Successfully sent and processed.
	// ***************************************************************************
	function sendValidRequests()
	{
		if ($this->SOCKET->write("valid-requests\n") != true) {
		    return false;
		}
		$this->processResponse();
		return true;
	}
	
	// ***************************************************************************
	//     Function: sendUseUnchanged()
	//       Author: Brian A Cheeseman.
	//   Parameters: None.
	// Return Value: boolean		- Successfully sent.
	// ***************************************************************************
	function sendUseUnchanged()
	{
		if ($this->ALLOWED_REQUESTS["UseUnchanged"] == true) {
		    if ($this->SOCKET->write("UseUnchanged\n") != true) {
		        return false;
		    }
		}
		return true;
	}
	
	// ***************************************************************************
	//     Function: sendArgument()
	//       Author: Brian A Cheeseman.
	//   Parameters: string			- Value of argument to send.
	// Return Value: boolean		- Successfully sent.
	// ***************************************************************************
	function sendArgument($ArgToSend)
	{
		if ($this->ALLOWED_REQUESTS["Argument"] == true) {
		    if ($this->SOCKET->write("Argument $ArgToSend\n") != true) {
		        return false;
		    }
		}
		return true;
	}
	
	// ***************************************************************************
	//     Function: sendRLog()
	//       Author: Brian A Cheeseman.
	//   Parameters: None.
	// Return Value: boolean		- Successfully sent.
	// ***************************************************************************
	function sendRLog()
	{
		if ($this->ALLOWED_REQUESTS["rlog"] == true) {
		    if ($this->SOCKET->write("rlog\n") != true) {
		        return false;
		    }
		}
		return true;
	}
	
	/**
	* Helper Methods.
	**/

	// ***************************************************************************
	//     Function: RLogDirectory()
	//       Author: Brian A Cheeseman.
	//   Parameters: string			- Directory to get the RLog for.
	// Return Value: boolean		- Were we successful.
	// ***************************************************************************
	function RLog($Folder)
	{
		$this->sendCVSROOT();
		$this->sendValidResponses();
		$this->sendValidRequests();
	
		if (!$this->sendUseUnchanged()) {
		    return false;
		}
		
		if (!$this->sendArgument($Folder)) {
		    return false;
		}

		if (!$this->sendRLog()) {
		    return false;
		}

		$this->processResponse();

		if (!($this->FINAL_RESPONSE)) {
		    return $this->RLog(substr($Folder, 1));
		}

		if ($Folder == "") {
		    $Folder = "/";
		}

		$DirCount = -1;
		$FileCount = -1;
		$SeenFolders = "";
		$CurrentDecode = 0;
		$FileRevision = -1;
		$CurrentRevision = "";
		$LineProcessed = false;
		if ($this->FINAL_RESPONSE) {
			$Responses = explode("\n", $this->MESSAGE_CONTENT);
			// Iterate through each line.
			foreach ($Responses as $Line)
			{
				$LineProcessed = false;
				// Are we dealing with a file or a folder?
				if (strncmp($Line, "RCS file: ", 10) == 0) {
					// We have the file/dir name, so we can now determine what we are dealing with.
					$TempLine = substr($Line, 10+strlen($this->CVS_REPOSITORY.$Folder));
					if (strncmp($TempLine, "/", 1) == 0) {
					    $TempLine = substr($TempLine, 1);
					}
					if (($SlashPos = strpos($TempLine, "/")) > 0) {
						// We have a folder.
						$FolderName = substr($TempLine, 0, $SlashPos);
						if (strpos($SeenFolders, $FolderName) === false) {
							$DirCount++;
							$this->FOLDERS[$DirCount]["Name"] = $FolderName;
							$SeenFolders .= " " . $FolderName;
							$CurrentDecode = 1;
							$CurrentRevision = "";
							$LineProcessed = true;
						}
					}
					else
					{
						// We have a file.
						$FileCount++;
						$FileName = substr($TempLine, 0, -2);
						$this->FILES[$FileCount]["Name"] = $FileName;
						$CurrentDecode = 2;
						$LineProcessed = true;
					}
				} 
				// Lets continue, but only if we have a CurrentDecode type of 2 (ie a file).
				if ($CurrentDecode == 2) {
				    // Process for the remaining file attributes.
					
					// Head version of file.
					if (strncmp($Line, "head:", 5) == 0) {
						$this->FILES[$FileCount]["Head"] = trim(substr($Line, 6));
						$LineProcessed = true;
					}
					
					// Default branch.
					if (strncmp($Line, "branch:", 7) == 0) {
						$this->FILES[$FileCount]["Branch"] = trim(substr($Line, 8));
						$LineProcessed = true;
					}

					// Locking Mechanism.
					if (strncmp($Line, "locks:", 6) == 0) {
						$this->FILES[$FileCount]["Locks"] = trim(substr($Line, 7));
						$LineProcessed = true;
					}
					
					// Access list.
					if (strncmp($Line, "access list:", 12) == 0) {
						$this->FILES[$FileCount]["Access"] = trim(substr($Line, 13));
						$LineProcessed = true;
					}
					
					// Process the symbolic names.
					if (strncmp($Line, "symbolic names:", 15) == 0) {
					    $LineProcessed = true;
					}
					
					if (strncmp($Line, "\t", 1) == 0) {
						$TempLine = substr($Line, 1);
						$SymbolName = trim(substr($TempLine, 0, strpos($TempLine, ":")));
						$SymbolValue = trim(substr($TempLine, strpos($TempLine, ":")+1));
						$this->FILES[$FileCount]["Symbols"]["$SymbolName"] = $SymbolValue;
						$this->FILES[$FileCount]["Symbols"]["$SymbolValue"] = $SymbolName;
						$LineProcessed = true;
					}
					
					// Process the Keyword Substitution.
					if (strncmp($Line, "keyword substitution:", 21) == 0) {
					    $this->FILES[$FileCount]["KeywordSubst"] = trim(substr($Line, 22));
						$LineProcessed = true;
					}
					
					// Process the Total Revisions.
					if (strncmp($Line, "total revisions:", 16) == 0) {
					    $TempLine = substr($Line, 17);
						$this->FILES[$FileCount]["TotalRevs"] = trim(substr($TempLine, 0, strpos($TempLine, ";")));
						$this->FILES[$FileCount]["SelectedRevs"] = trim(substr($TempLine, strpos($TempLine, ";")+22));
						$LineProcessed = true;
					}
					
					// Process the description.
					if (strncmp($Line, "description:", 12) == 0) {
					    $this->FILES[$FileCount]["Description"] = trim(substr($Line, 13));
						$LineProcessed = true;
					}					
					
					// Process the individual revision information.
					if (strncmp($Line, "-------------", 13) == 0) {
					    $LineProcessed = true;
					}
					
					// Get this revision number.
					if (strncmp($Line, "revision", 8) == 0) {
					    $CurrentRevision = substr($Line, 9);
						$this->FILES[$FileCount]["Revisions"]["$CurrentRevision"]["Revision"] = $CurrentRevision;
						$this->FILES[$FileCount]["Revisions"]["$CurrentRevision"]["LogMessage"] = "";
						$LineProcessed = true;
					}
					
					// Get the Date, Author, State and Lines of this revision.
					if (strncmp($Line, "date:", 5) == 0) {
					    $Segment = strtok($Line, ";");
						while(!($Segment === false)){
							$SepPos = trim(strpos($Segment, ":"));
							$Name = trim(substr($Segment, 0, $SepPos));
							$Value = trim(substr($Segment, $SepPos+1));
							$this->FILES[$FileCount]["Revisions"]["$CurrentRevision"]["$Name"] = $Value;
							$Segment = strtok(";");
						} // while
						$LineProcessed = true;
					}
					
					// Get the current revisions branch.
					if (strncmp($Line, "branches:", 9) == 0) {
						$this->FILES[$FileCount]["Revisions"]["$CurrentRevision"]["Branches"] = trim(substr($Line, 10));
					    $LineProcessed = true;
					}
					
					// Deal with the new file seperator.
					if (strncmp($Line, "=============", 13) == 0) {
						$CurrentDecode = 0;
						$LineProcessed = true;
					}
					
					// Deal with the blank lines.
					
					// Get any lines not already processed and assume they are the log message.
					if (!$LineProcessed) {
						if (strlen($this->FILES[$FileCount]["Revisions"]["$CurrentRevision"]["LogMessage"]) > 0) {
						    $this->FILES[$FileCount]["Revisions"]["$CurrentRevision"]["LogMessage"] .= "\n";
						}
					    $this->FILES[$FileCount]["Revisions"]["$CurrentRevision"]["LogMessage"] .= trim($Line);
					}
				}
			}
		}
	}
}

?>
