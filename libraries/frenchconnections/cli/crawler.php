<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class Crawler
{

  /**
   * Array to hold all links found on the target domain
   */
  public $links = array();

  /*
   * Domain name being crawled 
   */
  public $domain = '';

  /*
   * User agent 
   */
  public $userAgent = 'frenchconnections/2.1 (http://www.frenchconnections.co.uk)';

  public $found = false;
  
  /**
   * 
   * @param type $domain
   */
  function __construct($domain = '')
  {
    $uri = JUri::getInstance($domain);
    
    $this->domain = $uri->getHost();
  }

  /**
   * 
   * @param type $url
   * @throws Exception
   */
  function crawl($url = '')
  {

    try
    {

      $response = $this->getUrl($url);

      if (!$response)
      {
        Throw new Exception('Problem fetching URL ' . $url);
        //echo $url . " oops \n";
      }

      // parse the html into a DOMDocument
      $dom = new DOMDocument();
      @$dom->loadHTML($response);

      // grab all the on the page
      $xpath = new DOMXPath($dom);
      $hrefs = $xpath->evaluate("/html/body//a");

      foreach ($hrefs as $key => $value)
      {
        $href = $hrefs->item($key);

        $link = $href->getAttribute('href');

        $uri = JURI::getInstance($link);

        $host = $uri->getHost();

        if (strpos)
          if (empty($host))
          {
            $uri->setHost($this->domain);
            $uri->setScheme('http');
            $uri->setPath('/' . $link);
          }

        $host = $uri->getHost();

        $link = $uri->toString();
        
        echo $link . "\n";
        
        if (strpos($host, 'frenchconnections.co.uk'))
        {
          
          // Do update, a dance or whatever 
          $this->found = true;
        }

        // Check that the URL is relative, i.e. on this domain
        if ((strpos($link, 'smugmug') === false) && (strpos($link, 'javascript') === false) && (strpos($link, 'jpg') === false) && (strpos($link, 'mailto:') === false) && (!in_array($link, $this->links) && ($this->domain == $host)))
        {

          $this->links[] = $link;
          $this->crawl($link);
        }
        
        
      }

      return $this->links;
    }
    catch (Exception $e)
    {
      print_r($e->getMessage());
    }
  }

  /**
   * 
   * @global type $base_url
   * @param type $url
   * @return type
   */
  function getUrl($url)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 4);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    $base_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    $http_response_code = curl_getinfo($ch);



    return $response;
  }

}