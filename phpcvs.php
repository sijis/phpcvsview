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

class phpcvs {
	var $CVS_REPOSITORY;		// Storage of the CVS Repository file system path.
	var $CVS_USERNAME;			// Username to use when authenticating with the PServer.
	var $CVS_PASSWORD;			// Password for the account above.
	var $CVS_PSERVER;			// Hostname of the server running the PServer.
	var $CVS_VALID_REQUESTS;	// List of valid requests the PServer accepts.

	var $SOCKET_HANDLE;			// The socket handle for communicating with the PServer.
	
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
		error_reporting(E_ALL);
		$this->SOCKET_HANDLE = fsockopen($this->CVS_PSERVER, 2401);		
		if ($this->SOCKET_HANDLE < 0) {
			return false;
		} // End of if ($this->SOCKET_HANDLE < 0) 
		if ($this->SOCKET_HANDLE < 1) {
			return false;
		} // End of if ($this->SOCKET_HANDLE < 1) 
		$CPassword = "A".$this->EncodePW($this->CVS_PASSWORD);
		$Request = "BEGIN AUTH REQUEST\n".$this->CVS_REPOSITORY."\n";
		$Request .= $this->CVS_USERNAME."\n".$CPassword."\n";
		$Request .= "END AUTH REQUEST\n";
		fputs($this->SOCKET_HANDLE, $Request);
		fflush($this->SOCKET_HANDLE);
		$Response = fgets($this->SOCKET_HANDLE, 11);
		if (strcmp($Response, "I LOVE YOU") == 0) {
			return true;
		} else { // Else of if (strcmp($Response, "I LOVE YOU") == 0)
			return false;
		} // End of if (strcmp($Response, "I LOVE YOU") == 0)
	} // End of function ConnectTcpAndLogon() {
	
	function DisconnectTcp() {
		fclose($this->SOCKET_HANDLE);
		$this->SOCKET_HANDLE = -1;
	} // End of function DisconnectTcp()
	
	function SendRoot() {
		if ($this->SOCKET_HANDLE > 0) {
			$SendCMD = "Root ".$this->CVS_REPOSITORY."\n";
		    fputs($this->SOCKET_HANDLE, $SendCMD);
			return true;
		} // End of if ($this->SOCKET_HANDLE > 0)
		return false;
	} // End of function SendRoot()
	
	function SendValidResponses() {
		if ($this->SOCKET_HANDLE > 0) {
			$SendCMD = "Valid-responses ok error Valid-requests Checked-in New-entry";
			$SendCMD .= " Checksum Copy-file Updated Created Update-existing Merged";
			$SendCMD .= " Patched Rcs-diff Mode Mod-time Removed Remove-entry";
			$SendCMD .= " Set-static-directory Clear-static-directory Set-sticky";
			$SendCMD .= " Clear-sticky Template Set-checkin-prog Set-update-prog";
			$SendCMD .= " Notified Module-expansion Wrapper-rcsOption M Mbinary E F MT\n";
		    fputs($this->SOCKET_HANDLE, $SendCMD);
			return true;
		} // End of if ($this->SOCKET_HANDLE > 0)
		return false;
	} // End of function SendValidResponses()
	
	function SendValidRequests() {
		if ($this->SOCKET_HANDLE > 0) {
			$SendCMD = "valid-requests\n";
			fputs($this->SOCKET_HANDLE, $SendCMD);
			$RecvCMD = fgets($this->SOCKET_HANDLE, 8192);
			$dummy = fgets($this->SOCKET_HANDLE);
			return true;
		} // End of if ($this->SOCKET_HANDLE > 0)
		return false;
	} // End of function SendValidRequests()
	
	function RLOGDir($Module = "/") {
		$SeenThis = "";
		$Elements = array();
		if ($this->SOCKET_HANDLE > -1) {
			$SendCMD = "UseUnchanged\nCase\nArgument $Module\nrlog\n";
			fputs($this->SOCKET_HANDLE, $SendCMD);
			$RecvLN = "";
			while(strncmp($RecvLN, "ok", 2) != 0){
				$RecvLN = fgets($this->SOCKET_HANDLE);
				if (strncmp($RecvLN, "M \n", 3) == 0) {
				    $FileName = fgets($this->SOCKET_HANDLE, 12+strlen($this->CVS_REPOSITORY.$Module));
					if (strncmp($FileName, "M RCS file", 10) == 0) {
						$FileName = fgets($this->SOCKET_HANDLE, 8192);
						if (strpos($FileName, '/') > 0) {
							$DirName = substr($FileName, 0, strpos($FileName, '/')+1);
							if (strpos($SeenThis, $DirName) === false) {
								$Elements[$DirName] = "DIR";
								$SeenThis .= $DirName;
							} // End of if (strpos($SeenThis, $DirName) === false)
						} else { // Else of if (strpos($FileName, '/') > 0)
							$FileName2 = substr($FileName, 0, strlen($FileName)-3);
							$Elements[$FileName2]["FILENAME"] = $FileName2;
							while(strpos($RecvLN, "M ======") === false){
								$RecvLN = fgets($this->SOCKET_HANDLE, 8192);
								if (strncmp($RecvLN, "M head:", 7) == 0) {
									$Elements[$FileName2]["HEAD"] = substr($RecvLN, 8, strlen($RecvLN)-9);
								} // End of if (strncmp($RecvLN, "M head:", 7) == 0)
								if (strncmp($RecvLN, "M branch:", 9) == 0) {
									$Elements[$FileName2]["BRANCH"] = substr($RecvLN, 10, strlen($RecvLN)-10);
								} // End of if (strncmp($RecvLN, "M branch:", 9) == 0)
								if (strncmp($RecvLN, "M -----", 7) == 0) {
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
			fputs($this->SOCKET_HANDLE, $SendCMD);
			$RecvLN = "";
			$CurrentRevision = "";
			$RevisionCounter = 0;
			$Elements["CODE"] = "";
			while(strncmp($RecvLN, "ok", 2) != 0){
				if (strncmp("M revision", $RecvLN, 10) == 0) {
					$RevisionCounter = $RevisionCounter + 1;
					$CurrentRevision = substr($RecvLN, 11, strlen($RecvLN)-11);
					$Elements[$RevisionCounter]["Revision"] = $CurrentRevision;
					$RecvLN = fgets($this->SOCKET_HANDLE);
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
					$RecvLN = fgets($this->SOCKET_HANDLE);
					while (strncmp("M branches", $RecvLN, 10) == 0) {
						$Elements[$RevisionCounter]["Branches"] = substr($RecvLN, 11, strlen($RecvLN)-11);
					    $RecvLN = fgets($this->SOCKET_HANDLE);
					} // End of while (strncmp("M branches", $RecvLN, 10) == 0)
					$Elements[$RevisionCounter]["Log"] = "";
					while((strncmp("M ----------------------------", $RecvLN, 30) != 0) &&
					      (strncmp("M ============================", $RecvLN, 30) != 0)){
						$Elements[$RevisionCounter]["Log"] .= substr($RecvLN, 2);
						$RecvLN = fgets($this->SOCKET_HANDLE);
					} // End of while looking for end of section (revision)
				} // End of if (strncmp("M revision", $RecvLN, 10) == 0)
				if (strncmp("M RCS file", $RecvLN, 10) == 0) {
					strtok($RecvLN, " :\t\n");
					strtok(" :\t\n");
					strtok(" :\t\n");
					$Elements[0]["RCSFile"] = strtok(" :\t\n");
				} // End of if (strncmp("M RCS file", $RecvLN, 10) == 0)
				if (strncmp("M head", $RecvLN, 6) == 0) {
					strtok($RecvLN, " :\t\n");
					strtok(" :\t\n");
					$Elements[0]["HeadRev"] = strtok(" :\t\n");
				} // End of if (strncmp("M head", $RecvLN, 6) == 0)
				if (strncmp("M branch", $RecvLN, 8) == 0) {
					strtok($RecvLN, " :\t\n");
					strtok(" :\t\n");
					$Elements[0]["HeadBranch"] = strtok(" :\t\n");
				} // End of if (strncmp("M branch", $RecvLN, 8) == 0)
				if (strncmp("M locks", $RecvLN, 7) == 0) {
				    strtok($RecvLN, " :\t\n");
					strtok(" :\t\n");
					$Elements[0]["Locks"] = strtok(" :\t\n");
				} // End of if (strncmp("M locks", $RecvLN, 7) == 0)
				if (strncmp("M access list", $RecvLN, 13) == 0) {
					strtok($RecvLN, " :\t\n");
					strtok(" :\t\n");
					strtok(" :\t\n");
					$Elements[0]["AccessList"] = strtok(" :\t\n");
				} // End of if (strncmp("M access list", $RecvLN, 13) == 0)
				if (strncmp("M symbolic names", $RecvLN, 16) == 0) {
					strtok($RecvLN, " :\t\n");
					strtok(" :\t\n");
					strtok(" :\t\n");
					$Elements[0]["SymNames"] = strtok(" :\t\n");
				} // End of if (strncmp("M symbolic names", $RecvLN, 16) == 0)
				if (strncmp("M keywork substitutions", $RecvLN, 23) == 0) {
				    strtok($RecvLN, " :\t\n");
					strtok(" :\t\n");
					strtok(" :\t\n");
					$Elements[0]["KeywrdSubst"] = strtok(" :\t\n");
				} // End of if (strncmp("M keywork substitutions", $RecvLN, 23) == 0)
				$RecvLN = fgets($this->SOCKET_HANDLE);
			} // End of while(strncmp($RecvLN, "ok", 2) != 0)
			$Elements[0]["TotalRevisions"] = $RevisionCounter;
		} // End of if ($this->SOCKET_HANDLE > -1)
		return $Elements;
	} // End of function RLOGFile();
	
	function ViewFile($File, $Revision, $Module="/") {
		if (strncmp($Module, "/", 1) == 0) {
		    $Module = substr($Module, 1, strlen($Module)-1);
		} // End of if (strncmp($Module, "/", 1) == 0)
		$SendCMD = "UseUnchanged\nCase\nArgument ".$Module.$File."\nDirectory .\n".$this->CVS_REPOSITORY."\nexpand-modules\n";
		fputs($this->SOCKET_HANDLE, $SendCMD);
		$RecvLN = "ABCD";
		while(strncmp($RecvLN, "ok", 2) != 0){
			$RecvLN = fgets($this->SOCKET_HANDLE);
		} // End of while(strncmp($RecvLN, "ok", 2) != 0)
		$SendCMD = "Argument -n\nArgument -l\nArgument -N\nArgument -P\nArgument -r\nArgument ".$Revision."\nArgument ".$Module.$File."\nDirectory .\n".$this->CVS_REPOSITORY."\nco\n";
		fputs($this->SOCKET_HANDLE, $SendCMD);
		$Elements = "";
		$RecvLN = "";
		while(strncmp($RecvLN, "ok", 2) != 0){
			if (strncmp($RecvLN, "Clear-sticky ", 13) == 0) {
				$RecvLN = fgets($this->SOCKET_HANDLE, 8192);
			} // End of if (strncmp($RecvLN, "Clear-sticky ", 13) == 0)
			if (strncmp($RecvLN, "Set-static-directory ", 21) == 0) {
				$RecvLN = fgets($this->SOCKET_HANDLE, 8192);
			} // End of if (strncmp($RecvLN, "Set-static-directory ", 21) == 0)
			if (strncmp($RecvLN, "Mod-time", 8) == 0) {
				$Elements["DATETIME"] = substr($RecvLN, 9, strlen($RecvLN)-9);
				$RecvLN = fgets($this->SOCKET_HANDLE);
				while(strncmp($RecvLN, "MT", 2) == 0){
					$RecvLN = fgets($this->SOCKET_HANDLE);
					if (strncmp($RecvLN, "MT -updated", 11) == 0) {
					    $RecvLN = "";
					} // End of if (strncmp($RecvLN, "MT -updated", 11) == 0)
				} // End of while(strncmp($RecvLN, "MT", 2) == 0)
			} // End of if (strncmp($RecvLN, "Mod-time", 8) == 0)
			if (strncmp($RecvLN, "Created", 7) == 0) {
				$RecvLN = fgets($this->SOCKET_HANDLE);
				$RecvLN = fgets($this->SOCKET_HANDLE);
				$RecvLN = fgets($this->SOCKET_HANDLE);
				$RecvLN = fgets($this->SOCKET_HANDLE);
				$TotalBytes = $RecvLN + 0;
				$Elements["CONTENT"] = fread($this->SOCKET_HANDLE, $TotalBytes);
			} // End of if (strncmp($RecvLN, "Created", 7) == 0)
			$RecvLN = fgets($this->SOCKET_HANDLE);			
		} // End of while(strncmp($RecvLN, "ok", 2) != 0)
		return $Elements;
	} // End of function ViewFile($File, $Revision, $Module="/")
	
	function CVSLogon() {
		if ($this->CVS_PSERVER == '') {
		    // We are logging into a locally connected filesystem.
		} else {
			// We are logging into a remote PServer repository.
		}
	}
}

?>
