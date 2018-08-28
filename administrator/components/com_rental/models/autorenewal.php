<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class RentalModelAutoRenewal extends JModelAdmin {

  /**
   * Returns a reference to the a Table object, always creating it.
   *
   * @param	type	The table type to instantiate
   * @param	string	A prefix for the table class name. Optional.
   * @param	array	Configuration array for model. Optional.
   * @return	JTable	A database object
   * @since	1.6
   */
  public function getTable($type = 'Property', $prefix = 'RentalTable', $config = array()) {
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/tables');

    return JTable::getInstance($type, $prefix, $config);
  }

  /**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   1.6
	 */
	public function delete(&$pks)
	{
		$pks = (array) $pks;
		$table = $this->getTable('AutoRenewal','RentalTable');

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				if ($this->canDelete($table))
				{

					if (!$table->delete($pk))
					{
						$this->setError($table->getError());

						return false;
					}
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					$error = $this->getError();

					if ($error)
					{
						\JLog::add($error, \JLog::WARNING, 'jerror');

						return false;
					}
					else
					{
						\JLog::add(\JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), \JLog::WARNING, 'jerror');

						return false;
					}
				}
			}
			else
			{
				$this->setError($table->getError());

				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}


  /**
   * Abstract method for getting the form from the model.
   *
   * @param   array    $data      Data for the form.
   * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
   *
   * @return  mixed  A JForm object on success, false on failure
   *
   * @since   12.2
   */
   public function getForm($data = array(), $loadData = true) {

   }




}
