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
	
	public function belvilla_jsonrpcCall($WebpartnerCode, $WebpartnerPassword) {
		$this->__username = $WebpartnerCode;
		$this->__pass = $WebpartnerPassword;
	}
	
	public function makeCall($call,$params = array()) { 
		$this->__call = strtolower($call);
		
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
		
		$context = stream_context_create(array(
			"http"=>array(
				"method"=>"POST",
				"header"=>"Content-Type: application/json",
				"content"=>$this->__request
			))
		);
		
		
		//if(file_exists("https://".$this->__call.".jsonrpc-partner.net/cgi/lars/jsonrpc-partner/jsonrpc.htm")){
			if ($file = file_get_contents("https://".$this->__call.".jsonrpc-partner.net/cgi/lars/jsonrpc-partner/jsonrpc.htm",false,$context)) {
				$this->__result = $file;
				
				$errorcheck_var = json_decode($file);
				if(isset($errorcheck_var->error)){
					$this->__expObj = new Exception($errorcheck_var->error->message,$errorcheck_var->error->code);
					$this->__logError();
					
					throw $this->__expObj;
				}
			}
			else{
				throw new Exception("Could not connect to JSONrpc server");	
			}
		/*}else{
			var_dump(file_exists("https://dataofhousesv1.jsonrpc-partner.net/cgi/lars/jsonrpc-partner/jsonrpc.htm"));
			echo "<br>";
			print_r("https://".$this->__call.".jsonrpc-partner.net/cgi/lars/jsonrpc-partner/jsonrpc.htm");
			throw new Exception("Method ".$this->__call." unknown");
			/*
				Geeft fout als call niet bestaat omdat subdomein niet bestaat.
				Oplossen door * subdomein in te stellen naar een locatie waar die jsonrpc.htm file niet bestaat
			*/
		//}	
	}
	
	public function callConnected() {
		return $this->connect_state;
	}
	
	public function getResult($outputtype="json") {
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