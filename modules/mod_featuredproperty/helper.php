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

  public function getFeaturedProperties(&$params)
  {

    $count = $params->get('count', 4);

    $type = $params->get('type');
    $offers_only = $params->get('offers');

    // In case we are on the 'special offers' search results page then override the offers flag...
    $input = JFactory::getApplication()->input;

    $offers = $input->get('offers', false, 'boolean');

    $lang = JFactory::getLanguage()->getTag();

    $date = date('Y-m-d H:i:s', mktime(14, 30, 0, date('m'), date('d'), date('y')));
   
    $db = JFactory::getDBO();
    $query = $db->getQuery(true);
    $query->select('
      a.id,
      b.id as unit_id,
      c.unit_title,
      left(c.description,150) as description,
      c.occupancy,
      i.image_file_name as thumbnail,
      g.title,
      g.alias,
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

    // Join the translations table to pick up any translations 
    if ($lang == 'fr-FR')
    {
      $query->select('j.unit_title');
      $query->join('left', '#__unit_versions_translations j on j.version_id = c.id');
      $query->join('left', '#__classifications_translations g ON g.id = e.department');
    }
    else
    {
      $query->join('left', '#__classifications g ON g.id = e.department');
    }


    $query->join('left', '#__property_images_library i on c.id = i.version_id');

    $query->where('b.ordering = 1');
    $query->where('a.published = 1');
    $query->where('b.published = 1');
    $query->where('i.ordering = 1');
    $query->where('a.expiry_date >= ' . $db->quote($date));

    // On the last minute page we don't want featured properties, just those with offers.
    // This is set by a module parameter and should only default to true on the last minute page.
    if (!$offers_only)
    {
      $query->join('left', '#__featured_properties h on h.property_id = a.id');
      $query->where('h.published = 1');
      $query->where('h.featured_property_type = ' . $type);
      $query->where('h.start_date <= ' . $db->quote($date));
      $query->where('h.end_date >= ' . $db->quote($date));
    }

    if ($offers_only || $offers)
    {
      $query->where('(select title from qitz3_special_offers k where k.published = 1 AND k.start_date <= ' . $db->quote($date) . ' AND k.end_date >= ' . $db->quote($date) . ' and k.unit_id = c.unit_id) is not null');
    }

    $query->order('rand()');
    $db->setQuery($query, 0, $count);

    $items = ($items = $db->loadObjectList()) ? $items : array();

    $this->items = $items;
    $this->lang = $lang;



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
