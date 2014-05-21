<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * HelloWorldList Model
 */
class EnquiriesModelEnquiries extends JModelList
{

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
          'id', 'e.id',
          'state', 'e.state',
          'created', 'e.date_created',
          'property_id', 'e.property_id'
      );
    }

    parent::__construct($config);
  }

  /**
   * Method to auto-populate the model state.
   *
   * Note. Calling getState in this method will result in recursion.
   *
   * @return	void
   * @since	1.6
   */
  protected function populateState($ordering = null, $direction = null)
  {
    $app = JFactory::getApplication();

    // Adjust the context to support modal layouts.
    if ($layout = $app->input->get('layout'))
    {
      $this->context .= '.' . $layout;
    }

    // List state information.
    parent::populateState('e.id', 'desc');
  }

  public function preprocessForm(\JForm $form, $data, $group = 'content')
  {
    parent::preprocessForm($form, $data, $group);

    $user = JFactory::getUser();

    $delete = $user->authorise('core.delete', $this->option);

    if ($delete)
    {

      $type_field = new SimpleXMLElement('<field />');
      $type_field->addAttribute('type', 'list');
      $type_field->addAttribute('name', 'state');
      $type_field->addAttribute('class', 'input-xlarge');
      $type_field->addAttribute('labelclass', 'element-invisible');
      $type_field->addAttribute('onchange', 'this.form.submit()');

      $options = array('' => 'COM_ENQUIRIES_OPTION_ALL',
          '1' => 'COM_ENQUIRIES_OPTION_READ',
          '0' => 'COM_ENQUIRIES_OPTION_UNREAD',
          '-1' => 'COM_ENQUIRIES_OPTION_FAILED_BANNED_EMAIL',
          '-3' => 'COM_ENQUIRIES_OPTION_FAILED_BANNED_PHRASE',
          '-4' => 'COM_ENQUIRIES_OPTION_FAILED_BANNED_MULTIPLE',
          '-2' => 'JTRASHED'
      );

      foreach ($options as $key => $value)
      {
        $child = $type_field->addChild('option', $value);
        $child->addAttribute('value', $key);
      }
      $form->setField($type_field, 'filter', true);
    }
  }

  /**
   * Method to build an SQL query to load the list data.
   *
   * @return	string	An SQL query
   *
   */
  protected function getListQuery()
  {

    // Get the user to authorise
    $user = JFactory::getUser();

    // Create a new query object.
    $db = JFactory::getDBO();
    $query = $db->getQuery(true);

    // Select some fields
    $query->select('
      e.id,
      e.guest_forename as forename,
      e.guest_surname as surname,
      e.guest_email as email,
      e.message,
      date_format(e.start_date, "%d-%m-%Y") as start_date,
      date_format(e.end_date, "%d-%m-%Y") as end_date,
      e.date_created,
      e.state,
      e.property_id,
      e.adults,
      e.children,
      e.replied,
      e.date_replied,
      u.unit_title
    ');

    // From the hello table
    $query->from('#__enquiries e');
    // Join on p.id so we can get the enqs for property owned by current user
    $query->leftJoin('#__property p on p.id = e.property_id');

    // Joing on unit versions to get the unit title    
    $query->leftJoin('#__unit_versions u on u.unit_id = e.unit_id');
    $query->where('u.review = 0');

    // Filter by published state
    $state = $this->getState('filter.state');

    if (is_numeric($state))
    {
      $query->where('e.state = ' . (int) $state);
    }
    else
    {
      $query->where('e.state IN (0,1)');
    }

    // Need to ensure that owners only see reviews assigned to their properties
    if (!$user->authorise('core.edit', 'com_enquiries') && $user->authorise('core.edit.own', 'com_enquiries'))
    { // User not permitted to edit their enquiries globally
      $query->where('p.created_by = ' . (int) $user->id); // Assume that this is an owner, or a user who we only want to show reviews assigned to properties they own
    }

    // Filter by search in title
    $search = $this->getState('filter.search');

    if ((int) $search)
    {
      $query->where('e.property_id = ' . (int) $search);
    }
    elseif(!empty($search))
    {
      $search = $db->Quote('%' . $db->escape($search, true) . '%');
      $query->where('(e.message LIKE ' . $search . ')');
    }

    $date = $this->getState('filter.date_received');

    if (!empty($date))
    {
      $query->where('e.date_created >= ' . $db->quote($db->escape($date, true)));
    }

    $listOrdering = $this->getState('list.ordering', 'date_created');
    $listDirn = $db->escape($this->getState('list.direction', 'desc'));
    $query->order($db->escape($listOrdering) . ' ' . $listDirn);

    return $query;
  }

}

