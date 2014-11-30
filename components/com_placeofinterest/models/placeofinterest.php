<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.error.log');

/**
 * HelloWorld Model
 */
class PlaceofinterestModelPlaceofinterest extends JModelItem {

  /**
   * @var object item
   */
  protected $item;

  /**
   * Method to get the tetimonial item.
   *
   * The base form is loaded from XML and then an event is fired
   *
   *
   * @param	array	$data		An optional array of data for the form to interrogate.
   * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
   * @return	JForm	A JForm object on success, false on failure
   * @since	1.6
   */
  public function getItem($data = array(), $loadData = true) {

    // Get the input and the unit ID
    $app = JFactory::getApplication();
    $input = $app->input;
    
    // Get the 'place' from the input data
    $place = $input->get('place', '', 'string');

    // Include the places of interest table class
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_interestingplaces/tables');
    
    // Get an instance of the table
    $table = $this->getTable();

    // Set the primary key to be the alias 
    $table->set('_tbl_keys', array('alias'));

    // Load the data based on the alias
    $table->load($place);

    // Get the properties 
    $properties = $table->getProperties(1);
    
    // To object and set item
    $item = JArrayHelper::toObject($properties, 'JObject');

    // Return item
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
  public function getTable($type = 'InterestingPlace', $prefix = 'InterestingPlacesTable', $config = array()) {
    return JTable::getInstance($type, $prefix, $config);
  }

}
