<?php

/**
 * Generate a sitemap listing all for sale property listed on the site.
 * 
 * Pings Google once done to notify of update.
 */
require_once 'sitemap.php';

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class GenerateSitemap extends Sitemap
{

    /**
     * Entry point for the script
     *
     * @return  void
     *
     * @since   2.5
     */
    public function doExecute()
    {
        $sitemap = '/sitemap-forsale-property.txt';
        $sitemap_url = 'http://www.frenchconnections.co.uk' . $sitemap;
        $props = $this->_getProps();
        $handle = fopen(JPATH_SITE . $sitemap, 'w');

        foreach ($props as $prop)
        {

            $url = 'http://www.frenchconnections.co.uk/listing-forsale/' . $prop->property_id;
            fwrite($handle, $url . "\n");
        }

        // The URL to ping to let Google know we've updated the sitemap
        $url_to_ping = 'http://google.com/ping?sitemap=' . $sitemap_url;

        $return = $this->myCurl($url_to_ping);

        // Check the return code and send an email if it's not 200 OK
        $this->return_code_check($url_to_ping, $return);
    }

    /*
     * Get a list of property IDs
     * 
     */

    private function _getProps()
    {
        $db = JFactory::getDBO();
        /**
         * Get the date 
         */
        $date = JFactory::getDate();

        $query = $db->getQuery(true);

        $query->select('a.id as property_id');

        // Select from the property table 
        $query->from('#__realestate_property a');
        // Live properties, that are published 
        $query->where('expiry_date >= ' . $db->quote($date->calendar('Y-m-d')));
        $query->where('a.published = 1');

        $db->setQuery($query);

        try
        {
            $rows = $db->loadObjectList();
        }
        catch (Exception $e)
        {
            $this->out('Problem getting props...');
            return false;
        }

        return $rows;
    }

}

JApplicationCli::getInstance('GenerateSitemap')->execute();
