<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * Images Model
 */
class HelloWorldModelImages extends JModelList
{
  
	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
	 *
	 * @since   12.2
	 */
	protected function getListQuery()
	{
    
    // Get the listing details from the session...
    $app = JFactory::getApplication();
    $id = $app->input->get('listing_id','','int');
        
		$db = $this->getDbo();
		$query = $db->getQuery(true);

    // Get a list of the images uploaded against this listing
    $query->select('
      id,
      property_id,
      image_file_name,
      caption
    ');
    $query->from('#__property_images_library');
    
    $query->where('property_id = ' . (int) $id);
    return $query;
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
      try {
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
      
      } catch (Exception $e) {
        print_r($e);
      }
      
      
      
      
      

    }
  }
  
}
