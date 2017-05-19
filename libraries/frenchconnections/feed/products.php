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
class JFeedParserproducts extends JFeedParser
{

  /**
   * @var    string  The feed element name for the entry elements.
   * @since  12.3
   */
  protected $entryElementName = 'products';

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
    $this->moveToNextElement('product');
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

    $facilities = array();
    $listing = new stdClass();

    $listing->affiliate_property_id = (string) $el->propertyID;
    $listing->description = (isset($el->media->texts->text)) ? JHtml::_('string.truncate', $el->media->texts->text, 3000, true, false) : '';
    $listing->unit_title = (isset($el->media->texts->text)) ? JHtml::_('string.truncate', $el->media->texts->text, 50, true, false) : '';
    $listing->changeover_day = 1521;
    $listing->latitude = (string) $el->address->coordinates->latitude;
    $listing->longitude = (string) $el->address->coordinates->longitude;

    // Get the nearest city detail based on lat and long
    //$city = $this->nearestcity((string) $el->address->coordinates->latitude, (string) $el->address->coordinates->longitude);
    //$listing->city = (int) $city;

    $listing->base_currency = 'EUR';
    $listing->occupancy = (string) $el->information->adultCount + $el->information->childrenCount;
    $listing->booking_url = 'https://booking.novasol.com/?opendocument=&V=EUR&NA=1&NC=0&H=' . $el->propertyID . '&C=191&L=999&COM=nov&PR=&U=whitelabel.novasol.com&theme=wl&wt_si_n=NormalSearchBookingFlow';

    //$listing->additional_price_notes = (string) $el->additional_price_notes;

    $listing->property_type = $this->getPropertyType($el->features->feature[0]);
    $listing->facilities = $this->getFacilities($el->features);
    $listing->images = $this->getImages($el->pictures);

    // Get list of rooms as an array
    $rooms = $this->getrooms($el->buildings);

    $listing->tariffs = $this->getTariffs($el->prices->price);

    // Add the rooms to the listing
    $listing->single_bedrooms = $rooms['single_bedrooms'];
    $listing->double_bedrooms = $rooms['double_bedrooms'];
    $listing->triple_bedrooms = $rooms['triple_bedrooms'];
    $listing->twin_bedrooms = $rooms['twin_bedrooms'];
    $listing->bathrooms = $rooms['bathrooms'];

    $listing->availability = (isset($el->availabilities->availability->days)) ? (string) $el->availabilities->availability->days : '';

    $feed->properties[] = $listing;

    return $feed;
  }

  public function getPropertyType($featuresXml = '') {

    $propertyType = '';

      switch ($featuresXml->subgroup)
      {
        case 1: // Apartment
          $propertyType = 1;
          break;
        case 2: // Farmhouse
          $propertyType = 10;
          break;
        default:
          $propertyType = 11;
          break;
      }


    return $propertyType;
  }

  public function getTariffs($tariffsXml = '') {

    $tariffs = array();

    if ($tariffsXml == '') {
      return $tariffs;
    }

    $counter = 0;

    foreach ($tariffsXml as $key => $value) {
      $tariffs[$counter]['start_date'] = (int) $value->from;
      $tariffs[$counter]['end_date'] = (int) $value->to;
      $tariffs[$counter]['tariff'] = (int) $value->price->salesMarket;

      $counter++;
    }

    return $tariffs;

  }

  public function getFacilities($facilitiesXml = '') {

    $facilities = array();

      foreach($facilitiesXml as $key => $value) {
        foreach ($value as $x=>$y) {

          $facilities[] = (string) $y->group;
        }
      }

      return $facilities;
  }

  public function getrooms($buildingsXml = '') {

    $bedrooms = array('double_bedrooms' => '', 'single_bedrooms' => '', 'twin_bedrooms' => '', 'triple_bedrooms' => '', 'bathrooms' => '');

    // Gah!
    foreach($buildingsXml as $building) {
      // Building has rooms
      foreach($building as $rooms) {

        // Loop over the rooms
        foreach($rooms as $room) {
          // Get the room attributes (e.g. double, single etc)
          $attributes = $room->attributes();

          // 101 is a bedroom
          if ((int) $attributes->type == 101) {

            foreach($room as $object)
            {
              // Get the room attributes (e.g. double bed, count etc)
              $bedroom = $object->attributes();

              // 202 is a double bedroom
              if ((int) $bedroom->type == 202 ) {
                $bedrooms['double_bedrooms'] += 1;
              }

              // 201 is a single bed
              if ((int) $bedroom->type == 201) {

                if ((int) $bedroom->count == 2 ) {
                  // A twin room
                  $bedrooms['twin_bedrooms'] += 1;
                }

                if ((int) $bedroom->count == 1) {
                  // A single room
                  $bedrooms['single_bedrooms'] += 1;
                }

                if ((int) $bedroom->count == 3) {
                  // A triple room
                  $bedrooms['triple_bedrooms'] += 1;
                }
              }
            }

          }
          // Room type is a Bathroom
          if ((int) $attributes->type == 131) {

            // Hello bathroom
            $bedrooms['bathrooms'] += 1;
          }
        }
      }
    }
    return $bedrooms;
  }

  public function getImages($imageXml = '')
  {
    // Init array to hold images
    $images = array();

    // Loop over each image and add them to the array...
    foreach($imageXml as $key => $value)
    {
      foreach ($value as $x => $image) {

        // Get image attributes
        $attributes = $image->attributes();

        if ($attributes->sequenceNumber) {

          $bits = explode('/' , $image->path);

          array_splice($bits, 2, 0, '600');

          $bits = implode($bits,'/');

          $obj = new StdClass;
          $obj->url = (string) $image->domain . $bits . $image->file;
          $images[(int) $attributes->sequenceNumber] = $obj;

        }
      }
    }

    // Sort based on the 'sequenceNumber'
    ksort($images);

    return $images;
  }

  /*
   *
   * Get the nearest town or city based on the town/city given and department
   */

  public function nearestcity($latitude = '', $longitude = '')
  {

    try
    {
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);

      $query->select("a.id, b.title");

      $query->from('#__classifications a');
      $query->innerjoin('#__classifications b on b.id = a.parent_id');
      $query->order('
        ( 3959 * acos(cos(radians(' . $latitude . ')) *
          cos(radians(a.latitude)) *
          cos(radians(a.longitude) - radians(' . $longitude . '))
          + sin(radians(' . $latitude . '))
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
