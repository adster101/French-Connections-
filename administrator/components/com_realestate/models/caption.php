<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 * 
 *    
 */
class RealestateModelCaption extends JModelAdmin
{

  /**
   * Returns a reference to the a Table object, always creating it.
   *
   * @param	type	The table type to instantiate
   * @param	string	A prefix for the table class name. Optional.
   * @param	array	Configuration array for model. Optional.
   * @return	JTable	A database object
   * @since	1.6
   */
  public function getTable($type = 'Image', $prefix = 'RealestateTable', $config = array())
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
  public function getForm($data = array(), $loadData = false)
  {
    // Get the form.
    $form = $this->loadForm('com_realestate.caption', 'caption', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form))
    {
      return false;
    }

    return $form;
  }
  /**
   * Method to save a caption for an image
   * 
   * @param type $data
   */
  public function save($data)
  {
    // TO DO - We can probably do away with this 'caption' model if we can deal with checking whether 
    // we need to create a new version in the ImageModel as well as saving the image...
    // 
    // One way to do this is to add a new method here:
    // 
    // $this->getPropertyVersion($realestate_property_id) - Simply returns the version ID to save the image against creating a new version if required
    // 
    // As long as we have a version ID we can proceed and save the image detail with
    // 
    // parent::save($image_detail);
    // 
    parent::save($data);
  }
  
}
