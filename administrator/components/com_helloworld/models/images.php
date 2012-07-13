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
	
  public function getItem($pk = null) 
  {
 		if ($item = parent::getItem($pk)) {    
  		// Convert the images field to an array.
			$item->images = json_decode( $item->images, $assoc = true ); 
    }
    
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
   *
   * Override the getItem method. In this case we need to pull the tariffs into $data object in order to inject 
   * the tariffs into the tariff view.
   * 
   * @param type $pk
   * @return boolean 
   */
  

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
      
        $XmlStr.= '
        <fieldset name="image_'.$counter.'">
         <field
            id="url_' . $counter . '"
            name="url"
            type="hidden"
            multiple="true"
            default="'. $image['url'] .'">
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
            default="'. $image['en-GB'] .'">
          </field>        
          <field
            id="filepath_'.$counter.'"
            name="filepath"
            type="hidden"
            multiple="true"
            default="'. $image['filepath'] .'">
          </field> 
          <field
            id="name'.$counter.'"
            name="name"
            type="hidden"
            multiple="true"
            default="'. $image['name'] .'">
          </field>           
          
       

        </fieldset>';
        $counter++;
      }
      
    $XmlStr.="</fields></form>";
    return $XmlStr;
  }
  
}
