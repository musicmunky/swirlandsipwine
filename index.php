<?php


/*

	POSSIBLE FONT FOR LOGO:  Kabel DT Condensed

*/


	//require 'php/shutdown.php';
	define('LIBRARY_CHECK',true);
	require 'php/saswlib.php';

	if(!isset($_SESSION))
	{
		session_name('swirlandsipwine');
		session_start();
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11-strict.dtd">
<html>
	<head><?php include("includes/head.html"); ?></head>
	<body>

		<?php include("includes/header.html"); ?>

		<div id="mainwrapper" class="mainwrapper">
			<div id="maincontent" class="maincontent">
				Welcome to Swirl and Sip!
			</div>
			<div id="wineinfo" style="width:100%;float:left;"></div>
		</div>

		<?php include("includes/footer.html"); ?>

	</body>
</html>