<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


require_once JPATH_LIBRARIES . '/PHPCrawl/libs/PHPCrawler.class.php';

class Crawler extends PHPCrawler
{

    public $file = '';

    public function __construct()
    {

$handle = fopen(JPATH_SITE . '/sitemap.txt', 'w');

        $this->file = $handle;
        parent::__construct();
    }

    function handleDocumentInfo(PHPCrawlerDocumentInfo $PageInfo)
    {
        // Your code comes here!
        // Do something with the $PageInfo-object that
        // contains all information about the currently 
        // received document.
        // As example we just print out the URL of the document
        fwrite($this->file, $PageInfo->url . "\n");
        //echo $PageInfo->url . "\n";
    }

 
}
