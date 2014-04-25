<?php

/*
 * Eqiat
 * Easy QTI Item Authoring Tool
 */

/*------------------------------------------------------------------------------
(c) 2010 JISC-funded EASiHE project, University of Southampton
Licensed under the Creative Commons 'Attribution non-commercial share alike' 
licence -- see the LICENCE file for more details
------------------------------------------------------------------------------*/


class DepositInWikiAction extends ItemAction {
	private $ai;
	private $errors = array();
	


	public function name() {
		return "deposit in Wiki";
	}

	public function description() {
		return "Deposit this item as a xml file in Wiki";
	}

	public function beforeLogic() {
		$this->ai = QTIAssessmentItem::fromQTIID($_REQUEST["qtiid"]);
		
	}

	public function getLogic() {
		// get login details if we don't already have them
		if ( !$this->haveLogin()) {
			$this->getLogin();
			return;
		}

		// show the deposit form
		$GLOBALS["title"] = "Deposit item in Wiki";
		include "htmlheader.php";
		?>
		<h2>Deposit item "<?php echo htmlspecialchars($this->ai->data("title")); ?>" in Wiki</h2>

		<?php if (!empty($this->errors)) showmessages($this->errors, "Error" . plural($this->errors), "error"); ?>

		<form action="<?php echo $this->actionURL($this->ai); ?>" method="post">
			"this is a test"
		<input type="submit" name="deposit" value="Deposit">
			
		</form>
		<?php
	
		include "htmlfooter.php";
	}

	public function postLogic() {
			
		
		// get login details if we don't already have them
		if (!$this->haveLogin()) {
			$this->postLogin();
			return;
		}

		// clear errors
		$this->errors = array();
		
		$myFile = "Faten.txt";
		$fh = fopen($myFile, 'w') or die("can't open file");
		
		fwrite($fh,'ID '.session_id()."\n");
		fwrite($fh,$_COOKIE["WikiSession"]."\n");
		
        $test=array();
        
		$session_save_handler='files';
		$session_name="WikiSession";
		
		$test=getSessionData($session_name,$session_save_handler);
        
        foreach( $test as $key => $value){
        	$datastring= "$key: $value\n";
        	fwrite($fh,$datastring);
        }
        
		/*foreach( $_SESSION as $key => $value){
			$datastring= "$key: $value\n";
			fwrite($fh,$datastring);
		}*/
		fclose($fh);
		
		$request['question']=$this->ai->getQTIIndentedString();
		
	    
		
		header("Location: http://localhost/Wiki/index.php?title=test&action=edit");

		/*$curl = curl_init();
		
		curl_setopt_array($curl,array(
			CURLOPT_URL				=>"localhost/Wiki/index.php?title=test&action=submit",
			CURLOPT_POST			=>	true,
			CURLOPT_HEADER			=>	true,
			CURLOPT_USERPWD			=>	$_SESSION[SESSION_PREFIX . "diea_username"] . ":" . $_SESSION[SESSION_PREFIX . "diea_password"],
			CURLOPT_RETURNTRANSFER	=>	true,
			CURLOPT_POSTFIELDS		=>	array("wpTextbox1"=> $this->ai->getQTIIndentedString(),),
			CURLOPT_HTTPHEADER		=>	array("Content-Type: application/xml"),));
		
		$response = curl_exec($curl);
		$responseinfo = curl_getinfo($curl);
		
			
		$code = $responseinfo["http_code"];
		$headers = response_headers($response);
		$body = response_body($response);
		
		switch ($code) {
			case 401:
				unset($_SESSION[SESSION_PREFIX . "diea_username"], $_SESSION[SESSION_PREFIX . "diea_password"]);
				$this->errors[] = "There was an authorization problem" . (isset($headers["X-Error-Code"]) ? ": " . $headers["X-Error-Code"] : "");
				$this->getLogic();
				return;
			case 201:
				// parse XML response
				$xml = simplexml_load_string($body);
				$eprintid = (integer) $xml->id;
				$treatment = null;
				foreach ($xml->children("http://purl.org/net/sword/") as $child) {
					if ($child->getName() == "treatment") {
						$treatment = (string) $child;
						break;
					}
				}

				// show "deposited" message and give link to the share
				$GLOBALS["title"] = "Item deposited in Wiki";
			
				break;
			default:
				$this->errors[] = "Unexpected response: " . $responseinfo["http_code"] . ". Response headers follow:";
				foreach ($headers as $k => $v)
					$this->errors[] = "$k: $v";
				$this->getLogic();
				return;
		}
		*/
	}

	
	public function available(QTIAssessmentItem $ai) {
		//return $ai->itemOK();
		return false;
	}

	// return true if we have good login details
	private function haveLogin() {
		return isset($_SESSION[SESSION_PREFIX . "diea_username"]);
	}

	// give an HTML form to collect login details
	private function getLogin() {
		
		$GLOBALS["title"] = "Deposit item in Wiki login details required";
		include "htmlheader.php";
		?>
		<h2>Deposit item "<?php echo htmlspecialchars($this->ai->data("title")); ?>" in Wiki</h2>

		<?php if (!empty($this->errors)) showmessages($this->errors, "Error" . plural($this->errors), "error"); ?>

		<p>Before the item can be deposited you need to provide your username 
		and password for the repository. This information is stored only for 
		this session.</p>
		<form action="<?php echo $this->actionURL($this->ai); ?>" method="post">
			<dl>
				<dt><label for="diea_username">Username</label></dt>
				<dd><input type="text" size="64" name="diea_username" id="diea_username"<?php if (isset($_POST["diea_username"])) { ?> value="<?php echo htmlspecialchars($_POST["diea_username"]); ?>"<?php } ?>></dd>

				<dt><label for="diea_password">Password</label></dt>
				<dd><input type="password" size="64" name="diea_password" id="diea_password"></dd>

				<dt></dt>
				<dd><input type="submit" name="edsharelogin" value="Log in"></dd>
			</dl>
		</form>
		<?php
		
		include "htmlfooter.php";
	}

	// handle the login details form being posted
	private function postLogin() {
		// clear errors
		$this->errors = array();

		// check a username and password were given
		if (!isset($_POST["diea_username"]) || empty($_POST["diea_username"]) || !isset($_POST["diea_password"]) || empty($_POST["diea_password"])) {
			$this->errors[] = "Wiki username or password not given";
			$this->getLogin();
			return;
		}

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL				=>	"http://localhost/Wiki/index.php?title=Special:UserLogin&returnto=Main+Page",
			CURLOPT_POST			=>	false,
			CURLOPT_HEADER			=>	true,
			CURLOPT_USERPWD			=>	$_POST["diea_username"] . ":" . $_POST["diea_password"],
			CURLOPT_RETURNTRANSFER	=>	true,
			CURLOPT_HTTPHEADER		=>	array(
				"Expect: ",
			),
		));
		$response = curl_exec($curl);
		$responseinfo = curl_getinfo($curl);
		$code = $responseinfo["http_code"];
		$headers = response_headers($response);
		$body = response_body($response);

		switch ($code) {
			case 401:
				// bad username/password
				unset($_SESSION[SESSION_PREFIX . "diea_username"], $_SESSION[SESSION_PREFIX . "diea_password"], $_SESSION[SESSION_PREFIX . "diea_servicedocxml"]);
				$this->errors[] = "There was an authorization problem" . (isset($headers["X-Error-Code"]) ? ": " . $headers["X-Error-Code"] : "");
				$this->getLogic();
				return;
			case 200:
				// parse XML response
				$_SESSION[SESSION_PREFIX . "diea_username"] = $_POST["diea_username"];
				$_SESSION[SESSION_PREFIX . "diea_password"] = $_POST["diea_password"];
				$_SESSION[SESSION_PREFIX . "diea_servicedocxml"] = $body;

				$this->getLogic();
				return;
			default:
				// unexpected response
				$this->errors[] = "Unexpected response: " . $responseinfo["http_code"] . ". Response headers follow:";
				foreach ($headers as $k => $v)
					$this->errors[] = "$k: $v";
				$this->getLogic();
				return;
		}
	}
}

?>
