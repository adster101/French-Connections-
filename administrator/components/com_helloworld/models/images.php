<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * Images Model
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
	 * Returns a reference to the a Gallery Images Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getGalleryImagesTable($type = 'Gallery_images', $prefix = 'HelloWorldTable', $config = array()) 
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
    
    $images= array();
    
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
      // TODO: Abstract this to a utility function? Or use the level as an indicator
      
      // Get the subtree for this property
      $subtree = $table->getTree( $pk );	
      
      if (count($subtree) == 1 && $table->isLeaf( $pk ) && $table->parent_id == 1) {
        
        // This is a single unit property, no?
        $images['gallery'] = $imagesTable->load_images( $pk );
       
        // Check for a table object error.
        if ($images === false && $imagesTable->getError())
        {
          $this->setError($images->getError());
          return false;
        }    
      }
      
      if (count($subtree) > 1 && $table->parent_id == 1) {
        
        // This is a parent node as subtree is gt 1 and parent id is 1 (e.g. root)
        
        // Get an instance of the gallery_images table
        $gallery_images = $this->getGalleryImagesTable();
       
        // As such we need to get a library 
        $images['library'] = $imagesTable->load_images( $pk );
        
        // And gallery of images to show in the image manager
        $images['gallery'] = $gallery_images->load( $pk );
        
      }
      
      if ($table->isLeaf( $pk ) && $table->parent_id != 1) {
        // This is a child node as isLeaf returns true and parent_id not root (1)
        
        // Get an instance of the gallery_images table
        $gallery_images = $this->getGalleryImagesTable(); 
        
        // As such we need to get a library 
        $images['library'] = $imagesTable->load_images( $table->parent_id );
        
        // And gallery of images to show in the image manager
        $images['gallery'] = $gallery_images->load( $pk );       
      }
    }
    
    $properties['images'] = $images;    
		$item = JArrayHelper::toObject($properties, 'JObject');
    
    if (count($item->images->gallery->getProperties())) {
      // Tick the availability progress flag to true
      JApplication::setUserState('com_helloworld.availability.progress', true);
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
    if(!empty($data)){
      // Generate the XML to inject into the form
      $XmlStr = $this->getImagesXml($data);    
      $form->load($XmlStr);     
    }
	}
  
  protected function getImagesXml ($data) 
  {
  
    // Build an XML string to inject additional fields into the form
    $XmlStr = '<form>';
    $counter=0;
    // Only do the library part library array exists in data
    if (array_key_exists('library', $data->images)) {
      $XmlStr.='<fields name="library-images">';
      // Loop over the existing availability first
      foreach ($data->images->library as $key => $image) {

        if( count($image) > 0 && !array_key_exists($key, $data->images->gallery) ) {
              
        $XmlStr.= '
          <fieldset name="library_image_'.$counter.'">
            <field
              id="image_file_name_'.$counter.'"
              name="image_file_name"
              type="hidden"
              multiple="true"
              default="'. $image->image_file_name .'">
            </field>
            <field
              id="caption_' . $counter . '"
              name="caption"
              label="COM_HELLOWORLD_IMAGES_IMAGE_CAPTION_LABEL"
              description="COM_HELLOWORLD_IMAGES_IMAGE_CAPTION_DESC"
              type="text"
              readonly="true"
              multiple="true"
              required="true"
              class="required"
              labelclass="image-caption-label"
              maxlength="50"
              size="30"
              default="'. $image->caption .'">
            </field>        
           <field
              id="url_' . $counter . '"
              name="image_url"
              type="hidden"
              multiple="true"
              default="'. $image->image_url . '">
            </field>       
           <field
              id="image_file_id_'.$counter.'"
              name="image_file_id"
              type="hidden"
              multiple="true"
              default="'. $image->id .'">
            </field>                  
          </fieldset>';
          $counter++;
          }
        }
        $XmlStr.="</fields>";
      }
      
      
      // Reset the counter
      $counter=0;

      // Build the fields for the image gallery...
      $XmlStr.='<fields name="gallery-images">';
      // Loop over the existing availability first
      foreach ($data->images->gallery as $image) {
        if( count($image) > 0 ) {
          $XmlStr.= '
          <fieldset name="gallery_image_'.$counter.'">
            <field
              id="file_name_'.$counter.'"
              name="image_file_name"
              type="hidden"
              multiple="true"
              default="'. $image->image_file_name .'">
            </field>    
            <field
              id="caption_' . $counter . '"
              name="caption"
              label="COM_HELLOWORLD_IMAGES_IMAGE_CAPTION_LABEL"
              description="COM_HELLOWORLD_IMAGES_IMAGE_CAPTION_DESC"
              type="text"
              readonly="true"
              multiple="true"
              class="required"
              labelclass="image-caption-label"
              size="30"
              default="'. $image->caption .'">
            </field>     
            <field
              id="url_' . $counter . '"
              name="image_url"
              type="hidden"
              multiple="true"
              default="'. $image->image_url . '">
            </field> 
            <field
              id="name_'.$counter.'"
              name="image_file_id"
              type="hidden"
              multiple="true"
              default="'. $image->id .'">
            </field>    
        </fieldset>';
          $counter++;
        }
      }
        
    $XmlStr.="</fields></form>";
    return $XmlStr;
  }
  
  /*
   * Method to generate a set of profile images for images being uploaded via the image manager
   * 
   * 
   */
  public function generateImageProfiles( $images = array(), $property_id = null ) {
    
    if (empty($images)) {
      return false;
    }
    
    
    foreach ($images as $image) {
 			      
      $imgObj = new JImage($image['filepath']);
      
      $baseDir[] = COM_IMAGE_BASE.'/'.$property_id.'/gallery/';
      $baseDir[] = COM_IMAGE_BASE.'/'.$property_id.'/thumbs/';
      $baseDir[] = COM_IMAGE_BASE.'/'.$property_id.'/thumb/';
      
      // Create folders for each of the profiles for the property, if they don't exist
      foreach ($baseDir as $dir) {
        if (!file_exists($dir))
        {
          jimport('joomla.filesystem.folder');
          JFolder::create($dir);
        }        
      }
      
      // Firstly create the main gallery image
      // If the width is greater than the height just create an 
      if (($imgObj->getWidth() > $imgObj->getHeight()) && $imgObj->getWidth() > 500 ) {
        
        // This image is roughly landscape orientated with a width greater than 500px
        $gallery_profile = $imgObj->resize(500,375,true,3);
        $thumbnail_profile = $imgObj->resize(230,150,true,3);

        // Need to generate a small square thumbnail as well here for gallery...
        
        // Write out the gallery file
        $gallery_profile->tofile(COM_IMAGE_BASE.'/'.$property_id.'/gallery/'.$image['image_file_name'] );
        
        //Scope here to further crop the images. E.g. If height more than 375 crop out center portion, keeping the width
        $thumbnail_profile->tofile(COM_IMAGE_BASE.'/'.$property_id.'/thumb/'.$image['image_file_name'] );
        
      } else if (($imgObj->getWidth() < $imgObj->getHeight()) && $imgObj->getWidth() > 500) {
        
        // This image is roughly portrait orientated with a width greater than 500px
        $gallery_profile = $imgObj->resize(500,375,true,2);
        $thumbnail_profile = $imgObj->resize(230,150,true,2);

        // Write out the gallery file
        $gallery_profile->tofile(COM_IMAGE_BASE.'/'.$property_id.'/gallery/'.$image['image_file_name'] );
        $thumbnail_profile->tofile(COM_IMAGE_BASE.'/'.$property_id.'/thumb/'.$image['image_file_name'] );
        
      } else if (($imgObj->getWidth() > $imgObj->getHeight()) && $imgObj->getWidth() < 500) {
        
        // This image is roughly landscape orientated with a width less than 500px
        // In this case we know the width is less than 500 so we let this one through, as is, for now.
        
        // Write out the gallery file
        $imgObj->tofile(COM_IMAGE_BASE.'/'.$property_id.'/gallery/'.$image['image_file_name'] );

        //Scope here to further crop the images. E.g. If height more than 150 crop out center portion, keeping the width
        $thumbnail_profile = $imgObj->resize(230,150,true,3);
        $thumbnail_profile->tofile(COM_IMAGE_BASE.'/'.$property_id.'/thumb/'.$image['image_file_name'] );

        
      } else if (($imgObj->getWidth() < $imgObj->getHeight()) && $imgObj->getHeight() < 375) {
        
        // This image is roughly portrait orientated with a width less than 500px
        // In this case we know the height is less than 375 and the width is less than height 
        // so we let this one through, as is, for now.
        // Write out the gallery file
        $imgObj->tofile(COM_IMAGE_BASE.'/'.$property_id.'/gallery/'.$image['image_file_name'] );       
    
        $thumbnail_profile = $imgObj->resize(230,150,true,2);
        $thumbnail_profile->tofile(COM_IMAGE_BASE.'/'.$property_id.'/thumb/'.$image['image_file_name'] );
       
      }
      
      
      
      
      

    }
  }
  
}
