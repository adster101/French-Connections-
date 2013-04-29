<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * HelloWorldList Model
 */
class HelloWorldModelListing extends JModelList {

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
          'alias', 'a.alias',
          'review', 'a.review',
          'access', 'a.access', 'access_level',
          'language', 'a.language',
          'expiry_date', 'a.expiry_date',
          'checked_out', 'a.checked_out',
          'checked_out_time', 'a.checked_out_time',
          'created_time', 'a.created_time',
          'created_user_id', 'a.created_user_id',
          'snoozed', 'a.snooze_until'
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

    $extension = $app->getUserStateFromRequest('com_helloworlds.property.filter.extension', 'extension', 'com_helloworlds', 'cmd');

    $this->setState('filter.extension', $extension);
    $parts = explode('.', $extension);

    // Should be an int. No filter is null so perhaps no filter should be -1?
    $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
    $this->setState('filter.published', $published);
    
    // extract the component name
    $this->setState('filter.component', $parts[0]);

    $search = $this->getUserStateFromRequest($context . '.search', 'filter_search');
    $this->setState('filter.search', $search);

    // List state information.
    parent::populateState('a.id', 'asc');
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
    $id .= ':' . $this->getState('filter.published');

    return parent::getStoreId($id);
  }

  /**
   * Method to build an SQL query to load the list data.
   *
   * @return	string	An SQL query
   */
  protected function getListQuery() {
    // Get the user ID
    $user = JFactory::getUser();
    $userId = $user->get('id');

    // Get the access control permissions in a handy array
    $canDo = HelloWorldHelper::getActions();
    
    // Get the app/input gubbins
    $app = JFactory::getApplication();
    $input = $app->input;
    
    // The listing ID
    $id = $input->get('id', '', 'int');
    
    // Create a new query object.		
    $db = JFactory::getDBO();
    $query = $db->getQuery(true);

    // Initialise the query.
    $query = $this->_db->getQuery(true);
    $query->select('
        pu.id,
        pu.parent_id,
        
        pu.ordering,
        pu.unit_title,
        CASE
          WHEN pu.review = 0 
            THEN (select count(*) from qitz3_property_images_library where property_id =  pu.id)
          ELSE
            (select count(*) from qitz3_property_images_library_version where property_id =  pu.id)
        END as image_count,
        (select count(*) from qitz3_availability where id = pu.id and end_date > CURDATE()) as availability,
        (select count(*) from qitz3_tariffs where id = pu.id and end_date > CURDATE()) as tariffs
      ');
    $query->from('#__property_units as pu');
    $query->join('left','#__property_listings pl on pl.id = pu.parent_id'); 
    $query->where('pu.parent_id = ' . (int) $id);
    
    // Check the user group this user belongs to. 
    // Fundamental check to ensure owners only see their own listings.
    // Should this be with an ACL check, e.g. core.edit.own and core.edit
    // if ($user->authorise('core.edit.own') && $user->authorise('core.edit'))
    //  // If true then has permission to edit all as well as own, otherwise just own
    if ($canDo->get('core.edit.own') && !$canDo->get('core.edit')) {
      $query->where('pl.created_by=' . $userId);
    }

    $query->where('pl.created_by !=0');

    // Filter by published state
    $published = $this->getState('filter.published');
    if (is_numeric($published)) {
      $query->where('pu.published = ' . (int) $published);
    } elseif ($published == '') {
      $query->where('(pu.published = 0 OR pu.published = 1)');
    }


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

