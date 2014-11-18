<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport('joomla.user.user');
jimport('joomla.user.helper');

/**
 * HelloWorld Controller
 */
class ImportControllerRealestateimages extends JControllerForm
{

  public function import()
  {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('POST') or die('Invalid Token');

    // Open a handle to the import file
    //$handle = fopen('/home/adam/Documents/qitz3_property_images_library.csv', "r");

    $handle = fopen('D:\\\users\dev1\Documents\Migration\qitz3_realestate_property_images_library.csv', "r");

    // Get a db instance
    $db = JFactory::getDBO();
    $db->truncateTable('#__realestate_property_images_library');

    $previous_property_id = '';

    // $db->truncateTable('#__property_images_library');
    // Create a log file for the email kickers
    jimport('joomla.error.log');

    jimport('joomla.filesystem.folder');

    JLog::addLogger(array('text_file' => 'images.import.php'), JLog::ALL, array('import_images'));

    $model = JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_realestate/models');

    $model = JModelLegacy::getInstance('Image', 'RealestateModel');

    define('COM_IMAGE_BASE', JPATH_ROOT . '/images/property/');

    while (($line = fgetcsv($handle, 0, $delimiter = "|")) !== FALSE)
    {

      // Initially we need to get the unit version id from the #__unit_versions table
      $query = $db->getQuery(true);

      $query->select('id');
      $query->from('#__realestate_property_versions');
      $query->where('realestate_property_id = ' . (int) $line[0]);

      // Set the query.
      $db->setQuery($query);

      // Do it, baby!
      $version_id = $db->loadRow();

      $query->clear();

      // If we don't have any images proceed
      if (empty($line[1]))
      {
        continue;
      }

      // Firstly, get all the images associated with this unit
      $images = explode(',', $line[1]);

      $images = implode(',', array_filter($images));

      // Get a query object
      $query = $db->getQuery(true);

      $query->select('fde_id, fde_filename, fde_description, 0 as ordering');
      $query->from('#__file_details');
      $query->where('fde_id in (' . $images . ')');

      // Set and execute the query
      $db->setQuery($query);

      $existing_images = $db->loadAssocList($key = 'fde_id');

      // Images array has the images in the order we want
      // Existing images has the file details we want
      // Need to reorder existing images based on images.
      // Implode the images back into an array

      $order = explode(',', $images);

      $images_to_insert = array();

      foreach ($order as $key => $value)
      {

        $existing_images[$value]['ordering'] = $key + 1;
        $images_to_insert[$value] = $existing_images[$value];
      }

      $query->clear();

      $query = $db->getQuery(true);

      $query->insert('#__realestate_property_images_library');
      $query->columns(array('version_id', 'realestate_property_id', 'image_file_name', 'caption', 'ordering'));

      // Loop over the list of images and insert them...
      // Need to select them all from the file_details table first...
      $insert_string = '';

      foreach ($images_to_insert as $images => $image)
      {
        $insert_string = "$version_id[0],$line[0],'" . mysql_escape_string($image['fde_filename']) . "','" . mysql_escape_string($image['fde_description']) . "'," . (int) $image['ordering'];
        $query->values($insert_string);
      }

      // Set and execute the query
      $db->setQuery($query);

      // Only do this is we find a unit version for this unit (e.g. import units first)
      if (!empty($version_id[0]) && !empty($existing_images))
      {

        if (!$db->execute())
        {
          $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $db->getErrorMsg()));
          print_r($db->getErrorMsg());
          print_r($insert_string);
          die;
        }
      }



      $baseDir[] = COM_IMAGE_BASE . $line[0] . '/gallery/';
      $baseDir[] = COM_IMAGE_BASE . $line[0] . '/thumbs/';
      $baseDir[] = COM_IMAGE_BASE . $line[0] . '/thumb/';

      // Create folders for each of the profiles for the property, if they don't exist
      foreach ($baseDir as $dir)
      {
        if (!file_exists($dir))
        {
          JFolder::create($dir);
        }
      }


      /* Move each image and create the profile images
      foreach ($images_to_insert as $key => $blah)
      {

        $filepath = COM_IMAGE_BASE . $line[0];

        $image_path = $filepath . '/' . $blah['fde_filename'];

        // Move the image into the relevant folder, if we don't have it already...
        if (!file_exists($image_path))
        {

          //$move = copy('/home/sysadmin/Pictures/' . $blah['fde_filename'], $filepath . '/' . $blah['fde_filename']);
          $move = copy('D:\\\Pics/_images/' . $blah['fde_filename'], $image_path);

          if (!$move)
          {
            JLog::add('Unable to move/locate image - ' . $blah['fde_filename'] . '(' . $line[0] . ')', JLog::ERROR, 'import_images');
          }
        }

        // Image has been uploaded, let's create some image profiles...
        try
        {
          $model->generateImageProfile($image_path, (int) $line[0], $blah['fde_filename'], 'gallery', 578, 435);
          $model->generateImageProfile($image_path, (int) $line[0], $blah['fde_filename'], 'thumbs', 100, 100);
          $model->generateImageProfile($image_path, (int) $line[0], $blah['fde_filename'], 'thumb', 210, 120);

          // Delete the original image here - space becomes an issue otherwise.
          //unlink('/home/sysadmin/Pictures/' . $blah['fde_filename']);
          unlink('D:\\\Pics/_images/' . $blah['fde_filename']);
        }
        catch (Exception $e)
        {
          JLog::add($e->getMessage() . ' - ' . $blah['fde_filename'] . '(' . $line[0] . ')', JLog::ERROR, 'import_images');
   
        }

        unset($move);
        unset($image_path);
        unset($images_to_insert);
      }*/
    }



    fclose($handle);

    $this->setMessage('Properties images imported, hooray!');

    $this->setRedirect('index.php?option=com_import&view=realestateimages');
  }

}

