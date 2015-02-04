<?php
/**
 * Belvilla_jspnrpc_class
 * 
 * This file calls the @leisure jsonrpc-server
 * and returns the returned data.
 * @author Kurt Cleeren <webtech@leisure-group.eu>
 * @version 1.0
 * @package jsonrpc
 */
class belvilla_jsonrpcCall {
	private $__request;
	private $__call;
	private $__params = array();
	private $__result;
	private $__jsonrpcversion = '2.0';
	private $__fileLogging = false;
	private $__logfile = "";
	private $__expObj;
	private $__dbLink;
	private $__dbLogging = false;
	private $__logEmail = "";
	private $__logFromEmail = "";
	private $__emailLogging = false;
	
	private $__username = '';
	private $__pass = '';
	
	private $__tries_call = array();

	public function belvilla_jsonrpcCall($WebpartnerCode, $WebpartnerPassword) {
		$this->__username = $WebpartnerCode;
		$this->__pass = $WebpartnerPassword;
	}
	
	public function makeCall($call,$params = array()) { 
		$this->__call = strtolower($call);
		
		//keep track of number of times that has been tried to call this call
		if(!array_key_exists($this->__call, $this->__tries_call)) $this->__tries_call[$this->__call] = 1;
		else $this->__tries_call[$this->__call] += 1;
		
		$this->__params = $params; //array
		$this->__params["WebpartnerCode"] = $this->__username;
		$this->__params["WebpartnerPassword"] = $this->__pass;
		
		$this->__request = json_encode(
			array(
				'jsonrpc' => $this->__jsonrpcversion,
				'method' => $this->__call,
				'params' => $this->__params,
				'id' => "id_".$this->__call
			)
		);
		
		$url = "https://".$this->__call.".jsonrpc-partner.net/cgi/lars/jsonrpc-partner/jsonrpc.htm";
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: application/json', 'Accept-Encoding: gzip, deflate'));
		curl_setopt($ch, CURLOPT_ENCODING, 1); /* Unzips the answer */ /*remove if you want to save the gz file -> if($handle = fopen('/data/www/demo.leisure-partners.net/json2db/test.gz',"w")){fwrite($handle,$result);}*/
		curl_setopt($ch, CURLOPT_SSLVERSION,4); /* Due to an OpenSSL issue */ 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  /* Due to a wildcard certificate */
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->__request);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		if ($result = curl_exec($ch)) {
			$this->__result = $result;
			
			if($res = json_decode($result)){
				if(isset($res->error)){//if an error occured, throw an Exception
					$this->__expObj = new Exception($res->error->message,$res->error->code);
					$this->__logError();
					
					throw $this->__expObj;
				}
			}else{
				$this->__expObj = new Exception(json_last_error());
				$this->__logError();
				
				throw $this->__expObj;
			}
		}else{//if curl gave errors then throw Exception with curl_error as content
			//most of the time this has something to do with that the host isn't resolvable.
			if($this->__tries_call[$this->__call] <= 3){
				sleep(1); //wait 1 second before trying again
				$this->makeCall($call,$params);
			}else{
				$this->__expObj = new Exception(curl_error($ch));
				$this->__logError();
			
				throw $this->__expObj;
			}
		}
		
		curl_close($ch);
	}
	
	public function callConnected() {
		return $this->connect_state;
	}
	
	public function getResult($outputtype="json") {
		//$this->__tries_call[$this->__call] = 0;
		switch($outputtype){
			case "array":
				$j = json_decode($this->__result,true);
				return $j["result"];
			break;
			case "json":
			default:
				$j = json_decode($this->__result);
				return $j->result;
			break;
		}
	}
	
	public function getRawResult($outputtype="") {
		$this->__tries_call[$this->__call] = 0;
		switch($outputtype){
			case "array":
				$j = json_decode($this->__result,true);
				return $j;
			break;
			case "json":
				$j = json_decode($this->__result);
				return $j;
			break;
			default:
				return $this->__result;
			break;
		}
	}
	
	public function printParams() { 
		echo "<pre>".print_r($this->__params,true)."</pre>";
	}
	
	public function printRequest() { 
		echo "<pre>"; var_dump($this->__request); echo "</pre>";
	}
	
	public function printResult($outputtype="json") {
		switch($outputtype){
			case "array":
				echo "<pre>".print_r($this->getResult("array"),true)."</pre>";
			break;
			default:
			case "json":
				echo "<pre>".print_r($this->getResult("json"),true)."</pre>";
			break;
		}
	}
	
	public function printRawResult($outputtype="") {
		switch($outputtype){
			case "array":
				echo "<pre>".print_r($this->getRawResult("array"),true)."</pre>";
			break;
			case "json":
				echo "<pre>".print_r($this->getRawResult("json"),true)."</pre>";
			break;
			default:
				echo "<pre>".print_r($this->getRawResult(),true)."</pre>";
			break;
		}
		
	}

	public function isError(){
		if(!empty($this->__expObj)){
			return true;
		}else{
			return false;
		}
	}
	
	public function getErrorObj(){
		if(!empty($this->__expObj)){
			$ret = array();
			$ret['code'] = $this->getErrorCode();	
			$ret['text'] = $this->getErrorText();
			
			echo json_encode($ret);	
		}else{
			return false;
		}
	}
	
	public function getErrorCode() {
		$e = $this->__expObj;
		$no = $e->getCode();
		return (int)$no;
	}
	
	public function getErrorText() {
		$e = $this->__expObj;
		return $e->getMessage();
	}
	
	public function getErrorFile(){
		$e = $this->__expObj->getTrace();
		return $e[0]['file'];
	}
	
	public function getErrorLine() {
		$e = $this->__expObj->getTrace();
		$no = $e[0]['line'];
		return (int)$no;
	}
	
	public function enableFileLogging($file){
		if(is_writable($file)){
			$this->__fileLogging = true;
			$this->__logfile = $file;
		}else{
			throw new Exception($file.' is not writable');
		}
	}
	public function enableDbLogging($dblink){
		$result = $dblink->query("CREATE TABLE IF NOT EXISTS `at_leisure_json_rpc_errors` (
										  `id` int(11) NOT NULL auto_increment,
										  `call` varchar(255) default NULL,
										  `errorCode` int(11) default NULL,
										  `errorText` text,
										  `file` text,
										  `line` int(11) default NULL,
										  `ip` int(10) unsigned default NULL,
										  `uri` text,
										  `exception` text,
										  PRIMARY KEY  (`id`)
										) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
		if(!$result){
			throw new Exception($dblink->error,$dblink->errno);
		}else{
			$this->__dbLink = $dblink;
			$this->__dbLogging = true;
		}
	}
	
	public function enableEmailLogging($emailaddress, $fromemailaddress = ""){
		$this->__emailLogging = true;
		$this->__logEmail = $emailaddress;
		$this->__logFromEmail = (empty($fromemailaddress)) ? $emailaddress : $fromemailaddress;
	}
	
	public function getNumberOfTriesForCurrentCall(){
		return $this->__tries_call[$this->__call];	
	}
	
	private function __logError() {		
		$errorline = "[".date("Y-m-d H:i:s")."]\tCall:".$this->__call."\tErrorCode:".$this->getErrorCode()."\tErrorText:".$this->getErrorText()."\tFile:".$this->getErrorFile()." on line ".$this->getErrorLine()."\tIP:".$_SERVER['REMOTE_ADDR']."\tURI:".$_SERVER['REQUEST_URI']."\r\n";
		
		if($this->__fileLogging){
			if ($handle = fopen($this->__logfile,"a")) {
				fwrite($handle,$errorline);
			}			
		}
		
		if($this->__dbLogging){
			$result = $this->__dbLink->query("INSERT INTO at_leisure_json_rpc_errors(`call`, errorCode, errorText, file, line, ip, uri, exception) VALUES ('".$this->__call."', ".$this->getErrorCode().", '".$this->getErrorText()."', '".$this->getErrorFile()."', ".$this->getErrorLine().", INET_ATON('".$_SERVER['REMOTE_ADDR']."'), '".$_SERVER['REQUEST_URI']."', '".print_r($this->__expObj,true)."');");
			if($this->__dbLink->affected_rows > 0){
				
			}
		}
		
		if($this->__logEmail){
			$message = $errorline."\n\n".print_r($this->__expObj,true);
			$headers = "From: ".$this->__logFromEmail."\r\n".
						"Reply-To: ".$this->__logFromEmail."\r\n".
						"X-Mailer: PHP/".phpversion();
						
			mail($this->__logEmail,"Belvilla JSONRPC error",$message,$headers);
		}
	}
}
?>