<?PHP

/**
 * PHP Class jsonRpcImport
 * @author	Kurt Cleeren <webtech@leisure-group.eu>
 * @link  https://www.leisure-partners.net/
 * @version  2.0
 */
define('BASE', __DIR__);
include_once(__DIR__ . '/../includes/dblive.php');
include_once(__DIR__ . '/belvilla_jsonrpc_curl_gz.class.php');

class jsonRpcImport extends belvilla_jsonrpcCall
{

  private $webpartnercode = "glynis"; //your partnercode
  private $webpartnerpassword = "gironde"; //your partnerpassword
  private $docroot = __DIR__; //the document root of your server
  private $txt_location; //the location of the generated txtfiles that will be imported later
  private $serverlocation = "http://demo.leisure-partners.net"; //the url of your website
  private $sendmailto = "adamrifat@frenchconnections.co.uk"; //the emailadres whereto the output will be emailed
  private $nextdb = array("jsonrpc" => "jsonrpc_b", "jsonrpc_b" => "jsonrpc"); //array of next databases
  //private $nextdb = array("jsonrpc" => "jsonrpc");
  private $dbname; //the databasename used for this import
  private $dbname_live = CURR_DB; //The current live database, defined in dblive.php
  private $all_accos_array = array(); //array to hold all accos
  private $truncate_all_tables = true; //do you want to truncate all tables in the database used for this import
  private $languages = array("EN"); //languages that will be requested
  private $errors = array(); //array to store all errors
  private $maillog; //var used to mail all output from class
  private $showprocess = true; //show output when using this class
  private $firstDate; //will be set to yesterday in contructor
  private $bedroomnumbers = array(1028, 1030, 1031, 1032, 1033, 1035, 1038, 1040);
  private $bathroomnumbers = array(1031, 1042);

  /* constructor method */

  public function __construct($sections = array(), $truncate_all_tables = true, $dbname = 'leisure')
  {
    $this->firstDate = new DateTime(date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")))); //yesterday

    if (is_array($sections) && count($sections) > 0)
      $this->sections = $sections;

    parent::belvilla_jsonrpcCall($this->webpartnercode, $this->webpartnerpassword);
    $this->truncate_all_tables = $truncate_all_tables;

    $this->__echolog("<h1>@Leisure JSON-RPC Import started at " . date("H:i:s") . "</h1>");
    $this->__setImportDatabaseTruncateTables($dbname);

    $this->txt_location = "C:/xammp/htdocs/cli/leisure/json2db/json2txt/";
  }

  /**
   * Public function setJsonRpcList
   * 
   * Sets all calls/textsfiles needed for this import. If list is made manual, the list is reduced
   * 		
   * @return void
   */
  public function setJsonRpcList()
  {
    $jsonrpc_list = array();
    $jsonrpc_list["ReferenceLayoutItemsV1"] = array("name" => "ReferenceLayoutItemsV1", "txtfiles" => array("reference_layout_types", "reference_layout_subtypes"));
    $jsonrpc_list["ReferenceLayoutDetailsV1"] = array("name" => "ReferenceLayoutDetailsV1", "txtfiles" => array("reference_layout_details"));
    $jsonrpc_list["ReferencePropertiesV1"] = array("name" => "ReferencePropertiesV1", "txtfiles" => array("property_categories", "property_properties"));
    $jsonrpc_list["ReferenceRegionsV1"] = array("name" => "ReferenceRegionsV1", "txtfiles" => array("countries", "regions"));
    $jsonrpc_list["ReferenceSkiAreasV1"] = array("name" => "ReferenceSkiAreasV1", "txtfiles" => array("reference_ski_areas"));
    $jsonrpc_list["ReferenceParksV1"] = array("name" => "ReferenceParksV1", "txtfiles" => array("reference_parks", "reference_parks_texts", "reference_parks_facilities"));

    $jsonrpc_list["ListOfHousesV1"] = array("name" => "ListOfHousesV1", "txtfiles" => array("list_of_houses"));
    $jsonrpc_list["DataOfHousesV1"] = array("name" => "DataOfHousesV1", "txtfiles" => array("acco_descriptions", "acco_properties", "acco_layout_simple", "acco_layout", "acco_layout_details", "acco_houseowner_tips", "acco_guestbooks", "acco_photos", "acco_distances", "acco_costs_on_site", "accos", "avail_by_night", "discount_nights", "discount_nights_lm", "avail_by_period", "acco_brands", "acco_city", "acco_remarks"));

    if ($this->__doCompleteImport())
      $this->jsonrpc_list = $jsonrpc_list;
    else
    { //sections passed in constructor to do a part of the import
      foreach ($this->sections as $section)
      {
        if (array_key_exists($section, $jsonrpc_list))
          $this->jsonrpc_list[$section] = $jsonrpc_list[$section];
      }
    }
  }

  /**
   * Public function handleErrors
   * 
   * Saves all errors to a logfile and emails them to a specific emailadress
   * 		
   * @return void
   */
  public function handleErrors()
  {
    $filepath = "/json2db/errors/importerrors_" . date("d-m-Y_H-i-s") . ".html";
    $filename = $this->docroot . $filepath;

    $this->__echolog("Errors occured. Location of errorlog: <a href='" . $this->serverlocation . $filepath . "'>" . $this->serverlocation . $filepath . "</a></span>");

    $error_handle = fopen($filename, "w+");

    $error_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
								<html xmlns="http://www.w3.org/1999/xhtml">
								<head>
								<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
								<title>MySQL Errors</title>
								<style type="text/css">body, td { font-family:Calibri;	font-size:12px;	}</style>
								</head>
								<body>
								<h1>Errors XML2SQL ' . date("d-m-Y H:i:s") . ' (' . count($this->errors) . ' entries)</h1>
								<table width="100%">';
    foreach ($this->errors as $key => $arr)
    {
      $error_content .= '<tr><td>' . $arr[0] . '</td><td>' . $arr[1] . '</td></tr>';
    }
    $error_content .= '</table></body></html>';
    fwrite($error_handle, $error_content);
    fclose($error_handle);

    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

    mail($this->sendmailto, "@Leisure-partner Import FAILED | " . date("d/m/Y H:i:s"), nl2br($this->__getMailLog()), $headers);
  }

  /**
   * Public function handleImportCompleted
   * 
   * Calculates the duration of the complete import and sends an email with the complete log of this import.
   * 		
   * @return void
   */
  public function handleImportCompleted($starttime = "")
  {
    if (!empty($starttime))
    {
      $totalsec = $this->calculateTime($starttime);
      $totalhours = floor((($totalsec / 60) / 60));
      $totalmin = floor((($totalsec - ($totalhours * 60 * 60)) / 60));
      $totalsec = $totalsec - ($totalmin * 60);
      $this->__echolog("<h1>Import Took " . $totalhours . "hours " . $totalmin . "min " . $totalsec . "sec</h1>");
      $this->__echolog("<h1>Import Ended at " . date("H:i:s") . "sec</h1>");
    }
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    mail($this->sendmailto, "@Leisure-partner Import SUCCESSFUL | " . date("d/m/Y H:i:s"), nl2br($this->__getMailLog()), $headers);
  }

  /**
   * Public function switchDatabaseReference
   * 
   * Generates a phpfile with 2 defined variables. The current database to use and the firstdate that is used for the nightprices
   * 		
   * @return void
   */
  public function switchDatabaseReference()
  {
    $txt = '<?php define("CURR_DB","' . $this->dbname . '"); define("FIRSTDATE","' . $this->firstDate->format('Y-m-d') . '"); ?>';

    $filename = $this->docroot . "/codebase/includes/dblive.php";
    if (!$handle = fopen($filename, "w+"))
      $this->__addError("switchDatabaseReference error", "Cannot open file (" . $filename . ")");
    else if (fwrite($handle, $txt) === false)
      $this->__addError("switchDatabaseReference error", "Cannot write to file (" . $filename . ")");
    else
      $this->__echolog("\r\n<h1 style='color:green;'>Live DB SWITCH FILE " . $this->dbname . "</h1>");
    fclose($handle);
  }

  /**
   * Private function __getMailLog
   * 
   * Generates a phpfile with 2 defined variables. The current database to use and the firstdate that is used for the nightprices
   * 		
   * @return string The complete emaillog
   */
  private function __getMailLog()
  {
    return htmlspecialchars($this->maillog);
  }

  /**
   * Public function errorsOccured
   * 
   * Checks if errors occured
   * 		
   * @return 	boolean 
   */
  public function errorsOccured()
  {
    return (count($this->errors) > 0);
  }

  /**
   * Public function calculateTime
   * 
   * calculates time between the 2 given params
   * 
   * @param	integer		The starttime
   * @param	integer		The endtime
   * @return 	integer 
   */
  public function calculateTime($t1, $t2 = "")
  {
    if (empty($t2))
      $t2 = microtime(true);
    return round(($t2 - $t1), 3) . " sec";
  }

  /**
   * Private function __doCompleteImport
   * 
   * Checks if there are sections defined to do a part of the import
   * 		
   * @return 	boolean
   */
  private function __doCompleteImport()
  {
    return (count($this->sections) > 0) ? false : true;
  }

  /**
   * Private function __addTextFileLine
   * 
   * saves text to a file
   * 
   * @param	string		the line of text to be saved
   * @param	string		the filename where it has to be saved to
   * @return 	void 
   */
  private function __addTextFileLine($line, $textfile)
  {
    fwrite($this->handle[$textfile], $line . "\n") or $this->__addError("Writeline fwrite error", "Error writing line to file " . $textfile . ":\n" . $line);
    $this->records[$textfile]++;
  }

  /**
   * Private function __as
   * 
   * adds slashes to a string
   * 
   * @param	string		the inputstring without slashes
   * @return 	string 
   */
  private function __as($str)
  {
    return addslashes($str);
  }

  /**
   * Private function __generateTxtFiles
   * 
   * generate textfiles from data source
   * 
   * @return 	void 
   */
  private function __generateTxtFiles()
  {
    $t1 = microtime(true);
    $this->__echolog("Start generating textfiles");
    $this->__openFileHandlers();
    foreach ($this->jsonrpc_list as $jsonrpc_call => $v)
      $this->__generateTxtFile($jsonrpc_call, $v["name"]);
    $this->__closeAllFileHandlers();
    $this->__echolog("End generating textfiles... time: " . $this->calculateTime($t1));
  }

  /**
   * Private function __openFileHandlers
   * 
   * opens file handlers for the requested files, prepare files for writing
   * 
   * @return 	void 
   */
  private function __openFileHandlers()
  {
    $this->__echolog("Opening file handlers");
    foreach ($this->jsonrpc_list as $jsonrpc_call => $txtarr)
    {
      foreach ($txtarr["txtfiles"] as $txtfile)
        $this->__openFileHandler($txtfile);
    }
  }

  /**
   * Private function __openFileHandler
   * 
   * opens a file handlers, prepare the file for writing
   * 
   * @return 	void 
   */
  private function __openFileHandler($txtfile)
  {
    if (!isset($this->handle[$txtfile]))
    {
      $this->handle[$txtfile] = fopen($this->txt_location . $txtfile . ".txt", "w+"); //handlers aanmaken voor de bestanden
      $this->records[$txtfile] = 0;
    }
  }

  /**
   * Private function __closeAllFileHandlers
   * 
   * close file handlers, writing to file has ended
   * 
   * @return 	void 
   */
  private function __closeAllFileHandlers()
  {
    $this->__echolog("Closing file handlers");
    foreach ($this->jsonrpc_list as $jsonrpc_call => $v)
    {
      foreach ($v["txtfiles"] as $txtfile)
      {
        fclose($this->handle[$txtfile]);
        unset($this->handle[$txtfile]);
      }
    }
  }

  /**
   * Private function __generateTxtFile
   * 
   * generates a textfile, which will be used to bulk insert into mysql database
   * 
   * @return 	void 
   */
  private function __generateTxtFile($jsonrpc_call, $name)
  {
    $t1 = microtime(true);
    $this->__echolog("Generating " . $name . ", JSON-RPC " . $jsonrpc_call, false);
    $func = "__generate_" . $name;
    $this->$func($jsonrpc_call);
    $this->__echolog(" done. Time: " . $this->calculateTime($t1));
  }

  /**
   * Public function goParse
   * 
   * parses jsonrpc data into textfiles
   * 
   * @return 	void 
   */
  public function goParse()
  {
    $t1 = microtime(true);
    $this->__generateTxtFiles();
    $this->__echolog("Parsing to textfiles done... This took " . $this->calculateTime($t1));
  }

  /**
   * Private function __echolog
   * 
   * logs a string to the maillog and outputs that string is showprocess is on
   * 
   * @param	string		the string to be logged
   * @param	Boolean		add breaks?
   * @return 	void 
   */
  private function __echolog($text, $addbreak = true)
  {
    $text = "-> " . $text;
    if ($addbreak)
      $text .= "\r\n";
    $this->maillog .= $text;
    if ($this->showprocess)
      echo $text;
  }

  /**
   * Private function __setImportDatabaseTruncateTables
   * 
   * sets the database for this importroutine
   * if empty it will check it there is a database defined to be used after the current live database
   * 
   * @param	string		the databasename used for this import
   * @return 	void 
   */
  private function __setImportDatabaseTruncateTables($dbname)
  {
    if (empty($dbname))
    {
      if (isset($this->nextdb[$this->dbname_live]))
      {
        $this->dbname = $this->nextdb[$this->dbname_live];
        if ($this->truncate_all_tables)
          $this->__truncateAllTables(); //truncate_all_tables => when updating one house eg, not necessary
      }
      else
        $this->__addError("Current live database has no NEXT database", "Database error");
    }
    else
      $this->dbname = $dbname;

    $this->__echolog("Database for import: " . $this->dbname);
  }

  /**
   * Private function __truncateAllTables
   * 
   * gets all tabled of the current database used for this import and truncated them all
   * 
   * @return 	void 
   */
  private function __truncateAllTables()
  {
    $sql = getData("SHOW TABLES FROM " . $this->dbname);
    while ($res = $sql->fetch_array())
      $this->__truncateTable($res[0]);
  }

  /**
   * Private function __truncateTable
   * 
   * truncates one table
   * 
   * @return 	void 
   */
  private function __truncateTable($table)
  {
    //truncate tables before inserting,
    $query = "TRUNCATE table " . $this->dbname . "." . $table;
    if (getData($query))
      $this->__echolog("Truncated " . $this->dbname . "." . $table);
    else
    {
      $this->__echolog("! ERROR ON TRUNCATE " . $this->dbname . "." . $table . " (" . mysqli_error($link) . ")");
      $this->__addError(mysqli_error($link), $query);
    }
  }

  /**
   * Private function __addError
   * 
   * adds an error to the errors array and puts it into the errorlog
   * 
   * @param	string		the error title
   * @param	string		the error message
   * @return 	void 
   */
  private function __addError($message, $error)
  {
    $this->errors[] = array($message, $error);
    $this->__echolog("\r\n" . $message . "\r\n" . $error);
  }

  /**
   * Public function importFilesToMySQL
   * 
   * imports all generated txt files
   * 
   * @return 	void 
   */
  public function importFilesToMySQL()
  {
    global $link;
    $this->__echolog("Start importing textfiles into database " . $this->dbname);

    foreach ($this->jsonrpc_list as $jsonrpc_call => $v)
    {
      foreach ($v["txtfiles"] as $table)
      {
        $log = "Importing textfile " . $this->txt_location . $table . ".txt into table " . $this->dbname . "." . $table;
        $log .= (filesize($this->txt_location . $table . ".txt") == 0) ? " | !!! Attention: No Data" : " | Table " . $this->dbname . "." . $table . " filled (" . $this->records[$table] . ")";

        $this->__disableKeys($this->dbname . "." . $table);

        $enclosed = "\'";

        $sql = "LOAD DATA LOCAL INFILE '" . $this->txt_location . $table . ".txt' IGNORE
							INTO TABLE " . $this->dbname . "." . $table . "
							FIELDS TERMINATED BY ','
							ENCLOSED BY '" . $enclosed . "'
							LINES TERMINATED BY '\n';"; //Use LOAD DATA LOCAL INFILE when your web- en mysql server are not on the same machine

        $query = mysqli_query($link, $sql) or $this->__addError(mysqli_error($link), $sql);

        $this->__enableKeys($this->dbname . "." . $table);

        $this->__echolog($log);
      }
    }
  }

  /**
   * Private function __disableKeys
   * 
   * disables the keys of a table
   * 
   * @return 	void 
   */
  private function __disableKeys($table)
  {
    global $link;

    $qr = "ALTER TABLE " . $table . " DISABLE KEYS";
    if (!mysqli_query($link, $qr))
      $this->__addError(mysqli_error($link), $qr);
  }

  /**
   * Private function __enableKeys
   * 
   * enables the keys of a table
   * 
   * @return 	void 
   */
  private function __enableKeys($table)
  {
    global $link;
    $qr = "ALTER TABLE " . $table . " ENABLE KEYS";
    if (!mysqli_query($link, $qr))
      $this->__addError(mysqli_error($link), $qr);
  }

  /**
   * Private function __optimizeTable
   * 
   * optimizes all tables in the param
   * 
   * @param	array or string		the tables to be optimized
   * @return 	void 
   */
  private function __optimizeTable($tables)
  {
    global $link;
    if (!is_array($tables))
      $tables = array($tables);
    foreach ($tables as $table)
    {
      $qr = "OPTIMIZE TABLE " . $this->dbname . "." . $table;
      if (getData($qr))
        $this->__addError(mysqli_error($link), $qr);
    }
  }

  /**
   * Public function removeAccosWithNoNightplanning
   * 
   * removes all accos that don't have planningNight
   * 
   * @return 	void 
   */
  public function removeAccosWithNoNightplanning()
  {
    global $link;
    $qr = "DELETE FROM " . $this->dbname . ".accos WHERE code NOT IN (SELECT distinct houseCode FROM " . $this->dbname . ".avail_by_night)";
    getData($qr);
  }

  /**
   * Private function __generate_ListOfHousesV1
   * 
   * calls ListOfHousesV1 and fills an array with all returned housecodes
   * 
   * @param	string		The name of the RPC call
   * @return 	void 
   */
  private function __generate_ListOfHousesV1($jsonrpc_call)
  {
    try
    {
      parent::makeCall($jsonrpc_call);
      $acco_objs = parent::getResult("json");

      if (!empty($acco_objs))
      {
        foreach ($acco_objs as $acco_obj)
        {
          // Just get the FR ones...
          $HouseCode = $acco_obj->HouseCode;
          $pos = strpos($HouseCode, 'FR');
          // Get only the FR houses
          if ($pos === 0)
          {
            array_push($this->all_accos_array, $HouseCode);
            $this->__addTextFileLine("'" . $HouseCode . "'", "list_of_houses");
          }
        }
      }
      else
      {
        $this->__addError($jsonrpc_call . " returned no results", $jsonrpc_call . " returned no results");
      }
    }
    catch (Exception $e)
    {
      $this->__addError(print_r($e, true), "loading " . $jsonrpc_call . " failed");
    }
  }

  /**
   * Private function __generate_DataOfHousesV1
   * 
   * calls DataOfHousesV1 in chunks of 100 for each housecode in the all_accos_array
   * 
   * @param	string		The name of the RPC call
   * @return 	void 
   */
  private function __generate_DataOfHousesV1($jsonrpc_call)
  {
    $this->accocode_chunks = array_chunk($this->all_accos_array, 100);
    $this->chunks_loaded = 0;

    foreach ($this->accocode_chunks as $acco_chunk)
    {
      $this->__loadAccoChunc($jsonrpc_call, $acco_chunk);
    }
  }

  /**
   * Private function __loadAccoChunc
   * 
   * calls DataOfHousesV1 in chunks of 100 for each housecode in the all_accos_array
   * 
   * @param	string		The name of the RPC call
   * @param	array		an array with housecodes to be requested
   * @return 	void 
   */
  private function __loadAccoChunc($jsonrpc_call, $acco_chunk)
  {
    $chunk_start = microtime(true);

    $this->chunks_loaded++;

    $params = array(
        "HouseCodes" => $acco_chunk,
        "Items" => array("BasicInformationV3",
            "MediaV2",
            "PropertiesV1",
            "LayoutExtendedV2",
            "DistancesV1",
            "AvailabilityPeriodV1")
    );

    $LanguagePacks = array("en" => "LanguagePackENV4");

    foreach ($this->languages as $l)
    {
      array_push($params["Items"], $LanguagePacks[strtolower($l)]);
    }

    try
    {
      parent::makeCall($jsonrpc_call, $params);
      $result = parent::getResult("json");
      if (!empty($result))
      {
        foreach ($result as $acco)
        {
          $HouseCode = $acco->HouseCode;
          if (!isset($acco->error))
          {
            $BasicInformation = $acco->BasicInformationV3;
            $Name = $this->__as($BasicInformation->Name);
            $MaxNumberOfPersons = $BasicInformation->MaxNumberOfPersons;
            $ExceedNumberOfBabies = $BasicInformation->ExceedNumberOfBabies;
            $NumberOfPets = $BasicInformation->NumberOfPets;
            $NumberOfStars = $BasicInformation->NumberOfStars;
            $DimensionM2 = ($BasicInformation->DimensionM2 > 0) ? $BasicInformation->DimensionM2 : 0;
            $ZipPostalCode = $BasicInformation->ZipPostalCode;
            //$City = $this->__as($BasicInformation->City);
            $Region = $BasicInformation->Region;
            $Country = $this->__as($BasicInformation->Country);
            $CreationDate = $BasicInformation->CreationDate;
            $WGS84Longitude = $BasicInformation->WGS84Longitude;
            $WGS84Latitude = $BasicInformation->WGS84Latitude;
            $OptionsAllowed = (strtolower($BasicInformation->OptionsAllowed) == 'yes') ? '1' : '0';
            $EnqeCount = $BasicInformation->EnqeCount;
            $EnqePoints = $BasicInformation->EnqePoints;
            $SkiArea = $BasicInformation->SkiArea;
            $HolidayPark = $BasicInformation->HolidayPark;
            $Brands = $BasicInformation->Brands;

            //Brands
            if (isset($Brands))
            {
              foreach ($Brands as $brand)
              {
                $line = "'" . $HouseCode . "','" . strtolower($brand->Brand) . "'," . $brand->SequenceNumber;
                $this->__addTextFileLine($line, "acco_brands");
              }
            }
            else
            {
              $this->__addError("No Brands for HouseCode: " . $HouseCode, "No Brand");
            }

            //Media
            if (isset($acco->MediaV2))
            {
              foreach ($acco->MediaV2 as $media)
              {
                if ($media->Type == "Photos")
                {
                  foreach ($media->TypeContents as $photo)
                  {
                    $sequenceNumber = $photo->SequenceNumber;
                    $tag = $photo->Tag;
                    foreach ($photo->Versions as $photoversion)
                    {
                      $height = $photoversion->Height;
                      $url = $photoversion->URL;

                      $line = "'" . $HouseCode . "'," . $sequenceNumber . ",'" . $tag . "'," . $height . ",'" . $url . "'";
                      $this->__addTextFileLine($line, "acco_photos");
                    }
                  }
                }
              }
            }
            else
            {
              $this->__addError("No MediaV1 for HouseCode: " . $HouseCode, "No Media");
            }

            //LanguagePacks
            foreach ($this->languages as $language)
            {
              $pack = $LanguagePacks[strtolower($language)];

              //Description
              if (isset($acco->$pack->Description))
              {
                $descr = $acco->$pack->Description;

                $line = "'" . $HouseCode . "','" . strtolower($language) . "','" . $this->__as($descr) . "'";
                $this->__addTextFileLine($line, "acco_descriptions");
              }
              else
              {
                $this->__addError("No Description " . $language . " for HouseCode: " . $HouseCode, "No Description");
              }

              //LayoutSimple
              if (isset($acco->$pack->LayoutSimple))
              {
                $layout = $acco->$pack->LayoutSimple;

                $line = "'" . $HouseCode . "','" . strtolower($language) . "','" . $this->__as($layout) . "'";
                $this->__addTextFileLine($line, "acco_layout_simple");
              }
              else
              {
                $this->__addError("No LayoutSimple " . $language . " for HouseCode: " . $HouseCode, "No LayoutSimple");
              }

              //HouseOwnerTip
              if (isset($acco->$pack->HouseOwnerTip))
              {
                $tip = $acco->$pack->HouseOwnerTip;
                if ($tip != '')
                {
                  $line = "'" . $HouseCode . "','" . strtolower($language) . "','" . $this->__as($tip) . "'";
                  $this->__addTextFileLine($line, "acco_houseowner_tips");
                }
              }
              else
              {
                //$this->__addError("No HouseOwnerTips ".$language." for HouseCode: ".$HouseCode,"No HouseOwnerTips");
              }

              //GuestBook
              if (isset($acco->$pack->GuestBook))
              {
                foreach ($acco->$pack->GuestBook as $gb)
                {
                  $line = "'" . $HouseCode . "','" . strtolower($language) . "','" . $gb->Date . "','" . $gb->ClientTitle . "','" . $this->__as($gb->ClientInitials) . "','" . $this->__as($gb->ClientSurname) . "','" . $gb->ClientCountry . "','" . $gb->ArrivalDate . "','" . $gb->DepartureDate . "','" . $this->__as($gb->Text) . "'";
                  $this->__addTextFileLine($line, "acco_guestbooks");
                }
              }
              else
              {
                //$this->__addError("No GuestBook ".$language." for HouseCode: ".$HouseCode,"No GuestBook");
              }

              //CostsOnSite
              if (isset($acco->$pack->CostsOnSite))
              {
                foreach ($acco->$pack->CostsOnSite as $cos)
                {
                  $line = "'" . $HouseCode . "'," . $cos->Number . ",'" . strtolower($language) . "','" . $this->__as($cos->Description) . "','" . $this->__as($cos->Value) . "'";
                  $this->__addTextFileLine($line, "acco_costs_on_site");
                }
              }
              else
              {
                $this->__addError("No CostOnSite " . $language . " for HouseCode: " . $HouseCode, "No CostOnSite");
              }

              //city & subcity
              if (isset($acco->$pack->City))
              {
                $city = $acco->$pack->City;
                $city = '';
                $subcity = $acco->$pack->SubCity;
                $line = "'" . $HouseCode . "','" . strtolower($language) . "','" . $this->__as($city) . "','" . $this->__as($subcity) . "'";
                $this->__addTextFileLine($line, "acco_city");
              }
              else
              {
                //$this->__addError("No City ".$language." for HouseCode: ".$HouseCode,"No City");
              }

              //city & subcity
              if (isset($acco->$pack->Remarks))
              {
                $remark = $acco->$pack->Remarks;
                $line = "'" . $HouseCode . "','" . strtolower($language) . "','" . $this->__as($remark) . "'";
                $this->__addTextFileLine($line, "acco_remarks");
              }
              else
              {
                //$this->__addError("No City ".$language." for HouseCode: ".$HouseCode,"No City");
              }
            }

            //PropertiesV1
            if (isset($acco->PropertiesV1))
            {
              foreach ($acco->PropertiesV1 as $prop)
              {
                foreach ($prop->TypeContents as $TypeContent)
                {
                  $line = "'" . $HouseCode . "'," . $prop->TypeNumber . "," . $TypeContent;
                  $this->__addTextFileLine($line, "acco_properties");
                }
              }
            }
            else
            {
              $this->__addError("No PropertiesV1 " . $language . " for HouseCode: " . $HouseCode, "No PropertiesV1");
            }

            //DistancesV1
            if (isset($acco->DistancesV1))
            {
              foreach ($acco->DistancesV1 as $dist)
              {
                $line = "'" . $HouseCode . "','" . $dist->To . "'," . $dist->DistanceInKm;
                $this->__addTextFileLine($line, "acco_distances");
              }
            }
            else
            {
              //$this->__addError("No DistancesV1 for HouseCode: ".$HouseCode,"No DistancesV1");
            }

            //LayoutExtendedV2
            $numberOfBedRooms = 0;
            $numberOfBathRooms = 0;

            if (isset($acco->LayoutExtendedV2))
            {
              foreach ($acco->LayoutExtendedV2 as $layout)
              {
                $item = $layout->Item;
                $sequenceNumber = $layout->SequenceNumber;
                $numberOfItems = $layout->NumberOfItems;
                $parentItem = isset($layout->ParentItem) ? $layout->ParentItem : 'NULL';
                $parentSequenceNumber = isset($layout->ParentSequenceNumber) ? $layout->ParentSequenceNumber : 'NULL';

                $line = "'" . $HouseCode . "'," . $item . "," . $sequenceNumber . "," . $numberOfItems . "," . $parentItem . "," . $parentSequenceNumber;
                $this->__addTextFileLine($line, "acco_layout");

                if (in_array($item, $this->bedroomnumbers))
                {
                  $numberOfBedRooms += $numberOfItems;
                  //echo $HouseCode." bedroom + ".$numberOfItems." = ".$numberOfBedRooms."\n";
                }
                if (in_array($item, $this->bathroomnumbers))
                {
                  $numberOfBathRooms += $numberOfItems;
                  //echo $HouseCode." bathroom +".$numberOfItems." = ".$numberOfBathRooms."\n";
                }

                if (isset($layout->Details))
                {
                  foreach ($layout->Details as $detailnumber)
                  {
                    $line = "'" . $HouseCode . "'," . $item . "," . $sequenceNumber . "," . $numberOfItems . "," . $parentItem . "," . $parentSequenceNumber . "," . $detailnumber;
                    $this->__addTextFileLine($line, "acco_layout_details");
                  }
                }
              }
            }
            else
            {
              $this->__addError("No LayoutExtendedV2 for HouseCode: " . $HouseCode, "No LayoutExtendedV2");
            }

            if ($numberOfBedRooms == 0)
              $numberOfBedRooms = 1;
            if ($numberOfBathRooms == 0)
              $numberOfBathRooms = 1;

            //AvailabilityPeriodV1
            if (isset($acco->AvailabilityPeriodV1))
            {
              foreach ($acco->AvailabilityPeriodV1 as $avper)
              {
                $ArrivalTimeFrom = $avper->ArrivalTimeFrom;
                $ArrivalTimeUntil = $avper->ArrivalTimeUntil;
                $DepartureTimeFrom = $avper->DepartureTimeFrom;
                $DepartureTimeUntil = $avper->DepartureTimeUntil;
                $OnRequest = ($avper->OnRequest == 'Yes') ? '1' : '0';
                $Price = $avper->Price;
                $PriceExclDiscount = $avper->PriceExclDiscount;

                try
                {
                  $ArrivalDate = DateTime::createFromFormat('Y-m-d', $avper->ArrivalDate);
                  $DepartureDate = DateTime::createFromFormat('Y-m-d', $avper->DepartureDate);

                  $lastMinute = ($avper->OnRequest == 'Yes') ? '1' : '0';
                  $nights = $DepartureDate->diff($ArrivalDate);
                  $periodId = $this->__getPeriod($ArrivalDate, $nights->days);

                  $line = "'" . $HouseCode . "','" . $ArrivalDate->format('Y-m-d') . "'," . $nights->days . ",'" . $periodId . "','" . $OnRequest . "'," . $PriceExclDiscount . "," . $Price . ",'" . $lastMinute . "','" . $ArrivalTimeFrom . "','" . $ArrivalTimeUntil . "','" . $DepartureTimeFrom . "','" . $DepartureTimeUntil . "'";
                  $this->__addTextFileLine($line, "avail_by_period");
                }
                catch (Exception $e)
                {
                  $this->__addError("AvailabilityPeriodV1 went wrong for HouseCode: " . $HouseCode . ". Message: " . $e->getMessage(), "AvailabilityPeriodV1 Error");
                }
              }
            }
            else
            {
              $this->__addError("No AvailabilityPeriodV1 for HouseCode: " . $HouseCode, "No AvailabilityPeriodV1");
            }


            $City = '';
            $line = "'" . $HouseCode . "','" . $Name . "'," . (($HolidayPark == '') ? "NULL" : "'" . $HolidayPark . "'") . "," . $MaxNumberOfPersons . "," . $NumberOfPets . "," . $ExceedNumberOfBabies . "," . $NumberOfStars . ",'" . $ZipPostalCode . "','" . $City . "','" . $Region . "'," . (($SkiArea == '') ? "NULL" : "'" . $SkiArea . "'") . ",'" . $Country . "','" . $CreationDate . "'," . $WGS84Longitude . "," . $WGS84Latitude . "," . $EnqeCount . "," . $EnqePoints . "," . $numberOfBedRooms . "," . $numberOfBathRooms . ",'" . $OptionsAllowed . "'," . $DimensionM2;
            $this->__addTextFileLine($line, "accos");
          }
          else
          {
            $this->__addError(print_r($acco->error, true), "acco error");
          }
        }
      }
      else
      {
        $this->__addError(print_r($acco_chunk, true), "loading acco_chunk " . $this->chunks_loaded . "/" . sizeof($this->accocode_chunks) . " failed");
      }
    }
    catch (Exception $e)
    {
      $this->__addError(print_r($e, true), "loading " . $jsonrpc_call . " failed");
    }

    $this->__echolog("acco_chunk " . $this->chunks_loaded . "/" . sizeof($this->accocode_chunks) . " loaded in " . $this->calculateTime($chunk_start));
  }

  /**
   * Private function __generate_ReferenceLayoutItemsV1
   * 
   * calls ReferenceLayoutItemsV1 and creates a txt file to import into out database
   * 
   * @param	string		The name of the RPC call
   * @return 	void 
   */
  private function __generate_ReferenceLayoutItemsV1($jsonrpc_call)
  {
    try
    {
      parent::makeCall($jsonrpc_call);
      $res_objs = parent::getResult("json");
      if (!empty($res_objs))
      {
        foreach ($res_objs as $res_obj)
        {
          $type = $res_obj->Type;
          foreach ($res_obj->Description as $descr)
          {
            $this->__addTextFileLine($type . ",'" . strtolower($descr->Language) . "','" . $this->__as($descr->Description) . "'", "reference_layout_types");
          }
          foreach ($res_obj->Items as $layout_subtypes)
          {
            $subtypeid = $layout_subtypes->Number;
            foreach ($layout_subtypes->Description as $layout_subtype_descr)
            {
              $this->__addTextFileLine($type . "," . $subtypeid . ",'" . strtolower($layout_subtype_descr->Language) . "','" . $this->__as($layout_subtype_descr->Description) . "'", "reference_layout_subtypes");
            }
          }
        }
      }
    }
    catch (Exception $e)
    {
      $this->__addError(print_r($e, true), "loading " . $jsonrpc_call . " failed");
    }
  }

  /**
   * Private function __generate_ReferenceLayoutDetailsV1
   * 
   * calls ReferenceLayoutDetailsV1 and creates a txt file to import into out database
   * 
   * @param	string		The name of the RPC call
   * @return 	void 
   */
  private function __generate_ReferenceLayoutDetailsV1($jsonrpc_call)
  {
    try
    {
      parent::makeCall($jsonrpc_call);
      $res_objs = parent::getResult("json");
      if (!empty($res_objs))
      {
        foreach ($res_objs as $res_obj)
        {
          $det_numb = $res_obj->Number;
          foreach ($res_obj->DetailDescription as $descr)
          {
            $this->__addTextFileLine($det_numb . ",'" . strtolower($descr->Language) . "','" . $this->__as($descr->Description) . "'", "reference_layout_details");
          }
        }
      }
    }
    catch (Exception $e)
    {
      $this->__addError(print_r($e, true), "loading " . $jsonrpc_call . " failed");
    }
  }

  /**
   * Private function __generate_ReferencePropertiesV1
   * 
   * calls ReferencePropertiesV1 and creates a txt file to import into out database
   * 
   * @param	string		The name of the RPC call
   * @return 	void 
   */
  private function __generate_ReferencePropertiesV1($jsonrpc_call)
  {
    try
    {
      parent::makeCall($jsonrpc_call);
      $all_props = parent::getResult("json");

      if (!empty($all_props))
      {
        foreach ($all_props as $prop)
        {
          $typenumber = $prop->TypeNumber;
          foreach ($prop->TypeDescription as $descr)
          {
            $this->__addTextFileLine("'" . $typenumber . "','" . strtolower($descr->Language) . "','" . $this->__as($descr->Description) . "'", "property_categories");
          }

          foreach ($prop->Properties as $property)
          {
            $number = $property->Number;
            foreach ($property->PropertyDescription as $descr)
            {
              $this->__addTextFileLine("'" . $number . "','" . $typenumber . "','" . strtolower($descr->Language) . "','" . $this->__as($descr->Description) . "'", "property_properties");
            }
          }
        }
      }
    }
    catch (Exception $e)
    {
      $this->__addError(print_r($e, true), "loading " . $jsonrpc_call . " failed");
    }
  }

  /**
   * Private function __generate_ReferenceRegionsV1
   * 
   * calls ReferenceRegionsV1 and creates a txt file to import into out database
   * 
   * @param	string		The name of the RPC call
   * @return 	void 
   */
  private function __generate_ReferenceRegionsV1($jsonrpc_call)
  {
    try
    {
      parent::makeCall($jsonrpc_call);
      $countries = parent::getResult("json");
      if (!empty($countries))
      {
        foreach ($countries as $country)
        {
          $countrycode = $country->CountryCode;
          foreach ($country->CountryDescription as $countryDescr)
          {
            $this->__addTextFileLine("'" . $countrycode . "','" . strtolower($countryDescr->Language) . "','" . $this->__as($countryDescr->Description) . "'", "countries");
          }

          foreach ($country->Regions as $region)
          {
            $regionCode = $region->RegionCode;
            foreach ($region->RegionDescription as $regionDescr)
            {
              $this->__addTextFileLine("'" . $countrycode . "','" . $regionCode . "','" . strtolower($regionDescr->Language) . "','" . $this->__as($regionDescr->Description) . "'", "regions");
            }
          }
        }
      }
    }
    catch (Exception $e)
    {
      $this->__addError(print_r($e, true), "loading " . $jsonrpc_call . " failed");
    }
  }

  /**
   * Private function __generate_ReferenceSkiAreasV1
   * 
   * calls ReferenceSkiAreasV1 and creates a txt file to import into out database
   * 
   * @param	string		The name of the RPC call
   * @return 	void 
   */
  private function __generate_ReferenceSkiAreasV1($jsonrpc_call)
  {
    try
    {
      parent::makeCall($jsonrpc_call);
      $res_objs = parent::getResult("json");
      if (!empty($res_objs))
      {
        foreach ($res_objs as $res_obj)
        {
          $code = $res_obj->Code;
          $country = $res_obj->Country;
          foreach ($res_obj->Definitions as $def)
          {
            $Language = strtolower($def->Language);
            $Description = $this->__as($def->Description);
            $this->__addTextFileLine("'" . $code . "','" . $country . "','" . $Language . "','" . $Description . "'", "reference_ski_areas");
          }
        }
      }
    }
    catch (Exception $e)
    {
      $this->__addError(print_r($e, true), "loading " . $jsonrpc_call . " failed");
    }
  }

  /**
   * Private function __generate_ReferenceParksV1
   * 
   * calls ReferenceParksV1 and creates a txt file to import into out database
   * 
   * @param	string		The name of the RPC call
   * @return 	void 
   */
  private function __generate_ReferenceParksV1($jsonrpc_call)
  {
    try
    {
      parent::makeCall($jsonrpc_call);
      $res_objs = parent::getResult("json");
      if (!empty($res_objs))
      {
        foreach ($res_objs as $res_obj)
        {
          $Code = $res_obj->Code;
          $Country = $res_obj->Country;
          $Type = $this->__as($res_obj->Type);
          $Name = $this->__as($res_obj->Name);
          $City = $this->__as($res_obj->City);
          $City = '';
          $NumberOfHouses = $res_obj->NumberOfHouses;
          $NumberOfStars = $res_obj->NumberOfStars;
          $Language = strtolower($res_obj->Language);
          $WebsiteURL = (!empty($res_obj->WebsiteURL)) ? "'" . $this->__as($res_obj->WebsiteURL) . "'" : "NULL";
          $WGS84Longitude = $res_obj->WGS84Longitude;
          $WGS84Latitude = $res_obj->WGS84Latitude;

          $this->__addTextFileLine("'" . $Code . "','" . $Country . "','" . $Type . "','" . $Name . "','" . $City . "'," . $NumberOfHouses . "," . $NumberOfStars . ",'" . $Language . "'," . $WebsiteURL . "," . $WGS84Longitude . "," . $WGS84Latitude, "reference_parks");

          foreach ($res_obj->Descriptions as $descr)
          {
            $Language = strtolower($descr->Language);
            $Description = (!empty($descr->Description)) ? "'" . $this->__as($descr->Description) . "'" : "NULL";
            $Type = 'descr';

            $this->__addTextFileLine("'" . $Code . "','" . $Type . "','" . $Language . "'," . $Description, "reference_parks_texts");
          }

          foreach ($res_obj->ShortDescriptions as $sdescr)
          {
            $Language = strtolower($sdescr->Language);
            $Description = (!empty($sdescr->Description)) ? "'" . $this->__as($sdescr->Description) . "'" : "NULL";
            $Type = 'sdescr';

            $this->__addTextFileLine("'" . $Code . "','" . $Type . "','" . $Language . "'," . $Description, "reference_parks_texts");
          }

          foreach ($res_obj->Details as $Details)
          {
            $Language = strtolower($Details->Language);
            $Detail = (!empty($Details->Detail)) ? "'" . $this->__as($Details->Detail) . "'" : "NULL";
            $Type = 'details';

            $this->__addTextFileLine("'" . $Code . "','" . $Type . "','" . $Language . "'," . $Detail, "reference_parks_texts");
          }

          foreach ($res_obj->ParkFacilities as $ParkFacilities)
          {
            $Language = strtolower($ParkFacilities->Language);
            foreach ($ParkFacilities->Facilities as $facility)
            {
              $line = "'" . $Code . "','" . $Language . "','" . $facility . "'";
              $this->__addTextFileLine($line, "reference_parks_facilities");
            }
          }
        }
      }
    }
    catch (Exception $e)
    {
      $this->__addError(print_r($e, true), "loading " . $jsonrpc_call . " failed");
    }
  }

  /**
   * Private function __getPeriod
   * 
   * defines a periodid depending on arrivaldate and number of nights
   * 
   * @param	DateTime		the arrivaldate
   * @param	integer			the number of nights
   * @return 	string 
   */
  private function __getPeriod($a_date, $nights)
  {
    switch ($nights) {
      case 2:
        if ($a_date->format('N') == 5)
          return "wk"; //vrijdag
        break;
      case 3:
        if ($a_date->format('N') == 5)
          return "lw"; //vrijdag
        break;
      case 4:
        if ($a_date->format('N') == 1)
          return "mw"; //maandag
        break;
      case 7:
        return "1w";
        break;
      case 14:
        return "2w";
        break;
      case 21:
        return "3w";
        break;
      default:
        return "";
    }
  }

}

?>