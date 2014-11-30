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
class TicketsModelTicket extends JModelAdmin {

  public function getItem($pk = null) {
    if ($item = parent::getItem($pk)) {

      // Decode any notes that have been saved against this issue
      $registry = new JRegistry;
      $registry->loadString($item->notes);
      $item->notes = $registry->toArray();
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
  public function getTable($type = 'Ticket', $prefix = 'TicketsTable', $config = array()) {
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
  public function getForm($data = array(), $loadData = true) {

    // Get the form.
    $form = $this->loadForm('com_tickets.ticket', 'ticket', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form)) {
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
  protected function loadFormData() {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_tickets.edit.ticket.data', array());

    if (empty($data)) {
      $data = $this->getItem();
    }

    return $data;
  }

  public function save($data) {

    $data['notes'] = array();
    $registry = new JRegistry;
    $note = array();
    $user = JFactory::getUser();

    $states = array('closed', 'open', 'testing', 'pending');

    // Attempt to load the existing item
    $item = $this->getItem($data['id']);

    // Set the updated data and time
    $data['date_updated'] = JFactory::getDate()->calendar('Y-m-d H:i:s');
    
    if (isset($data['note']) && !empty($data['note']) || $data['state'] != $item->state) {
      // If we have an id and it's not empty
      if (isset($data['id']) && !empty($data['id'])) {

        // Decode any notes that have been saved against this issue
        $data['notes'] = $item->notes;
        
        if ($data['state'] != $item->state) {
          $note['user'] = $user->get('name');
          $note['description'] = 'Status changed from ' . $states[$item->state] . ' to ' . $states[$data['state']];
          $note['date'] = JFactory::getDate()->calendar('d-m-Y H:i:s');
          $data['notes'][] = $note;
        }

        if (!empty($data['note'])) {
          $note['user'] = $user->get('name');
          $note['description'] = $data['note'];
          $note['date'] = JFactory::getDate()->calendar('d-m-Y H:i:s');
          $data['notes'][] = $note;
        }
        
        $registry->loadArray($data['notes']);
        $data['notes'] = (string) $registry;
      }
    }

    return parent::save($data);
  }

}