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

// The CVSROOT path to access as it is on the server, ie for this projects
// repository the value should be "/cvsroot/phpcvsview"
$config['cvsroot'] = "/cvsroot/phpcvsview";

// The hostname (or IP Address) of the server providing the PServer services.
$config['pserver'] = "cvs.sourceforge.net";

// The username to pass to the PServer for authentication purposes.
$config['username'] = "anonymous";

// The password associated with the username above for authentication process.
$config['password'] = "";

// Setup whether to use GeSHi project code for syntax highlighting or not.
$config['UseGeSHi'] = true;
$config['GeSHiPath'] = "geshi";
$config['GeSHiHighlightersPath'] = "geshi/geshi";

?>
