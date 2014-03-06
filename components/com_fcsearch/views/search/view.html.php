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
class FcSearchViewSearch extends JViewLegacy {

  protected $query;
  protected $params;
  protected $state;
  protected $user;

  /**
   * Method to display the view.
   *
   * @param   string  $tpl  A template file to load. [optional]
   *
   * @return  mixed  JError object on failure, void on success.
   *
   * @since   2.5
   */
  public function display($tpl = null) {

    // Get the app instance
    $app = JFactory::getApplication();
    
    // Get the currencies
    // Get view data.
    $this->state = $this->get('State');
    $this->localinfo = $this->get('LocalInfo');

    if ($this->localinfo === false) {

      $this->results = false;
      $this->total = 0;
      $this->document->setMetaData('robots', 'noindex, nofollow');
      $this->pagination = '';
    } else {

      $this->results = $this->get('Results');

      $this->pagination = $this->get('Pagination');

      // Has to be done after getState, as with all really.
      $this->attribute_options = $this->get('RefineAttributeOptions');
      $this->location_options = $this->get('RefineLocationOptions');
      $this->property_options = $this->get('RefinePropertyOptions');
      $this->accommodation_options = $this->get('RefineAccommodationOptions');
      $this->shortlist = $this->get('Shortlist');
      // Get the breadcrumb trail style search 
      $this->crumbs = $this->get('Crumbs');

      $search_url = JUri::getInstance()->toString();
      //$query = ($uri->getQuery()) ? '?' . $uri->getQuery() : '';
      //$search_url = $uri->current() . $query;

      // Save the search url into the session scope
      $app->setUserState('user.search', $search_url);

      // Configure the pathway.
      if (!empty($this->crumbs)) {
        $app->getPathWay()->setPathway($this->crumbs);
      }
    }

    $this->get('LogSearch');

    // Include the component HTML helpers.
    JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
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
    if (isset($active->query['layout'])) {
      // We need to set the layout in case this is an alternative menu item (with an alternative layout)
      $this->setLayout($active->query['layout']);
    }


    // Need to set valid meta data for the page here, load any JS, CSS Etc
    parent::display($tpl);
  }

  /**
   * Method to get hidden input fields for a search form so that control variables
   * are not lost upon form submission. E.g. if someone is filtering on gites we remember that so that the next search
   * is still focused on Gites.
   *
   * @return  string  A string of hidden input form fields
   *
   * @since   2.5
   */
  protected function getFilters() {
    $filter_str = array();

    // Get the input...
    $app = JFactory::getApplication();
    $input = $app->input->get('accommodation');

    // Obviously, these the search URL is built up via js prior to the form submit
    $filters = array('property', 'external', 'accommodation', 'internal', 'activities', 'kitchen');

    // Create hidden input elements for each part of the URI.
    foreach ($filters as $filter) {

      $filter_test = $app->input->get($filter, array(), 'array');

      if (is_array($filter_test)) {
        foreach ($filter_test as $key => $value) {

          $filter_str[] = $value;
        }
      }
    }

    $fields = '<input type="hidden" name="filter" value="' . implode('/', $filter_str) . '" id="filter" />';

    return $fields;
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
  protected function prepareDocument() {

    $document = JFactory::getDocument();
    $app = JFactory::getApplication();
    $input = $app->input;

    // Get the pagination object 
    if ($this->pagination) {
      $pages = $this->pagination->getData();
    }

    $property_type = $input->get('property', '', 'array');
    $accommodation_type = $input->get('accommodation', '', 'array');

    // Add next and prev links to head
    $this->addHeadLinks($pages, $document);

    // Add canonical link depending on the property type
    //$this->addCanonicalLink($property_type, $document);
    // Location title - e.g. the location being searched on
    $title = UCFirst(JStringNormalise::toSpaceSeparated($this->state->get('list.searchterm')));



    if ($property_type) {
      $parts = explode('_', $property_type[0]);
      array_pop($parts);
      array_shift($parts);
      $type = implode(' ', $parts);
      $type = JStringNormalise::toSpaceSeparated($type);
      $title = JText::sprintf('COM_FCSEARCH_PROPERTY_TYPE_TITLE', ucfirst($type), ucwords($title), ucwords($title), ucfirst($type)) . ' - ' . $app->getCfg('sitename');
    } elseif ($accommodation_type) {
      $parts = explode('_', $accommodation_type[0]);
      array_pop($parts);
      array_shift($parts);
      array_shift($parts);
      $type = implode(' ', $parts);
      $type = JStringNormalise::toSpaceSeparated($type);
      $title = JText::sprintf('COM_FCSEARCH_ACCOMMODATION_TYPE_TITLE', ucfirst($type), ucwords($title), ucwords($title), ucfirst($type)) . ' - ' . $app->getCfg('sitename');
    } else {
      $title = JText::sprintf('COM_FCSEARCH_TITLE', ucwords($title), ucwords($title)) . ' - ' . $app->getCfg('sitename');
    }

    $bedrooms = $this->state->get('list.bedrooms');
    $occupancy = $this->state->get('list.occupancy');

    $activities = $app->input->get('activities', array(), 'array');

    $activityStr = (string) '';

    if (count($activities) > 0) {
      foreach ($activities as $key => $value) {
        $parts = explode('_', $value);
        array_pop($parts);
        array_shift($parts);
        $activity = implode(' ', $parts);
        $activity = JStringNormalise::toSpaceSeparated($activity);
        $activityStr .= ' | ' . $activity;
      }
    }

    $title = ($bedrooms ? $title . ' | ' . $bedrooms . ' ' . JText::_('COM_FCSEARCH_SEARCH_BEDROOMS') : $title);
    $title = ($occupancy ? $title . ' | ' . $occupancy . ' ' . JText::_('COM_FCSEARCH_SEARCH_OCCUPANCY') : $title);
    $title = ($activityStr ? $title . $activityStr : $title);

    $this->document->setTitle($title);

    // Configure the document meta-description.
    if (!empty($this->explained)) {
      $explained = $this->escape(html_entity_decode(strip_tags($this->explained), ENT_QUOTES, 'UTF-8'));
      $this->document->setDescription($explained);
    }

    $document->addScript(JURI::root() . 'media/jui/js/cookies.jquery.min.js', 'text/javascript', true);
    $document->addScript(JURI::root() . 'media/fc/js/search.js', 'text/javascript', true);
    $document->addScript(JURI::root() . 'media/fc/js/general.js', 'text/javascript', true);
    $document->addScript(JURI::root() . 'media/fc/js/jquery-ui-1.10.1.custom.min.js', 'text/javascript', true);
    $document->addStyleSheet(JURI::root() . 'media/fc/css/jquery-ui-1.10.1.custom.min.css');
    $document->addStyleSheet(JURI::root() . 'media/fc/css/general.css');
    JText::script('COM_FCSEARCH_SEARCH_SHOW_MORE_OPTIONS');
    JText::script('COM_FCSEARCH_SEARCH_SHOW_LESS_OPTIONS');

    JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'), 'sort_by', JHtml::_('select.options', array('beds' => 'Bedrooms'), 'value', 'text', $this->state->get('filter.published'), true)
    );
  }

  /**
   * Given the property_type datadetermines if the user is searching on more than one property type
   * and if so adds a canonical link to indicate which page is the 'master'
   * 
   * @param type $pages
   * @param type $document
   * 
   * @return void
   */
  private function addCanonicalLink(array $property_type, $document) {

    if (count($property_type) > 1) {

      //$uri = str_replace('http://', '', JUri::current());
      //array_pop($property_type);
      //$document->addHeadLink($pages->next->link, 'next', 'rel');
    }

    // If we have next links then add a rel=next head link
    //if ($pages->next->link) {
    //}
  }

  /**
   * Given the pagination object determines if there are next/previous links and adjusts the head 
   * part of the document accordingly
   * 
   * @param type $pages
   * @param type $document
   * 
   * @return void
   */
  private function addHeadLinks($pages, $document) {

    // If we have next links then add a rel=next head link
    if ($pages->next->link) {
      $document->addHeadLink($pages->next->link, 'next', 'rel');
    }

    // If we have next links then add a rel=prev head link
    if ($pages->previous->link) {
      $document->addHeadLink($pages->previous->link, 'prev', 'rel');
    }
  }

  /**
   * Get a list of filter options for the state of a module.
   *
   * @return	array	An array of JHtmlOption elements.
   */
  protected function getSortFields() {
// Build the filter options.
    $options = array();

    $options[] = JHtml::_('select.option', '', JText::_('COM_FCSEARCH_SEARCH_PLEASE_CHOOSE'));
    $options[] = JHtml::_('select.option', 'order_price_ASC', JText::_('COM_FCSEARCH_SEARCH_ORDER_PRICE_ASC'));
    $options[] = JHtml::_('select.option', 'order_price_DESC', JText::_('COM_FCSEARCH_SEARCH_ORDER_PRICE_DESC'));
    $options[] = JHtml::_('select.option', 'order_occupancy_ASC', JText::_('COM_FCSEARCH_SEARCH_ORDER_OCCUPANCY'));
    $options[] = JHtml::_('select.option', 'order_reviews_desc', JText::_('COM_FCSEARCH_SEARCH_ORDER_REVIEWS'));
    return $options;
  }

  /**
   * Get a list of filter options for the state of a module.
   *
   * @return	array	An array of JHtmlOption elements.
   */
  protected function getBudgetFields($start = 250, $end = 5000, $step = 250, $budget = 'min_') {
// Build the filter options.
    $options = array();

    $options[] = JHtml::_('select.option', '', JText::_('COM_FCSEARCH_SEARCH_MINIMUM_PRICE'));

    for ($i = $start; $i < $end; $i = $i + $step) {
      $options[] = JHtml::_('select.option', $budget . $i, $i);
    }

    return $options;
  }

}
