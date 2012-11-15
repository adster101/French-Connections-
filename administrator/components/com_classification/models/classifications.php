<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * HelloWorldList Model
 */
class ClassificationModelClassifications extends JModelList {

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
   * Method to auto-populate the model state.
   *
   * Note. Calling getState in this method will result in recursion.
   *
   * @param	string	An optional ordering field.
   * @param	string	An optional direction (asc|desc).
   *
   * @return	void
   * @since	1.6
   */
  protected function populateState($ordering = null, $direction = null) {
    // Initialise variables
    $app = JFactory::getApplication();
    $context = $this->context;

    $extension = $app->getUserStateFromRequest('com_classification.classification.filter.extension', 'extension', 'com_classification', 'cmd');

    $this->setState('filter.extension', $extension);
    $parts = explode('.', $extension);

    // extract the component name
    $this->setState('filter.component', $parts[0]);

    $search = $this->getUserStateFromRequest($context . '.search', 'filter_search');
    $this->setState('filter.search', $search);

    $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
    $this->setState('filter.published', $published);

    // List state information.
    parent::populateState('lft', 'asc');
  }

  /**
   * Method to get a store id based on model configuration state.
   *
   * This is necessary because the model is used by the component and
   * different modules that might need different sets of data or different
   * ordering requirements.
   *
   * @param	string		$id	A prefix for the store id.
   *
   * @return	string		A store id.
   * @since	1.6
   */
  protected function getStoreId($id = '') {
    // Compile the store id.
    $id .= ':' . $this->getState('filter.search');
    $id .= ':' . $this->getState('filter.extension');

    return parent::getStoreId($id);
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
    $query->select(
            'id,title,parent_id,level,lft,rgt,alias,access,published');
    
    // From the hello table
    $query->from('#__classifications');

    $query->where('parent_id > 0');

    // Filter by published state

    $published = $this->getState('filter.published');
    if (is_numeric($published)) {
      $query->where('published = ' . (int) $published);
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
