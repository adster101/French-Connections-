<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');

class InvoicesModelAccount extends JModelForm {
 
	/**
	 * Method to get the menu item form.
	 *
	 * @param   array      $data        Data for the form.
	 * @param   boolean    $loadData    True if the form is to load its own data (default case), false if not.
	 * @return  JForm    A JForm object on success, false on failure
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_invoices.account', 'account', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		return $form;
	}

  
  public function getTable($name = 'Account', $prefix = 'InvoicesTable', $options = array()) {
    return JTable::getInstance($name, $prefix, $options);
  }
  
  
}