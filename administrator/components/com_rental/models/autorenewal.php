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

  /**
   * Method to save the form data.
   *
   * @param   array  $data  The form data.
   *
   * @return  boolean  True on success, False on error.
   *
   * @since   12.2
   */
  public function save($data)
  {
    $dispatcher = JEventDispatcher::getInstance();
    $table = $this->getTable('Property', 'RentalTable');
    $key = $table->getKeyName();
    $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
    $isNew = true;

    // Include the content plugins for the on save events.
    JPluginHelper::importPlugin('content');

    // Allow an exception to be thrown.
    try
    {
      // Load the row if saving an existing record.
      if ($pk > 0)
      {
        $table->load($pk);
        $isNew = false;
      }

      // Bind the data.
      if (!$table->bind($data))
      {
        $this->setError($table->getError());
        return false;
      }

      // Prepare the row for saving
      $this->prepareTable($table);

      // Check the data.
      if (!$table->check())
      {
        $this->setError($table->getError());
        return false;
      }

      // Store the data.
      if (!$table->store())
      {
        $this->setError($table->getError());
        return false;
      }

      // Clean the cache.
      $this->cleanCache();

      // Trigger the onContentAfterSave event.
      $dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, $table, $isNew));
    }
    catch (Exception $e)
    {
      $this->setError($e->getMessage());
      return false;
    }

    $pkName = $table->getKeyName();

    if (isset($table->$pkName))
    {
      $this->setState($this->getName() . '.id', $table->$pkName);
    }
    $this->setState($this->getName() . '.new', $isNew);

    return true;
  }
}
