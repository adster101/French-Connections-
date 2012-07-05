<?php
 /**
  * @package mycms
  * @copyright bela, http://tbela.net/
  *
  * cms upload handler, cross-browser ajax file upload
  * feel free to use and/or modify
  */

	define('_JEXEC', 1);
	
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date du passé
	
	if(function_exists('mb_internal_encoding'))
		mb_internal_encoding('utf-8');

	if(!defined('DS'))
		define('DS', DIRECTORY_SEPARATOR);

	if(!defined('BASE_PATH'))
		define('BASE_PATH', dirname(__FILE__));

	if(!defined('TEMP_PATH'))
		define('TEMP_PATH', BASE_PATH.DS.'tmp');

	//upload max size in bytes
	if(!defined('UPLOAD_MAX_SIZE'))
		define('UPLOAD_MAX_SIZE', 0); //2621440 2.5M

	require dirname(__FILE__).DS.'uploadhelper.php';

	if (!function_exists('apache_request_headers'))  {

		function apache_request_headers() {
		
		   foreach ($_SERVER as $name => $value) 
			   if (substr($name, 0, 5) == 'HTTP_') 
				   $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
				   
		   return $headers;
		}
	}

	ob_start();

	if(uploadhelper::getVar('dl') == 1) require BASE_PATH.DS.'download.php';
	
	else {
		
		$headers = apache_request_headers();
		
		require !empty($headers['Sender']) ? BASE_PATH.DS.'upload.html5.php' :  BASE_PATH.DS.'upload.html.php';		
	}

	ob_flush();
	
//garbage colletor, remove old files

 error_reporting(0);
  
   $t = time();
   
   //file are not supposed to stay here for a long period, they should be moved after being uploaded
   $max_age = 3600 * 24;
   
   if($handle = opendir(TEMP_PATH)) {
   
		while (false !== ($file = readdir($handle))) {
		
			if($file == '.' || $file == '..'|| $file == 'index.php')
				continue;

			if($t - filemtime(TEMP_PATH.DS.$file) > $max_age)
				unlink(TEMP_PATH.DS.$file);
		}
		
		closedir($handle);
   }
   
   exit();
?>