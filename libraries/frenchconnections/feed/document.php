<?php

/**
 * @package     Joomla.Platform
 * @subpackage  Feed
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_PLATFORM') or die;

/**
 * RSS Feed Parser class.
 *
 * @package     Joomla.Platform
 * @subpackage  Feed
 * @link        http://cyber.law.harvard.edu/rss/rss.html
 * @since       12.3
 */
class JFeedParserdocument extends JFeedParser
{

  /**
   * @var    string  The feed element name for the entry elements.
   * @since  12.3
   */
  protected $entryElementName = 'property';

  /**
   * @var    string  The feed format version.
   * @since  12.3
   */
  protected $version;

  /**
   * Method to initialise the feed for parsing.  Here we detect the version and advance the stream
   * reader so that it is ready to parse feed elements.
   *
   * @return  void
   *
   * @since   12.3
   */
  protected function initialise()
  {
// We want to move forward to the first element after the <channel> element.
    $this->moveToNextElement('properties');
    $this->moveToNextElement();
  }

  /**
   * Method to parse the feed into a JFeed object.
   *
   * @return  JFeed
   *
   * @since   3.0
   */
  public function parse()
  {
    $feed = new stdClass();
    $feed->properties = array();

// Detect the feed version.
    $this->initialise();

// Let's get this party started...
    do
    {
// Expand the element for processing.
      $el = $this->expandToSimpleXml();

// Process the element.
      $this->processElement($feed, $el);

// Skip over this element's children since it has been processed.
      $this->moveToClosingElement();
    }
    while ($this->moveToNextElement());

    return $feed;
  }

  /**
   * Method to parse a specific feed element.
   *
   * @param   JFeed             $feed        The JFeed object being built from the parsed feed.
   * @param   SimpleXMLElement  $el          The current XML element object to handle.
   * @param   array             $namespaces  The array of relevant namespace objects to process for the element.
   *
   * @return  void
   *
   * @since   3.0
   */
  protected function processElement(stdClass $feed, SimpleXMLElement $el)
  {

    $images = array();
    $listing = new stdClass();

    $listing->region = (string) $el->Address->region;
    $listing->agency_reference = (string) $el->Price->reference;
    $listing->price = (int) $el->Price->price;
    $listing->base_currency = (string) $el->Price->currency;
    $listing->description = (string) $el->Description->description;
    $listing->title = JHtml::_('string.truncate', $el->Description->description, 100, true, false);
    $listing->single_bedrooms = (int) $el->Description->bedrooms;
    $listing->bathrooms = (int) $el->Description->fullBathrooms;
    $listing->latitude = (string) $el->latitude;
    $listing->longitude = (string) $el->longitude;

    // This is needed because we don't have lat and long for some feeds
    if (!empty($el->latitude) && !empty($el->latitude))
    {
      $city = $this->nearestcity((string) $el->latitude, (string) $el->longitude, (string) $el->Address->subRegion);
      $listing->city = (int) $city;
    }

    if (!empty($el->Address->subRegion))
    {
      $listing->department = (string) $el->Address->subRegion;
    }
    else
    {
      $listing->department = $this->department((string) $el->Address->region);
    }


    // Add an EPC diagram if there is one
    if (!empty($el->EPC))
    {
      $listing->description .= "<h4>EPC diagram</h4><img src='" . $el->EPC . "' alt='EPC Diagram for " . $el->Price->reference . ".' />";
    }

    // Get the images
    foreach ($el->images->image as $image)
    {
      $images[] = (string) $image->image;
    }

    $listing->images = $images;

    $feed->properties[] = $listing;
  }

  /*
   * Get the nearest town or city based on the town/city given and department
   */

  public function department($department = '')
  {
    try
    {
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);

      $query->select("a.id");

      $query->from('#__classifications a');
      $query->where('a.alias = ' . $db->quote(JStringNormalise::toDashSeparated(JApplication::stringURLSafe($department))));


      $db->setQuery($query, 0, 1);
      $rows = $db->loadObject();
    }
    catch (Exception $e)
    {
      return false;
    }

    // If there's a nearest city then return it.
    if (!empty($rows))
    {
      return $rows->id;
    }

    return false;
  }

  /*
   * Get the nearest town or city based on the town/city given and department
   */

  public function nearestcity($latitude = '', $longitude = '', $department = '')
  {

    try
    {
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);

      $query->select("a.id, b.title");

      $query->from('#__classifications a');
      $query->innerjoin('#__classifications b on b.id = a.parent_id');
      $query->where('b.alias = ' . $db->quote(JStringNormalise::toDashSeparated(JApplication::stringURLSafe($department))));
      $query->order('
        ( 3959 * acos(cos(radians(' . $longitude . ')) *
          cos(radians(a.latitude)) *
          cos(radians(a.longitude) - radians(' . $latitude . '))
          + sin(radians(' . $longitude . '))
          * sin(radians(a.latitude))) ) 
        ');

      $db->setQuery($query, 0, 1);
      $rows = $db->loadObject();
    }
    catch (Exception $e)
    {
      return false;
    }

    // If there's a nearest city then return it.
    if (!empty($rows))
    {
      return $rows->id;
    }

    return false;
  }

}

