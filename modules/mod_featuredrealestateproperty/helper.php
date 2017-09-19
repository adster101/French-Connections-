<?php

/**
 * @package	Joomla.Tutorials
 * @subpackage	Module
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license	License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

class modFeaturedRealestatePropertyHelper
{

  var $items;

  public function getFeaturedProperties(&$params)
  {

    $count = $params->get('count', 4);
    $type = $params->get('type');

    // In case we are on the 'special offers' search results page then override the offers flag...
    $lang = JFactory::getLanguage()->getTag();

    // Using JHtml::date instead of JDate...
    $date = JHtml::date($input = 'now', 'Y-m-d', false);
    $db = JFactory::getDBO();

    $query = $db->getQuery(true);
    $query->select('
      a.id,
      b.title,
      (b.single_bedrooms + b.double_bedrooms) as bedrooms,
      left(b.description,150) as description,
      i.image_file_name as thumbnail,
      g.title as location,
      g.alias,
      b.base_currency,
      b.price,
      b.description,
      b.bathrooms
    ');

    $query->from('#__realestate_property as a');

    // Here we only join the unit version where review is 0. Should ensure that we only take published units
    $query->join('left', '#__realestate_property_versions b on (a.id = b.realestate_property_id and b.id = (select max(c.id) from #__realestate_property_versions c where realestate_property_id = a.id and review = 0))');

    // Join the translations table to pick up any translations
    if ($lang == 'fr-FR')
    {
      $query->select('j.unit_title');
      $query->join('left', '#__unit_versions_translations j on j.version_id = c.id');
      $query->join('left', '#__classifications_translations g ON g.id = e.department');
    }
    else
    {
      $query->join('left', '#__classifications g ON g.id = b.department');
    }

    $query->join('left', '#__realestate_property_images_library i on b.id = i.version_id');
    //$query->where('(i.ordering = (select min(ordering) from #__property_images_library h where h.version_id = b.id))');

    $query->where('a.published = 1');
    $query->where('i.ordering = 1');
    $query->where('a.expiry_date >= ' . $db->quote($date));

    $query->join('left', '#__featured_properties h on h.property_id = a.id');

    $query->where('h.published = 1');
    $query->where('h.featured_property_type = ' . $type);
    $query->where('h.start_date <= ' . $db->quote($date));
    $query->where('h.end_date >= ' . $db->quote($date));
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
    $document = & JFactory::getDocument();
    $uri = & JURI::getInstance();
    // add stylesheets to document header

    require(JModuleHelper::getLayoutPath('mod_featuredrealestateproperty', 'default'));
  }

  /**
   * Function tagline to return a tagline for a property based on the number of bathrooms
   * and bedrooms. Necessary because sometimes properties get added with 0 bedrooms
   * for example if it's a plot of land or a rennovation.
   *
   * @param type $bedrooms
   * @param type $bathrooms
   * @param type $price
   */
  public static function tagline($price = '', $bedrooms = 0, $bathrooms = 0)
  {

    $bedrooms_str = '';
    $bathrooms_str = '';

    if ($bedrooms)
    {
      $bedrooms_str = ($bedrooms == 1) ? JText::_('MOD_FEATURED_REALESTATE_N_BEDROOMS_1') : JText::plural('MOD_FEATURED_REALESTATE_N_BEDROOMS', $bedrooms);
    }

    if ($bathrooms)
    {
      $bathrooms_str = ($bathrooms == 1) ? JText::_('MOD_FEATURED_REALESTATE_N_BATHROOMS_1') : JText::plural('MOD_FEATURED_REALESTATE_N_BATHROOMS', $bathrooms);
    }

    $tagline = JText::sprintf('MOD_FEATURED_REALESTATE_PROPERTY_DETAIL', $price, $bedrooms_str, $bathrooms_str);

    return $tagline;
  }

}
