<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class HelloWorldModelImages extends JModelAdmin
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
	public function getTable($type = 'HelloWorld', $prefix = 'HelloWorldTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
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
	public function getImagesTable($type = 'Images', $prefix = 'HelloWorldTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}

  
	/**
	 * Method to get the record form. 
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	2.5
	 */
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_helloworld.images', 'images',
		                        array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}

  /**
   *
   * Override the getItem method. In this case we need to pull the tariffs into $data object in order to inject 
   * the tariffs into the tariff view.
   * 
   * @param type $pk
   * @return boolean 
   */
  
	public function getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

    $table = $this->getTable();

		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
		}
    
		// Convert to the JObject before adding other data.
		$properties = $table->getProperties(1);
    
    // Get an instance of the images table
    $imagesTable = $this->getImagesTable();

    // Now we need to get the existing images detail for this property
    if ($pk > 0)
    {
  
      // We first need to determine what type of node we are looking at. E.g. single unit prop, a unit, or parent of a multi unit property
      
      // Get the subtree for this property
      $subtree = $table->getTree( $pk );	
      
      if (count($subtree) == 1 && $table->isLeaf( $pk ) && $table->parent_id == 1) {
        
        // This is a single unit property, no?
        $images = $imagesTable->load( $pk );
       
        // Check for a table object error.
        if ($images === false && $imagesTable->getError())
        {
          $this->setError($images->getError());
          return false;
        }    
      }
      
      if (count($subtree) > 1 && $table->parent_id == 1) {
        
        // This is a parent node as subtree is gt 1 and parent id is 1 (e.g. root)
        $images = $imagesTable->load( $pk );
        
        $images['gallery'] = $imagesTable->load( $pk, $pk );
        
      }
      
      if ($table->isLeaf( $pk ) && $table->parent_id != 1) {
        echo "Child unit node";
      }

    }
    $properties['images'] = $images;
 
		$item = JArrayHelper::toObject($properties, 'JObject');
		return $item;
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
		$data = JFactory::getApplication()->getUserState('com_helloworld.edit.images.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}
		return $data;
	}	

	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getScript() 
	{
		return '/administrator/components/com_helloworld/js/images.js';
	}
  
	/**
	 * Method to allow derived classes to preprocess the form.
	 *
	 * @param	object	A form object.
	 * @param	mixed	The data expected for the form.
	 * @param	string	The name of the plugin group to import (defaults to "content").
	 * @throws	Exception if there is an error in the form event.
	 * @since	1.6
	 */
	protected function preprocessForm(JForm $form, $data)
	{
    //print_r($data);die;
    // Generate the XML to inject into the form
    $XmlStr = $this->getImagesXml($form, $data->images);    
    $form->load($XmlStr);
	}
  
  protected function getImagesXml ($form, $data) 
  {
    // Build an XML string to inject additional fields into the form
    $XmlStr = '<form>';
    $counter=0;
    $XmlStr.='<fields name="images">';
    // Loop over the existing availability first
    foreach ($data as $image) {
      if( count($image) > 0 ) {
      $XmlStr.= '
        <fieldset name="image_'.$counter.'">
         <field
            id="url_' . $counter . '"
            name="image_url"
            type="hidden"
            multiple="true"
            default="'. $image->image_url . '">
          </field> 
          <field
            id="caption_' . $counter . '"
            name="caption"
            label="COM_HELLOWORLD_IMAGES_IMAGE_CAPTION_LABEL"
            description="COM_HELLOWORLD_IMAGES_IMAGE_CAPTION_DESC"
            type="text"
            multiple="true"
            maxlength="50"
            size="30"
            default="'. $image->caption .'">
          </field>        
         
          <field
            id="name'.$counter.'"
            name="image_file_name"
            type="hidden"
            multiple="true"
            default="'. $image->image_file_name .'">
          </field>           
          
       

        </fieldset>';
        $counter++;
        }
      }
      
    $XmlStr.="</fields></form>";
    return $XmlStr;
  }
  
}
