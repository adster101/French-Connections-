<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
// jimport('joomla.application.component.modellegacy');

/**
 * HelloWorld Model
 */
class StatsModelStats extends JModelList
{
  /*
   * The property id for the stats we are retrieving
   */

  protected $id = '';

  /**
   * Constructor.
   *
   * @param	array	An optional associative array of configuration settings.
   * @see		JController
   * @since	1.6
   */
  public function __construct($config = array())
  {
    if (empty($config['filter_fields']))
    {
      $config['filter_fields'] = array(
          'start_date', 'end_date', 'id', 'search'
      );
    }

    parent::__construct($config);
  }

  public function getGraphData()
  {

    $input = JFactory::getApplication()->input;
    // Get the id from the model state
    if ($this->getState('filter.search'))
    {
      $id = (int) $this->getState('filter.search');
    }
    elseif ($this->getState('filter.id'))
    {
      $id = (int) $this->getState('filter.id');
    }
    else
    {
      $id = $input->get('id', '', 'int');
    }

    $property_type = PropertyHelper::getPropertyType($id);

    $start_date = JFactory::getDate($this->getState('filter.start_date', ''))->calendar('Y-m-d');
    $end_date = JFactory::getDate($this->getState('filter.end_date', ''))->calendar('Y-m-d');

    // Set up an array to hold the series data
    $graph_data = array();

    // Get the various data to populate the report
    $graph_data['views'] = $this->getData($id, '#__property_views', $start_date, $end_date, $property_type);
    $graph_data['enquiries'] = $this->getData($id, '#__enquiries', $start_date, $end_date, $property_type);
    $graph_data['clicks'] = $this->getData($id, '#__website_views', $start_date, $end_date, $property_type);
    $graph_data['reviews'] = $this->getData($id, '#__reviews', $start_date, $end_date, $property_type);


    return $graph_data;
  }

  /**
   * preprocessForm - Checks which user the group is in and amends the form 
   * 
   * @param type $form
   * @param type $data
   * @param type $group
   */
  public function preprocessForm($form, $data, $group = 'content')
  {

    $user = JFactory::getUser();

    $groups = JAccess::getGroupsByUser($user->id, false);

    // If in the owner user group then remove the search box 
    // and present the user a list of their properties.
    if (in_array(10, $groups))
    {
      $form->removeField('search', 'filter');
    }
    else
    {
      $form->removeField('id', 'filter');
    }

    // Add or remove the search box depending on the user privileges.
    // If owner and not admin remove the box, retain the property dropdown
    // If admin and not owner remove the dropdown and show the search box

    parent::preprocessForm($form, $data, $group);
  }

  public function populateState()
  {

    parent::populateState();

    // Get the request data
    $input = JFactory::getApplication()->input;

    // If we have an id in the url then use that to set the form filter value. This is just for completeness.
    if ($input->get('id', '', 'int'))
    {
      $id = $input->get('id', '', 'int');
      $this->setState('filter.id', $id);
    }
  }

  public function getData($id = '', $table = '', $start_date = '', $end_date, $property_type = 'rental')
  {

    $user = JFActory::getUser();

    $property_table = ($property_type == 'rental') ? '#__property' : '#__realestate_property';  
    
    if (empty($id))
    {
      return false;
    }

    // Add in the number of page view this property has had in the last twelve months...
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    $query->select('
       count(a.id) as count
     ');
    $query->from($db->quoteName($table, 'a'));

    $query->where('property_id = ' . (int) $id);

    if ($start_date)
    {
      $query->where('date_created >= ' . $db->quote($start_date));
    }

    if ($end_date)
    {
      $query->where('date_created <= ' . $db->quote($end_date));
    }

    // If user not authorised to view all stats just limit them to properties they own
    if (!$user->authorise('stats.view.all', 'com_stats'))
    {
      $query->leftJoin($db->quoteName($property_table, 'b') . ' ON b.id = a.property_id');
      $query->where('b.created_by = ' . $user->id);
    }

    $db->setQuery($query);

    try {
      $rows = $db->loadRow();
    }
    catch (Exception $e) {
      return false;
    }

    return $rows;
  }

  public function loadFormData()
  {
    // Get any data stored in the user state context (if any)
    $data = JFactory::getApplication()->getUserState($this->context, new stdClass);

    // Get the request data
    $input = JFactory::getApplication()->input;

    // If we have an id in the url then use that to set the form filter value. This is just for completeness.
    if ($input->get('id', '', 'int'))
    {
      $id = $input->get('id', '', 'int');
      $data->filter = array('id' => $id);
    }

    return $data;
  }

}
