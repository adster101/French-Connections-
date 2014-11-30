<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * HelloWorldList Model
 */
class InterestingPlacesModelInterestingPlaces extends JModelList {

  /**
   * Constructor.
   *
   * @param	array	An optional associative array of configuration settings.
   * @see		JController
   * @since	1.6
   */
  public function __construct($config = array()) {
    if (empty($config['filter_fields'])) {
      $config['filter_fields'] = array(
          'id', 'a.id',
          'title', 'a.title',
          'published', 'a.published',
          'lft', 'a.lft',
          'rgt', 'a.rgt',
          'level', 'a.level'
      );
    }
    parent::__construct($config);
  }

  /**
   * Method to build an SQL query to load the list data.
   *
   * @return	string	An SQL query
   */
  protected function getListQuery() {

    // Create a new query object.		
    $db = JFactory::getDBO();
    $query = $db->getQuery(true);

    // Select some fields
    $query->select('id, title, published');

    // From the hello table
    $query->from('#__places_of_interest a');


    // Filter by published state

    $published = $this->getState('filter.published');
    if (is_numeric($published)) {
      $query->where('published = ' . (int) $published);
    }

    $search = $this->getState('filter.search');

    if (!empty($search)) {

      if (stripos($search, 'id:') === 0) {
        $query->where('a.id = ' . (int) $search);
      } else {
        $search = $db->Quote('%' . $db->escape($search, true) . '%');
        $query->where('(title like ' . $search . ' OR alias LIKE ' . $search . ')');
      }
    }


    $listOrdering = $this->getState('list.ordering', 'title');
    $listDirn = $db->escape($this->getState('list.direction', 'ASC'));

    $query->order($db->escape($listOrdering) . ' ' . $listDirn);

    return $query;
  }

  function getLanguages() {
    $lang = & JFactory::getLanguage();
    $languages = $lang->getKnownLanguages(JPATH_SITE);

    $return = array();
    foreach ($languages as $tag => $properties)
      $return[] = JHTML::_('select.option', $tag, $properties['name']);

    return $return;
  }

}
