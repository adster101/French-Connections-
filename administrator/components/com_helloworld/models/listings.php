<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * HelloWorldList Model
 */
class HelloWorldModelListings extends JModelList {

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
          'a.id', 'a.id',
          'title', 'a.title',
          'alias', 'a.alias',
          'review', 'a.review',
          'access', 'a.access', 'access_level',
          'language', 'a.language',
          'a.expiry_date', 'a.expiry_date',
          'checked_out', 'a.checked_out',
          'checked_out_time', 'a.checked_out_time',
          'created_user_id', 'a.created_user_id',
          'a.created_on', 'a.created_on',
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

    $start_date = $this->getUserStateFromRequest($this->context . '.filter.start_date', 'start_date', '', 'date');
    $this->setState('filter.start_date', $start_date);

    $end_date = $this->getUserStateFromRequest($this->context . '.filter.end_date', 'end_date', '', 'date');
    $this->setState('filter.end_date', $end_date);
   
    $date_filter = $this->getUserStateFromRequest($this->context . '.filter.date_filter', 'date_filter', '', 'string');
    $this->setState('filter.date_filter', $date_filter);
    
    $review_state = $this->getUserStateFromRequest($this->context . '.filter.review', 'filter_review', '');
    $this->setState('filter.review', $review_state);

    $snooze_state = $this->getUserStateFromRequest($this->context . '.filter.snoozed', 'filter_snoozed', false);

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
    $id .= ':' . $this->getState('filter.review');
    $id .= ':' . $this->getState('filter.snoozed');
    $id .= ':' . $this->getState('filter.expiry_end_date');
    $id .= ':' . $this->getState('filter.expiry_start_date');

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

    // Add in the number of page view this property has had in the last twelve months...
    $now = date('Y-m-d');
    $last_year = strtotime("-1 year", strtotime($now));

    // Select some fields
    $query->select('
      a.id,
      b.title,
      a.checked_out,
      a.checked_out_time,
      a.created_by,
      a.published,
      date_format(a.expiry_date, "%D %M %Y") as expiry_date,
      date_format(a.created_on, "%D %M %Y") as created_on,
      date_format(a.modified, "%D %M %Y") as modified,
      a.VendorTxCode,
      a.review,
      d.id as unit_id,
      f.image_file_name as thumbnail
    ');

    // Join the user details if the user has the ACL rights.
    if ($canDo->get('helloworld.display.owner')) {
      $query->select('
        u.email,
        p.phone_1,
        u.name,
        uc.name as editor
      ');
      $query->join('LEFT', '#__users AS u ON u.id = a.created_by');
      $query->join('LEFT', '#__user_profile_fc AS p ON p.user_id = u.id');

      // Join over the users for the checked out user.
      $query->select('uc.name AS editor');
      $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');
    }



    // Fundamental check to ensure owners only see their own listings.
    // This is an ACL check, e.g. core.edit.own and core.edit
    // if ($user->authorise('core.edit.own') && $user->authorise('core.edit'))
    //If true then has permission to edit all as well as own, otherwise just own
    if ($canDo->get('core.edit.own') && !$canDo->get('core.edit')) {
      $query->where('a.created_by=' . $userId);
    }

    $query->where('a.created_by !=0');

    // Filter by published state
    $published = $this->getState('filter.published');
    if (is_numeric($published)) {
      $query->where('a.published = ' . (int) $published);
    } elseif ($published === '') {
      $query->where('a.published in (0,1)');
    }

    // Filter by review state
    $review_state = $this->getState('filter.review');
    if (is_numeric($review_state)) {
      $query->where('a.review = ' . (int) $review_state);
    }

    // Filter by snooze state
    // Should only apply to users who can view and change snooze state
    if ($canDo->get('helloworld.notes.add')) {

      $snooze_state = $this->getState('filter.snoozed');

      // If snooze state is not set or set to hide snoozed...
      if ($snooze_state == false || $snooze_state == 1) {

        // ...hide snoozed properties (i.e. only select expired snooze or where snooze hasn't been set
        $query->where('(a.snooze_until < ' . $db->quote(JFactory::getDate()->calendar("Y-m-d")) . ' OR a.snooze_until is null)');
      } elseif ($snooze_state == 2) {

        // Don't filter, user wants to see all snoozed props as well as not snoozed etc
      }
    }

    // Filter on expiry date
    $start_date = $this->getState('filter.start_date');
    $end_date = $this->getState('filter.end_date');
    $date_filter = $this->getState('filter.date_filter');
    
    if ($start_date && $end_date && $date_filter) {
      $query->where('a.' . $db->escape($date_filter) .  ' >=' . $db->quote($start_date) . ' and a.' . $db->escape($date_filter) . ' <=' . $db->quote($end_date));
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
        $query->where('(u.name LIKE ' . $search . ' OR u.username LIKE ' . $search . ' OR u.email LIKE ' . $search . ')');
      } elseif (stripos($search, 'accid:') === 0) {
        $search = substr($search, 6);
        $query->where('(u.id = ' . $search . ')');
      } else {
        $search = $db->Quote('%' . $db->escape($search, true) . '%');
        $query->where('(b.title LIKE ' . $search . ')');
      }
    }

    // From the hello table
    $query->from('#__property as a');
    $query->join('inner', '#__property_versions as b on (
      a.id = b.property_id
      and b.id = (select max(c.id) from #__property_versions as c where c.property_id = a.id)
    )');
    
    // Join the units for the image
    $query->join('left', '#__unit d on d.property_id = a.id');
    $query->join('left', '#__unit_versions e on (d.id = e.unit_id and e.id = (select max(f.id) from #__unit_versions f where unit_id = d.id))');
    $query->where('(d.ordering = 1 or d.ordering is null)');
    
    // Join the images, innit!
    $query->join('left', '#__property_images_library f on e.id = f.version_id' );
    $query->where('(f.ordering = 1 or f.ordering is null)');
    
    $listOrdering = $this->getState('list.ordering', 'a.id');
    $listDirn = $db->escape($this->getState('list.direction', ''));

    // Order if we have a specific ordering.
    if ($listOrdering) {
      $query->order($db->escape($listOrdering) . ' ' . $listDirn);
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

