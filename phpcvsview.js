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
