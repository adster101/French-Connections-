<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * HelloWorldList Model
 */
class RentalModelListings extends JModelList {

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
                'snoozed', 'published',
                'modified', 'a.modified',
                'date_filter',
                'value', 'a.value'
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
        $canDo = RentalHelper::getActions();

        // Create a new query object.
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        // Add in the number of page view this property has had in the last twelve months...
        $now = date('Y-m-d');

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
      date_format(a.modified, "%D %M %Y %H:%i:%s") as modified,
      a.VendorTxCode,
      a.review,
      d.id as unit_id,
      f.image_file_name as thumbnail,
      f.url_thumb,
              (select count(*) from #__vouchers v where v.property_id = a.id and v.state = 1' . ' and v.end_date >= ' . $db->quote($now) . ' and v.item_cost_id = ' . $db->quote("1006-002") . ' ) as payment

    ');

        // Join the user details if the user has the ACL rights.
        if ($canDo->get('rental.listings.showowner')) {
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
            $query->select('a.value');
            $query->where('a.review = ' . (int) $review_state);
        }

        // Filter on expiry date
        $date_filter = $this->getState('filter.date_filter');
        $start_date = JFactory::getDate($this->getState('filter.start_date'))->calendar('Y-m-d');
        $end_date = JFactory::getDate($this->getState('filter.end_date'))->calendar('Y-m-d');

        // Filter by snooze state
        // Should only apply to users who can view and change snooze state
        if ($canDo->get('rental.listings.filter')) {
            $snooze = $this->getState('filter.snoozed');

            // If snooze state set to hide...
            if (($snooze == 1 && $date_filter) || ($snooze == false && $date_filter) || $snooze == 1) {
                // ...hide snoozed properties (i.e. only select expired snooze or where snooze hasn't been set
                $query->where('(a.snooze_until < ' . $db->quote(JFactory::getDate()->calendar("Y-m-d")) . ' OR a.snooze_until is null)');
            }
        }

        if ($this->getState('filter.start_date') && $this->getState('filter.end_date') && $date_filter == 'expiry_date') {
            // This filter includes any properties with snooze dates between the dates being filtered on.
            // This allows us to show properties that expired outside the dates being filtered on but
            // have been snoozed to appear between the dated being filtered. We also, exlude properties that 
            // are no longer expired. That is, they have been renewed since they were snoozed...
            $query->where('((a.' . $db->escape($date_filter) . ' >=' . $db->quote($start_date) . ' and a.'
                    . $db->escape($date_filter) . ' <=' . $db->quote($end_date) . ')' .
                    ' OR (' . $db->escape('a.snooze_until') . ' >=' . $db->quote($start_date) . ' and '
                    . $db->escape('a.snooze_until') . ' <=' . $db->quote($end_date) . ' and a.expiry_date <= ' . $db->quote($now) . '))');

            // Exclude unpublished properties.......
            $query->where('a.expiry_date IS NOT NULL');

            // Exclude properties where a non-renewal reason has been given
            $query->where('a.renewalreason = ""');

            // We need to pull in additional information for this report, yippee!!
            $query->select('(select count(*) from ' . $db->quoteName('#__enquiries', 'enq') . ' where property_id = a.id and enq.date_created >= SUBDATE(a.expiry_date, INTERVAL 1 YEAR)) as enquiries');
            $query->select('(select count(*) from ' . $db->quoteName('#__website_views', 'webviews') . ' where property_id = a.id and webviews.date_created >= SUBDATE(a.expiry_date, INTERVAL 1 YEAR)) as clicks');
        } elseif ($this->getState('filter.start_date') && $this->getState('filter.end_date') && $date_filter == 'created_on') {
            $query->where('(a.' . $db->escape($date_filter) . ' >=' .
                    $db->quote(JFactory::getDate($start_date)->calendar('Y-m-d')) .
                    ' and a.' . $db->escape($date_filter) . ' <=' .
                    $db->quote(JFactory::getDate($end_date)->calendar('Y-m-d')) . ')');
            $query->select('(select count(b2.id) + count(c2.id) from ' . $db->quoteName('qitz3_users', 'u') . 'left join ' . $db->quoteName('qitz3_property', 'b2') . ' on b2.created_by = u.id left join ' . $db->quoteName('qitz3_realestate_property', 'c2') . ' on c2.created_by = u.id where u.id = a.created_by ) as existing');
        } elseif ($date_filter == 'created_on') {
            $query->select('(select count(b2.id) + count(c2.id) from ' . $db->quoteName('qitz3_users', 'u') . 'left join ' . $db->quoteName('qitz3_property', 'b2') . ' on b2.created_by = u.id left join ' . $db->quoteName('qitz3_realestate_property', 'c2') . ' on c2.created_by = u.id where u.id = a.created_by ) as existing');
        }

        // Filter by search in title
        // TODO - Try and tidy up this logic a bit.
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            // If search cast to int and doesn't contain a comma is true
            if ((int) $search && (strpos($search, ',') === false)) {
                // This pulls out the property with ID searched on, it's parent and any siblings.
                $query->where('a.id = ' . (int) $search);
            }
            // If the exploded array contains more than one element and the search term contains a comma
            elseif (count(explode(',', $search) > 1) && (strpos($search, ',') > 0)) {
                // Escape the search term
                $search = $db->escape($search);
                $query->where('a.id in (' . $search . ')');
            } elseif (stripos($search, 'account:') === 0) {
                $search = $db->Quote('%' . $db->escape(substr($search, 8), true) . '%');
                $query->where('(u.name LIKE ' . $search . ' OR u.username LIKE ' . $search . ' OR u.email LIKE ' . $search . ')');
            } elseif (stripos($search, 'accid:') === 0) {
                $search = substr($search, 6);
                $query->where('(u.id = ' . $search . ')');
            } elseif (stripos($search, 'dept:') === 0) {
                $search = substr($search, 5);
                $query->where('(h.title = ' . $db->quote($db->escape($search, true)) . ')');
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('(e.unit_title LIKE ' . $search . ')');
            }
        }

        $query->from('#__property as a');
        $query->join('inner', '#__property_versions as b on (
      a.id = b.property_id
      and b.id = (select max(c.id) from #__property_versions c where c.property_id = a.id)
    )');

        // Join the units for the image
        $query->join('left', '#__unit d on d.property_id = a.id');
        $query->join('left', '#__unit_versions e on (d.id = e.unit_id and e.id = (select max(f.id) from #__unit_versions f where unit_id = d.id))');
        $query->where('(d.ordering = 1 or d.ordering is null)');



        // Join the images, innit!
        $query->join('left', '#__property_images_library f on e.id = f.version_id');
        $query->where('(f.ordering = (select min(ordering) from #__property_images_library g where g.version_id = e.id) or f.ordering is null)');

        // Join the classification table...
        $query->join('left', '#__classifications h on h.id = b.department');

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

    /**
     * Get the content
     *
     * @return  string    The content.
     *
     * @since   1.6
     */
    public function getContent() {
        if (!isset($this->content)) {
            $this->setState('list.limit', '');

            // Load the list items.
            $query = $this->_getListQuery();

            $query->clear('select');

            $query->select('a.id, p.user_id, p.firstname, u.email, a.expiry_date');

            try {
                $items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
            } catch (RuntimeException $e) {
                $this->setError($e->getMessage());

                return false;
            }



            $this->content = '';

            foreach ($items[0] as $key => $value) {
                $this->content .= $key . "\t";
            }

            $this->content .= "\r\n";

            foreach ($items as $item) {
                $bits = JArrayHelper::fromObject($item);

                $this->content .= implode("\t", $bits) . "\r\n";
            }
        }

        return $this->content;
    }

}
