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
	
	function phpcvs(	$Repository = '', 
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
				    $FileName = fgets($this->SOCKET_HANDLE, 13+strlen($this->CVS_REPOSITORY.$Module));
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
			while(strncmp($RecvLN, "ok", 2) != 0){
				$Elements .= substr($RecvLN, 2, strlen($RecvLN)-2);
			} // End of while(strncmp($RecvLN, "ok", 2) != 0)
		} // End of if ($this->SOCKET_HANDLE > -1)
		return $Elements;
	} // End of function RLOGFile();
	
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
