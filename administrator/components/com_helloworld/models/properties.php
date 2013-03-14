<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * HelloWorldList Model
 */
class HelloWorldModelProperties extends JModelList {

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
          'state', 'a.state',
          'access', 'a.access', 'access_level',
          'language', 'a.language',
          'expiry_date', 'a.expiry_date',
          'checked_out', 'a.checked_out',
          'checked_out_time', 'a.checked_out_time',
          'created_time', 'a.created_time',
          'created_user_id', 'a.created_user_id',
          'level', 'a.level',
          'path', 'a.path',
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

    $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
    $this->setState('filter.published', $published);

    $review_state = $this->getUserStateFromRequest($this->context . '.filter.review_state', 'filter_state', '');
    $this->setState('filter.review_state', $review_state);

    $snooze_state = $this->getUserStateFromRequest($this->context . '.filter.snoozed', 'filter_snoozed', '');
    $this->setState('filter.snoozed', $snooze_state);

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
    $id .= ':' . $this->getState('filter.review_state');
    $id .= ':' . $this->getState('filter.snoozed');

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

    // Create a new query object.		
    $db = JFactory::getDBO();
    $query = $db->getQuery(true);

    // Select some fields
    $query->select('
      a.id, 
      a.title, 
      a.created_by, 
      a.published,
      a.expiry_date,
      a.modified,
      views.count,
      a.auto_renew
    ');

    // Join the user details if the user has the ACL rights.
    if ($canDo->get('helloworld.display.owner')) {
      $query->select('
        u.email,
        p.phone_1,
        u.name

      ');
      $query->join('LEFT', '#__users AS u ON u.id = a.created_by');
      $query->join('LEFT', '#__user_profile_fc AS p ON p.user_id = u.id');
    }

    // Add in the number of page view this property has had in the last twelve months...
    $now = date('Y-m-d');
    $last_year = strtotime("-1 year", strtotime($now));
    $query->join('left', '(SELECT property_id, count(id) as count FROM #__property_views where  date > ' . $db->quote(date('Y-m-d', $last_year)) . ' group by property_id) views on views.property_id = a.id');

    // Check the user group this user belongs to. 
    // Fundamental check to ensure owners only see their own listings.
    // Should this be with an ACL check, e.g. core.edit.own and core.edit
    // if ($user->authorise('core.edit.own') && $user->authorise('core.edit'))
    //  // If true then has permission to edit all as well as own, otherwise just own
    if ($canDo->get('core.edit.own') && !$canDo->get('core.edit')) {
      $query->where('a.created_by=' . $userId);
    }

    $query->where('a.created_by !=0');

    // Filter by published state
    $published = $this->getState('filter.published');
    if (is_numeric($published)) {
      $query->where('a.published = ' . (int) $published);
    } elseif ($published == '') {
      $query->where('(a.published = 0 OR a.published = 1)');
    }

    // Filter by review state
    $review_state = $this->getState('filter.review_state');
    if (is_numeric($review_state)) {
      $query->where('a.state = ' . (int) $review_state);
    }

    // Filter by snooze state
    // Should only apply to users who can view and change snooze state
    if ($canDo->get('helloworld.snooze')) {
      $snooze_state = $this->getState('filter.snoozed');
      if (!empty($snooze_state)) {
        if ($snooze_state == 0) {
          $query->where('a.snooze_until < ' . date('Y-m-d'));
        }
      } else {
        $query->where('(a.snooze_until < ' . date('Y-m-d') . ' OR a.snooze_until is null)');
      }
    }

    // Filter by search in title
    // TODO - Try and tidy up this logic a bit.
    $search = $this->getState('filter.search');
    if (!empty($search)) {
      if ((int) $search) {

        // This pulls out the property with ID searched on, it's parent and any siblings. 
        $query->where('a.id = ' . (int) $search);
      } elseif (stripos($search, 'account:') === 0) {
        $search = $db->Quote('%' . $db->escape(substr($search, 8), true) . '%');
        $query->where('(u.name LIKE ' . $search . ' OR ua.username LIKE ' . $search . ')');
      } elseif (stripos($search, 'accid:') === 0) {
        $search = substr($search, 6);
        $query->where('(u.id = ' . $search . ')');
      } else {
        $search = $db->Quote('%' . $db->escape($search, true) . '%');
        $query->where('(a.title LIKE ' . $search . ')');
      }
    }


    // From the hello table
    $query->from('#__property_listings as a');

    $listOrdering = $this->getState('list.ordering', 'a.id');
    $listDirn = $db->escape($this->getState('list.direction', 'ASC'));

    $query->order($db->escape($listOrdering) . ' ' . $listDirn);

    $query->order($db->escape('a.created_by'));

    //$query->where('h.id is not null');

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
