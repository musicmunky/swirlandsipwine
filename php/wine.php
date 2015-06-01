<?php

	class Wine
	{
		private $apikey = "";
		private $apiurl = "";
		private $windat = "";
		private $wincnt = 0;
		private $rqcode = -1;
		private $status = "";
		private $conmsg = array();
		private $errmsg = array(
			0 	=> "Everything worked, no problems.",
			100 => "A critical error was encountered. This is due to a bug in the service. Please notify wine.com ASAP to correct this issue.",
			200 => "Unable to Authorize. We cannot authorize this account.",
			300 => "No Access. Account does not have access to this service."
		);

/*
	Parameter 	Description 														Example
	========================================================================================================================================
	apikey 		The key given to the developer that is requesting the resource. 	3scale-F92B6F756B784DB88EE29EB2BFA054C0
	version 	The version of the API to use. Allows us to have multiple live		Current release is beta.
				versions running at the same time.									v1.0
																					v2.3
	format 		Determines the return format. 										Supported values are: XML and JSON
	resource 	The resource being requested.										Supported values are: catalog, reference, and catalogmap
	parameters 	The various resource parameters that are required to render the		Examples:
				results.															size=25&offset=10&filter=categories(7155+124)
																					term=mondavi+cab
	affiliateId Optional parameter for affiliates.

	Example Link:
		http://services.wine.com/api/beta2/service.svc/<format>/<resource>?apikey=<key>&<parameters>
		http://services.wine.com/api/beta2/service.svc/JSON/catalog?apikey=7636169f537a17119ac9c4cd2dc8a256&term=ornellaia+2006

	API Reference:
		https://api.wine.com/wiki/api-object-dictionary#_catalog_objects
*/

		/**
		* Create a new instance
		*
		* @param Boolean $loadfromdb
		* @param String $apikey
		* @param String $apiurl
		*/
		function __construct($loadfromdb, $apikey = "", $apiurl = "")
		{
			if($loadfromdb)
			{
				try {
					$apinfo = mysql_fetch_assoc(mysql_query("SELECT APIKEY, URL FROM wineapikeys WHERE SERVICE='wine.com';"));
					$apikey = $apinfo['APIKEY'];
					$apiurl = $apinfo['URL'];
				}
				catch(Exception $e) {}

			}
			$this->apikey = $apikey;
			$this->apiurl = $apiurl;
		}

		public function loadWineData($srch, $format = "JSON", $resource = "catalog")
		{
			try
			{
				$url = $this->getApiUrl();
				$key = $this->getApiKey();

				if($srch == "")
				{
					throw new Exception("Invalid search string");
				}

				if($url == "" || $key == "")
				{
					throw new Exception("Invalid parameters set for Wine object - please check value of Key and Url");
				}

				$search   = urlencode($srch);
				$requrl   = $url . $format . "/" . $resource . "?apikey=" . $key . "&term=" . $search;
				$content  = file_get_contents($requrl);
				$rescon   = json_decode($content, true);

				if(!isset($rescon['Status']['ReturnCode']) || $rescon['Status']['ReturnCode'] != 0)
				{
					throw new Exception("Bad result from wine server - please check parameters and try again");
				}

 				$wd = $this->setWineData($rescon['Products']['List']);
				if(!$wd)
				{
					throw new Exception("Error attempting to create Wine object");
				}

				$this->setWineCount($rescon['Products']['Total']);
				$this->setRequestCode($rescon['Status']['ReturnCode']);
			}
			catch(Exception $e)
			{
				$this->setErrorMessages($rescon['Status']['Messages']);
				$this->setStatus("ERROR: " . $e->getMessage() . "<br>" . $this->errmsg[$rescon['Status']['ReturnCode']]);
				$this->setRequestCode($rescon['Status']['ReturnCode']);
				$this->setWineData(array("ERROR" => $e->getMessage()));
			}
		}

		private function getApiKey()
		{
			return $this->apikey;
		}

		private function setApiKey($key)
		{
			$r = true;
			try {
				$this->apikey = $key;
			}
			catch(Exception $e) {
				$r = false;
			}
			return $r;
		}

		private function getApiUrl()
		{
			return $this->apiurl;
		}

		private function setApiUrl($url)
		{
			$r = true;
			try {
				$this->apiurl = $url;
			}
			catch(Exception $e) {
				$r = false;
			}
			return $r;
		}

		public function getStatus()
		{
			return $this->status;
		}

		private function setStatus($s)
		{
			$r = true;
			try {
				$this->status = $this->errmsg[$s];
			}
			catch(Exception $e) {
				$r = false;
			}
			return $r;
		}

		public function getRequestCode()
		{
			return $this->rqcode;
		}

		private function setRequestCode($c)
		{
			$r = true;
			try {
				$this->rqcode = $c;
			}
			catch(Exception $e) {
				$r = false;
			}
			return $r;
		}

		public function getWineCount()
		{
			return $this->wincnt;
		}

		private function setWineCount($c)
		{
			$r = true;
			try {
				$this->wincnt = $c;
			}
			catch(Exception $e) {
				$r = false;
			}
			return $r;
		}

		public function getErrorMessages()
		{
			return $this->conmsg;
		}

		private function setErrorMessages($m)
		{
			$r = true;
			try {
				$this->conmsg = $m;
			}
			catch(Exception $e) {
				$r = false;
			}
			return $r;
		}

		public function getWineData()
		{
			return $this->windat;
		}

		private function setWineData($w)
		{
			$r = true;
			try {
				$this->windat = $w;
			}
			catch(Exception $e) {
				$r = false;
			}
			return $r;
		}


		private function getWineInfo($k)
		{}
/*
		public function getLatitude($type = "string")
		{
			return $this->getLatLongInformation("lat", $type);
		}

		public function getLongitude($type = "string")
		{
			return $this->getLatLongInformation("lng", $type);
		}

		public function getLatLong($type = "1DArray")
		{
			return $this->getLatLongInformation("latlng", $type);
		}

		public function getFormattedAddress($type = "string")
		{
			return $this->getPlaceInformation("formatted_address", $type);
		}

		public function getPlaceID($type = "string")
		{
			return $this->getPlaceInformation("place_id", $type);
		}

		public function getSuburb($type = "string")
		{
			return $this->getAddressComponents("suburb", $type);
		}

		public function getCity($type = "string")
		{
			return $this->getAddressComponents("city", $type);
		}

		public function getState($type = "string")
		{
			return $this->getAddressComponents("state", $type);
		}

		public function getCountry($type = "string")
		{
			return $this->getAddressComponents("country", $type);
		}

		public function getZipCode($type = "string")
		{
			return $this->getAddressComponents("zipcode", $type);
		}

		public function getResultCount()
		{
			return $this->rescnt;
		}

		public function getGeoData()
		{
			return $this->geodat;
		}

		private function getLatLongInformation($k, $type)
		{
			$rval = array();
			$rslt = $this->getGeoData();
			$alen = count($rslt);
			try {
				if($k == "latlng"){
					if($type == "array" || $type == "a"){
						for($i = 0; $i < $alen; $i++)
						{
							array_push($rval, array($rslt[$i]['geometry']['location']['lat'], $rslt[$i]['geometry']['location']['lng']));
						}
					}
					else
					{
						array_push($rval, array($rslt[0]['geometry']['location']['lat'], $rslt[0]['geometry']['location']['lng']));
					}
				}
				else
				{
					if($type == "array" || $type == "a"){
						for($i = 0; $i < $alen; $i++)
						{
							array_push($rval, $rslt[$i]['geometry']['location'][$k]);
						}
					}
					else
					{
						$rval = $rslt[0]['geometry']['location'][$k];
					}
				}

			} catch(Exception $e) {}
			return $rval;
		}

		private function getPlaceInformation($k, $type)
		{
			$rval = array();
			$kval = $k;
			$rslt = $this->getGeoData();
			$alen = count($rslt);
			try {
				if($type == "array" || $type == "a"){
					for($i = 0; $i < $alen; $i++)
					{
						array_push($rval, $rslt[$i][$kval]);
					}
				}
				else
				{
					$rval = $rslt[0][$kval];
				}
			} catch(Exception $e) { $rval = $this->status; }
			return $rval;
		}

		private function getAddressComponents($k, $type)
		{
			$rval = array();
			$kval = $this->lookup[$k];
			$rslt = $this->getGeoData();
			$alen = count($rslt);
			try {
				if($type == "array" || $type == "a"){
					for($i = 0; $i < $alen; $i++)
					{
						$addcomps = $rslt[$i]['address_components'];
						$flag = 0;
						for($j = 0; $j < count($addcomps); $j++)
						{
							if($addcomps[$j]['types'][0] == $kval)
							{
								array_push($rval, array("long_name" => $addcomps[$j]['long_name'], "short_name" => $addcomps[$j]['short_name']));
								$flag = 1;
								break;
							}
						}
						if($flag == 0){
							array_push($rval, array("long_name" => "NO_DATA_FOR_KEY " . $kval, "short_name" => "NO_DATA"));
						}
					}
				}
				else
				{
					$addcomps = $rslt[0]['address_components'];
					$flag = 0;
					for($j = 0; $j < count($addcomps); $j++)
					{
						if($addcomps[$j]['types'][0] == $kval)
						{
							$rval = ($kval == "locality") ? $addcomps[$j]['long_name'] : $addcomps[$j]['short_name'];
							$flag = 1;
							break;
						}
					}
					if($flag == 0){
						$rval = "NO_DATA";
					}
				}
			} catch(Exception $e) { $rval = $this->status; }
			return $rval;
		}
*/

	}

?>