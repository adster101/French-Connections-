<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modellist');

/**
 * Images Model
 */
class HelloWorldModelImages extends JModelList {

  /**
   * Method to get a JDatabaseQuery object for retrieving the data set from a database.
   *
   * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
   *
   * @since   12.2
   */
  protected function getListQuery() {

    // Get the listing details from the model state...
    $app = JFactory::getApplication();
    
    $id = $this->getState('version_id','');
        
    $db = $this->getDbo();
    $query = $db->getQuery(true);

    // Get a list of the images uploaded against this listing
    $query->select('
      id,
      property_id,
      image_file_name,
      caption,
      ordering
    ');
    $query->from('#__property_images_library');

    $query->where('version_id = ' . (int) $id);

    $query->order('ordering', 'asc');

    return $query;
  }

  /*
   * Method to generate a set of profile images for images being uploaded via the image manager
   *
   *
   */

  public function generateImageProfile($image = '', $property_id = '', $image_file_name = '', $profile = '', $max_width = 550, $max_height = 375) {

    if (empty($image)) {
      return false;
    }

    if (!file_exists($image)) {
      return false;
    }

    if (!$profile) {
      return false;
    }

    // Create a new image object ready for processing
    $imgObj = new JImage($image);

    // Create a folder for the profile, if it doesn't exist
    $dir = COM_IMAGE_BASE . '/' . $property_id . '/' . $profile;
    if (!file_exists($dir)) {
      jimport('joomla.filesystem.folder');
      JFolder::create($dir);
    }

    $file_path = COM_IMAGE_BASE . '/' . $property_id . '/' . $profile . '/' . $image_file_name;
    if (!file_exists($file_path)) {
      try {

        $width = $imgObj->getWidth();
        $height = $imgObj->getHeight();

        // If the width is greater than the height just create it
        if (($width > $height) && $width > $max_width) {

          // This image is roughly landscape orientated with a width greater than max width allowed
          $profile = $imgObj->resize($max_width, $max_height, true, 3);

          // Check the aspect ratio. I.e. we want to retain a 4:3 aspect ratio
          if ($profile->getHeight() > $max_height) {

            // Crop out the extra height
            $profile = $profile->crop($max_width, $max_height, 0, ($profile->getHeight() - $max_height) / 2, false);
          } else if ($profile->getWidth() > $max_width) {

            // Crop out the extra width
            $profile = $profile->crop($max_width, $max_height, ($profile->getWidth() - $max_width) / 2, 0, false);
          }

          // Put it out to a file
          $profile->tofile($file_path);

        } else if ($width < $height) {

          // This image is roughly portrait orientated with a width greater than the max width allowed
          $profile = $imgObj->resize($max_width, $max_height, false, 2);

          // Check the resultant width
          if ($profile->getWidth() < $max_width) {

            $blank_image = $this->createBlankImage($max_width, $max_height);

            // Write out the gallery file
            // Need to do this as imagecopy requires a handle
            $profile->tofile($file_path);

            // Load the existing image
            $existing_image = imagecreatefromjpeg($file_path);

            // Copy the existing image into the new one
            imagecopy($blank_image, $existing_image, ($max_width - $profile->getWidth()) / 2, ($max_height - $profile->getHeight()) / 2, 0, 0, $profile->getWidth(), $profile->getHeight());

            // Save it out
            imagejpeg($blank_image, $file_path, 100);
          } else {

            // Width is okay, just write it out
            $profile->tofile($file_path);
          }
        } else if ((($width > $height) && $width < $max_width) || (($width < $height) && $height < $max_height)) {

          // This image is landscape orientated with a width less than 500px
          // Create a blank image
          $blank_image = $this->createBlankImage($max_width, $max_height);

          // Write out the gallery file, unprocessed
          $imgObj->tofile($file_path);

          // Load the existing image
          $existing_image = imagecreatefromjpeg($file_path);

          // Copy the existing image into the new one
          imagecopy($blank_image, $existing_image, ($max_width - $imgObj->getWidth()) / 2, ($max_height - $imgObj->getHeight()) / 2, 0, 0, $imgObj->getWidth(), $imgObj->getHeight());

          // Save it out
          imagejpeg($blank_image, $file_path, 100);
        }
      } catch (Exception $e) {

      }
    }
  }

  /*
   * Method to generate a blank image for use in the above profile creation method.
   */

  public function createBlankImage($max_width = '', $max_height = '', $red = 255, $green = 255, $blue = 255) {

    // Create a new image with dimensions as provided
    $blank_image = imagecreatetruecolor($max_width, $max_height);

    // Set it's background to white
    $color = imageColorAllocate($blank_image, $red, $green, $blue);
    imagefill($blank_image, 0, 0, $color);

    return $blank_image;
  }
  

  
  
}
