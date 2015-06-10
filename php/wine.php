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
		private $wintyp = array();

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
		http://services.wine.com/api/beta2/service.svc/JSON/catalog?apikey=3scale-F92B6F756B784DB88EE29EB2BFA054C0&term=mondavi+cab

	API Reference:
		https://api.wine.com/wiki/api-object-dictionary#_catalog_objects
*/

		/**
		* Create a new instance
		* LINK FOR EXAMPLE:
		* http://stackoverflow.com/questions/1699796/best-way-to-do-multiple-constructors-in-php/28123116#28123116
		*/
		function __construct()
		{
			$get_arguments       = func_get_args();
			$number_of_arguments = func_num_args();

			if(method_exists($this, $method_name = '__construct' . $number_of_arguments))
			{
				call_user_func_array(array($this, $method_name), $get_arguments);
			}
		}


		/**
		* Create a new instance, no parameters
		*/
		function __construct0()
		{
			$apikey = "";
			$apiurl = "";

			try {
				$apinfo = mysql_fetch_assoc(mysql_query("SELECT APIKEY, URL FROM wineapikeys WHERE SERVICE='wine.com';"));
				$apikey = $apinfo['APIKEY'];
				$apiurl = $apinfo['URL'];
			}
			catch(Exception $e) {}

			$this->apikey = $apikey;
			$this->apiurl = $apiurl;
		}


		/**
		* Create a new instance, two parameters
		*
		* @param String $key
		* @param String $url
		*/
		function __construct2($key, $url)
		{
			$this->apikey = $key;
			$this->apiurl = $url;
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
				$requrl   = $url . $format . "/" . $resource . "?apikey=" . $key . "&size=100&term=" . $search;
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

				//$this->setWineCount($rescon['Products']['Total']);
				$this->setRequestCode($rescon['Status']['ReturnCode']);
				$this->setStatus($rescon['Status']['ReturnCode']);
			}
			catch(Exception $e)
			{
				$this->setErrorMessages($rescon['Status']['Messages']);
				$this->setStatus($rescon['Status']['ReturnCode'], "ERROR: " . $e->getMessage());
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

		private function setStatus($s, $devmsg = "")
		{
			$r = true;
			try {
				$dm = $devmsg == "" ? "" : $devmsg . "<br>";
				$this->status = $dm . $this->errmsg[$s];
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
			$data = array();
			try {
				for($i = 0; $i < count($w); $i++)
				{
					if(isset($w[$i]['Name']) && $w[$i]['Name'] != "")
					{
						array_push($data, $w[$i]);
					}
				}
			}
			catch(Exception $e) {
				$r = false;
			}
			$this->windat = $data;
                        $this->setWineCount(count($data));
			return $r;
		}

		private function getWineInfo($k, $n = 0)
		{
			$result = "";
			if(isset($this->getWineData()[$n]) && gettype($this->getWineData()[$n]) != "NULL")
			{
				if(isset($this->getWineData()[$n][$k]) && gettype($this->getWineData()[$n][$k] != "NULL"))
				{
					$result = $this->getWineData()[$n][$k];
				}
			}
			return $result;
		}

		public function getWineId($n = 0)
		{
			return $this->getWineInfo("Id", $n);
		}

		public function getWineVintage($n = 0)
		{
			$vinarray = array();
			$winename = $this->getWineName($n);
			try {
				//preg_match_all("/(19\d{2}|2\d{3})/", $winename, $vinarray);
				preg_match("/(19\d{2}|2\d{3})/", $winename, $vinarray);
			}
			catch(Exception $e) { $vinarray[0] = "ERROR: " . $e->getMessage(); }
			$nodups = array_unique($vinarray);
			return $nodups;
		}

		public function getWineName($n = 0)
		{
			return $this->getWineInfo("Name", $n);
		}

		public function getWineUrl($n = 0)
		{
			return $this->getWineInfo("Url", $n);
		}

		public function getWinePrice($n = 0)
		{
			return $this->getWineInfo("PriceRetail", $n);
		}

		public function getPriceMax($n = 0)
		{
			return $this->getWineInfo("PriceMax", $n);
		}

		public function getPriceMin($n = 0)
		{
			return $this->getWineInfo("PriceMin", $n);
		}

		public function getWineType($n = 0)
		{
			return $this->getWineInfo("Type", $n);
		}

		public function getWineYear($n = 0)
		{
			return $this->getWineInfo("Year", $n);
		}

		public function getAppellation($n = 0)
		{
			$appell = array("appellation" => "", "region" => "");
			try {
				$a = $this->getWineInfo("Appellation", $n);
				if($a != "")
				{
					if(isset($a['Name'])) {
						$appell['appellation'] = $a['Name'];
					}
					if(isset($a['Region']['Name'])) {
						$appell['region'] = $a['Region']['Name'];
					}
				}
			}
			catch(Exception $e){}
			return $appell;
		}

		public function getRatings($n = 0)
		{
			$rating = 0;
			try {
				$r = $this->getWineInfo("Ratings", $n);
				if(isset($r['HighestScore']))
				{
					$rating = $r['HighestScore'];
				}
			}
			catch(Exception $e){}
			return $rating;
		}

		public function getLabels($n = 0)
		{
			$labels = array();
			try {
				$l = $this->getWineInfo("Labels", $n);
				for($i = 0; $i < count($l); $i++)
				{
					if(isset($l[$i]['Url'])){
						array_push($labels, $l[$i]['Url']);
					}
				}
			}
			catch(Exception $e){}
			return $labels;
		}

		public function getVarietal($n = 0)
		{
			$varietal = array("name" => "", "type" => "");
			try {
				$v = $this->getWineInfo("Varietal", $n);
				if(isset($v['Name']))
				{
					$varietal['name'] = $v['Name'];
					if(isset($v['WineType']['Name']))
					{
						$varietal['type'] = $v['WineType']['Name'];
					}
				}
			}
			catch(Exception $e){}
			return $varietal;
		}

		public function getVineyard($n = 0)
		{
			$vineyard = "";
			try {
				$v = $this->getWineInfo("Vineyard", $n);
				if(isset($v['Name']))
				{
					$vineyard = $v['Name'];
				}
			}
			catch(Exception $e){}
			return $vineyard;
		}

		public function getProductAttributes($n = 0)
		{
			$attr = array();
			try{
				$a = $this->getWineInfo("ProductAttributes", $n);
				for($i = 0; $i < count($a); $i++)
				{
					if(isset($a[$i]['Name'])){
						array_push($attr, $a[$i]['Name']);
					}
				}
			}
			catch(Exception $e){}
			return $attr;
		}

	}

?>
