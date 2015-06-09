$( document ).ready(function() {

// 	$('input, textarea').placeholder({customClass:'my-placeholder'});
	$('#srchtxt').placeholder({customClass:'placeholder-text'});

	//IE doesn't like Google fonts...apparently it's Google's fault
	//at the moment, but whatever...load Web Safe font for IE users
	var gbr = FUSION.get.browser();
	if(gbr.browser && gbr.browser == "IE")
	{
		document.body.style.fontFamily = "'Trebuchet MS', Helvetica, sans-serif";
	}
});


function runSearch()
{
	var srch = FUSION.get.node("srchtxt").value;
	if(FUSION.lib.isBlank(srch))
	{
		FUSION.lib.alert("<p>Please enter a search term!</p>");
		return false;
	}

	var info = {
		"type": "GET",
		"path": "php/saswlib.php",
		"data": {
			"method": 	"getWineInfo",
			"libcheck": true,
			"search":	srch
		},
		"func": runSearchResponse
	};
	FUSION.lib.ajaxCall(info);
}


function runSearchResponse(h)
{
	var hash = h || {};
	var rc = hash['num_recs'];

	FUSION.get.node("wineinfo").innerHTML = "";

	if(parseInt(rc) > 0)
	{
		FUSION.get.node("srchtxt").value = "";
		var wines = hash['wines'];

		for(var i = 0; i < rc; i++)
		{
			var windiv = FUSION.lib.createHtmlElement({"type":"div",
													   "style":{
														   "float":"left",
														   "width":"100%",
														   "marginTop":"20px",
														   "paddingTop":"10px",
														   "borderTop":"1px solid #ddd",
														   "height":"100px"}});
			var wnwrap = FUSION.lib.createHtmlElement({"type":"div","style":{"float":"left","height":"100%","width":"890px"}});
			var imgdiv = FUSION.lib.createHtmlElement({"type":"div",
													   "style":{
														   "width":"100px",
														   "height":"100px",
														   "float":"left",
														   "overflow":"hidden",
													   	   "marginRight":"10px"}});
			var winimg = FUSION.lib.createHtmlElement({"type":"img","style":{"width":"100px","float":"left"},
													   "attributes":{"src":wines[i]['label']}});
			imgdiv.appendChild(winimg);


			var divnam = FUSION.lib.createHtmlElement({"type":"div","style":{"float":"left","height":"100%","width":"290px"}});
			var lblnam = FUSION.lib.createHtmlElement({"type":"label","style":{"width":"100%"},
													   "text":"Wine Name: "});
			var spnnam = FUSION.lib.createHtmlElement({"type":"span","style":{"fontWeight":"bold"}});
			var lnknam = FUSION.lib.createHtmlElement({"type":"a","text":wines[i]['name'],"style":{"textDecoration":"none"},
								   "attributes":{"href":wines[i]['url'],"target":"_blank"}});
			var lblvin = FUSION.lib.createHtmlElement({"type":"label","style":{"width":"100%"},
													   "text":"Vineyard: "});
			var spnvin = FUSION.lib.createHtmlElement({"type":"span","style":{"fontWeight":"bold"},
													   "text":wines[i]['vineyardname']});
			spnnam.appendChild(lnknam);
			lblnam.appendChild(spnnam);
			lblvin.appendChild(spnvin);
			divnam.appendChild(lblnam);
			divnam.appendChild(lblvin);


			var divreg = FUSION.lib.createHtmlElement({"type":"div","style":{"float":"left","height":"100%","width":"290px"}});
			var lblreg = FUSION.lib.createHtmlElement({"type":"label","style":{"width":"100%"},
													   "text":"Appellation / Region: "});
			var spnreg = FUSION.lib.createHtmlElement({"type":"span","style":{"fontWeight":"bold"},
													   "text":wines[i]['appellation'] + " / " + wines[i]['region']});
			var lblvar = FUSION.lib.createHtmlElement({"type":"label","style":{"width":"100%"},
													   "text":"Varietals: "});
			var spnvar = FUSION.lib.createHtmlElement({"type":"span","style":{"fontWeight":"bold"},
													   "text":wines[i]['varietal'] + ", " + wines[i]['varietaltype']});
			lblreg.appendChild(spnreg);
			lblvar.appendChild(spnvar);
			divreg.appendChild(lblreg);
			divreg.appendChild(lblvar);


			var keytxt = wines[i]['attributes'].replace(/&amp;/g, '&');
			var divprc = FUSION.lib.createHtmlElement({"type":"div","style":{"float":"left","height":"100%","width":"290px"}});
			var lblprc = FUSION.lib.createHtmlElement({"type":"label","style":{"width":"100%"},
													   "text":"Avg Retail Price: "});
			var spnprc = FUSION.lib.createHtmlElement({"type":"span","style":{"fontWeight":"bold"},
													   "text":wines[i]['retailprice']});
			var lblrat = FUSION.lib.createHtmlElement({"type":"label","style":{"width":"100%"},
													   "text":"Highest Rating: "});
			var spnrat = FUSION.lib.createHtmlElement({"type":"span","style":{"fontWeight":"bold"},
													   "text":wines[i]['rating']});
			var lblkey = FUSION.lib.createHtmlElement({"type":"label","style":{"width":"100%"},
													   "text":"Keywords: "});
			var spnkey = FUSION.lib.createHtmlElement({"type":"span","style":{"fontWeight":"bold"},
													   "text":keytxt});
			lblprc.appendChild(spnprc);
			lblrat.appendChild(spnrat);
			lblkey.appendChild(spnkey);
			divprc.appendChild(lblprc);
			divprc.appendChild(lblrat);
			divprc.appendChild(lblkey);

			wnwrap.appendChild(divnam);
			wnwrap.appendChild(divreg);
			wnwrap.appendChild(divprc);

			windiv.appendChild(imgdiv);
			windiv.appendChild(wnwrap);

			FUSION.get.node("wineinfo").appendChild(windiv);
		}
		var wh = FUSION.get.pageHeight() - 60;
		var dh = ((132 * rc) + 200);
// 		var ph = (wh > dh) ? wh : dh;
		var ph = (wh > dh) ? "100%" : dh + "px";
		FUSION.get.node("mainwrapper").style.height = ph;// + "px";
	}
	else
	{
		//processForecast(hash);
		FUSION.lib.alert("<p>No wines found using search term: '" + FUSION.get.node("srchtxt").value + "'</p>");
	}
}
