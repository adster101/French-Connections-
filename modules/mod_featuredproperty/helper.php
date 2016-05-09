<?php

/**
 * @package	Joomla.Tutorials
 * @subpackage	Module
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license	License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

class modFeaturedPropertyHelper
{

    var $items;

    /**
     * Returns a list of featured properties to display
     *
     * */
    public function getFeaturedProperties(&$params)
    {
        // The total number of properties to return
        $count = $params->get('count', 4);

        // The featured property 'type' corresponds to a menu item
        $type = $params->get('type');

        // Only return properties with special offers
        $offers_only = $params->get('offers');

        // In case we are on the 'special offers' search results page then override the offers flag...
        $input = JFactory::getApplication()->input;

        // User is filtering on special offers show only show FP with offers...
        $offers = $input->get('offers', false, 'boolean');

        // Get the region, if any
        $region = $params->get('region', '');

        // Get the departments specified for this module
        $departments = $params->get('departments', '');

        $paid_for = $this->_getFeaturedProperties($count, $type, $offers_only, $offers, $region, $departments);

        // Check whether we have enough paid for listing to account for the total required
        if (count($paid_for) < $count)
        {
            $remaining = $count - count($paid_for);

            $padding = $this->_getFeaturedProperties($remaining, '', $offers_only, $offers, $region, $departments);
        }

        $props = array_merge($paid_for, $padding);

        return $props;
    }

    private function _getFeaturedProperties($count = 4, $type = '', $offers_only = false, $offers = false, $region = '', $departments = '')
    {

        // Using JHtml::date instead of JDate...
        $date = JHtml::date($input = 'now', 'Y-m-d', false);
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);
        $query->select('
      a.id,
      b.id as unit_id,
      c.unit_title,
      left(c.description,150) as description,
      c.occupancy,
      i.image_file_name as thumbnail,
      i.url_thumb,
      (single_bedrooms + double_bedrooms + triple_bedrooms + quad_bedrooms + twin_bedrooms) as bedrooms,
      j.title as property_type,
      k.title as accommodation_type,
      c.bathrooms,
      g.title,
      l.title as tariff_based_on,
      m.title as changeover_day,
      g.alias,
      c.occupancy,
      c.base_currency,
      (select min(tariff) from qitz3_tariffs i where i.unit_id = b.id and end_date > now() group by unit_id ) as price
    ');

        $query->select('(select title from qitz3_special_offers k where k.published = 1 AND k.start_date <= ' . $db->quote($date) . ' AND k.end_date >= ' . $db->quote($date) . ' and k.unit_id = c.unit_id) as offer');

        $query->from('#__property as a');

        $query->join('left', '#__unit b ON a.id = b.property_id');
        // Here we only join the unit version where review is 0. Should ensure that we only take published units

        $query->join('left', '#__unit_versions c on (b.id = c.unit_id and c.id = (select max(d.id) from qitz3_unit_versions d where unit_id = b.id and review = 0))');

        // Same goes for the property version, which we join to get the location
        $query->join('left', '#__property_versions e on (a.id = e.property_id and e.id = (select max(f.id) from qitz3_property_versions f where property_id = a.id and review = 0))');
        $query->join('left', '#__classifications g ON g.id = e.department');
        $query->join('left', '#__property_images_library i on c.id = i.version_id');

        $query->join('left', '#__attributes j on j.id = c.property_type');
        $query->join('left', '#__attributes k on k.id = c.accommodation_type');
        $query->join('left', '#__attributes l on l.id = c.tariff_based_on');

        // Take this out by storing changeover day as an int 0-6 etc
        $query->join('left', '#__attributes m on m.id = c.changeover_day');

        $query->where('b.ordering = 1');
        $query->where('a.published = 1');
        $query->where('b.published = 1');
        $query->where('i.ordering = 1');
        $query->where('a.expiry_date >= ' . $db->quote($date));

        // On the last minute page we don't want featured properties, just those with offers.
        // This is set by a module parameter and should only default to true on the last minute page.
        if ($type)
        {
            $query->join('left', '#__featured_properties h on h.property_id = a.id');
            $query->where('h.published = 1');
            $query->where('h.featured_property_type = ' . $type);
            $query->where('h.start_date <= ' . $db->quote($date));
            $query->where('h.end_date >= ' . $db->quote($date));
        }

        if ($region)
        {
            $query->where('e.region = ' . (int) $region);
        }

        if ($departments)
        {
            $str = array();
            foreach ($departments as $department)
            {
                $str[] = 'e.department = ' . $department;
            }

            $query->where('(' . implode(' OR ', $str) . ')');
        }

        if ($offers_only || $offers)
        {
            $query->where('(select title from qitz3_special_offers k where k.published = 1 AND k.start_date <= ' . $db->quote($date) . ' AND k.end_date >= ' . $db->quote($date) . ' and k.unit_id = c.unit_id) is not null');
        }

        $query->order('rand()');
        $db->setQuery($query, 0, $count);

        $items = ($items = $db->loadObjectList()) ? $items : array();

        $this->items = $items;

        return $items;
    }

    function renderLayout(&$params)
    {

        // Do other stuff here to prepare content etc
        /**
          GENERATING FINAL XHTML CODE START
         * */
        // create instances of basic Joomla! classes
        $document = & JFactory::getDocument();
        $uri = & JURI::getInstance();
        // add stylesheets to document header


        require(JModuleHelper::getLayoutPath('mod_featuredproperty', 'default'));
    }

}
