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
class JFeedParserEnvelope extends JFeedParser {

    /**
     * @var    string  The feed element name for the entry elements.
     * @since  12.3
     */
    protected $entryElementName = 'Envelope';

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
    protected function initialise() {
        // We want to move forward to the first element after the <channel> element.
        $this->moveToNextElement('advert');
    }

    /**
     * Method to parse the feed into a JFeed object.
     *
     * @return  JFeed
     *
     * @since   3.0
     */
    public function parse() {
        $feed = new stdClass();
        $feed->properties = array();

        // Detect the feed version.
        $this->initialise();

        // Let's get this party started...
        do {
            // Expand the element for processing.
            $el = $this->expandToSimpleXml();

            // Process the element.
            $this->processElement($feed, $el);

            // Skip over this element's children since it has been processed.
            $this->moveToClosingElement();
        } while ($this->moveToNextElement());

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
    protected function processElement(stdClass $feed, SimpleXMLElement $el) {

        $images = array();
        $listing = new stdClass();

        $listing->agency_reference = (string) $el->reference;
        $listing->price = (string) $el->price;
        $listing->title = JHtml::_('string.truncate', $el->title_en, 125, true, false);
        $listing->description = JHtml::_('string.truncate', $el->summary_en, 1500, true, false);
        $listing->bedrooms = $el->n_beds;
        $listing->bathrooms = $el->n_baths;
        $listing->latitude = (string) str_replace(',', '.', $el->latitude);
        $listing->longitude = (string) str_replace(',', '.', $el->longitude);

        // This is needed because we don't have lat and long for some feeds
        if (!empty($listing->latitude) && !empty($listing->latitude)) {
            $city = $this->nearestcity((string) $listing->latitude, (string) $listing->longitude);
            $listing->city = (int) $city;
        }

        // Get the images as an array.
        foreach ((array) $el as $index => $node){
           if (strpos($index, 'pic_') !== false)
           {
             $images[] = (string) $el->$index;
           }
        }

        $listing->images = $images;
        $feed->properties[] = $listing;

        return $feed;
    }

    /*
     *
     * Get the nearest town or city based on the town/city given and department
     */

    public function nearestcity($latitude = '', $longitude = '') {

        try {
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
        } catch (Exception $e) {
            return false;
        }

        // If there's a nearest city then return it.
        if (!empty($rows)) {
            return $rows->id;
        }

        return false;
    }

}
