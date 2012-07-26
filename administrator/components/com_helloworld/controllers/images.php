<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

// Import file lib for checking file types of files being uploaded
jimport('joomla.filesystem.file');

// require helper file
require_once('/administrator/components/com_media/helpers/media.php');

class HelloWorldUpload extends MediaHelper {
  
}


/**
 * HelloWorld Controller
 */
class HelloWorldControllerImages extends JControllerForm
{
  /**
   * 
	 * The folder we are uploading into
	 */
	protected $folder = '';
  
	protected function allowEdit($data = array()) { 
		// This is a point where we need to check that the user can edit this data. 
		// E.g. check that this user actually 'owns' this property and can hence edit availability
		return true;  //always allow to edit record 
	}

  function upload () {
    
    // An array to hold the that are good to save against the property
    $images = array();
    
    // Check that this is a valid call from a logged in user.
    JSession::checkToken( 'get' ) or die( 'Invalid Token' );
    
    // Check that this user is authorised to edit (i.e. owns) this this property
    $this->allowEdit();
    
    // Get the component parameters
 		$params = JComponentHelper::getParams('com_media');

 		// Get some data from the request
		$files			= JRequest::getVar('jform_upload_images', '', 'files', 'array');

    // Get the property ID from the GET variable
    $id = JRequest::getVar( 'id', '', 'GET', 'int' );   

    // Get the parent property ID from the GET variable
    $parent_id = JRequest::getVar( 'parent_id', '', 'GET', 'int' );  
    
    // Create the folder path into which we are uploading the images to - This is why they are not copying on test...
    $this->folder = 'C:XAMPP/htdocs/images/' . JRequest::getVar('id', 'GET', '', 'integer');
    //$this->folder = 'D:Inetpub/wwwroot/rebuild/images/' . JRequest::getVar('id', 'GET', '', 'integer');
    
    // Check the total size of files being uploaded. If it's too large we just exit?
    if (
			$_SERVER['CONTENT_LENGTH']>($params->get('upload_maxsize', 0) * 1024 * 1024) ||
			$_SERVER['CONTENT_LENGTH']>(int)(ini_get('upload_max_filesize'))* 1024 * 1024 ||
			$_SERVER['CONTENT_LENGTH']>(int)(ini_get('post_max_size'))* 1024 * 1024 ||
			$_SERVER['CONTENT_LENGTH']>(int)(ini_get('memory_limit'))* 1024 * 1024
		)
		{
      // Not acceptable. Too large a total file size.
			// return an error message and ...
      jexit();
    }
    
    // Input is in the form of an associative array containing numerically indexed arrays - passed in from PHP/Apache in this format?
		// We want a numerically indexed array containing associative arrays
		// Cast each item as array in case the Filedata parameter was not sent as such
		$files = array_map( array($this, 'reformatFilesArray'),
			(array) $files['name'], (array) $files['type'], (array) $files['tmp_name'], (array) $files['error'], (array) $files['size']
		);
    
		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');
		JPluginHelper::importPlugin('content');
		$dispatcher	= JDispatcher::getInstance(); 

    // Loop over each file in the files array and do some checking, if the file checks out we proceed to transfer it to the relevant folder
    foreach ($files as &$file)
		{
      
      // Firstly check if the image already exists...if it does we don't want to upload it agin
			if (JFile::exists($file['filepath']))
			{
				// A file with this name already exists
        $file['error'][] = JText::_('COM_HELLOWORLD_IMAGES_IMAGE_ALREADY_EXISTS');
      }

      // Check that it has a valid name
			if (!isset($file['name']))
			{
				// This file doesn't have a filename after running through make path safe
				$file['error'][] = JText::_('COM_HELLOWORLD_IMAGES_IMAGE_NAME_NOT_VALID');
      }      
      
			// The file is valid at this point
			$err = null;

      // canUpload does a further raft of checks to ensure that the image is 'safe' (i.e. checks mime type and that it is an image file etc
			if (!MediaHelper::canUpload($file, $err))
			{
				// The file can't be uploaded
        $file['error'][] = JText::_($err);
			
			}
      
      // If there are no errors recorded for this file, we move it to the relevant folder for this property
      if (count($file['error']) == 0) {
        
        // Create a new JObject to 
        $object_file = new JObject($file);

        if (!JFile::upload($file['tmp_name'], $file['filepath']))
        {
          // Error in upload
          $file['error'][] = JText::_('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE');
        }
        else
        {
          // Trigger the onContentAfterSave event.
          // Should trigger this after the files have been done into the database? e.g. post process uploaded files
          $dispatcher->trigger('onContentAfterSave', array('com_media.file', &$object_file, true));
        }
        
        // Add the url to the uploaded files array
        $file['image_url'] = JURI::root() . 'images/'. $id . '/' . $file['name'];
        $file['caption'] = '';
        $file['image_file_name'] = $file['name'];
      }
    } 
    

    // Load the relevant table model(s) so we can save the data back to the db
    $images_model = $this->getModel('images');
    
    // Get an instance of the helloworld table
    $table = $images_model->getImagesTable();
    
      
    // Lastly, loop over the $files array (again) and add any new images to the existing ones
    foreach ($files as &$file) {
      // If the image uploaded correctly there won't be any errors
      if (count($file['error']) == 0) {
        unset($file['error']);
        unset($file['tmp_name']);
        $images[] = $file;
      }
    }
    

    // Save the file details back to the database.
    // Need to ensure that the images are always stored against the parent property ID if this is a leaf node
    if ($parent_id == 1) {
      if (!$table->save($id, $files))
      {
        // TODO: This won't return to the user
        JError::raiseWarning(500, $table->getError());
        return false;
      }      
    } else {
      
      // Store the image against the parent property
      if (!$table->save($parent_id, $files))
      {
        // TODO: This won't return to the user
        JError::raiseWarning(500, $table->getError());
        return false;
      }
    }

    
    jexit(); // Exit this request now as results passed back to client via xhr transport.
  }

	/**
	 * Used as a callback for array_map, turns the multi-file input array into a sensible array of files
	 * Also, removes illegal characters from the 'name' and sets a 'filepath' as the final destination of the file
	 *
	 * @param	string	- file name			($files['name'])
	 * @param	string	- file type			($files['type'])
	 * @param	string	- temporary name	($files['tmp_name'])
	 * @param	string	- error info		($files['error'])
	 * @param	string	- file size			($files['size'])
	 *
	 * @return	array
	 * @access	protected
	 */
	protected function reformatFilesArray($name, $type, $tmp_name, $error, $size)
	{
		$name = JFile::makeSafe($name);
		return array(
			'name'		=> $name,
			'type'		=> $type,
			'tmp_name'	=> $tmp_name,
			'error'		=> array(),
			'size'		=> $size,
			'filepath'	=> JPath::clean(implode(DS, array($this->folder, $name))),
		);
	}  
  
}
