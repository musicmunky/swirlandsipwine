<?php

	//require 'php/shutdown.php';
	define('LIBRARY_CHECK',true);
// 	require 'php/saswlib.php';

	define('INCLUDE_CHECK',true);
	require 'php/connect.php';
	require 'php/wine.php';

	date_default_timezone_set('America/New_York');

	if(!isset($_SESSION))
	{
		session_name('swirlandsipwine');
		session_start();
	}

	$txt = "Ornellaia 2010";
 	$tmp = escapeArray(array($txt));
 	$search = $tmp[0];
	$search = urlencode($search);
// 	$search = urlencode($txt);
//	$search = $txt;

	$foo = array(1,"b",3);
	$bar = (isset($foo[1]) && gettype($foo[1]) != "NULL") ? gettype($foo[1]) : "NULL";

	$wine = new Wine(true);
	$wine->loadWineData($search);

// 	$wine = getWineInfo(array("search" => "Ornellaia 2010"));

	$rcode = $wine->getRequestCode();

	$total = "";
	$wines = "";

	if($rcode == 0)
	{
		$total = $wine->getWineCount();
		$wines = $wine->getWineData();
	}

	$stats = $wine->getStatus();
	$mssgs = $wine->getErrorMessages();
	$mtext = implode("<br />", $mssgs);

	unset($wine);


	function escapeArray($post)
	{
		//recursive function called on the POST object sent back by an AJAX call
		//it accounts for nested arrays/hashes (these were being nulled out previously)
		foreach($post as $key => $val)
		{
			if(gettype($val) == "array") {
				escapeArray($val);
			}
			else {
				$val = urldecode($val);
				$val = mysql_real_escape_string($val);
				$post[$key] = $val;
			}
		}
		return $post;
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11-strict.dtd">
<html>
	<head>
		<title>Swirl and Sip Wine</title>
		<link rel="icon" type="image/icon" href="images/winelogo.ico">
		<link rel='stylesheet' href='css/swirlandsipstyle.css' type="text/css" media="screen" charset="utf-8">
		<link rel='stylesheet' href='css/fusionlib.css' type="text/css" media="screen" charset="utf-8">
		<link rel='stylesheet' href='css/jquery-ui.min.css' type="text/css" media="screen" charset="utf-8">
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Roboto">
<!--		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans">//-->
		<script language="javascript" type="text/javascript" src="javascript/jquery-1.11.0.min.js"></script>
		<script language="javascript" type="text/javascript" src="javascript/jquery-ui-1.10.4.custom.min.js"></script>
		<script language="javascript" type="text/javascript" src="javascript/fusionlib.js"></script>
		<script language="javascript" type="text/javascript" src="javascript/sasw.js"></script>

	</head>
	<body>

		<div id="header" class="header"></div>
		<div id="mainwrapper" class="mainwrapper">
			<div id="maincontent" class="maincontent">
				Welcome! BAR IS: <?php echo $bar; ?>
			</div>
			<div style="float:left;width:100%;margin-top:20px;">
				SEARCH TERM: <?php echo $search; ?>
			</div>
			<div style="float:left;width:100%;margin-top:20px;">
				STATUS OF REQUEST: <?php echo $stats; ?>
			</div>
			<div style="float:left;width:100%;margin-top:20px;">
				TOTAL WINES RETURNED: <?php echo $total; ?>
			</div>
			<div style="float:left;width:100%;margin-top:20px;">
				RETURN CODE: <?php echo $rcode; ?>
			</div>
			<div style="float:left;width:100%;margin-top:20px;">
				MESSAGES: <?php echo $mtext; ?>
			</div>
			<div style="float:left;width:100%;margin-top:20px;">
				WINE INFO:<br />
				<pre><?php var_dump($wines); ?></pre>
			</div>
		</div>
		<div class="footer"></div>

	</body>
</html>