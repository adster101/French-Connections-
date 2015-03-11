<?php
include_once(__DIR__ . '/../codebase/includes/dblive.php');
include_once(__DIR__ . '/../codebase/includes/db_connect.php');
include_once(__DIR__ . '/../codebase/includes/constants.php');
include_once(__DIR__ . '/../codebase/classes/jsonrpc_import.class.php');

$starttime = microtime(true);

$jsonrpc = new jsonRpcImport(/*array('ReferencePropertiesV1'),false*/); //sections, generate_t
$jsonrpc->setJsonRpcList();
$jsonrpc->goParse(); //parse JSONRPC and create textfiles
$jsonrpc->importFilesToMySQL(); //import generated textfiles into mysql
$jsonrpc->removeAccosWithNoNightplanning(); //remove houses that have no nightplanning
if ($jsonrpc->errorsOccured()) $jsonrpc->handleErrors(); //create log file + send maill
else {
	$jsonrpc->switchDatabaseReference();
	
	if ($jsonrpc->errorsOccured()) $jsonrpc->handleErrors(); //create log file + send mail
	else $jsonrpc->handleImportCompleted($starttime); //send mail
}
?>