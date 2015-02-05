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
class FcSearchControllerMapSearch extends JControllerLegacy
{

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
  public function markers($cachable = false, $urlparams = false)
  {
    $return = array();

    // Require the component router
    require_once(JPATH_SITE . '/components/com_fcsearch/router.php');

    // Get the application instance
    $app = JFactory::getApplication();

    // Get the input date for this request
    $input = $app->input;

    // The 'url' comes as a get variable from the ajax get call
    $url = $app->input->get('s_kwds', '', 'string');
    
    // Plug this into JUri for easy processing
    $uri = JUri::getInstance($url);

    // Get the query string part
    $query_string = $uri->getQuery(true);

    // If we have a query string we set the value in the input 
    if (count($query_string) > 0)
    {
      foreach ($query_string as $key => $value)
      {
        $input->set($key, $value);
      }
    }

    // Break the path up into segments
    $segments = array_filter(explode('/', $uri->getPath()));

    // Need to remove the first element of the array as it will contain 
    // 'forsale' or 'accommodation' which is the alias used to route 
    // the non ajax http request
    array_shift($segments);

    // Get the vars for this request
    $vars = FcSearchParseRoute($segments);

    // And set them in the input scope
    foreach ($vars as $key => $value)
    {
      $input->set($key, $value);
    }

    // Get an instance of the search model
    $model = $this->getModel('Search', 'FcSearchModel');

    // Populate the state information
    $model->populateState();

    // Get the area/region/town etc that the search is being performed against
    $localInfo = $model->getLocalInfo();

    // Set the location
    $model->location = $localInfo->id;

    // Get a list of markers for this map/search combinations
    $results = $model->getMapMarkers();

    // Process the results a bit...could look at adding a template for the frontend
    foreach ($results as &$result)
    {
      $result->link = JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $result->id . '&unit_id=' . (int) $result->unit_id);
      $result->thumbnail = '/images/property/' . $result->unit_id . '/thumbs/' . $result->thumbnail;
      $result->description = JHtml::_('string.truncate', $result->description, 125, true, false);
    }

    // Check the data.
    if (empty($results))
    {
      $results = array();
    }

    // Use the correct json mime-type
    header('Content-Type: application/json');

    // Send the response.
    echo json_encode($results);
    JFactory::getApplication()->close();
  }

}
