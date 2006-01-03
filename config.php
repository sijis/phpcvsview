<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: To store the configuration for this instance of phpCVSView
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2006 Brian A Cheeseman
 **/

// CVSROOT configuration.
/* phpCVSView Source Repository */
$config['cvs']['phpCVSView']['server']		= "cvs.sourceforge.net";
$config['cvs']['phpCVSView']['cvsroot']		= "/cvsroot/phpcvsview";
$config['cvs']['phpCVSView']['username']	= "anonymous";
$config['cvs']['phpCVSView']['password']	= "";
$config['cvs']['phpCVSView']['mode']		= "pserver";
$config['cvs']['phpCVSView']['description']	= "PHP based CVS Repository Viewer";
$config['cvs']['phpCVSView']['html_title']	= "phpCVSView Source Code Library";
$config['cvs']['phpCVSView']['html_header']	= "phpCVSView Source Code Library";

/* phpCVSView Source Repository */
$config['cvs']['RPak']['server']			= "bcheese.homeip.net";
$config['cvs']['RPak']['cvsroot']			= "/cvsroot/mstsrpak";
$config['cvs']['RPak']['username']			= "anonymous";
$config['cvs']['RPak']['password']			= "";
$config['cvs']['RPak']['mode']				= "pserver";
$config['cvs']['RPak']['description']		= "MS Train Sim Route Packaging System";
$config['cvs']['RPak']['html_title']		= "RPak Source Code Library";
$config['cvs']['RPak']['html_header']		= "RPak Source Code Library";

// Default CVSROOT configuration to use.
$config['default_cvs'] = "phpCVSView";

// The default theme
$config['theme'] = "Default";

// Setup whether to use GeSHi project code for syntax highlighting or not.
$config['GeSHi']['Enable']				= true;
$config['GeSHi']['Path']				= "geshi";
$config['GeSHi']['HighlightersPath']	= "geshi/geshi";

// The default language
$config['language'] = "en";

// Settings for TAR creation.
$config['TempFileLocation'] = "/tmp";

// Settings for Output Cache.
$config['Cache']['Enable']		= true;
$config['Cache']['Location']	= "/tmp/phpCVSViewCache";

?>
