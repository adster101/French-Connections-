<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

/**
 * Suggestions JSON controller for Finder.
 *
 * @package     Joomla.Site
 * @subpackage  com_finder
 * @since       2.5
 */
class RealEstateSearchControllerMapSearch extends JControllerLegacy {

  /**
   * Method to find search query suggestions.
   *
   * @param   boolean  $cachable   If true, the view output will be cached
   * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
   *
   * @return  void
   *
   * @since   2.5
   * 
   */
  public function markers($cachable = false, $urlparams = false) {

    // Require the component router
    require_once(JPATH_SITE . '/components/com_realestatesearch/router.php');

    // Get the application instance
    $app = JFactory::getApplication();

    // Get the input date for this request
    $input = $app->input;

    // Set the filter vars (this comes in the form of one big long string)
    $filter_vars = $app->input->get('s_kwds', '', 'string');

    // Break it up into segments
    $segments = array_filter(explode('/', $filter_vars));
    
    // Need to remove the first element of the array as it will contain 'forsale'
    // which is the alias used to route the normal http url
    array_shift($segments);
    
    // Get the vars for this request
    $vars = RealestateSearchParseRoute($segments);

    // And set them in the input scope
    foreach ($vars as $key => $value) {
      $input->set($key, $value);
    }

    // Get an instance of the search model
    $model = $this->getModel('Search', 'RealEstateSearchModel');
    
    // Populate the state information
    $model->populateState();
    
    // Get the area/region/town etc that the search is being performed against
    $localInfo = $model->getLocalInfo();

    // Set the location
    $model->location = $localInfo->id;

    // Get a list of markers for this map/search combinations
    $results = $model->getMapMarkers();

    // Get the realestate property for sale item id
    $Itemid = SearchHelper::getItemid(array('component', 'com_realestate'));
    
    // Process the results so we don't need to do that in the browser
    foreach ($results as &$result) {
      $result->link = JRoute::_('index.php?option=com_realestate&Itemid=' . (int) $Itemid . '&id=' . (int) $result->property_id);
      $result->thumbnail = '/images/property/' . $result->property_id . '/thumbs/' . $result->thumbnail;
      $result->description = JHtml::_('string.truncate', $result->description, 125, true, false);
      $result->unit_title = JHtml::_('string.truncate', $result->description, 25, true, false);
    }


    // Check the data.
    if (empty($results)) {
      $results = array();
    }

    // Use the correct json mime-type
    header('Content-Type: application/json');

    // Send the response.
    echo json_encode($results);
    JFactory::getApplication()->close();
  }

}
