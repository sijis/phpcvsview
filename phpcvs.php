<?php

/**
 * Purpose: To provide the main class required to access a CVS repository
 *
 * @author Brian A Cheeseman <brian@bcheese.homeip.net>
 * @version $Id$
 * @copyright 2002 ARECOM International
 **/

class phpcvs {
	var $CVS_REPOSITORY;
	var $CVS_USERNAME;
	var $CVS_PASSWORD;
	var $CVS_PSERVER;
	var $CVS_VALID_REQUESTS;

	var $SOCKET_HANDLE;
	
	/**
	* 
	* Class Constructor.
	* 
	**/
	
	function phpcvs($Repository = '', 
	                $PServer = '',
					$UserName = '',
					$Password = '') {
					
		$this->CVS_REPOSITORY = $Repository;
		$this->CVS_PSERVER = $PServer;
		$this->CVS_USERNAME = $UserName;
		$this->CVS_PASSWORD = $Password;
		$this->SOCKET_HANDLE = -1;
		$this->SHOW_CVSROOT = true;
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

	function EncodePW(&$ClearPW) {
	   	$NewChars = array(
			'!' => 120, '"' => 53, '%' => 109, '&' => 72, '\'' => 108,
			'(' => 70, ')' => 64, '*' => 76, '+' => 67, ',' => 116,
			'-' => 74, '.' => 68, '/' => 87, '0' => 111, '1' => 52,
			'2' => 75, '3' => 119, '4' => 49, '5' => 34, '6' => 82,
			'7' => 81, '8' => 95, '9' => 65, ':' => 112, ';' => 86,
			'<' => 118, '=' => 110, '>' => 122, '?' => 105, 'A' => 57,
			'B' => 83, 'C' => 43, 'D' => 46, 'E' => 102, 'F' => 40,
			'G' => 89, 'H' => 38, 'I' => 103, 'J' => 45, 'K' => 50, 
			'L' => 42, 'M' => 123, 'N' => 91, 'O' => 35, 'P' => 125,
			'Q' => 55, 'R' => 54, 'S' => 66, 'T' => 124, 'U' => 126,
			'V' => 59, 'W' => 47, 'X' => 92, 'Y' => 71, 'Z' => 115,
			'a' => 121, 'b' => 117, 'c' => 104, 'd' => 101, 'e' => 100,
			'f' => 69, 'g' => 73, 'h' => 99, 'i' => 63, 'j' => 94,
			'k' => 93, 'l' => 39, 'm' => 37, 'n' => 61, 'o' => 48,
			'p' => 58, 'q' => 113, 'r' => 32, 's' => 90, 't' => 44,
			'u' => 98, 'v' => 60, 'w' => 51, 'x' => 33, 'y' => 97,
			'z' => 62, '_' => 56);
			
		$CryptPW = '';
		for ($i=0; $i<strlen($ClearPW); $i++) {
			$ch = substr($ClearPW, $i, 1);
			$CryptPW .= chr($NewChars[$ch]);
		}
		return $CryptPW;
	}
	
	function ConnectTcpAndLogon() {
		// Report All Errors back to this script.
		error_reporting(E_ALL);
		
		// Get the TCP Port.
		$CVSPort = getservbyname('cvs', 'tcp');
		
		// Create the Socket to communicate through.
		$this->SOCKET_HANDLE = fsockopen($this->CVS_PSERVER, 2401);		

		// Check and see if we have a socket.
		if ($this->SOCKET_HANDLE < 0) {
			return false;
		}		
		
		// Could we connect.
		if ($this->SOCKET_HANDLE < 1) {
			return false;
		}
		
		// Cool, we have a valid connection to the PServer, so we can now 
		// authenticate with the server.
		
		// Encrypt the password.
		$CPassword = "A".$this->EncodePW($this->CVS_PASSWORD);
		
		// Build the string to send to the PServer.
		$Request = "BEGIN AUTH REQUEST\n".$this->CVS_REPOSITORY."\n";
		$Request .= $this->CVS_USERNAME."\n".$CPassword."\n";
		$Request .= "END AUTH REQUEST\n";
		
		// Send this command to the backend PServer.
		fputs($this->SOCKET_HANDLE, $Request);
		fflush($this->SOCKET_HANDLE);
		
		// Read the response to see if our credentials were OK.
		$Response = fgets($this->SOCKET_HANDLE, 11);

		// Generate the return value.
		if (strcmp($Response, "I LOVE YOU") == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	function DisconnectTcp() {
		// Close off the socket to the PServer.
		fclose($this->SOCKET_HANDLE);
		$this->SOCKET_HANDLE = -1;
	}
	
	function SendRoot() {
		if ($this->SOCKET_HANDLE > 0) {
			$SendCMD = "Root ".$this->CVS_REPOSITORY."\n";
		    fputs($this->SOCKET_HANDLE, $SendCMD);
			return true;
		}
		return false;
	}
	
	function SendValidResponses() {
		if ($this->SOCKET_HANDLE > 0) {
			// Build the String to send out the socket.
			$SendCMD = "Valid-responses ok error Valid-requests Checked-in New-entry";
			$SendCMD .= " Checksum Copy-file Updated Created Update-existing Merged";
			$SendCMD .= " Patched Rcs-diff Mode Mod-time Removed Remove-entry";
			$SendCMD .= " Set-static-directory Clear-static-directory Set-sticky";
			$SendCMD .= " Clear-sticky Template Set-checkin-prog Set-update-prog";
			$SendCMD .= " Notified Module-expansion Wrapper-rcsOption M Mbinary E F MT\n";
			
			// Send the command to the backend PServer.
		    fputs($this->SOCKET_HANDLE, $SendCMD);
			
			// return success.
			return true;
		}
		return false;
	}
	
	function SendValidRequests() {
		if ($this->SOCKET_HANDLE > 0) {
		    // Build the String to send out the socket.
			$SendCMD = "valid-requests\n";
			
			// Send the Command to the backend PServer.
			fputs($this->SOCKET_HANDLE, $SendCMD);
			
			// OK, Now wait for the response from the server.
			$RecvCMD = fgets($this->SOCKET_HANDLE, 8192);
			
			// Clear off the OK message from the buffer.
			$dummy = fgets($this->SOCKET_HANDLE);
			
			// Lets transfer the allowable messages into our message above.
			// Although at this stage I will skip this and add it in later.
			
			return true;
		}
		return false;
	}
	
	function RLOGDir($Module = "/") {
		$SeenThis = "";
		$Elements = array();
		if ($this->SOCKET_HANDLE > -1) {
			$SendCMD = "UseUnchanged\nCase\nArgument $Module\nrlog\n";
			
			// Send this command to the PServer.
			fputs($this->SOCKET_HANDLE, $SendCMD);
			
			// Lets start receiving the response from the PServer.
			$RecvLN = "";
			while(strncmp($RecvLN, "ok", 2) != 0){
				$RecvLN = fgets($this->SOCKET_HANDLE);
				
				// Determine if it is local dir or a subdir.
				if (strncmp($RecvLN, "M \n", 3) == 0) {
				    $FileName = fgets($this->SOCKET_HANDLE, 12+strlen($this->CVS_REPOSITORY.$Module));
					if (strncmp($FileName, "M RCS file", 10) == 0) {
						$FileName = fgets($this->SOCKET_HANDLE, 8192);
						
						if (strpos($FileName, '/') > 0) {
						    // This is a Directory, and not a file.
							$DirName = substr($FileName, 0, strpos($FileName, '/')+1);
							if (strpos($SeenThis, $DirName) === false) {
								$Elements[$DirName] = "DIR";
								$SeenThis .= $DirName;
							} // End of if (strpos($SeenThis, $DirName) === false)
						} else {
							// This is a file.
							$FileName2 = substr($FileName, 0, strlen($FileName)-3);
							$Elements[$FileName2]["FILENAME"] = $FileName2;
							while(strpos($RecvLN, "M ======") === false){
								$RecvLN = fgets($this->SOCKET_HANDLE, 8192);
								if (strncmp($RecvLN, "M head:", 7) == 0) {
								    // This contains the HEAD revision.
									$Elements[$FileName2]["HEAD"] = substr($RecvLN, 8, strlen($RecvLN)-9);
								} // End of if (strncmp($RecvLN, "M head:", 7) == 0)
								if (strncmp($RecvLN, "M branch:", 9) == 0) {
								    // This contains the Branch revision.
									$Elements[$FileName2]["BRANCH"] = substr($RecvLN, 10, strlen($RecvLN)-10);
								} // End of if (strncmp($RecvLN, "M branch:", 9) == 0)
								if (strncmp($RecvLN, "M -----", 7) == 0) {
								    // This subsection contains the description and other revision based information.
									while(strncmp($RecvLN, "M revision ", 11) != 0) {
										$RecvLN = fgets($this->SOCKET_HANDLE, 8192);
									} // End of while(strncmp($RecvLN, "M revision ", 11) != 0)
									$Rev = substr($RecvLN, 11, strlen($RecvLN)-11);
									$HeadRev = $Elements[$FileName2]["HEAD"];
									if (strcmp(trim($Rev), trim($HeadRev)) == 0) {
									    $RecvLN = fgets($this->SOCKET_HANDLE, 8192);
										$Elements[$FileName2]["DATE"] = strtotime(substr($RecvLN, 8, 19));
										$RecvLN = substr($RecvLN, 38, strLen($RecvLN)-38);
										$Elements[$FileName2]["AUTHOR"] = substr($RecvLN, 0, strpos($RecvLN, ";"));
										$Elements[$FileName2]["LOG"] = "";
										$RecvLN = fgets($this->SOCKET_HANDLE, 8192);
										while((strpos($RecvLN, "M ======") === false) && (strpos($RecvLN, "M ------") === false)){
											if (strlen(trim($RecvLN)) > 1) {
												if (strncmp($RecvLN, "M branches:", 11) != 0) {
													$Elements[$FileName2]["LOG"] .= substr($RecvLN, 2, strlen($RecvLN)-1);
												} // End of if (strncmp($RecvLN, "M branches:", 11) == 0)
											} // End of if (strlen(trim($RecvLN)) > 1)
											$RecvLN = fgets($this->SOCKET_HANDLE, 8192);
										} // End of while ((strpos($RecvLN, "M ======") === false) && (strpos($RecvLN, "M ------") === false))
									} // End of if (strcmp(trim($Rev), trim($HeadRev)) == 0)
								} // End of if (strncmp($RecvLN, "M -----", 7) == 0)
							} // End of while(strpos($RecvLN, "M ======") === false)
						} // End of if (strpos($FileName, '/') > 0)
					} // End of if (strncmp($FileName, "M RCS file", 10) == 0)
				} // End of if (strncmp($RecvLN, "M \n", 3) == 0)
			} // End of while(strncmp($RecvLN, "ok", 2) != 0)
		} // End of if ($this->SOCKET_HANDLE > -1)
		return $Elements;
	} // End of function RLOGDir().
	
	function RLOGFile($File, $Module = "/") {
		$SeenThis = "";
		$Elements = array();
		if ($this->SOCKET_HANDLE > -1) {
			$SendCMD = "UseUnchanged\nCase\nArgument ".$Module.$File."\nrlog\n";
			
			// Send this command to the PServer.
			fputs($this->SOCKET_HANDLE, $SendCMD);
			
			// Lets start receiving the response from the PServer.
			$RecvLN = "";
			$CurrentRevision = "";
			$RevisionCounter = 0;
			$Elements["CODE"] = "";
			while(strncmp($RecvLN, "ok", 2) != 0){
				// if we have a line beginning with 'M revision' then this is a new revision.
				if (strncmp("M revision", $RecvLN, 10) == 0) {
				    // New Revision details.
					$RevisionCounter = $RevisionCounter + 1;
					$CurrentRevision = substr($RecvLN, 11, strlen($RecvLN)-11);
					$Elements[$RevisionCounter]["Revision"] = $CurrentRevision;
					$RecvLN = fgets($this->SOCKET_HANDLE);
					
					// This line contains the Date, Author, State, and Line Counts.
					strtok($RecvLN, " :;\n\t");
					strtok(" :;\t\n");
					$Elements[$RevisionCounter]["Date"] = strtok(" ;\n\t");
					$Elements[$RevisionCounter]["Time"] = strtok(" ;\n\t");
					strtok(" :;\n\t");
					$Elements[$RevisionCounter]["Author"] = strtok(" :;\n\t");
					strtok(" :;\n\t");
					$Elements[$RevisionCounter]["State"] = strtok(" :;\n\t");
					strtok(" :;\n\t");
					$Elements[$RevisionCounter]["LinesAdd"] = substr(strtok(" :;\n\t"), 1);
					$Elements[$RevisionCounter]["LinesSub"] = substr(strtok(" :;\n\t"), 1);
					
					// The following lines up until we get minuses or equals is the revision log.
					$RecvLN = fgets($this->SOCKET_HANDLE);
					$Elements[$RevisionCounter]["Log"] = "";
					while((strncmp("M ----------------------------", $RecvLN, 30) != 0) &&
					      (strncmp("M ============================", $RecvLN, 30) != 0)){
						// Add the text to the log.
						$Elements[$RevisionCounter]["Log"] .= substr($RecvLN, 2);
						$RecvLN = fgets($this->SOCKET_HANDLE);
					} // while
				}
			
				// if we have a line beginning with 'M RCS file' then we have the full file system path of the file.
				if (strncmp("M RCS file", $RecvLN, 10) == 0) {
				    // Pull out the Full RCS filename.
					strtok($RecvLN, " :\t\n");
					strtok(" :\t\n");
					strtok(" :\t\n");
					$Elements[0]["RCSFile"] = strtok(" :\t\n");
				}
				
				// if we have a line beginning with 'M head' then we have the head revision number for this file.
				if (strncmp("M head", $RecvLN, 6) == 0) {
				    // Pull out the head revision.
					strtok($RecvLN, " :\t\n");
					strtok(" :\t\n");
					$Elements[0]["HeadRev"] = strtok(" :\t\n");
				}
				
				// if we have a line beginning with 'M branch' then we have the name of the head branch.
				if (strncmp("M branch", $RecvLN, 8) == 0) {
				    // Pull out the head branch.
					strtok($RecvLN, " :\t\n");
					strtok(" :\t\n");
					$Elements[0]["HeadBranch"] = strtok(" :\t\n");
				}
				
				// if we have a line beginning with 'M locks' then we have the locking scheme in use by this CVS server.
				if (strncmp("M locks", $RecvLN, 7) == 0) {
					// Pull out the locking methodology.
				    strtok($RecvLN, " :\t\n");
					strtok(" :\t\n");
					$Elements[0]["Locks"] = strtok(" :\t\n");
				}
				
				// if we have a line beginning with 'M access list' then we have the current accessing list.
				if (strncmp("M access list", $RecvLN, 13) == 0) {
				    // Pull out the accessing list.
					strtok($RecvLN, " :\t\n");
					strtok(" :\t\n");
					strtok(" :\t\n");
					$Elements[0]["AccessList"] = strtok(" :\t\n");
				}
				
				// if we have a line beginning with 'M symbolic names' then we have the list of symbolic names for this file.
				if (strncmp("M symbolic names", $RecvLN, 16) == 0) {
				    // Pull out the symbolic names.
					strtok($RecvLN, " :\t\n");
					strtok(" :\t\n");
					strtok(" :\t\n");
					$Elements[0]["SymNames"] = strtok(" :\t\n");
				}
				
				// if we have a line beginning with 'M keyword substitution' then we have the keywork substitutions.
				if (strncmp("M keywork substitutions", $RecvLN, 23) == 0) {
				    strtok($RecvLN, " :\t\n");
					strtok(" :\t\n");
					strtok(" :\t\n");
					$Elements[0]["KeywrdSubst"] = strtok(" :\t\n");
				}
				
				$Elements["CODE"] .= substr($RecvLN, 2, strlen($RecvLN)-2);
				$RecvLN = fgets($this->SOCKET_HANDLE);
			} // End of while(strncmp($RecvLN, "ok", 2) != 0)
			$Elements[0]["TotalRevisions"] = $RevisionCounter;
		} // End of if ($this->SOCKET_HANDLE > -1)
		return $Elements;
	} // End of function RLOGFile();
	
	function ViewFile($File, $Revision, $Module="/") {
		// Here we will export a copy of a given file, returning a series of "Strings".
		if (strncmp($Module, "/", 1) == 0) {
		    $Module = substr($Module, 1, strlen($Module)-1);
		}
		
		$SendCMD = "UseUnchanged\nCase\nArgument ".$Module.$File."\nDirectory .\n".$this->CVS_REPOSITORY."\nexpand-modules\n";
		fputs($this->SOCKET_HANDLE, $SendCMD);
		
		$RecvLN = "ABCD";
		while(strncmp($RecvLN, "ok", 2) != 0){
			$RecvLN = fgets($this->SOCKET_HANDLE);
		} // End of while(strncmp($RecvLN, "ok", 2) != 0)

		// Send the checkout command.
		$SendCMD = "Argument -n\nArgument -l\nArgument -N\nArgument -P\nArgument -r\nArgument ".$Revision."\nArgument ".$Module.$File."\nDirectory .\n".$this->CVS_REPOSITORY."\nco\n";
		fputs($this->SOCKET_HANDLE, $SendCMD);
		
		// Clear out the Return Elements, in preparation for returning the information.
		$Elements = "";
		
		$RecvLN = "";
		while(strncmp($RecvLN, "ok", 2) != 0){
			if (strncmp($RecvLN, "Clear-sticky ", 13) == 0) {
				$RecvLN = fgets($this->SOCKET_HANDLE, 8192);
			}
			if (strncmp($RecvLN, "Set-static-directory ", 21) == 0) {
				$RecvLN = fgets($this->SOCKET_HANDLE, 8192);
			}
			if (strncmp($RecvLN, "Mod-time", 8) == 0) {
			    // We had the Date and time this revision was modified.
				$Elements["DATETIME"] = substr($RecvLN, 9, strlen($RecvLN)-9);
				$RecvLN = fgets($this->SOCKET_HANDLE);
				while(strncmp($RecvLN, "MT", 2) == 0){
					$RecvLN = fgets($this->SOCKET_HANDLE);
					if (strncmp($RecvLN, "MT -updated", 11) == 0) {
					    $RecvLN = "";
					}
				} // while
			}
			if (strncmp($RecvLN, "Created", 7) == 0) {
				
			    // We are getting the file from the Server.
				$RecvLN = fgets($this->SOCKET_HANDLE);
				$RecvLN = fgets($this->SOCKET_HANDLE);
				$RecvLN = fgets($this->SOCKET_HANDLE);
				$RecvLN = fgets($this->SOCKET_HANDLE);

				// RecvLN Holds the length of the file as a string
				$TotalBytes = $RecvLN + 0;
				$Elements["CONTENT"] = fread($this->SOCKET_HANDLE, $TotalBytes);
			}
			$RecvLN = fgets($this->SOCKET_HANDLE);			
		} // End of while(strncmp($RecvLN, "ok", 2) != 0)
		
		return $Elements;
	}
	
	function CVSLogon() {
		// Here we will login to the CVS PServer (or local filesystem if the
		// CVSPSERVER variable is a blank string).
		if ($this->CVS_PSERVER == '') {
		    // We are logging into a locally connected filesystem.
		} else {
			// We are logging into a remote PServer repository.
		}
	}
}

?>
