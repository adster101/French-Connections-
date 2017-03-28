<?php

/**
 * @version     1.0.0
 * @package     com_invoices
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');

/**
 * Methods supporting a list of Invoices records.
 */
class TicketsModelTicket extends JModelAdmin
{

  public function getItem($pk = null)
  {
    if ($item = parent::getItem($pk))
    {

      // Decode any notes that have been saved against this issue
      $registry = new JRegistry;
      $registry->loadString($item->notes);
      $item->notes = $registry->toArray();

      // Get the tags
      if (!empty($item->id))
      {
        $item->tags = new JHelperTags;
        $item->tags->getTagIds($item->id, 'com_tickets.ticket');
        $item->metadata['tags'] = $item->tags;
      }
    }

    return $item;
  }

  /**
   * Returns a reference to the a Table object, always creating it.
   *
   * @param	type	The table type to instantiate
   * @param	string	A prefix for the table class name. Optional.
   * @param	array	Configuration array for model. Optional.
   * @return	JTable	A database object
   * @since	1.6
   */
  public function getTable($type = 'Ticket', $prefix = 'TicketsTable', $config = array())
  {
    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * Method to get the record form.
   *
   * @param	array	$data		Data for the form.
   * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
   * @return	mixed	A JForm object on success, false on failure
   * @since	1.6
   */
  public function getForm($data = array(), $loadData = true)
  {

    // Get the form.
    $form = $this->loadForm('com_tickets.ticket', 'ticket', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form))
    {
      return false;
    }
    return $form;
  }

  /**
   * Method to get the data that should be injected in the form.
   *
   * @return	mixed	The data for the form.
   * @since	1.6
   */
  protected function loadFormData()
  {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_tickets.edit.ticket.data', array());

    if (empty($data))
    {
      $data = $this->getItem();
    }

    return $data;
  }

  public function save($data)
  {
    $data['notes'] = array();
    $registry = new JRegistry;
    $note = array();
    $user = JFactory::getUser();
    $isNew = true;
    $params = JComponentHelper::getParams('com_tickets');
    $states = array('closed', 'open', 'testing', 'pending', 'fixed');
    $severities = array('','Critical', 'High', 'Medium', 'Low', 'Minor');

    // Attempt to load the existing item
    // TO DO - Save notes into separate table then we can do away with this lookup
    // as any note will need saving

    $item = $this->getItem($data['id']);

    if ($item->id)
    {
      $isNew = false;

      // Decode any notes that have been saved against this issue
      $data['notes'] = $item->notes;
    }

    // Set the updated data and time
    $data['date_updated'] = JFactory::getDate()->calendar('Y-m-d H:i:s');

    // TO DO - Wrap the below into a language string or helper function
    // If the incoming state is different to the current state
    if (($data['state'] != $item->state) && !$isNew)
    {
      $note['user'] = $user->get('name');
      $note['description'] = 'Status changed from ' . $states[$item->state] . ' to ' . $states[$data['state']];
      $note['date'] = JFactory::getDate()->calendar('d-m-Y H:i:s');
      $data['notes'][] = $note;
    }

    // Check whether the severity has been changed
    if (($data['severity'] != $item->severity) && !$isNew)
    {
      $note['user'] = $user->get('name');
      $note['description'] = 'Severity changed from ' . $severities[$item->severity] . ' to ' . $severities[$data['severity']];
      $note['date'] = JFactory::getDate()->calendar('d-m-Y H:i:s');
      $data['notes'][] = $note;
    }

    // Add the new note to the notes array
    if (!empty($data['note']))
    {
      $note['user'] = $user->get('name');
      $note['description'] = $data['note'];
      $note['date'] = JFactory::getDate()->calendar('d-m-Y H:i:s');
      $data['notes'][] = $note;
    }

    //
    if (!empty($data['notes']))
    {
      $registry->loadArray($data['notes']);
      $data['notes'] = (string) $registry;
    }

    $ret = parent::save($data);

    if ($ret)
    {
      if ($isNew)
      {
        // Get the email address of who to notify about this new ticket
        $notify = $params->get('notify', '', 'string');
        $from = JFactory::getApplication()->getCfg('mailfrom');
        $from_name = JFactory::getApplication()->getCfg('sitename');
        $subject = JText::sprintf('COM_TICKETS_NEW_TICKET_SUBJECT', $data['title']);
        $body = JText::sprintf('COM_TICKETS_NEW_TICKET_BODY', $user->name, $data['description'], $severities[$data['severity']]);
        // Email should include all ticket detail including notes...
        JFactory::getMailer()->sendMail($from, $from_name, $notify, $subject, $body, true);
      }
      return $ret;
    }

    return $ret;
  }
  
  public function preprocessForm(\JForm $form, $data, $group = 'content')
  {

      $user = JFactory::getUser();
      $params = JComponentHelper::getParams('com_tickets');
      
      if (!$user->authorise('core.manage')) 
      {
          $form->setFieldAttribute('tags', 'readonly', 'true');
      }
      
   }

}