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
$config['cvsroot'] = "/cvsroot/phpcvsview";

// The hostname (or IP Address) of the server providing the PServer services.
$config['pserver'] = "cvs.sourceforge.net";

// The username to pass to the PServer for authentication purposes.
$config['username'] = "anonymous";

// The password associated with the username above for authentication process.
$config['password'] = "";

// The HTMLTitle and HTMLHeading are used purely for the generation of the
// resultant web pages.
$config['html_title'] = "phpCVSView Source Code Library";
$config['html_header'] = "phpCVSView Source Code Library";

$config['html_tbl_head_bg'] = "#CCCCCC";
$config['html_tbl_cell_one'] = "#FFFFFF";
$config['html_tbl_cell_two'] = "#CCCCEE";

// Setup whether to use GeSHi project code for syntax highlighting or not.
$config['UseGeSHi'] = true;
$config['GeSHiPath'] = "geshi";
$config['GeSHiHighlightersPath'] = "geshi/geshi";

?>
