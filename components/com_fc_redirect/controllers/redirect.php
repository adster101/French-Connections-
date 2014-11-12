<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class Fc_RedirectControllerRedirect extends JControllerLegacy
{

  public function GeoRegionSearch()
  {
    $app = JFactory::getApplication();
    $input = $app->input;
    $db = JFactory::getDbo();
    $Itemid = SearchHelper::getItemid(array('component', 'com_fcsearch'));
    $parts = explode('/', $input->get('filter', '', 'string'));

    // Look up the location
    $query = $db->getQuery(true);

    // We're interested in the alias 
    $query->select('alias, id');
    $query->from('#__classifications a');

    $part = array_pop($parts);
    $query->where('a.id = ' . (int) $part);

    // Serious redirects
    $db->setQuery($query);

    try
    {
      // Get the location 
      $location = $db->loadObject();

      if (!$location)
      {
        throw new Exception('Page not found', 404);
      }

      $Itemid = SearchHelper::getItemid(array('component', 'com_fcsearch'));
      $route = JRoute::_('index.php?option=com_fcsearch&Itemid=' . $Itemid . '&s_kwds=' . JApplication::stringURLSafe($location->alias));

      // 301 redirect
      $app->redirect($route, true);
    }
    catch (Exception $e)
    {

      $uri = JUri::getInstance();

      // Log this as a redirect error
      // Redirect to home page

      JLog::addLogger(array('text_file' => '301-redirect-search'), JLog::ALL, array('redirect-search'));
      JLog::add('Problem 301 redirecting old search type url: ' . $e->getMessage() . ' :: ' . JUri::current() . $uri->getQuery(), JLog::ALL, 'redirect-search');
      $route = JRoute::_('index.php?option=com_fcsearch&Itemid=' . $Itemid . '&s_kwds=france');
      $app->redirect($route, true);
    }
  }

  /**
   * Deals with legacy search urls 
   * e.g. /en/search/gite/var
   */
  public function PropertySearch()
  {
    $app = JFactory::getApplication();
    $input = $app->input;
    $db = JFactory::getDbo();
    $Itemid = SearchHelper::getItemid(array('component', 'com_fcsearch'));

    $filter = $input->get('filter', '', 'string');

    $parts = array_filter(explode('/', $filter));

    // Define array which maps e.g. gite => property_gite_14 or whatever it is
    $propertyArr = array('apartment' => 'property_apartment_1', 'manoir' => 'property_manoir_15', 'farmhouse' => 'property_farmhouse_10', 'country-house' => 'property_country-house_9', 'cottage' => 'property_cottage_7', 'converted-barn' => 'property_converted-barn_8', 'chalet' => 'property_chalet_5', 'chateau' => 'property_chateau_6', 'auberge' => 'property_auberge_2', 'appartment' => 'property_apartement_1', 'gite' => 'property_gite_11');

    // Look up the location
    $query = $db->getQuery(true);

    // We're interested in the alias 
    $query->select('alias, id');
    $query->from('#__classifications a');

    // Take the keyword search and make it an alias
    $alias = JApplication::stringURLSafe($parts[1]);
    $query->where('a.alias = ' . $db->quote($alias));

    $db->setQuery($query);
    
    try
    {
      // Get the location 
      $location = $db->loadObject();

      if (!$location)
      {
        // Set the alias to france so we get something sensible
        $location->alias = 'france';

        // Log the problem for review
        $uri = JUri::getInstance();

        JLog::addLogger(array('text_file' => '301-redirect-search'), JLog::ALL, array('redirect-search'));
        JLog::add('Problem 301 redirecting old search type url: ' . JUri::current() . $uri->getQuery(), JLog::ALL, 'redirect-search');
      }
      // Route the new url - Don't use JRoute here as it appends the URL base to it.
      $route = JRoute::_('index.php?option=com_fcsearch&Itemid=' . $Itemid . '&s_kwds=' . JApplication::stringURLSafe($location->alias) . '/' . $propertyArr[$parts[0]]);

      // 301 redirect
      $app->redirect($route, true);
    }
    catch (Exception $e)
    {
      // Log the problem for review
      $uri = JUri::getInstance();

      JLog::addLogger(array('text_file' => '301-redirect-search'), JLog::ALL, array('redirect-search'));
      JLog::add('Exception 301 redirecting old search type url: ' . $e->getMessage() . '::' . JUri::current() . $uri->getQuery(), JLog::ALL, 'redirect-search');

      throw new Exception('Page not found', 404);
    }
  }

  public function Search()
  {

    // Define variables
    $allowable_params = array('vtab' => 'string', 'component' => 'string', 'filter' => 'string', 'sr_reg' => 'int', 'sr_dept' => 'int', 'sr_area' => 'int', 's_reg' => 'int', 's_dept' => 'int', 's_area' => 'int', 's_kwds' => 'string', 's_ptype' => 'int', 'lang' => 'string');
    $params_present = new stdClass;
    $app = JFactory::getApplication();
    $input = $app->input;
    $db = JFactory::getDbo();
    $Itemid = SearchHelper::getItemid(array('component', 'com_fcsearch'));

    // Loop over allowed params and check the input to see if they are present
    foreach ($allowable_params as $k => $v)
    {
      $param_id = $input->get($k, '', $v);
      if ($param_id)
      {
        $params_present->$k = $param_id;
      }
    }

    // Look up the location
    $query = $db->getQuery(true);

    // We're interested in the alias 
    $query->select('alias, id');
    $query->from('#__classifications a');

    // If a keyword search then see if it maps to a location
    if (!empty($params_present->s_kwds))
    {
      // Take the keyword search and make it an alias
      $alias = JApplication::stringURLSafe($params_present->s_kwds);
      $query->where('a.alias = ' . $db->quote($alias));
    }
    elseif (
            !empty($params_present->s_area) ||
            !empty($params_present->s_reg) ||
            !empty($params_present->s_dept) ||
            !empty($params_present->sr_area) ||
            !empty($params_present->sr_reg) ||
            !empty($params_present->sr_dept)
    )
    {

      // Must have a location based search
      // Department takes priority
      if (!empty($params_present->s_dept))
      {
        $query->where('a.id = ' . (int) $params_present->s_dept);
        // Then region
      }
      elseif (!empty($params_present->s_reg))
      {
        $query->where('a.id = ' . (int) $params_present->s_reg);
        // And lastly the area
      }
      elseif (!empty($params_present->s_area))
      {
        $query->where('a.id = ' . (int) $params_present->s_area);
      }
      elseif (!empty($params_present->sr_dept))
      {
        $query->where('a.id = ' . (int) $params_present->sr_dept);
      }
      elseif (!empty($params_present->sr_reg))
      {
        $query->where('a.id = ' . (int) $params_present->sr_reg);
      }
      elseif (!empty($params_present->sr_area))
      {
        $query->where('a.id = ' . (int) $params_present->sr_area);
      }
      else
      {
        $query->where('a.id = 2'); // If no areas are present, and no keywords are present then default to France? Could redirect to homepage
      }
    }
    elseif (!empty($params_present->filter))
    {
      // Array filter removes empty array value
      $parts = array_filter(explode('/', $params_present->filter));
      // TO DO - Make sure this works for 123-asd and aasdasd aliases
      if (count($parts) === 1)
      {
        $id = (int) $parts[0];

        if ($id > 0)
        {
          $query->where('a.id = ' . (int) $id);
        }
        else
        {
          // Take the keyword search and make it an alias
          $alias = JApplication::stringURLSafe($parts[0]);
          $query->where('a.alias = ' . $db->quote($alias));
        }
      }
      else
      {
        $part = array_pop(array_filter($parts));
        $query->where('a.id = ' . (int) $part);
      }
    }
    else
    {
      $query->where('a.id = 2'); // Final case where no location parms have been supplied.
    }

    if (!empty($params_present->s_ptype))
    {
      // Need to map these to the property types based on ID. Will work once we put the alias in...
    }

    // Serious redirects
    $db->setQuery($query);

    try
    {
      // Get the location 
      $location = $db->loadObject();

      if (!$location)
      {
        // Set the alias to france so we get something sensible
        $location->alias = 'france';

        // Log the problem for review
        $uri = JUri::getInstance();

        JLog::addLogger(array('text_file' => '301-redirect-search'), JLog::ALL, array('redirect-search'));
        JLog::add('Problem 301 redirecting old search type url: ' . JUri::current() . $uri->getQuery(), JLog::ALL, 'redirect-search');
      }
      // Route the new url - Don't use JRoute here as it appends the URL base to it.
      if (empty($params_present->component))
      {
        $Itemid = SearchHelper::getItemid(array('component', 'com_fcsearch'));
        $route = JRoute::_('index.php?option=com_fcsearch&Itemid=' . $Itemid . '&s_kwds=' . JApplication::stringURLSafe($location->alias));
      }
      else
      {
        $Itemid = SearchHelper::getItemid(array('component', 'com_realestatesearch'));
        $route = JRoute::_('index.php?option=com_realestatesearch&Itemid=' . $Itemid . '&s_kwds=' . JApplication::stringURLSafe($location->alias));
      }
      // 301 redirect
      $app->redirect($route, true);
    }
    catch (Exception $e)
    {
      // Log the problem for review
      $uri = JUri::getInstance();

      JLog::addLogger(array('text_file' => '301-redirect-search'), JLog::ALL, array('redirect-search'));
      JLog::add('Exception 301 redirecting old search type url: ' . $e->getMessage() . '::' . JUri::current() . $uri->getQuery(), JLog::ALL, 'redirect-search');

      throw new Exception('Page not found', 404);
    }
  }

}

