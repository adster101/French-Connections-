<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class RealEstateModelImage extends JModelAdmin
{

  /**
   * Method override to check if you can edit an existing record.
   *
   * @param	array	$data	An array of input data.
   * @param	string	$key	The name of the key for the primary key.
   *
   * @return	boolean
   * @since	1.6
   */
  protected function allowEdit($data = array(), $key = 'id')
  {
    // Check specific edit permission then general edit permission.
    return JFactory::getUser()->authorise('core.edit', 'com_rental.message.' . ((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
  }

  /**
   * Method to test whether a record can be deleted.
   *
   * @param   object  $record  A record object.
   *
   * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
   *
   * @since   12.2
   */
  protected function canDelete($record)
  {
    // This need expanding to check the user is not only authorise but actually owns the resource.
    $user = JFactory::getUser();
    return $user->authorise('rental.images.delete', $this->option);
  }

  public function delete($pks)
  {

    $db = JFactory::getDbo();
    $db->transactionStart();
    $image_profiles = array('', 'gallery', 'thumb', 'thumbs');
    $image_file_path = '';

    try
    {

      $table = $this->getTable();

      $image = $table->load($pks);

      if (!$image)
      {
        Throw new Exception('Problem loading image before deleting');
      }

      // Delete the image from the database
      if (parent::delete($pks))
      {
        // Need to delete the main image, the gallery image and thumbs
        $path = JPATH_SITE . '/images/property/' . $table->unit_id . '/';
        // Delete the actual image file from the file system
        // This is actually a bad idea as previous versions might need images at some point
        //foreach ($image_profiles as $profile) {
        //$image_file_path = $path . $profile . '/' . $table->image_file_name;
        //if (JFile::exists($image_file_path)) {
        //if (!JFile::delete($image_file_path)) {
        //Throw new Exception('Problem deleting image from file system');
        //}
        //}
        //}
      }
    }
    catch (Exception $e)
    {

      $this->setError($e->getMessage());
      $db->transactionRollback();
      return false;
    }

    $db->transactionCommit();

    return true;
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
    $form = $this->loadForm('com_rental.caption', 'caption', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form))
    {
      return false;
    }

    return $form;
  }

  /**
   * Method to test whether a record can be deleted.
   *
   * @param   object  $record  A record object.
   *
   * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
   *
   * @since   12.2
   */
  protected function canEditState($record)
  {

    $user = JFactory::getUser();
    return $user->authorise('rental.images.reorder', $this->option);
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
    $data = JFactory::getApplication()->getUserState('com_rental.edit.helloworld.data', array());
    if (empty($data))
    {
      $data = $this->getItem();
    }
    return $data;
  }

  /**
   * A protected method to get a set of ordering conditions.
   *
   * @param   object	A record object.
   *
   * @return  array  An array of conditions to add to add to ordering queries.
   * @since   1.6
   */
  protected function getReorderConditions($table)
  {
    $condition = array();
    $condition[] = 'unit_id = ' . (int) $table->unit_id;
    $condition[] = 'version_id = ' . (int) $table->version_id;
    return $condition;
  }

  /**
   * Save method to save an newly upload image file, taking into account a new version if necessary.
   * 
   * @param type $data
   */
  public function save($data)
  {

    $unit = JModelLegacy::getInstance('PropertyVersions', 'RealestateModel');

    // Image has been uploaded, let's create some image profiles...
    // TO DO - Put the image dimensions in as params against the component
    $this->generateImageProfile($data['filepath'], (int) $data['realestate_property_id'], $data['image_file_name'], 'gallery', 578, 435);
    $this->generateImageProfile($data['filepath'], (int) $data['realestate_property_id'], $data['image_file_name'], 'thumbs', 100, 100);
    $this->generateImageProfile($data['filepath'], (int) $data['realestate_property_id'], $data['image_file_name'], 'thumb', 210, 120);


    // Hit up the unit versions save method to determine if a new version is needed.
    if (!$unit->save($data))
    {
      return false;
    }

    $version_id = $unit->getState('new.version.id');

    $ordering = $this->getOrderPosition($version_id);

    if (!$ordering)
    {
      // Something went wrong
      // TO DO - This need wrapping in a try / catch and possibly transaction...
    }

    // Arrange the data for saving into the images table
    $data['id'] = '';
    $data['ordering'] = $ordering + 1;
    $data['version_id'] = $version_id;

    // Call the parent save method to save the actual image data to the images table
    if (!parent::save($data))
    {
      return false;
    }

    $this->setState($this->getName() . '.version_id', $unit->getState($unit->getName() . '.version_id'));
    $this->setState($this->getName() . '.review', $unit->getState($unit->getName() . '.review'));


    // Return to the controller

    return true;
  }

  /**
   * Given a version ID gets the max ordering for a given unit
   * 
   * @param type $version_id
   * @return boolean
   */
  public function getOrderPosition($version_id)
  {
    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    $query->select('max(ordering) as ordering');
    $query->where($db->quoteName('version_id') . ' = ' . (int) $version_id);
    $query->from($db->quoteName('#__realestate_property_images_library'));

    $db->setQuery($query);

    try
    {
      $result = $db->loadObject();
    }
    catch (Exception $e)
    {

      return false;
    }

    return $result->ordering;
  }

  /*
   * Method to generate a set of profile images for images being uploaded via the image manager
   *
   *
   */

  public function generateImageProfile($image = '', $property_id = '', $image_file_name = '', $profile = '', $max_width = 550, $max_height = 375)
  {

    if (empty($image))
    {
      return false;
    }

    if (!file_exists($image))
    {
      return false;
    }

    if (!$profile)
    {
      return false;
    }

    // Create a new image object ready for processing
    $imgObj = new JImage($image);

    // Create a folder for the profile, if it doesn't exist
    $dir = COM_IMAGE_BASE . '/' . $property_id . '/' . $profile;
    if (!file_exists($dir))
    {
      jimport('joomla.filesystem.folder');
      JFolder::create($dir);
    }

    $file_path = COM_IMAGE_BASE . '/' . $property_id . '/' . $profile . '/' . $image_file_name;
    if (!file_exists($file_path))
    {
      try
      {

        $width = $imgObj->getWidth();
        $height = $imgObj->getHeight();

        // If the width is greater than the height just create it
        if (($width > $height) && $width > $max_width)
        {

          // This image is roughly landscape orientated with a width greater than max width allowed
          $profile = $imgObj->resize($max_width, $max_height, true, 3);

          // Check the aspect ratio. I.e. we want to retain a 4:3 aspect ratio
          if ($profile->getHeight() > $max_height)
          {

            // Crop out the extra height
            $profile = $profile->crop($max_width, $max_height, 0, ($profile->getHeight() - $max_height) / 2, false);
          }
          else if ($profile->getWidth() > $max_width)
          {

            // Crop out the extra width
            $profile = $profile->crop($max_width, $max_height, ($profile->getWidth() - $max_width) / 2, 0, false);
          }

          // Put it out to a file
          $profile->tofile($file_path);

          // Load the existing image
          $existing_image = imagecreatefromjpeg($file_path);
          
          // Make it progressive
          $bit = imageinterlace($existing_image, 1);

          // Save it out
          imagejpeg($existing_image, $file_path, 100);

          // Free up memory
          imagedestroy($existing_image);
          
        }
        else if ($width < $height)
        {

          // This image is roughly portrait orientated with a width greater than the max width allowed
          $profile = $imgObj->resize($max_width, $max_height, false, 2);

          // Check the resultant width
          if ($profile->getWidth() < $max_width)
          {

            $blank_image = $this->createBlankImage($max_width, $max_height);

            // Write out the gallery file
            // Need to do this as imagecopy requires a handle
            $profile->tofile($file_path);

            // Load the existing image
            $existing_image = imagecreatefromjpeg($file_path);

            // Copy the existing image into the new one
            imagecopy($blank_image, $existing_image, ($max_width - $profile->getWidth()) / 2, ($max_height - $profile->getHeight()) / 2, 0, 0, $profile->getWidth(), $profile->getHeight());

            // Make it progressive
            imageinterlace($blank_image, 1);

            // Save it out
            imagejpeg($blank_image, $file_path, 100);

            // Free up memory
            imagedestroy($blank_image);
          }
          else
          {
            // Width is okay, just write it out
            $profile->tofile($file_path);
          }
        }
        else if ((($width > $height) && $width < $max_width) || (($width < $height) && $height < $max_height))
        {

          // This image is landscape orientated with a width less than 500px
          // Create a blank image
          $blank_image = $this->createBlankImage($max_width, $max_height);

          // Write out the gallery file, unprocessed
          $imgObj->tofile($file_path);

          // Load the existing image
          $existing_image = imagecreatefromjpeg($file_path);

          // Copy the existing image into the new one
          imagecopy($blank_image, $existing_image, ($max_width - $imgObj->getWidth()) / 2, ($max_height - $imgObj->getHeight()) / 2, 0, 0, $imgObj->getWidth(), $imgObj->getHeight());

          // Make it progressive
          imageinterlace($blank_image, 1);

          // Save it out
          imagejpeg($blank_image, $file_path, 100);

          // Free up memory
          imagedestroy($blank_image);
        }
      }
      catch (Exception $e)
      {
        
      }
    }
  }

  /*
   * Method to generate a blank image for use in the above profile creation method.
   */

  public function createBlankImage($max_width = '', $max_height = '', $red = 255, $green = 255, $blue = 255)
  {

    // Create a new image with dimensions as provided
    $blank_image = imagecreatetruecolor($max_width, $max_height);

    // Set it's background to white
    $color = imageColorAllocate($blank_image, $red, $green, $blue);
    imagefill($blank_image, 0, 0, $color);

    return $blank_image;
  }

}
