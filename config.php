<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To store the configuration for this instance of phpCVSView
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2004 Brian A Cheeseman
 **/

// The CVSROOT path to access. For sourceforge you need the usual expansion 
// of the path based on the project name.
$CVSROOT = "/cvsroot/phpcvsview";

// The hostname (or IP Address) of the server providing the PServer services.
$PServer = "cvs.sourceforge.net";

// The username to pass to the PServer for authentication purposes.
$UserName = "anonymous";

// The password associated with the username above for authentication process.
$Password = "";

// The HTMLTitle and HTMLHeading are used purely for the generation of the 
// resultant web pages.
$HTMLTitle = "phpCVSView Source Code Library";
$HTMLHeading = "phpCVSView Source Code Library";

$HTMLTblHdBg  = "#CCCCCC";
$HTMLTblCell1 = "#FFFFFF";
$HTMLTblCell2 = "#CCCCEE";

?>