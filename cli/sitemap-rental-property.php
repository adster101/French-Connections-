<?php

/**
 * Generate a sitemap listing all rental property listed on the site.
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
        $sitemap = '/sitemap-rental-property.txt';
        $sitemap_url = 'http://www.frenchconnections.co.uk' . $sitemap;
        $props = $this->_getProps();
        $handle = fopen(JPATH_SITE . $sitemap, 'w');

        foreach ($props as $prop)
        {
            // Determine the SEF route for this item
            //$route = 'index.php?option=com_accommodation&Itemid=' . (int) $Itemid . '&id=' . (int) $prop->property_id . '&unit_id=' . (int) $prop->unit_id;

            $url = 'http://www.frenchconnections.co.uk/listing/' . $prop->property_id . '&unit_id=' . $prop->unit_id;
            fwrite($handle, $url . "\n");
        }

        // The URL to ping to let Google know we've updated the sitemap
        $url = 'http://google.com/ping?sitemap=' . $sitemap_url;

        $return = $this->myCurl($url);
        
        // Check the return code and send an email if it's not 200 OK
        $this->return_code_check($url, $return);
    }

    /*
     * Get a list of property IDs
     * 
     */

    private function _getProps()
    {
        // Could just as easily be done with comma separated list as a param on the rental component
        $users_to_ignore = array();
        $users_to_ignore[] = JUser::getInstance('atleisure')->id;

        $db = JFactory::getDBO();
        /**
         * Get the date 
         */
        $date = JFactory::getDate();

        $query = $db->getQuery(true);

        $query->select('
      a.id as property_id, 
      b.id as unit_id'
        );

        // Select from the property table 
        $query->from('#__property a');

        // Join the unit table
        $query->leftJoin('#__unit b ON b.property_id = a.id');

        // Live properties, that are published 
        $query->where('expiry_date >= ' . $db->quote($date->calendar('Y-m-d')));
        $query->where('a.published = 1');
        $query->where('b.published = 1');
        $query->where('a.created_by not in (' . implode(',', $users_to_ignore) . ')');

        $query->order('rand()');

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
