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
 * Search HTML view class for the Finder package.
 *
 * @package     Joomla.Site
 * @subpackage  com_finder
 * @since       2.5
 */
class FcSearchViewSearch extends JViewLegacy
{

  protected $query;
  protected $params;
  protected $state;
  protected $user;

  /**
   * Given the pagination object determines if there are next/previous links and adjusts the head 
   * part of the document accordingly
   * 
   * @param type $pages
   * @param type $document
   * 
   * @return void
   */
  private function addHeadLinks($pages, $document)
  {

// If we have next links then add a rel=next head link
    if ($pages->next->link)
    {
      $document->addHeadLink($pages->next->link, 'next', 'rel');
    }

// If we have next links then add a rel=prev head link
    if ($pages->previous->link)
    {
      $document->addHeadLink($pages->previous->link, 'prev', 'rel');
    }
  }

  /**
   * Method to display the view.
   *
   * @param   string  $tpl  A template file to load. [optional]
   *
   * @return  mixed  JError object on failure, void on success.
   *
   * @since   2.5
   */
  public function display($tpl = null)
  {

// Get the app instance
    $app = JFactory::getApplication();

// Get the currencies
// Get view data.
    $this->state = $this->get('State');
    $this->localinfo = $this->get('LocalInfo');


    if ($this->localinfo === false)
    {
      $this->results = false;
      $this->total = 0;
      $this->document->setMetaData('robots', 'noindex, nofollow');
      $this->pagination = '';
    }
    else
    {

      $this->results = $this->get('Results');

      $this->pagination = $this->get('Pagination');

// Has to be done after getState, as with all really.
      $this->attribute_options = $this->get('RefineAttributeOptions');
      $this->location_options = $this->get('RefineLocationOptions');
      $this->property_options = $this->get('RefinePropertyOptions');
      $this->accommodation_options = $this->get('RefineAccommodationOptions');

      $this->seo_copy = $this->setPropertyTypeSEOCopy();

      $this->lwl = $this->get('RefineLWLOptions');
      $this->so = $this->get('RefineSOOptions');
      $this->shortlist = $this->get('Shortlist');
// Get the breadcrumb trail style search 
      $this->crumbs = $this->get('Crumbs');

      $search_url = JUri::getInstance()->toString();
//$query = ($uri->getQuery()) ? '?' . $uri->getQuery() : '';
//$search_url = $uri->current() . $query;
// Save the search url into the session scope
      $app->setUserState('user.search', $search_url);

// Configure the pathway.
      if (!empty($this->crumbs))
      {
        $app->getPathWay()->setPathway($this->crumbs);
      }
    }

    $this->get('LogSearch');

// Include the component HTML helpers.
    JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

// Check for errors.
    if (count($errors = $this->get('Errors')))
    {
      JError::raiseError(500, implode("\n", $errors));
      return false;
    }

    $this->prepareDocument();
    $this->sidebar = JHtmlSidebar::render();

// Log the search
    JSearchHelper::logSearch('Log some useful search information...', 'com_fcsearch');

// Check for layout override only if this is not the active menu item
// If it is the active menu item, then the view and category id will match
    $active = $app->getMenu()->getActive();
    if (isset($active->query['layout']))
    {
// We need to set the layout in case this is an alternative menu item (with an alternative layout)
      $this->setLayout($active->query['layout']);
    }

// Need to set valid meta data for the page here, load any JS, CSS Etc
    parent::display($tpl);
  }

  /**
   * Prepares the document
   *
   * @param   FinderIndexerQuery  $query  The search query
   *
   * @return  void
   *
   * @since   2.5
   */
  protected function prepareDocument()
  {

    $document = JFactory::getDocument();
    $app = JFactory::getApplication();
    $input = $app->input;

// Get the pagination object 
    if ($this->pagination)
    {
// Add next and prev links to head
      $pages = $this->pagination->getData();
      $this->addHeadLinks($pages, $document);
    }

    $property_type = $input->get('property', array(), 'array');
    $accommodation_type = $input->get('accommodation', array(), 'array');

    $bedrooms = $this->state->get('list.bedrooms');
    $occupancy = $this->state->get('list.occupancy');

// Location title - e.g. the location being searched on
    $location = ucwords(JStringNormalise::toSpaceSeparated($this->state->get('list.searchterm')));

// Add Chateua to the inflector
    $inflector = JStringInflector::getInstance();
    $inflector->addWord('Chateau', 'Chateaux ');

// Generate the page META title
    $title = $this->getTitle($property_type, $accommodation_type, $location, $bedrooms, $occupancy, $inflector);
    $description = $this->getDescription($property_type, $accommodation_type, $location, $inflector);

// Append the site name to keep the SEOs happy
    $title .= ' - ' . $app->getCfg('sitename');

// Set the page and document title
    $this->document->setTitle($title);
    $this->document->setDescription($description);

// Add some scripts and shit
    $document->addScript(JURI::root() . 'media/jui/js/cookies.jquery.min.js', 'text/javascript', true);
    $document->addScript(JURI::root() . 'media/fc/js/search.js', 'text/javascript', true);
    $document->addScript(JURI::root() . 'media/fc/js/general.js', 'text/javascript', true);
    $document->addScript(JURI::root() . 'media/fc/js/jquery-ui-1.10.1.custom.min.js', 'text/javascript', true);
    JText::script('COM_FCSEARCH_SEARCH_SHOW_MORE_OPTIONS');
    JText::script('COM_FCSEARCH_SEARCH_SHOW_LESS_OPTIONS');

    JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'), 'sort_by', JHtml::_('select.options', array('beds' => 'Bedrooms'), 'value', 'text', $this->state->get('filter.published'), true)
    );
  }

  /**
   * Get a list of filter options for the state of a module.
   *
   * @return	array	An array of JHtmlOption elements.
   */
  protected function getBudgetFields($start = 250, $end = 5000, $step = 250, $budget = 'min_')
  {
// Build the filter options.
    $options = array();

    $options[] = JHtml::_('select.option', '', JText::_('COM_FCSEARCH_SEARCH_' . strtoupper($budget). 'PRICE'));

    for ($i = 50; $i < 250; $i = $i + 50)
    {
      $options[] = JHtml::_('select.option', $budget . $i, $i);
    }


    for ($i = $start; $i < $end; $i = $i + $step)
    {
      $options[] = JHtml::_('select.option', $budget . $i, $i);
    }

    return $options;
  }

  /**
   * Get a list of filter options for the state of a module.
   *
   * @return	array	An array of JHtmlOption elements.
   */
  protected function getSortFields()
  {
// Build the filter options.
    $options = array();

    $options[] = JHtml::_('select.option', '', JText::_('COM_FCSEARCH_SEARCH_PLEASE_CHOOSE'));
    $options[] = JHtml::_('select.option', 'order_price_ASC', JText::_('COM_FCSEARCH_SEARCH_ORDER_PRICE_ASC'));
    $options[] = JHtml::_('select.option', 'order_price_DESC', JText::_('COM_FCSEARCH_SEARCH_ORDER_PRICE_DESC'));
    $options[] = JHtml::_('select.option', 'order_occupancy_ASC', JText::_('COM_FCSEARCH_SEARCH_ORDER_OCCUPANCY'));
    $options[] = JHtml::_('select.option', 'order_reviews_desc', JText::_('COM_FCSEARCH_SEARCH_ORDER_REVIEWS'));
    $options[] = JHtml::_('select.option', 'order_bedrooms_ASC', JText::_('COM_FCSEARCH_SEARCH_ORDER_BEDROOMS_LOW_TO_HIGH'));
    $options[] = JHtml::_('select.option', 'order_bedrooms_DESC', JText::_('COM_FCSEARCH_SEARCH_ORDER_BEDROOMS_HIGH_TO_LOW'));
    return $options;
  }

  /**
   * Method to generate a page title for use in the META and H1 elements 
   * 
   * @param type $property_types
   * @param type $accommodation_types
   * @param type $location
   * @param type $bedrooms
   * @param type $occupancy
   * 
   * @return type string
   */
  private function getTitle($property_types = array(), $accommodation_types = array(), $location = '', $bedrooms = '', $occupancy = '', $inflector = '')
  {

    $app = JFactory::getApplication();
    $input = $app->input;
    $accommodation_type = '';
    $property_type = '';
    $location_title = str_replace('France', '', $location);
    $lwl = $input->get('lwl', false, 'boolean');
    $offers = $input->get('offers', false, 'boolean');

// Work out the property type we have
    if (!empty($property_types))
    {
      $property_parts = explode('_', $property_types[0]);
      $property_type = JStringNormalise::toSpaceSeparated($property_parts[1]);
    }

// Work out the accommodation type we have
    if (!empty($accommodation_types))
    {
      $accommodation_parts = explode('_', $accommodation_types[0]);
      $accommodation_type = JStringNormalise::toSpaceSeparated($accommodation_parts[1]);
    }

// Work out the meta title pattern to use
    if ($property_type && $accommodation_type)
    {
      $plural_property_type = $inflector->toPlural($property_type);
      $title = JText::sprintf('COM_FCSEARCH_ACCOMMODATION_PROPERTY_TITLE', ucwords($location_title), ucwords($accommodation_type), ucwords($plural_property_type));
    }
    elseif ($accommodation_type)
    {
      $title = JText::sprintf('COM_FCSEARCH_ACCOMMODATION_TYPE_TITLE', ucfirst($accommodation_type), ucwords($location_title));
    }
    elseif ($property_type)
    {
      $plural_property_type = $inflector->toPlural($property_type);
// In this case we want to strip all occurances of France as it appears in the language string
      $title = JText::sprintf('COM_FCSEARCH_PROPERTY_TYPE_TITLE', ucfirst($plural_property_type), ucwords($location_title));
    }
    else
    {
      $title = JText::sprintf('COM_FCSEARCH_TITLE', ucwords($location_title));
    }

    if ($lwl)
    {
      $title = JText::sprintf('COM_FCSEARCH_TITLE_LONG_WINTER_LETS', $location);
    }

    if ($offers)
    {
      $title = JText::sprintf('COM_FCSEARCH_TITLE_SPECIAL_OFFERS', $location);
    }

// Amend the title based on bedroom and occupancy filter
    $bedrooms = ($bedrooms == 10) ? "$bedrooms+" : $bedrooms;
    $title .= ($bedrooms) ? ' | ' . $bedrooms . ' ' . JText::_('COM_FCSEARCH_SEARCH_BEDROOMS') : '';
    $title .= ($occupancy) ? ' | ' . JText::_('COM_FCSEARCH_SEARCH_OCCUPANCY') . ' ' . $occupancy : '';

    return $title;
  }

  /**
   * Method to generate a page title for use in the META and H1 elements 
   * 
   * @param type $property_types
   * @param type $accommodation_types
   * @param type $location
   * @param type $bedrooms
   * @param type $occupancy
   * 
   * @return type string
   */
  private function getDescription($property_types = array(), $accommodation_types = array(), $location = '', $inflector)
  {
    $accommodation_type = '';
    $property_type = '';
    $location_title = str_replace('France', '', $location);


// Work out the property type we have
// TO DO - extend this to add a canonical tag if multiple property types are selected.
    if (!empty($property_types))
    {
      $property_parts = explode('_', $property_types[0]);
      $property_type = JStringNormalise::toSpaceSeparated($property_parts[1]);
    }

// Work out the accommodation type we have
// TO DO - extend this to add a canonical tag if both accommodation type are selected.
    if (!empty($accommodation_types))
    {
      $accommodation_parts = explode('_', $accommodation_types[0]);
      $accommodation_type = JStringNormalise::toSpaceSeparated($accommodation_parts[1]);
    }

// Work out the meta title pattern to use
    if ($property_type && $accommodation_type)
    {
      $plural_property_type = $inflector->toPlural($property_type);
      $title = JText::sprintf('COM_FCSEARCH_ACCOMMODATION_PROPERTY_DESCRIPTION', ucwords($accommodation_type), ucwords($location_title), ucwords($property_type));
    }
    elseif ($accommodation_type)
    {
      $title = JText::sprintf('COM_FCSEARCH_ACCOMMODATION_TYPE_DESCRIPTION', ucfirst($accommodation_type), ucwords($location_title), ucfirst($accommodation_type));
    }
    elseif ($property_type)
    {
      $plural_property_type = $inflector->toPlural($property_type);
      $title = JText::sprintf('COM_FCSEARCH_PROPERTY_TYPE_DESCRIPTION', ucfirst($property_type), ucwords($location_title), ucfirst($location_title));
    }
    else
    {
      $title = JText::sprintf('COM_FCSEARCH_DESCRIPTION', ucwords($location), ucwords($location_title));
    }

    return $title;
  }

  public function setPropertyTypeSEOCopy()
  {

    $app = JFactory::getApplication();
    $input = $app->input;

    $property_types = $input->get('property', array(), 'array');    
    
    // Work out the property type we have
    // TO DO - extend this to add a canonical tag if multiple property types are selected.
    if (!empty($property_types))
    {
      $property_parts = explode('_', $property_types[0]);
      $property_type = JStringNormalise::toSpaceSeparated($property_parts[1]);
    }

    $property_type_info = json_decode($this->localinfo->property_type_info);
    
    if (!empty($property_type_info->$property_type))
    {
     return $property_type_info->$property_type;
    }
    else
    {
      return false;
    }
  }

}
