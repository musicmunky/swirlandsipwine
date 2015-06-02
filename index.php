<?php

	//require 'php/shutdown.php';
	define('LIBRARY_CHECK',true);
	require 'php/saswlib.php';

	if(!isset($_SESSION))
	{
		session_name('swirlandsipwine');
		session_start();
	}

// 	$search		= "Domaine Zind Humbrecht Brand Riesling Grand Cru 2007";
	$search		= "Domaine des Baumard Quarts de Chaume 1990";
 	$wine		= getWineInfo(array("search" => $search), false);
	$wineinfo	= $wine['content'];
	$winehtml	= "";

	$rcode = $wineinfo['response_code'];
	$stats = $wineinfo['status'];
	$mtext = implode("<br />", $wineinfo['error_messages']);
	$total = 0;

	if($wine['status'] == "success")
	{
		$total = $wineinfo['num_recs'];
		$wines = $wineinfo['wines'];
		for($i = 0; $i < $total; $i++)
		{
			$winehtml .= "<div style='float:left;width:100%;margin-top:20px;height:100px;'>";
			$winehtml .= "<img src='" . $wines[$i]['label'] . "' style='width:100px;height:100px;float:left;' />";
			$winehtml .= "<div style='float:left;height:100%;width:900px;'>";
			$winehtml .= "<div style='float:left;height:100%;width:300px;'>
							<label style='width:100%;'>Wine Name: <span style='font-weight:bold;'>" . $wines[$i]['name'] . "</span></label>";
			$winehtml .= "<label style='width:100%;'>
							Vineyard: <span style='font-weight:bold;'>" . $wines[$i]['vineyardname'] . "</span>
						  </label></div>";
			$winehtml .= "<div style='float:left;height:100%;width:300px;'>
							<label style='width:100%;'>Appellation / Region:
								<span style='font-weight:bold;'>" . $wines[$i]['appellation'] . " / " . $wines[$i]['region'] . "</span>
							</label>
							<label style='width:100%;'>Varietals:
								<span style='font-weight:bold;'>" . $wines[$i]['varietal'] . ", " . $wines[$i]['varietaltype'] . "</span>
							</label>
						  </div>";
			$winehtml .= "<div style='float:left;height:100%;width:300px;'>
							<label style='width:100%;'>Avg Retail Price: <span style='font-weight:bold;'>" . $wines[$i]['retailprice'] . "</span></label>";
			$winehtml .= "<label style='width:100%;'>Highest Rating: <span style='font-weight:bold;'>" . $wines[$i]['rating'] . "</span></label>";
			$winehtml .= "<label style='width:100%;'>Keywords: <span style='font-weight:bold;'>" . $wines[$i]['attributes'] . "</span></label></div>";
			$winehtml .= "</div></div>";
// 			$winehtml .= "WINE ID: " . $wines[$i]['id'] . "<br />";
// 			$winehtml .= "MAX PRICE: " . $wines[$i]['maxprice'] . ",
// 						  MIN PRICE: " . $wines[$i]['minprice'] . ",
// 			$winehtml .= "<label>Wine Type: <span style='font-weight:bold;'>" . $wines[$i]['type'] . "</span></label>";
// 			$winehtml .= "YEAR: " . $wines[$i]['year'] . "<br />";

		}
	}
	else
	{
		$winehtml = "<div style='float:left;width:100%;font-size:24px;color:red;height:500px;>
						ERROR: " . $wineinfo['status'] . "<br />RESPONSE CODE: " . $wineinfo['response_code'] . "<br />
						MESSAGES: " . implode("<br />", $wineinfo['error_messages']) . "
					</div>";
	}


	unset($wine);

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
		<script language="javascript" type="text/javascript" src="javascript/jquery-1.11.0.min.js"></script>
		<script language="javascript" type="text/javascript" src="javascript/jquery-ui-1.10.4.custom.min.js"></script>
		<script language="javascript" type="text/javascript" src="javascript/fusionlib.js"></script>
		<script language="javascript" type="text/javascript" src="javascript/sasw.js"></script>

	</head>
	<body>

		<div id="header" class="header">
			<div id="header-middle" class="header-middle">
				foobar
			</div>
		</div>
		<div id="mainwrapper" class="mainwrapper">
			<div id="maincontent" class="maincontent">
				Welcome!
			</div>
			<div style="float:left;width:100%;margin-top:20px;">
				SEARCH TERM: <?php echo $search; ?><br />
				STATUS OF REQUEST: <?php echo $stats; ?><br />
				TOTAL WINES RETURNED: <?php echo $total; ?><br />
				RETURN CODE: <?php echo $rcode; ?><br />
				MESSAGES: <?php echo $mtext; ?><br />
			</div>
			<?php echo $winehtml; ?>
		</div>
		<div class="footer"></div>

	</body>
</html>