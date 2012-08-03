<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla nested table library
jimport('joomla.database.table');


/**
 * Hello Table class
 */
class HelloWorldTableGallery_images extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__images_property_gallery', 'id', $db);
	}
	
	/**
	 * Overloaded load function - loads a set of images stored against a property.
	 *
	 * @param       int $id property id, not primary key in this case
	 * @param       boolean $reset reset data
	 * @return      boolean
	 * @see JTable:load
	 */
	public function load($id = null) 
	{

		$query = $this->_db->getQuery(true);
		$query->select('ipg.id, ipg.property_id, ipg.property_library_id, ipl.caption, ipl.image_url, ipl.id, ipl.image_file_name');
		$query->from('#__images_property_gallery as ipg');
    
    
    $query->where('ipg.property_id = ' . $this->_db->quote($id));
    $query->join('left', '#__images_property_library as ipl on ipg.property_library_id = ipl.id');
    // Select the images for this property. No need to worry about lib/gall
    // Need to add a join here to select the image details for the property...or does the array key exists work on the image ID?
    $this->_db->setQuery($query);
    try
    {
      $result = $this->_db->loadObjectList($key='image_file_name');
      return $result;
    }
    catch (RuntimeException $e)
    {
      $je = new JException($e->getMessage());
      $this->setError($je);
      return false;
    }
	}
  
  /**
   * Overloaded save function
   * This is called from two places. Firstly, from the images screen when a user upload an image. Once the image has been processed and verified this function is called. 
   * Line 157 of images sub contoller.
   * Second place this is called from is the helloworld model when a user applies any changes to existing images.  
   *  
   * @param boolean $map_array When user updating existing images we need to map the $_POST array into the correct format. When uploading images the array is passed in the right format. 
   * 
   * 
   */
  
  
  public function save_images($id = null, $images = array(), $map_array = false ) 
  {      
    

    if (!$this->check($images)) {
      JLog::add('JDatabaseMySQL::queryBatch() is deprecated.', JLog::WARNING, 'deprecated');
      return false;
      
    } else {
      
      if ($map_array) {
        $images = array_map( array($this, 'reformatFilesArray'), (array) $images['caption'], (array) $images['image_file_id'] );
      }
      
      $query = $this->_db->getQuery(true);

      $query->insert('#__images_property_gallery');
      
			$query->columns(array('property_id','property_library_id'));
      
      foreach ($images as $image) {
        // Only insert if there are some images 
        if ($image['image_file_id'] !='') {
          $image_to_insert = $image['image_file_id'];
          $insert_string = "$id" . ',' . "$image_to_insert";
          $query->values($insert_string);
        }
      }
      
			$this->_db->setQuery($query);
      
			if (!$this->_db->execute())
			{
				$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $this->_db->getErrorMsg()));
				$this->setError($e);
				return false;
			}
      return true;
    }
  }
  
	/**
	 * Used as a callback for array_map, turns the multi-file input array into a sensible array of files
	 * Also, removes illegal characters from the 'name' and sets a 'filepath' as the final destination of the file
	 *
	 * @param	string	- file name			($files['name'])
	 * @param	string	- file type			($files['type'])
	 * @param	string	- temporary name	($files['tmp_name'])
	 * @param	string	- error info		($files['error'])
	 * @param	string	- file size			($files['size'])b
	 *
	 * @return	array
	 * @access	protected
	 */
	protected function reformatFilesArray($caption, $name)
	{
		$name = JFile::makeSafe($name);
		return array(
      'caption' => $caption,
      'image_file_id'       => $name
		);
	}  
  
  
  /**
   * Overloaded check function. This should sanity check the data we are about to insert.
   * Perhaps do this before deleting?
   * 
   * @return boolean 
   */
  public function check($images= array()) {
    if (!count($images)) {
      return false;
      
    }
    return true;
  }
  
  /* 
   * Delete function, used to delete images from the images table prior to resinsertion
   */
  public function delete_images( $property_id = null ) {
    // Delete images
    // Delete the row by primary key.
    $query = $this->_db->getQuery(true);
    $query->delete();
    $query->from($this->_tbl);
    $query->where(' property_id = ' . $this->_db->quote($property_id));
    $this->_db->setQuery($query);
    
    // TO DO -Check for a database error.
    $this->_db->execute();
    return true;
  }
}
