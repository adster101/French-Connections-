<?php

error_reporting(E_ALL);

define('OTAURL', 'https://otageo.cartrawler.com/cartrawlerota/');
define('ABEURL', 'https://ajaxgeo.cartrawler.com/cartrawlerabe/');

define('LOCALABEURL', 'https://www.cartrawler.com/');

define('FILES', 'files/');
$clientAddress = getRealIpAddr();

$otaheaders = array(
    'X-OTAProxy: 1.0',
    'X-OTAProxy-Secure: ' . (((!isset($_SERVER['HTTPS'])) || (strtolower($_SERVER['HTTPS'])) != 'on' ) ? ('false') : ('true')),
    'X-OTAProxy-ClientAddress: ' . $clientAddress
);
$message = '';

if ((isset($_POST['getfile'])) || (isset($_GET['getfile']))) {
    $getfile = ((isset($_POST['getfile'])) ? ($_POST['getfile']) : ($_GET['getfile']));
    $message = process_getfile(OTAURL, $otaheaders, $getfile);
} elseif ((isset($_POST['abegetfile'])) || (isset($_GET['abegetfile']))) {
    $getfile = ((isset($_POST['abegetfile'])) ? ($_POST['abegetfile']) : ($_GET['abegetfile']));
    $message = process_getfile(ABEURL, $otaheaders, $getfile);
} elseif ((isset($_POST['abegetlocalfile'])) || (isset($_GET['abegetlocalfile']))) {
    $getfile = ((isset($_POST['abegetlocalfile'])) ? ($_POST['abegetlocalfile']) : ($_GET['abegetlocalfile']));
    $message = process_getlocalfile(LOCALABEURL, $otaheaders, $getfile);
} elseif ((isset($_POST['geturl'])) || (isset($_GET['geturl']))) {
    $url = ((isset($_POST['geturl'])) ? ($_POST['geturl']) : ($_GET['geturl']));
    $message = process_getURL($url, $otaheaders);
} elseif (isset($HTTP_RAW_POST_DATA)) {
    $message = process_rawpost(OTAURL, $otaheaders, $HTTP_RAW_POST_DATA);
} else {
    $message = 'Unsupported request';
}

if (strlen($message) > 0) {
    header("HTTP/1.0 404 $message");
}

function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function process_getfile($url, $otaheaders, $getfile) {
    if (validatefilename($getfile)) {
        $curl_handle = curl_init();

        $url .= FILES . $getfile;
        setoptions($curl_handle, $url, $otaheaders);

        $buffer = curl_exec($curl_handle);
        if (curl_errno($curl_handle)) {
            $msg = 'CURL Error ' + curl_error($curl_handle);
            curl_close($curl_handle);
            return $msg;
        }

        $type = curl_getinfo($curl_handle, CURLINFO_CONTENT_TYPE);

        if (curl_getinfo($curl_handle, CURLINFO_HTTP_CODE) != 200) {
            return 'No such file';
        }

        curl_close($curl_handle);
        header("Expires: " . gmdate("D, d M Y H:i:s", (time() + 60 * 60)) . " GMT");
        header('Content-type: ' . $type);
        echo $buffer;
    } else {
        return 'File validation failed';
    }
    return '';
}

function process_getlocalfile($url, $otaheaders, $getfile) {
    $curl_handle = curl_init();

    $url .= $getfile;
    setoptions($curl_handle, $url, $otaheaders);

    $buffer = curl_exec($curl_handle);

    if (curl_errno($curl_handle)) {
        $msg = 'CURL Error ' + curl_error($curl_handle);
        curl_close($curl_handle);
        return $msg;
    }

    $type = curl_getinfo($curl_handle, CURLINFO_CONTENT_TYPE);

    if (curl_getinfo($curl_handle, CURLINFO_HTTP_CODE) != 200) {
        return 'No such file';
    }

    curl_close($curl_handle);
    header("Expires: " . gmdate("D, d M Y H:i:s", (time() + 60 * 60)) . " GMT");
    header('Content-type: ' . $type);
    echo $buffer;

    return '';
}

function process_getURL($url, $otaheaders) {

    $curl_handle = curl_init();

    setoptions($curl_handle, $url, $otaheaders);
    $buffer = curl_exec($curl_handle);
    if (curl_errno($curl_handle)) {
        $msg = 'CURL Error ' + curl_error($curl_handle);
        curl_close($curl_handle);
        echo $msg;
        return $msg;
    }

    $type = curl_getinfo($curl_handle, CURLINFO_CONTENT_TYPE);

    if (curl_getinfo($curl_handle, CURLINFO_HTTP_CODE) != 200) {
        curl_close($curl_handle);
        header("Cache-Control: no-store, no-cache");
        header('Content-type: ' . $type);
        echo "status=failed_badly";		
        return '';
    }
    curl_close($curl_handle);

    header("Cache-Control: no-store, no-cache");
    header('Content-type: ' . $type);
    echo $buffer;

    return '';
}

function process_rawpost($url, $otaheaders, $rawpost) {
    $curl_handle = curl_init();

    setoptions($curl_handle, $url, $otaheaders);
    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $rawpost);

    $buffer = curl_exec($curl_handle);
    if (curl_errno($curl_handle)) {
        $msg = 'CURL Error ' + curl_error($curl_handle);
        curl_close($curl_handle);
        return $msg;
    }

    $type = curl_getinfo($curl_handle, CURLINFO_CONTENT_TYPE);

    if (curl_getinfo($curl_handle, CURLINFO_HTTP_CODE) != 200) {
        curl_close($curl_handle);
        header("Cache-Control: no-store, no-cache");
        header('Content-type: ' . $type);
        echo "status=failed_badly";		
        return '';
    }
    curl_close($curl_handle);

    header("Cache-Control: no-store, no-cache");
    header('Content-type: ' . $type);
    echo $buffer;

    return '';
}

function setoptions($curl_handle, $url, $headers) {
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
    if (preg_match('@^https://@', $url)) {
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, FALSE);
    }
    curl_setopt($curl_handle, CURLOPT_ENCODING, "");
    curl_setopt($curl_handle, CURLOPT_URL, $url);
    curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
}

function validatefilename($filename) {
    if ((strlen($filename) > 128) || (strlen($filename) == 0)) {
        return false;
    }
    if (preg_match('/(^[.\\/\\\\])|([.\\/\\\\]$)|([.\\/\\\\]{2})|([^\w.\\/])/', $filename)) {
        return false;
    }
    return true;
}
?>
