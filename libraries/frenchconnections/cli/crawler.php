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

  /**
   * The page where the link was found.
   * 
   * @var type 
   */
  public $page = '';

  /**
   * Flag to indicate whether the link to FC was found or not
   * @var type 
   */
  public $found = false;

  /**
   * A list of domains to not crawl
   * 
   * @var type 
   */
  public $domains_to_ignore = array();
  
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

    // Use the root domain if no URL 
    if (empty($url))
    {
      $url = $this->domain;
    }

    try
    {

      $response = $this->getUrl($url);

      if (!$response)
      {
        //Throw new Exception('Problem fetching URL ' . $url);
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

        $link_from_html = $href->getAttribute('href');

        // Get the url details from the link, convenience
        $uri = JURI::getInstance($link_from_html);

        // Get the host
        $host = $uri->getHost();

        // If host is empty then the URLs are relative
        // so we set the domain from the constructor
        if (empty($host))
        {
          $uri->setHost($this->domain);
          $uri->setScheme('http');
          $uri->setPath('/' . $link_from_html);
        }

        //  I don't think we're interested in query string urls
        $uri->setQuery('');

        // Get the domain which should now be set
        $domain = $uri->getHost();

        // Convert the uri to a string value
        $link = $uri->toString();

        //echo $link . "\n";

        if (strpos($domain, 'frenchconnections.co.uk'))
        {

          // Do update, a dance or whatever 
          $this->found = true;
          $this->page = $url;
        }

        // Check that the URL is relative, i.e. on this domain
        if ((strpos($link,'villalemas') === false) && (strpos($link,'closdesseguineries') === false) && (strpos($link, 'smugmug') === false) && (strpos($link, 'javascript') === false) && (strpos($link, 'jpg') === false) && (strpos($link, 'mailto:') === false) && (!in_array($link, $this->links) && ($this->domain == $domain)))
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
    curl_setopt($ch, CURLOPT_COOKIESESSION, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 4);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    $base_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    $http_response = curl_getinfo($ch);
    
    // close cURL resource, and free up system resources
    curl_close($ch);
    
    if (strpos($http_response['content_type'], 'text/html') === false)
    {
      return false;
    }

    return $response;
  }

}