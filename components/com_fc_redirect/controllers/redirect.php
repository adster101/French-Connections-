<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class Fc_RedirectControllerRedirect extends JControllerLegacy {

  public function Search() {

    // Define variables
    $allowable_params = array('s_reg' => 'int', 's_dept' => 'int', 's_area' => 'int', 's_kwds' => 'string', 's_ptype' => 'int', 'lang' => 'string');
    $params_present = new stdClass;
    $app = JFactory::getApplication();
    $input = $app->input;
    $db = JFactory::getDbo();

    // Loop over allowed params and check the input to see if they are present
    foreach ($allowable_params as $k => $v) {
      $param_id = $input->get($k, '', $v);
      if ($param_id) {
        $params_present->$k = $param_id;
      }
    }

    // Look up the location
    $query = $db->getQuery(true);

    // We're interested in the alias 
    $query->select('alias, id');

    // Switch on the language parameter
    if ($params_present->lang == 'en-GB') {
      $query->from('#__classifications a');
    } else {
      $query->from('#__classifications_translations a');
    }

    // If a keyword search then see if it maps to a location
    if (!empty($params_present->s_kwds)) {
      // Take the keyword search and make it an alias
      $alias = JApplication::stringURLSafe($params_present->s_kwds);
      $query->where('a.alias = ' . $db->quote($alias));
    } elseif ($params_present->s_area || $params_present->s_reg || $params_present->s_dept) {

      // Must have a location based search
      // Department takes priority
      if (!empty($params_present->s_dept)) {
        $query->where('a.id = ' . (int) $params_present->s_dept);
        // Then region
      } elseif (!empty($params_present->s_reg)) {
        $query->where('a.id = ' . (int) $params_present->s_reg);
        // And lastly the area
      } elseif (!empty($params_present->s_area)) {
        $query->where('a.id = ' . (int) $params_present->s_area);
      } else {
        $query->where('a.id = 2'); // If no areas are present, and no keywords are present then default to France? Could redirect to homepage
      }
    }

    if (!empty($params_present->s_ptype)) {
      // Need to map these to the property types based on ID. Will work once we put the alias in...
    }

    // Serious redirects
    $db->setQuery($query);

    try {
      // Get the location 
      $location = $db->loadObject();

      if (!$location) {
        throw new Exception('Redirect failed');
      }

      // Route the new url - Don't use JRoute here as it appends the URL base to it.
      // $route = JRoute::_('index.php?option=com_fcsearch&Itemid=165&s_kwds=' . JApplication::stringURLSafe($location->alias));

      if ($params_present->lang == 'en-GB') {
        // Hardcoded aliases. Slightly better than giving two 301 redirects to google
        $route = '/accommodation/' . JApplication::stringURLSafe($location->alias);
      } else {
        $route = '/fr/hebergement' . JApplication::stringURLSafe($location->alias);
        ;
      }
      // 301 redirect
      $app->redirect($route, true);
    } catch (RuntimeException $e) {

      $uri = JUri::getInstance();

      // Log this as a redirect error
      // Redirect to home page
      JLog::addLogger(array('text_file' => '301-redirect-search'), JLog::ALL, array('redirect-search'));
      JLog::add('Problem 301 redirecting old search type url: ' . $e->getMessage() . ' :: ' . JUri::current() . $uri->getQuery(), JLog::ALL, 'redirect-search');
      $app->redirect('/');
    }
  }
}

