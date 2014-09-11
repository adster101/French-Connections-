<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla nested table library
jimport('joomla.database.table');

// import the model helper lib
//jimport('joomla.application.component.model');

/**
 * Hello Table class
 */
class RealEstateTableProperty extends JTable
{

  /**
   * Constructor
   *
   * @param object Database connector object
   */
  function __construct(&$db)
  {
    parent::__construct('#__realestate_property', 'id', $db);
  }

  public function store($updateNulls = true)
  {
    $date = JFactory::getDate();
    $user = JFactory::getUser();

    if ($this->id)
    {
      // Existing item
      $this->modified_by = $user->get('id');
      $this->modified = $date->toSql();
    }
    else
    {
      // New newsfeed. A feed created and created_by field can be set by the user,
      // so we don't touch either of these if they are set.

      if (empty($this->created_on))
      {
        $this->created_on = $date->toSql();
      }

      // New property owner so add in the who created it and when
      if (empty($this->created_by))
      {
        $this->created_by = $user->id;
      }
    }

    return parent::store($updateNulls = true);
  }

}
