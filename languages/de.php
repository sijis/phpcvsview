<?php

/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: German translation file.
 *
 * @author Sijis Aviles <sijis@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2005 Brian A Cheeseman
 **/

$lang['encoding']                = 'iso-8859-16'; //needed for german umlauts, otherwise ä has to be ae, ö has to be oe, ü has to be ue

// listing options
$lang['file']                    = 'Datei';
$lang['rev']                     = 'Rev.';
$lang['age']                     = 'Alter';
$lang['author']                  = 'Autor';
$lang['last_log']                = 'Letzter Logeintrag';

// customization options
$lang['language']                = 'Deutsch';
$lang['theme']                   = 'Theme ändern: ';

// quick link bar listing
$lang['change_theme']        	 = 'Theme ändern: ';
$lang['change_lang']             = 'Sprache: ';
$lang['change_cvsroot']          = 'CVS Repository: ';
$lang['up_folder']               = 'Einen Ordner nach oben';
$lang['rev_history']             = 'Revisionsgeschichte für: ';
$lang['rev_diff']                = 'Revisionunterschied für: ';
$lang['code_view']               = 'Codeansicht für: ';
$lang['navigate_to']             = 'Navigieren zu: ';
$lang['file_ext']                = 'Dateiendung: ';
$lang['annotate_history']        = 'Kommentargeschichte für: ';
$lang['mime_type']               = 'Mime-Type ist: ';

// time and date related
$lang['year']                    = 'Jahr';
$lang['years']                   = 'Jahre';
$lang['week']                    = 'Woche';
$lang['weeks']                   = 'Wochen';
$lang['day']                     = 'Tag';
$lang['days']                    = 'Tage';
$lang['hour']                    = 'Stunde';
$lang['hours']                   = 'Stunden';
$lang['minute']                  = 'Minute';
$lang['minutes']                 = 'Minuten';
$lang['second']                  = 'Sekunde';
$lang['seconds']                 = 'Sekunden';
$lang['ago']                     = 'her'; // if meant "Last change one hour ago" this translation is okay. But the best thing here would be: Letzte (last) Änderung (change) vor(ago) 1 Stunde (hour). If you mean something other please contact me again

// navigation path
$lang['root']                    = 'Root';

// top message
$lang['message']                 = '<p>Willkommen beim CVS Repository viewing system für das phpCVSView Projekt gehosted von SourceForge.net</p><p>Ziel des Projektes ist es, eine php Anwendung/Klasse zu erstellen, die den Zugang zu CVS basierten Quellcoderepositorien über die unterschiedlichen CVS Verbindungsmechanismen herstellt. Es sind auch weitere Erweiterungen für zukünftige Releases geplant, so zum Beispiel ein Web basierter CVS Client.</p><p>Ihr seid eingeladen, Euch unseren Code anzuschauen, neue Features vorzuschlagen, den Code in Eurer eigenen Umgebung zu testen, Bugs zu melden onment, submit bugs, und natürlich das Engagement der Open Source Entwickler durch Nutzung der vielen wunderbaren Produkte zu unterstützen.</p><p>Mit freundlichen Grüßen,<br />Brian Cheeseman. <br />phpCVSView Project Leader.</p>';

// bottom messages
$lang['generated']               = 'Diese Seite wurde erstellt von <a href="http://phpcvsview.sourceforge.net/">phpCVSView</a> in ';
$lang['created_by']              = 'phpCVSView erstellt von <a href="mailto:bcheesem@users.sourceforge.net">Brian Cheeseman</a> und <a href="mailto:sijis@users.sourceforge.net">Sijis Aviles</a>.';

// file options
$lang['view']                    = 'ansehen';
$lang['download']                = 'herunterladen';
$lang['diff']                    = 'Unterschied zum vorhergehenden';
$lang['annotate']                = 'Anmerkung';

// file details
$lang['revision']                = 'Revision';
$lang['last_checkin']            = 'Letzter Checkin:';
$lang['notice']                  = 'Notiz:';
$lang['branch']                  = 'Branch:';
$lang['date']                    = 'Datum:';
$lang['time']                    = 'Zeit:';
$lang['state']                   = 'State:';
$lang['changes']                 = 'Änderungen seit ';
$lang['log_message']             = 'Log Nachricht:';

// error messages
$lang['err_connect']             = '<h2>FEHLER: Verbindung zum PServer fehlgeschlagen.</h2>'."\n";
$lang['err_get_rev']             = '<h3>FEHLER: Empfange Revision für Datei:</h3>';

?>