<?php

	if(isset($_POST['libcheck']) && !empty($_POST['libcheck'])){
		define('LIBRARY_CHECK', true);
	}

	if(!defined('LIBRARY_CHECK')){
		die ('<div style="width:100%;height:100%;text-align:center;">
				<div style="width:100%;font-family:Georgia;font-size:2em;margin-top:100px;">
					Sorry, this isn\'t a real page, so I have nothing to show you :-(
				</div>
				<div style="width:100%;font-family:Georgia;font-size:2em;margin-top:30px;margin-bottom:30px;">Wait, here\'s a funny cat!</div>
				<div style="background-repeat:no-repeat;margin-left:auto;margin-right:auto;width:500px;height:280px;background:url(../images/cat.gif)">
				</div>
			</div>');
	}

	define('INCLUDE_CHECK',true);
	require 'connect.php';
	require 'wine.php';
	date_default_timezone_set('America/New_York');

	$webaddress = "http://swirlandsipwine.com/";

	if(isset($_POST['method']) && !empty($_POST['method']))
	{
		$method = $_POST['method'];
		$method = urldecode($method);
		$method = mysql_real_escape_string($method);

		switch($method)
		{
			case 'getWineInfo': getWineInfo($_POST);
				break;
			case 'updateUser': updateUser($_POST);
				break;
			case 'createUser': createUser($_POST);
				break;
			case 'updatePassword': updatePassword($_POST);
				break;
			default: noFunction($_POST);
				break;
		}
		mysql_close($link);
	}


	function noFunction()
	{
		$func = $_POST['method'];
		$result = array(
				"status"	=> "failure",
				"message"	=> "User attempted to call function: " . $func . " which does not exist",
				"content"	=> "You seem to have encountered an error - Contact the DHD web admin if this keeps happening!"
		);
		echo json_encode($result);
	}


	function getWineInfo($P, $ajax = true)
	{
		$P = escapeArray($P);
		$search = urlencode($P['search']);

		$status  = "failure";
		$message = "";
		$result	 = array();
		$content = array();

		$wine = new Wine();
		$wine->loadWineData($search);

		$rcode = $wine->getRequestCode();

		$total = 0;
	 	$wines = array();

		$constat = $wine->getStatus();
		$conmsgs = $wine->getErrorMessages();

		if($rcode == 0)
		{
			$total = $wine->getWineCount() > 10 ? 10 : $wine->getWineCount();
			$wntmp = array();
			for($i = 0; $i < $total; $i++)
			{
				$wntmp['id'] 			= $wine->getWineId($i);
				$wntmp['name'] 			= $wine->getWineName($i);
				$wntmp['vineyardname'] 	= $wine->getVineyard($i);
				$wntmp['maxprice'] 		= $wine->getPriceMax($i);
				$wntmp['minprice'] 		= $wine->getPriceMin($i);
				$wntmp['retailprice'] 	= $wine->getWinePrice($i);
				$wntmp['type'] 			= $wine->getWineType($i);
				$wntmp['year'] 			= $wine->getWineYear($i);
				$wntmp['appellation'] 	= $wine->getAppellation($i)['appellation'];
				$wntmp['region'] 		= $wine->getAppellation($i)['region'];
				$wntmp['rating'] 		= $wine->getRatings($i);
				$wntmp['label'] 		= "http://cache.wine.com/labels/" . $wine->getWineId($i) . "l.jpg"; //$wine->getLabels($i)[0];
				$wntmp['varietal'] 		= $wine->getVarietal($i)['name'];
				$wntmp['varietaltype'] 	= $wine->getVarietal($i)['type'];
				$wntmp['attributes'] 	= implode(", ", $wine->getProductAttributes($i));
				array_push($wines, $wntmp);
			}

			$status = "success";
			$message = "request completed successfully";
		}

		unset($wine);

		$content['wines']			= $wines;
		$content['status']			= $constat;
		$content['error_messages']	= $conmsgs;
		$content['response_code']	= $rcode;
		$content['num_recs']		= $total;

		$result = array(
				"status"  => $status,
				"message" => $message,
				"content" => $content
		);

 		if($ajax){
 			echo json_encode($result);
 		}
 		else {
			return $result;
 		}
	}


	function createUser($P)
	{
		global $webaddress;

		$P = escapeArray($P);

		$uname  = $P['username'];
		$fname  = $P['firstname'];
		$lname  = $P['lastname'];
		$email  = $P['useremail'];

		$status  = "success";
		$message = "";
		$content = "";

		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$count = mb_strlen($chars);
		$password = "";
		$length = 8;
		for ($i = 0, $password = ''; $i < $length; $i++)
		{
			$index = rand(0, $count - 1);
			$password .= mb_substr($chars, $index, 1);
		}
		$hashedpassword = md5($password);

		$insertuser = mysql_query( "INSERT INTO
									eventadmin (USER, FIRST, LAST, EMAIL, PASSWORD)
									VALUES ('" . $uname . "', '" . $fname . "', '" . $lname . "', '" . $email . "', '" . $hashedpassword . "');");
		$userid = mysql_insert_id();
		if(mysql_errno())
		{
			$status = "error";
			$message = "There was a problem with the database - please call your administrator";
			//$message = "MySQL error " . mysql_errno() . ": " . mysql_error();
		}
		else
		{
			$message = "New user created!";
			$to      =  $email;
			$subject =  "New Account Created";
			$emailmessage =  "Hello,\r\n\r\nYour account has been created!\r\n\r\nYour login information is:\r\n" .
					"username: " . $uname . "\r\npassword: " . $password . "\r\n\r\n" .
					"Please go here to login and change your password:\r\n" .
					$webaddress . "tim/login.php";
			$headers =  "From: admins@doghousediaries.com" . "\r\n" .
					"Reply-To: admins@doghousediaries.com" . "\r\n" .
					"X-Mailer: PHP/" . phpversion();
			mail($to, $subject, $emailmessage, $headers);

			$content = "";
			$userquery = mysql_query("SELECT * FROM eventadmin ORDER BY ID ASC;");
			$content = "<table style='border-collapse:collapse;width:100%;'>
							<tr class='headerrow'>
								<td>username</td>
								<td>first name</td>
								<td>last name</td>
								<td>email</td><td></td><td></td></tr>";
			$count = 0;
			while($row = mysql_fetch_assoc($userquery))
			{
				$count++;
				$altclass = ($count % 2) ? "" : "altrow";
				$btnhtml  = ($row['ID'] == $_SESSION['userid']) ? 
								"<input type='button' class='updateuserbtn' value='Update' onclick='showUpdateUserForm(" . $row['ID'] . ")' />" : 
									"";
				$passhtml = ($row['ID'] == $_SESSION['userid']) ? 
								"<input type='button' class='passbtn' value='Change Password' onclick='showUpdatePasswordForm(" . $row['ID'] . ")' />" : 
									"";
				$content .= "   <input type='hidden' id='unamehdn" . $row['ID'] . "' value='" . $row['USER'] . "' />
								<input type='hidden' id='firsthdn" . $row['ID'] . "' value='" . $row['FIRST'] . "' />
								<input type='hidden' id='lasthdn" . $row['ID'] . "' value='" . $row['LAST'] . "' />
								<input type='hidden' id='emailhdn" . $row['ID'] . "' value='" . $row['EMAIL'] . "' />
								<tr class='tablerow'" . $altclass . ">
									<td id='tduname" . $row['ID'] . "'>" . $row['USER'] . "</td>
									<td id='tdfname" . $row['ID'] . "'>" . $row['FIRST'] . "</td>
									<td id='tdlname" . $row['ID'] . "'>" . $row['LAST'] . "</td>
									<td id='tdemail" . $row['ID'] . "'>" . $row['EMAIL'] . "</td>
									<td>" . $btnhtml . "</td>
									<td>" . $passhtml . "</td></tr>";
			}
			$content .= "</table>";
		}

		$result = array(
				"status"	=> $status,
				"message"	=> $message,
				"content"	=> $content
		);

		echo json_encode($result);
	}


	function updateUser($P)
	{
		$P = escapeArray($P);

		$userid = $P['userid'];
		$uname  = $P['username'];
		$fname  = $P['firstname'];
		$lname  = $P['lastname'];
		$email  = $P['useremail'];

		$status  = "success";
		$message = "";
		$content = "";

		$update = mysql_query( "UPDATE eventadmin
								SET USER='"  . $uname . "',
									FIRST='" . $fname . "',
									LAST='"  . $lname . "',
									EMAIL='" . $email . "'
								WHERE ID=" . $userid . ";");
		if(mysql_errno())
		{
			$status = "error";
			$message = "There was a problem with the database - please call your administrator";
			//$message = "MySQL error " . mysql_errno() . ": " . mysql_error();
		}
		else
		{
			$message = "Your information has been updated!";
			$_SESSION['username'] = $uname;
		}

		$result = array(
				"status"	=> $status,
				"message"	=> $message,
				"content"	=> $content
		);

		echo json_encode($result);
	}


	function updatePassword($P)
	{
		$P = escapeArray($P);

		$userid 	= $P['userid'];
		$currpass  	= $P['currpass'];
		$newpass  	= $P['newpass'];

		$status  = "success";
		$message = "";
		$content = "";

		$checkpass = mysql_fetch_assoc(mysql_query("SELECT ID FROM eventadmin WHERE ID='" . $userid . "' AND PASSWORD='" . md5($currpass) . "';"));

		if(isset($checkpass['ID']) && $checkpass['ID'] != "")
		{
			$update = mysql_query( "UPDATE eventadmin
									SET PASSWORD='"  . md5($newpass) . "'
									WHERE ID=" . $userid . ";");
			if(mysql_errno())
			{
				$status = "error";
				$message = "There was a problem with the database - please call your administrator";
				//$message = "MySQL error " . mysql_errno() . ": " . mysql_error();
			}
			else
			{
				$message = "Your password has been updated!";
			}
		}
		else
		{
			$status = "error";
			$message = "Please check your current password!";
		}

		$result = array(
			"status"	=> $status,
			"message"	=> $message,
			"content"	=> $content
		);

		echo json_encode($result);
	}


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
