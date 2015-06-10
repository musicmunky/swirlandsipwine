var FUSION = FUSION || {};

var loadjq = window.jQuery ? true : false;
if(!loadjq){
	console.log("NOTICE: jQuery library is not loaded");
}

FUSION.get = {

	pageHeight: function() {
		try {
			var body = document.body;
			var html = document.documentElement;
			return FUSION.lib.getMax([body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight]);
		}
		catch(err) {
			FUSION.error.logError(err);
			return 0;
		}
	},

	browser: function() {
		var br = {};
		var ua = navigator.userAgent, tem,
		M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
		if(/trident/i.test(M[1])){
			tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
			br['browser'] = "IE";
			br['version'] = tem[1] || 0;
			return br;
		}
		if(M[1] === 'Chrome'){
			tem = ua.match(/\b(OPR|Edge)\/(\d+)/);
			if(tem != null) {
				var brstr = tem.slice(1).join(' ').replace('OPR', 'Opera');
				var brarr = brstr.split(" ");
				br['browser'] = brarr[0];
				br['version'] = brarr[1];
				return br;
			}
		}
		M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
		if((tem = ua.match(/version\/(\d+)/i)) != null) M.splice(1, 1, tem[1]);
		br['browser'] = M[0];
		br['version'] = M[1];
		return br;
	},

	node: function(el) {
		try {
			var elm = (typeof el === "string") ? document.getElementById(el) : el;
			return elm;
		}
		catch(err) {
			FUSION.error.logError(err);
			return null;
		}
	},

	byName: function(el) {
		try {
			var elm = (typeof el === "string") ? document.getElementsByName(el) : [];
			return elm;
		}
		catch(err) {
			FUSION.error.logError(err);
			return [];
		}
	},

	byTagName: function(el) {
		try {
			var elm = (typeof el === "string") ? document.getElementsByTagName(el) : [];
			return elm;
		}
		catch(err) {
			FUSION.error.logError(err);
			return [];
		}
	},

	mouseX: function(e) {
		if (e.pageX) {
			return e.pageX;
		}
		if (e.clientX) {
			return e.clientX + (document.documentElement.scrollLeft ?
									document.documentElement.scrollLeft :
										document.body.scrollLeft);
		}
		return null;
	},

	mouseY: function(e) {
		if (e.pageY) {
			return e.pageY;
		}
		if (e.clientY) {
			return e.clientY + (document.documentElement.scrollTop ?
									document.documentElement.scrollTop :
										document.body.scrollTop);
		}
		return null;
	},

	objSize: function(obj) {
		try {
			var hasNonLeafNodes = false;
			var childCount = 0;

			for(var child in obj)
			{
				if(typeof obj[child] === 'object')
				{
					childCount += FUSION.get.objSize(obj[child]);
					hasNonLeafNodes = true;
				}
				else
				{
					childCount++;
				}
			}
			if(hasNonLeafNodes)
			{
				obj.num_children = childCount;
				return childCount;
			}
			else
			{
				return childCount;
			}
		}
		catch(err) {
			obj.is_error = true;
			obj.error_message = err;
			console.log("ERROR: " + err);
			return 0;
		}
	},

	urlParamByName: function(name) {
		return decodeURI(
			(RexExp(name + "=" + "(.+?)(&|$)").exec(location.search)||[,null])[1]
		);
	},

	urlParams: function() {
		var phash = {};
		try {
			var pstr = window.location.search;
			if(pstr && !FUSION.lib.isBlank(pstr))
			{
				var parr = pstr.substring[1].split("&");
				for(var i = 0; i < parr.length; i++)
				{
					var a = parr[i].split("=");
					phash[a[0]] = decodeURI(a[1]);
				}
			}
			return phash;
		}
		catch(err) {
			phash['is_error'] = true;
			phash['error'] = err;
			return phash;
		}
	},

	selectedText: function(el) {
		//get the currently selected text in a select box / dropdown list
		try {
			var sel = FUSION.get.node(el);
			var idx = sel.selectedIndex;
			var txt = sel.options[idx].innerHTML;
			return txt;
		}
		catch(err) {
			FUSION.error.showError(err);
			return false;
		}
	},

	selectedValue: function(el) {
		//get the currently selected text in a select box / dropdown list
		try {
			var sel = FUSION.get.node(el);
			var val = (sel.options[sel.selectedIndex]) ? sel.options[sel.selectedIndex].value : "";
			return val;
		}
		catch(err) {
			FUSION.error.showError(err);
			return false;
		}
	},

	elementsByAttr: function(attr) {
		var matchingElements = [];
		var allElements = FUSION.get.byTagName('*');
		for (var i = 0; i < allElements.length; i++)
		{
			if (allElements[i].getAttribute(attr))
			{
				matchingElements.push(allElements[i]);
			}
		}
		return matchingElements;
	},
};


//BEGIN LIBRARY SETTER METHODS
FUSION.set = {

	overlayMouseWait: function() {
		//Set the mouse cursor to the "waiting" icon
		jQuery("body").addClass("wait");
	},

	overlayMouseNormal: function() {
		//Set the mouse cursor back to the "default" icon
		jQuery("body").removeClass("wait");
	},

	selectedText: function(el,t) {
		//set the selected text of a select box / dropdown list
		//return true if a value is found, otherwise return false
		try {
			var sel = FUSION.get.node(el);
			var res = false;
			for (var i = 0; i < sel.options.length; i++)
			{
				if (sel.options[i].text == t)
				{
					sel.selectedIndex = i;
					res = true;
					break;
				}
			}
			return res;
		}
		catch(err) {
			FUSION.error.showError(err);
			FUSION.error.logError(err);
			return false;
		}
	},
};


//BEGIN LIBRARY REMOVE METHODS
FUSION.remove = {

	rowById: function(id) {
		try {
			var row = FUSION.get.node(id);
			var tbl = row.parentNode;
			tbl.removeChild(row);
			return true;
		}
		catch(err) {
			FUSION.error.showError(err);
			return false;
		}
	},

	dTRowById: function(t, r) {
		try {
			var remrow = FUSION.get.node(r);
			if(remrow)
			{
				var table = jQuery("#" + t).DataTable();
				table.row("#" + r).remove().draw();
				return true;
			}
			return false;
		}
		catch(err) {
			FUSION.error.showError(err);
			return false;
		}
	},

	node: function(el) {
		try {
			var elm = FUSION.get.node(el);
			var par = elm.parentNode;
			par.removeChild(elm);
			return true;
		}
		catch(err) {
			FUSION.error.logError(err);
			FUSION.error.showError(err);
		}
	},
};


//BEGIN LIBRARY ERROR METHODS
FUSION.error = {

	showError: function(e, m) {
		var emsg = "An error occurred during this operation:\n'" + e + "'\nPlease try again, or contact your administrator";
		if(typeof m !== "undefined")
		{
			emsg = m + "\n" + emsg;
		}
		alert(emsg);
	},

	logError: function(e, m) {
		m = m || "";
		var usrmsg = !FUSION.lib.isBlank(m) ? "DEVELOPER MESSAGE: " + m + "\n" : "";
		var errnme = "ERROR NAME: " + e.name + "\n";
		var errmsg = "ERROR MESSAGE: " + e.message + "\n";
		var fncnme = "";
		var linnum = "";
		try {
			linnum = e.lineNumber || linnum;
			var a = e.stack.split("\n");
			var b = a[0].split("@");
			var fncnme = b[0];
		}
		catch(err){}
		var functn = "ERROR FUNCTION: " + fncnme + "\n";
		var lnenmb = "ERROR LINE NUMBER: " + linnum + "\n";
		console.log(usrmsg + errnme + errmsg + functn + lnenmb);
	},

	showErrorDialog: function(h)
	{
		var hash = h || {};
		var did = hash['divid'] || "jserrorform";
		var ttl = hash['title'] || "Error Message";
		var msg = hash['message'] || "It looks like something went wrong!  Please click the link below to let the dev team know about the error."
		var lnk = hash['linktext'] || "Click here to notify the developers of this error";
		var eml = hash['emailfunction'] || "";
		var stk = hash['stacktrace'] || "";
		var err = hash['errormessage'] || "";
		var vislnk = hash['linkvisible'] || true;

		var div = "<div id='" + did + "' title='" + ttl + "'>";
		div += "<div style='with:100%;margin-top:15px;height:165px;'>";
		div += "<label style='width:100%;text-align:center;'>" + msg + "</label>";
		div += "<label style='width:100%;text-align:center;margin-top:10px;'>";
		if(vislnk){
			div += "<a href='javascript:void(0)' onclick='" + eml + "' style='color:#68a4c4;'>" + lnk + "</a>";
		}
		div += "</label>";
		div += "<input type='hidden' id='errormessage' value='" + err + "' />";
		div += "<input type='hidden' id='fullstacktrace' value='" + stk + "' />";
		div += "</div></div>";

		var	html = jQuery.parseHTML( div );
		jQuery("body").append(html);
		jQuery( "#" + did ).dialog({
			modal: true,
			autoOpen: true,
			width:350,
			height:330,
			close: function() {
				jQuery(this).dialog('destroy').remove();
			},
			buttons: {
				Close: function() {
					jQuery(this).dialog("close");
				}
			}
		});
	},
};


//BEGIN LIBRARY GENERIC METHODS
FUSION.lib = {

	titleCase: function(str) {
		try {
			return str.replace(/\w\S*/g, function(txt){ return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase(); });
		}
		catch(err) {
			FUSION.error.logError(err);
			return "";
		}
	},

	dragable: function(cel, del) {
		//cel = div to click and drag
		//del = containing div
		var p = FUSION.get.node(cel);
		var t = FUSION.get.node(del);
		var drag = false;
		var offsetX = 0;
		var offsetY = 0;
		var mousemoveTemp = null;

		if (t) {
			var move = function(x,y) {
				var l = t.style.left ? parseInt(t.style.left) : 0;
				var o = t.style.top ?  parseInt(t.style.top)  : 0;
				t.style.left = (l + x) + "px";
				t.style.top  = (o + y) + "px";
			}
			var mouseMoveHandler = function(e) {
				e = e || window.event;

				if(!drag){ return true };

				var x = FUSION.get.mouseX(e);
				var y = FUSION.get.mouseY(e);
				if (x != offsetX || y != offsetY) {
					move(x - offsetX, y - offsetY);
					offsetX = x;
					offsetY = y;
				}
				return false;
			}
			var start_drag = function(e) {
				e = e || window.event;
				p.style.cursor = "-moz-grabbing";
				offsetX = FUSION.get.mouseX(e);
				offsetY = FUSION.get.mouseY(e);
				drag = true; // basically we're using this to detect dragging

				// save any previous mousemove event handler:
				if(document.body.onmousemove) {
					mousemoveTemp = document.body.onmousemove;
				}
				document.body.onmousemove = mouseMoveHandler;
				return false;
			}
			var stop_drag = function() {
				drag = false;
				p.style.cursor = "-moz-grab";
				// restore previous mousemove event handler if necessary:
				if(mousemoveTemp) {
					document.body.onmousemove = mousemoveTemp;
					mousemoveTemp = null;
				}
				return false;
			}
			p.onmousedown = start_drag;
			p.onmouseup = stop_drag;
		}
	},

	alert: function(o) {

		/*
		Extend for "levels", ie, Message, Info, Warning, Error, etc
		*/
		var obj = o || {};
		var msg = (typeof obj === "string") ? obj : obj['message'];
		var hgt = obj['height'] 		|| 120;
		var wdt = obj['width'] 			|| 350;
		var fnt = obj['font-size'] 		|| 14;
		var bcl = obj['background'] 	|| "#FFFFFF";
		var col = obj['color'] 			|| "#333";
		var txt = obj['text-align'] 	|| "left";
		var btn = obj['button-text'] 	|| "Okay";

		var odid = "alert" + FUSION.lib.getRandomInt(1000,9999);
		var idid = "inner_" + odid;
		var mdid = "message_" + odid;
		var bttn = "button_" + odid;

		var wht = jQuery(window).scrollTop() + jQuery(window).height() / 2;
		var top = wht - (hgt / 2) - 100;
// 		var top = (wht / 2) - (hgt / 2) - 100;

		var ao = FUSION.lib.createHtmlElement({"type":"div",
											   "attributes":{"id":odid, "class":"fl_alert_overlay"},
											   "style":{"display":"block", "height":FUSION.get.pageHeight() + "px"}});

		var aw = FUSION.lib.createHtmlElement({"type":"div",
											   "attributes":{"id":idid, "class":"fl_alert_wrapper"},
											   "style":{"top":	 Math.round(top)+"px",
													    "width": wdt+"px",
													    "height":hgt+"px"}});

		var amd = FUSION.lib.createHtmlElement({"type":"div",
											    "attributes":{"id":mdid, "class":"fl_alert_message_div"},
											    "style":{"fontSize":		fnt+"px",
														 "backgroundColor":bcl,
														 "color":			col,
														 "textAlign":		txt}});

		var abd = FUSION.lib.createHtmlElement({"type":"div",
											    "attributes":{"class":"fl_alert_button_div"}});

		var ab = FUSION.lib.createHtmlElement({"type":"input",
											   "onclick":"FUSION.remove.node('" + odid + "')",
											   "attributes":{"type":"button","class":"fl_alert_button","value":btn,"id":bttn}});
		amd.innerHTML = msg;
		abd.appendChild(ab);
		aw.appendChild(amd);
		aw.appendChild(abd);
		ao.appendChild(aw);
		document.body.appendChild(ao);
		FUSION.get.node(bttn).focus();
		FUSION.lib.dragable(idid, idid);
	},

	ajaxCall: function(ajaxobj) {
		if(!ajaxobj['type'] || !ajaxobj['data'] || !ajaxobj['path'])
		{
			alert("Unable to complete call to the server - insufficient data");
			return false;
		}

		var tmot = ajaxobj['timeout'] || 10000;
		FUSION.set.overlayMouseWait();
		jQuery.ajax({
			type: ajaxobj['type'],
			beforeSend: function(xhr) {
				xhr.setRequestHeader("X-CSRF-Token", jQuery('meta[name="csrf-token"]').attr('content'));
				xhr.setRequestHeader("Accept", "text/html");
			},
			url: ajaxobj['path'],
			data: ajaxobj['data'],
			success: function(result) {
				var response = JSON.parse(result);
				if(response['status'] == "success")
				{
					FUSION.set.overlayMouseNormal();
					if(ajaxobj['func'])
					{
						var fn = ajaxobj['func'];
						fn.apply(this, [response['content']]);
					}
				}
				else
				{
					FUSION.set.overlayMouseNormal();
					FUSION.error.showErrorDialog({'stacktrack':response['content'],
												  'errormessage':response['message'],
												  'linkvisible':false});
				}
			},
			error: function(xhr, errtype, errthrown) {
				FUSION.set.overlayMouseNormal();
				switch(errtype) {
					case "error":
						FUSION.lib.alert("<p>Error completing request: " + errthrown + "</p>");
						break;
					case "abort":
						FUSION.lib.alert("<p>Call to server aborted: " + errthrown + "</p>");
						break;
					case "parsererror":
						FUSION.lib.alert("<p>Parser error during request: " + errthrown + "</p>");
						break;
					case "timeout":
						FUSION.lib.alert("<p>Call to server timed out - please try again</p>");
						break;
					default:
						FUSION.error.showError("The call to the server failed", "AJAX Error");
				}
			},
			timeout: tmot
		});
		return false;
	},

	modifyIETable: function(tid, d) {
		//Workaround for IE so I can replace the HTML in the tables
		//an old bug versions of IE > 6, and still present in at least
		//version 9 (not sure about 10/11/etc)
		//first set the html for the main table...
		try{
			var r = FUSION.lib.getRandomInt(1000,9999);
			var sid = "tempspan" + r;
			var tempspan = FUSION.lib.createHtmlElement({"type":"span", "style":{"visibility":"hidden"}, "attributes":{"id":sid}});
			document.body.appendChild(tempspan);

			var spn = FUSION.get.node(sid);
			var tbl = FUSION.get.node(tid);
			spn.innerHTML = "<table><tbody id='" + tid + "'>" + d;
			tbl.parentNode.replaceChild(spn.firstChild.firstChild,tbl);
			FUSION.remove.node(sid);

			return true;
		}
		catch(err){
			FUSION.error.logError(err);
			FUSION.error.showError("Error attempting to modify table contents of " + tid, err);
			return false;
		}
	},

	getRandomInt: function(min, max) {
 		return Math.floor(Math.random() * (max - min)) + min;
	},

	padZero: function(n, width, z) {
		z = z || '0';
		n = n + '';
		return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
	},

	daysInMonth: function(month,year) {
		try{
    		return new Date(year, month, 0).getDate();
		}
		catch(err){
			FUSION.error.logError(err);
			return 0;
		}
	},

	isBlank: function(t) {
		try {
			var ts = t.toString();
			return ts.match(/^\s*$/);
		}
		catch(err) {
			FUSION.error.logError(err);
			return true;
		}
	},

	getMax: function(a) {
		try {
			return Math.max.apply(Math, a);
		}
		catch(err) {
			FUSION.error.logError(err, "Error during getMax function");
			return NaN;
		}
	},

	getMin: function(a) {
		try {
			return Math.min.apply(Math, a);
		}
		catch(err) {
			FUSION.error.logError(err, "Error during getMin function");
			return NaN;
		}
	},

	isLeapYear: function(year) {
		var y = parseInt(year);
		return ((y % 4 == 0) && (y % 100 != 0)) || (y % 400 == 0);
	},

	//create an HTMLElement with parameters passed in by hash
	createHtmlElement: function(hash)
	{
		try {
			var type  = hash['type'];
			var text  = hash['text'];
			var click = hash['onclick'];
			var chnge = hash['onchange'];
			var keyup = hash['keyup'];
			var styls = hash['style'];
			var attrs = hash['attributes'];

			var el = "";
			if(type && !FUSION.lib.isBlank(type))
			{
				el = document.createElement(type);
			}
			else
			{
				FUSION.error.logError("","You did not give a type when creating a new HTMLElement - Defaulting to div");
				el = document.createElement("div");
			}

			if(attrs && FUSION.get.objSize(attrs) > 0)
			{
				var br = FUSION.get.browser();
				var attrname = "";
				for (var key in attrs)
				{
					attrname = key;
					if(key.match(/^class/i)){
						attrname = (br.browser.match(/ie/i) && parseInt(br.version) < 9) ? "className" : "class";
					}
					el.setAttribute(attrname, attrs[key]);
				}
			}

			if(styls && FUSION.get.objSize(styls) > 0)
			{
				//jQuery( el ).css(styls);
				var stylename = "";
				for (var key in styls)
				{
					stylename = key;
					if(key.match(/^float/)){
						stylename = "cssFloat";
					}
					el.style[stylename] = styls[key];
				}
			}

			if(text && !FUSION.lib.isBlank(text))
			{
				var tn = document.createTextNode(text);
				el.appendChild(tn);
			}

			if(click && !FUSION.lib.isBlank(click))
			{
				//el.onclick = function(){ showRemoveMentorForm(me_id, mr_id, me_name, mr_name); };
				//callback issues here with variable assignment...trying this way to see if it will work
				el.onclick = new Function(click);
			}

			if(chnge && !FUSION.lib.isBlank(chnge))
			{
				el.onchange = new Function(chnge);
			}

			if(keyup && !FUSION.lib.isBlank(keyup))
			{
				el.onkeyup = new Function(keyup);
			}
			return el;
		}
		catch(err) {
			FUSION.error.showError(err, "There was an error creating the desired element: ");
			return document.createElement("div");
		}
	},

	sendErrorEmail: function(id) {
		var uid = id;
		var msg = FUSION.get.node("errormessage").value;
		var fst = FUSION.get.node("fullstacktrace").value;
		hideGeneralError();
		var info = {
			"type": "POST",
			"path": "URLtoEmailFunctionality",
			"data": {
				user_id: uid,
				message: msg,
				trace: fst
			}
		};
		FUSION.lib.ajaxCall(info);
	},

	//prevents a users from typing anything but numer characters in a text field
	noAlpha: function(th)
	{
		th.value = th.value.replace(/[^\d]+/,"");
	},

	isNode: function(o) {
		return (
			typeof Node === "object" ? o instanceof Node :
				o && typeof o === "object" && typeof o.nodeType === "number" && typeof o.nodeName === "string"
		);
	},

	isElement: function(o) {
		return (
			typeof HTMLElement === "object" ? o instanceof HTMLElement :
				o && typeof o === "object" && o !== null && o.nodeType === 1 && typeof o.nodeName === "string"
		);
	},

	/**
	* Does a PHP var_dump'ish behavior.  It only dumps one variable per call.  The
	* first parameter is the variable, and the second parameter is an optional
	* name.  This can be the variable name [makes it easier to distinguish between
	* numerious calls to this function], but any string value can be passed.
	*
	* @param mixed var_value - the variable to be dumped
	* @param string var_name - ideally the name of the variable, which will be used
	*       to label the dump.  If this argumment is omitted, then the dump will
	*       display without a label.
	* @param boolean - annonymous third parameter.
	*       On TRUE publishes the result to the DOM document body.
	*       On FALSE a string is returned.
	*       Default is TRUE.
	* @returns string|inserts Dom Object in the BODY element.
	*/
	vardump: function(var_value, var_name)
	{
		// Check for a third argument and if one exists, capture it's value, else
		// default to TRUE.  When the third argument is true, this function
		// publishes the result to the document body, else, it outputs a string.
		// The third argument is intend for use by recursive calls within this
		// function, but there is no reason why it couldn't be used in other ways.
		var is_publish_to_body = typeof arguments[2] === 'undefined' ? true:arguments[2];

		// Check for a fourth argument and if one exists, add three to it and
		// use it to indent the out block by that many characters.  This argument is
		// not intended to be used by any other than the recursive call.
		var indent_by = typeof arguments[3] === 'undefined' ? 0:arguments[3]+3;

		var do_boolean = function (v)
		{
			return 'Boolean(1) ' + (v ? 'TRUE' : 'FALSE');
		};

		var do_number = function(v)
		{
			var num_digits = ('' + v).length;
			return 'Number(' + num_digits + ') ' + v;
		};

		var do_string = function(v)
		{
			var num_chars = v.length;
			return 'String(' + num_chars + ') "' + v + '"';
		};

		var do_object = function(v)
		{
			if (v === null)
			{
				return "NULL(0)";
			}

			var out = '';
			var num_elem = 0;
			var indent = '';

			if (v instanceof Array)
			{
				num_elem = v.length;
				for (var d=0; d < indent_by; ++d)
				{
					indent += ' ';
				}
				out = "Array("+num_elem+") \n"+(indent.length === 0?'':'|'+indent+'')+"(";
				for (var i=0; i<num_elem; ++i)
				{
					out += "\n"+(indent.length === 0?'':'|'+indent)+"|   ["+i+"] = " + FUSION.lib.vardump(v[i],'',false,indent_by);
				}
				out += "\n"+(indent.length === 0?'':'|'+indent+'')+")";
				return out;
			}
			else if (v instanceof Object)
			{
				for (var d=0; d<indent_by; ++d)
				{
					indent += ' ';
				}
				out = "Object \n"+(indent.length === 0?'':'|'+indent+'')+"(";
				for (var p in v)
				{
					out += "\n"+(indent.length === 0?'':'|'+indent)+"|   ["+p+"] = " + FUSION.lib.vardump(v[p],'',false,indent_by);
				}
				out += "\n"+(indent.length === 0?'':'|'+indent+'')+")";
				return out;
			}
			else
			{
				return 'Unknown Object Type!';
			}
		};

		// Makes it easier, later on, to switch behaviors based on existance or
		// absence of a var_name parameter.  By converting 'undefined' to 'empty 
		// string', the length greater than zero test can be applied in all cases.
		var_name = typeof var_name === 'undefined' ? '':var_name;
		var out = '';
		var v_name = '';

		switch(typeof var_value)
		{


			case "boolean":
				v_name = var_name.length > 0 ? var_name + ' = ':''; // Turns labeling on if var_name present, else no label
				out += v_name + do_boolean(var_value);
				break;

			case "number":
				v_name = var_name.length > 0 ? var_name + ' = ':'';
				out += v_name + do_number(var_value);
				break;

			case "string":
				v_name = var_name.length > 0 ? var_name + ' = ':'';
				out += v_name + do_string(var_value);
				break;

			case "object":
				v_name = var_name.length > 0 ? var_name + ' =\> ':'';
				out += v_name + do_object(var_value);
				break;

			case "function":
				v_name = var_name.length > 0 ? var_name + ' = ':'';
				out += v_name + "Function";
				break;

			case "undefined":
				v_name = var_name.length > 0 ? var_name + ' = ':'';
				out += v_name + "Undefined";
				break;

			default:
				out += v_name + ' is unknown type!';

		}

		// Using indent_by to filter out recursive calls, so this only happens on the
		// primary call [i.e. at the end of the algorithm]
		if (is_publish_to_body  &&  indent_by === 0)
		{
			var div_dump = FUSION.get.node('div_dump');
			if (!div_dump)
			{
				div_dump = document.createElement('div');
				div_dump.id = 'div_dump';

				var style_dump = FUSION.get.byTagName("style")[0];
				if (!style_dump)
				{
					var head = FUSION.get.byTagName("head")[0];
					style_dump = document.createElement("style");
					head.appendChild(style_dump);
				}

				// Thank you Tim Down [http://stackoverflow.com/users/96100/tim-down]
				// for the following addRule function
				var addRule;
				if (typeof document.styleSheets != "undefined" && document.styleSheets) {
					addRule = function(selector, rule) {
						var styleSheets = document.styleSheets, styleSheet;
						if (styleSheets && styleSheets.length) {
							styleSheet = styleSheets[styleSheets.length - 1];
							if (styleSheet.addRule) {
								styleSheet.addRule(selector, rule);
							} else if (typeof styleSheet.cssText == "string") {
								styleSheet.cssText = selector + " {" + rule + "}";
							} else if (styleSheet.insertRule && styleSheet.cssRules) {
								styleSheet.insertRule(selector + " {" + rule + "}", styleSheet.cssRules.length);
							}
						}
					};
				} else {
					addRule = function(selector, rule, el, doc) {
						el.appendChild(doc.createTextNode(selector + " {" + rule + "}"));
					};
				}

				// Ensure the dump text will be visible under all conditions [i.e. always
				// black text against a white background].
				addRule('#div_dump', 'background-color:white', style_dump, document);
				addRule('#div_dump', 'color:black', style_dump, document);
				addRule('#div_dump', 'padding:15px', style_dump, document);

				style_dump = null;
			}

			var pre_dump = FUSION.get.node('pre_dump');
			if (!pre_dump)
			{
				pre_dump = document.createElement('pre');
				pre_dump.id = 'pre_dump';
				pre_dump.innerHTML = out + "\n";
				div_dump.appendChild(pre_dump);
				document.body.appendChild(div_dump);
			}
			else
			{
				pre_dump.innerHTML += out+"\n";
			}
		}
		else
		{
			return out;
		}

	},

	findByRegex: function(rgx) {
		jQuery.expr[':'].regex = function(elem, index, match) {
			var matchParams = match[3].split(','),
				validLabels = /^(data|css):/,
				attr = {
					method: matchParams[0].match(validLabels) ?
								matchParams[0].split(":")[0] : "attr",
					property: matchParams.shift().replace(validLabels, "")
				},
				regexFlags = "ig",
				regex = new RegExp(matchParams.join("").replace(/^\s+|\s+$/g), regexFlags);
			return regex.test(jQuery(elem)[attr.method](attr.property));
		};
		return jQuery(rgx);
	},

	sort_by: function(field, reverse, primer) {
		var key = function (x) {return primer ? primer(x[field]) : x[field]};
		return function (a,b)
		{
			var A = key(a), B = key(b);
			return ( (A < B) ? -1 : ((A > B) ? 1 : 0) ) * [-1,1][+!!reverse];
		}
	},
};
