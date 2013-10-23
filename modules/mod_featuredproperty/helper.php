<?php

/**
 * @package	Joomla.Tutorials
 * @subpackage	Module
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license	License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

class modFeaturedPropertyHelper {

  var $items;

  public function getFeaturedProperties(&$params) {
    
    $count = $params->get('count',4);
    
    $type = $params->get('type');
    
    $lang = JFactory::getLanguage()->getTag();

    //if ($lang === 'fr-FR') {
    //$select = 'c.title,hel.id,trans.title,trans.description,occupancy, thumbnail';
    //} else {
    //$select = 'c.title,hel.id,unit.unit_title,unit.description,occupancy, thumbnail';
    //}

    $db = JFactory::getDBO();
    $query = $db->getQuery(true);
    $query->select('
      a.id,
      b.id as unit_id,
      -- c.thumbnail,
      c.unit_title,
      c.occupancy,
      g.title,
      (select min(tariff) from qitz3_tariffs i where i.unit_id = b.id and end_date > now() group by unit_id ) as price
    ');
    $query->from('#__property as a');
    $query->join('left', '#__unit b ON a.id = b.property_id');
    // Here we only join the unit version where review is 0. Should ensure that we only take published units
    $query->join('left', '#__unit_versions c on (b.id = c.unit_id and c.id = (select max(d.id) from qitz3_unit_versions d where unit_id = b.id and review = 0))');
    // Same goes for the property version, which we join to get the location
    $query->join('left','#__property_versions e on (a.id = e.property_id and e.id = (select max(f.id) from qitz3_property_versions f where property_id = a.id and review = 0))' );
    $query->join('left','#__classifications g ON g.id = e.department');
    $query->join('left','#__featured_properties h on h.property_id = a.id');
    $query->where('b.ordering = 1');
    $query->where('a.published = 1');
    $query->where('b.published = 1');
    $query->where('a.expiry_date >= ' . $db->quote(JFactory::getDate()->calendar('Y-m-d')));
    $query->where('h.published = 1');
    $query->where('h.featured_property_type = ' . $type);
    $query->where('h.start_date <= ' . $db->quote(JFactory::getDate()->calendar('Y-m-d')) );
    $query->where('h.end_date >= ' . $db->quote(JFActory::getDate()->calendar('Y-m-d')));
    $query->order('rand()');
    $db->setQuery($query, 0,$count);

    // Load the JSON string
    //$params = new JRegistry;
    //$params->loadJSON($this->item->params);
    //$this->item->params = $params;

    $items = ($items = $db->loadObjectList()) ? $items : array();
    $this->items = $items;
    $this->lang = $lang;
  }

  function renderLayout(&$params) {

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
