<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Methods supporting a list of tracks.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 * @since       1.6
 */
class FcadminModelNoproperty extends JModelList
{

    protected $ignore_users = array();

    public function __construct($config = array())
    {
        parent::__construct($config);

        // Get the params so we can find users to ignore
        // Put this into a method...probably
        $params = JComponentHelper::getParams('com_fcadmin');

        $ignore = $params->get('users_to_ignore', '');

        $users = explode(',', $ignore);

        foreach ($users as $username)
        {
            $id = JFactory::getUser($username)->id;

            if ($id)
            {
                $this->ignore_users[] = $id;
            }
        }
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  JDatabaseQuery
     *
     * @since   1.6
     */
    protected function getListQuery()
    {

        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query2 = $db->getQuery(true);

        // Select the required fields from the table.
        // Initialise the query.
        $query->select('
            a.firstname,
            a.surname,
            d.email,
            a.phone_1,
            a.user_id,
            d.registerDate
      ');
        $query->from('#__user_profile_fc as a');
        $query->join('left', '#__property b on a.user_id = b.created_by');
        $query->join('left', '#__user_usergroup_map c on c.user_id = a.user_id');
        $query->join('left', '#__users d on d.id = a.user_id');
        $query->where('b.id is null');
        $query->where('c.group_id = 10');

        $query->where('d.registerDate >= date_sub(now(), INTERVAL 6 MONTH)');
        $query->order('d.registerDate', 'desc');

        // Select the required fields from the table.
        // Initialise the query.
        $query2->select('
            a.user_id            
      ');
        $query2->from('#__user_profile_fc as a');
        $query2->join('left', '#__realestate_property b on a.user_id = b.created_by');
        $query2->join('left', '#__user_usergroup_map c on c.user_id = a.user_id');
        $query2->join('left', '#__users d on d.id = a.user_id');
        $query2->where('b.id is null');
        $query2->where('c.group_id = 10');

        $query2->where('d.registerDate >= date_sub(now(), INTERVAL 6 MONTH)');

        $query->where('a.user_id in ( ' . $query2->__toString() . ')');


        return $query;
    }

    /**
     * Get the content
     *
     * @return  string    The content.
     *
     * @since   1.6
     */
    public function getContent()
    {
        if (!isset($this->content))
        {

            $items = $this->getItems();

            $this->content = '';

            foreach ($items[0] as $key => $value)
            {
                $this->content .= $key . "\t";
            }

            $this->content .= "\r\n";

            foreach ($items as $item)
            {
                $bits = JArrayHelper::fromObject($item);

                $this->content .= implode("\t", $bits) . "\r\n";
            }
        }

        return $this->content;
    }

    public function populateState($ordering = null, $direction = null)
    {

        parent::populateState($ordering, $direction);

        $this->setState('list.limit', 0);
    }

}
