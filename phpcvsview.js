function postBackThemeChange(form)
{
	var ddTheme=form.ThemeSelect.value;
	var hfRequest=form.URLRequest.value;
	if (hfRequest.indexOf("?") == -1)
	{
		newlocation=hfRequest+'?tm='+ddTheme;
	}
	else
	{
		newlocation=hfRequest+'&tm='+ddTheme;
	}
	location=newlocation;
}

function postBackDiffRequest(form)
{
	var ddRev1=form.DiffRev1.value;
	var ddRev2=form.DiffRev2.value;
	if (ddRev1 > ddRev2)
	{
		// Swap the values.
		var ddTemp = ddRev1;
		ddRev1 = ddRev2;
		ddRev2 = ddTemp;
	}
	if (ddRev1 == ddRev2)
	{
		alert('Cannot generate a diff of a revision to itself!')
	}
	else
	{
		var dfDiffReq=form.URLDiffReq.value;
		location=dfDiffReq+'&r1='+ddRev1+'&r2='+ddRev2+'&df';
	}
}
