/**
 * This source code is distributed under the terms as layed out in the
 * GNU General Public License.
 *
 * Purpose: Provides theming and validation support
 *
 * @author Brian A Cheeseman <bcheesem@users.sourceforge.net>
 * @version $Id$
 * @copyright 2003-2006 Brian A Cheeseman
 **/

function postBackThemeChange(form)
{
	var ddTheme		= form.ThemeSelect.value;
	var hfRequest	= form.URLRequest.value;

	if (hfRequest.indexOf("?") == -1) {
		newlocation = hfRequest+'?tm='+ddTheme;
	} else {
		newlocation = hfRequest+'&tm='+ddTheme;
	}
	location = newlocation;
}

function postBackLangChange(form)
{
	var ddLang		= form.langSelect.value;
	var hfRequest	= form.URLRequest.value;

	if (hfRequest.indexOf("?") == -1){
		newlocation = hfRequest+'?lg='+ddLang;
	} else {
		newlocation = hfRequest+'&lg='+ddLang;
	}
	location = newlocation;
}

function postBackReposChange(form)
{
	var ddRepos		= form.reposSelect.value;
	var hfRequest	= form.URLRequest.value;

	if (hfRequest.indexOf("?") == -1){
		newlocation = hfRequest+'?cr='+ddRepos;
	} else {
		newlocation = hfRequest+'&cr='+ddRepos;
	}
	location = newlocation;
}

function postBackDiffRequest(form)
{
	var ddRev1 = form.DiffRev1.value;
	var ddRev2 = form.DiffRev2.value;

	if (form.DiffRev1.selectedIndex < form.DiffRev2.selectedIndex) {
		// Swap the values.
		var ddTemp = ddRev1;
		ddRev1 = ddRev2;
		ddRev2 = ddTemp;
	}

	if (ddRev1 == ddRev2) {
		alert('Cannot generate a diff of a revision to itself!')
	} else {
		var dfDiffReq = form.URLDiffReq.value;
		location = dfDiffReq+'&r1='+ddRev1+'&r2='+ddRev2+'&df';
	}
}

