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
class ImportControllerImages extends JControllerForm {

  public function import() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('POST') or die('Invalid Token');

    // The file we are importing from
    $userfile = JRequest::getVar('import_file', null, 'files', 'array');

    // Open a handle to the import file
    $handle = fopen($userfile['tmp_name'], "r");

    // Get a db instance
    $db = JFactory::getDBO();

    $previous_property_id = '';
    
    $db->truncateTable('#__images_property_gallery');
    $db->truncateTable('#__images_property_library');

    
    while (($line = fgetcsv($handle)) !== FALSE) {


      if ($previous_property_id == $line[1]) { // Must be a new unit of the same property
      // 
        // 1. This is a unit so store the previous images into the gallery images table 
        // against the previous property ID if unit count is 1 (if unit count is > 1 don't store)        
        if ($unit_count == 1) {
          
          $initial_library_image_names = array();
          
          
          

          $initial_gallery_images = '\'';

          // Now we need to get a list of filename so we can select those from the library...
          foreach ($previous_images as $images => $image) {
            $initial_library_image_names[] = $image['fde_filename'];
          }

          $initial_gallery_images .= implode('\',\'', $initial_library_image_names) . '\'';




          $query->clear();
          $query = $db->getQuery(true);

          $query->select('id');
          $query->from('#__images_property_library');
          $query->where('image_file_name in (' . $initial_gallery_images . ')');

          // Set and execute the query
          $db->setQuery($query);

          $initial_gallery_images = $db->loadAssocList($key = 'id');
        
        


          // Insert this lot of images into the library_images table. If a single unit property the images are stored in the library.
          // Start building a new query to insert any attributes... 
          $query = $db->getQuery(true);

          $query->insert('#__images_property_gallery');

          $query->columns(array('property_id', 'property_library_id'));

          // Loop over the list of images and insert them...
          $insert_string = '';

          // Previous images includes the unit images for this parent property          
          foreach ($initial_gallery_images as $images => $image) {
            $insert_string = "$property_id,'" . $image['id'] . "'";
            $query->values($insert_string);
          }

          // Set and execute the query
          $db->setQuery($query);

          if (!$db->execute()) {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $db->getErrorMsg()));
            print_r($db->getErrorMsg());
            print_r($insert_string);
            die;
          }
        }



        // 2. Add any *unit* images to the library.  
        // Check that there are additional unit images
        if (!empty($line[3])) {


          $extra_library_images = $line[3];

          $query->clear();

          $query->select('fde_id, fde_filename, fde_description');
          $query->from('#__file_details');
          $query->where('fde_id in (' . $extra_library_images . ')');

          // Set and execute the query
          $db->setQuery($query);

          $additional_library_images = $db->loadAssocList($key = 'fde_id');

          $query->clear();
          $query = $db->getQuery(true);

          $query->insert('#__images_property_library');
          $query->columns(array('property_id', 'image_file_name', 'caption'));

          // Loop over the list of images and insert them...
          // Need to select them all from the file_details table first...
          $insert_string = '';

          foreach ($additional_library_images as $images => $image) {
            $insert_string = "$line[1],'" . mysql_escape_string($image['fde_filename']) . "','" . mysql_escape_string($image['fde_description']) . "'";
            $query->values($insert_string);
          }

          // Set and execute the query
          $db->setQuery($query);

          if (!$db->execute()) {
            $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $db->getErrorMsg()));
            print_r($db->getErrorMsg());
            print_r($insert_string);
            die;
          }
        }
   
        // 4. Insert gallery images (actually unit+property images combined) into the gallery table
        // This is a list of image IDs associated with this unit.
        $gallery_images = '';
        $library_image_names='';
        if (!empty($line[3])) {
          $gallery_images = $line[2].','.$line[3];    
        } else {
          $gallery_images = $line[2];
        }

        $query->clear();

        $query->select('fde_id, fde_filename, fde_description');
        $query->from('#__file_details');
        $query->where('fde_id in (' . $gallery_images . ')');

        // Set and execute the query
        $db->setQuery($query);

        $gallery_images = $db->loadAssocList();
        
 
        $property_gallery_images = '\'';

        // Now we need to get a list of filename so we can select those from the library...
        foreach ($gallery_images as $images => $image) {
          $library_image_names[] = $image['fde_filename'];
        }


  
        $property_gallery_images .= implode('\',\'', $library_image_names) . '\'';

        $query->clear();
        $query = $db->getQuery(true);

        $query->select('id');
        $query->from('#__images_property_library');
        $query->where('image_file_name in (' . $property_gallery_images . ')');

        // Set and execute the query
        $db->setQuery($query);

        $gallery_images = $db->loadAssocList($key = 'id');

        $query->insert('#__images_property_gallery');

        $query->columns(array('property_id', 'property_library_id'));

        // Loop over the list of images and insert them...
        $insert_string = '';

        // Previous images includes the unit images for this parent property
        foreach ($gallery_images as $image => $id) {
          $insert_string = "$line[0]," . $id['id'] . "";
          $query->values($insert_string);
        }

        // Set and execute the query
        $db->setQuery($query);

        if (!$db->execute()) {
          $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $db->getErrorMsg()));
          print_r($db->getErrorMsg());
          print_r($insert_string);
          die;
        }

        // Library table should now hold a deduped list of ALL images associated with this property (so far).
        // This may be added next time around, if it's a new unit.
        // 
        // 5. As this is a unit, add the property and unit images to the gallery images table
        // Increment unit count
        $unit_count++;
        
      } else { // Must be a new property 
      
        // Select all images for this property, loop ever them and move them to the correct folder...
        if ($previous_property_id !='') {
         $query->clear();
         $query->select('id,image_file_name');
         $query->from('#__images_property_library');
         $query->where('property_id = ' . $previous_property_id);
         
         $db->setQuery($query);

         $images_to_move = $db->loadAssocList($key='id');
         foreach ($images_to_move as $images=>$image) {
         

           $move = copy('D:\\\Pics/_images/'.$image['image_file_name'], 'C://Xampp/htdocs/images/'.$image['image_file_name']);
           echo $move;die;
           
         }
         
        }
        
        // Reset previous images
        $previous_images = array();

        // Reset unit count
        $unit_count = 1;

        $property_id = $line[1]; // Set property ID
        // Add this lot of image to a library_images array. This is used next time around to append any additional images (if a multi unit)
        
        if (!empty($line[3])) {
          $images = $line[2] . ',' . $line[3];
        } else {
          $images = $line[2];
              
        }

        // Need to get the image filenames and captions 
        $query = $db->getQuery(true);

        $query->clear();

        $query->select('fde_id, fde_filename, fde_description');
        $query->from('#__file_details');
        $query->where('fde_id in (' . $images . ')');

        // Set and execute the query
        $db->setQuery($query);

        $library_images = $db->loadAssocList($key = 'fde_id');





        // Insert this lot of images into the library_images table. If a single unit property the images are stored in the library.
        // Start building a new query to insert any attributes... 
        $query = $db->getQuery(true);

        $query->insert('#__images_property_library');

        $query->columns(array('property_id', 'image_file_name', 'caption'));

        // Loop over the list of images and insert them...
        // Need to select them all from the file_details table first...



        $insert_string = '';

        foreach ($library_images as $images => $image) {
          $insert_string = "$property_id,'" . mysql_escape_string($image['fde_filename']) . "','" . mysql_escape_string($image['fde_description']) . "'";
          $query->values($insert_string);
        }

        // Set and execute the query
        $db->setQuery($query);

        if (!$db->execute()) {
          $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $db->getErrorMsg()));
          print_r($db->getErrorMsg());
          print_r($insert_string);
          die;
        }


        $previous_images = $library_images;
      }

      // Track the property ID              
      $previous_property_id = $line[1];
    }


    fclose($handle);

    $this->setMessage('Properties images imported, hooray!');

    $this->setRedirect('index.php?option=com_import&view=images');
  }

}
