<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

// Import file lib for checking file types of files being uploaded
jimport('joomla.filesystem.file');

// require helper file
require_once('administrator/components/com_media/helpers/media.php');

class HelloWorldUpload extends MediaHelper {
  
}

/**
 * HelloWorld Controller
 */
class HelloWorldControllerImages extends JControllerAdmin
{
  protected $extension;

	/**
	 * Constructor.
	 *
	 * @param  array  $config  An optional associative array of configuration settings.
	 *
	 * @since  1.6
	 * @see    JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Guess the JText message prefix. Defaults to the option.
		if (empty($this->extension))
		{
			$this->extension = JRequest::getCmd('extension', 'com_helloworld');
		}
	}
  /**
   * 
	 * The folder we are uploading into
	 */
	protected $folder = '';
  
	/**
	 * Method to check if you can edit a record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
    echo $recordId;die;
    $user = JFactory::getUser();
    
		$userId = $user->get('id');
    // This covers the case where the user is creating a new property (i.e. id is 0 or not set
    if ($recordId === 0 && $user->authorise('core.edit.own', $this->extension)) {
      return true;
    } 

		// Check general edit permission first.
		if ($user->authorise('core.edit', $this->extension))
		{      
			return true;
		}
    
		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', $this->extension))
		{    

			// Now test the owner is the user.
			$ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;
			if (empty($ownerId) && $recordId)
			{
				// Need to do a lookup from the model.
        $model = $this->getModel('Property');
        $record = $model->getItem($recordId);
        
				if (empty($record))
				{
					return false;
				}

				$ownerId = $record->created_by;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId)
			{
				return true;
			}
		}
		return false;
	}
  
  
  function updatecaption() {
    
    // Check that this is a valid call from a logged in user.
    JSession::checkToken( 'get' ) or die( 'Invalid Token' );
   
    $app = JFactory::getApplication();
    
    // Check that this user is authorised to edit (i.e. owns) this this property
    if (!$this->allowEdit()) {
      $app->enqueueMessage(JText::_('COM_HELLOWORLD_NOT_PERMITTED_TO_EDIT_THIS_PROPERTY'), 'message');
      $this->setRedirect(JRoute::_('index.php?option=com_helloworld' . $this->getRedirectToListAppend(), false));
      return false;
    }   
    
    // Get the property ID from the GET variable
    $id = JRequest::getVar( 'id', '', 'GET', 'int' );   
    
    // Get the image file ID of which we need to delete
    $file_id = JRequest::getVar ('file_id','','GET','int');
    
    // Get the caption, needs filtering
    $raw = JRequest::getVar ('jform','','POST','array');
    
    // Let's update this puppy...first we need to get the 
    JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');	
    $table = JTable::getInstance('Images', 'HelloWorldTable');   
    
    $table->id = $file_id;
    
		// Bind the translated fields to the JTable instance	
		if (!$table->bind($raw))
		{
			JError::raiseWarning(500, $table->getError());
		}	else {
     $table->store($file_id);
     $app->enqueueMessage(JText::_('COM_HELLOWORLD_IMAGES_CAPTION_SUCCESSFULLY_UPDATED'), 'message');
    }
    
    // Log out to a file 
    // User ID updates caption ID from to on this 
    
    
    // Set the redirection once the delete has completed...
    $this->setRedirect(JRoute::_('index.php?option=com_helloworld&task=images.edit' . $this->getRedirectToItemAppend($id, 'id'), false));

  }
  
  function delete() {
    
    // Check that this is a valid call from a logged in user.
    JSession::checkToken( 'get' ) or die( 'Invalid Token' );
   
    $app = JFactory::getApplication();
    
    // Check that this user is authorised to edit (i.e. owns) this this property
    if (!$this->allowEdit()) {
      $app->enqueueMessage(JText::_('COM_HELLOWORLD_NOT_PERMITTED_TO_EDIT_THIS_PROPERTY'), 'message');
      $this->setRedirect(JRoute::_('index.php?option=com_helloworld' . $this->getRedirectToListAppend(), false));
      return false;
    }   
    
    // Get the property ID from the GET variable
    $id = JRequest::getVar( 'id', '', 'GET', 'int' );   


    
    // Get the image file ID of which we need to delete
    $file_id = JRequest::getVar ('file','','GET','int');
    
    // Let's delete this puppy...first we need to get the file details
    JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');	
    $table = JTable::getInstance('Images', 'HelloWorldTable');
    
    // Get the image details
    if($table->load($file_id)){
          
      // Name of the image to remove
      $file = $table->image_file_name;

      if ($file !== JFile::makeSafe($file))
      {
        // filename is not safe
        $filename = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
        Error::raiseWarning(100, JText::sprintf('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FILE_WARNFILENAME', substr($filename, strlen(COM_IMAGE_BASE))));
      }

      $fullPaths = array();
      // Create a path to delete the image and each of the profile images that would've been created
      $fullPaths[] = JPath::clean(implode('/', array(COM_IMAGE_BASE, $id, $file)));
      $fullPaths[] = JPath::clean(implode('/', array(COM_IMAGE_BASE, $id, 'gallery', $file)));
      $fullPaths[] = JPath::clean(implode('/', array(COM_IMAGE_BASE, $id, 'thumbs', $file)));
      $fullPaths[] = JPath::clean(implode('/', array(COM_IMAGE_BASE, $id, 'thumb', $file)));

      // Loop over each file path
      foreach ($fullPaths as $path) {
        if (is_file($path))
        {
          JFile::delete($path);
          $app = JFactory::getApplication();
        }        
      }
      
      $del = $table->delete($file_id);
      
      // Also need to check and delete this from the gallery_images table if 
   
      
      // Set the message
      $app->enqueueMessage(JText::_('COM_HELLOWORLD_IMAGES_IMAGE_SUCCESSFULLY_DELETED'), 'message');
    } else {
      $app->enqueueMessage(JText::_('COM_HELLOWORLD_IMAGES_IMAGE_DELETE_PROBLEM_FETCHING_IMAGE_DETAILS'), 'message');
    }

        
 
    
    // Set the redirection once the delete has completed...
    $this->setRedirect(JRoute::_('index.php?option=com_helloworld&task=images.edit' . $this->getRedirectToItemAppend($id, 'id'), false));

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
		$files			= JRequest::getVar('jform', '', 'files', 'array');
    
    // Get the property ID from the GET variable
    $id = JRequest::getVar( 'id', '', 'GET', 'int' );   

    // Get the parent property ID from the GET variable
    $parent_id = JRequest::getVar( 'parent_id', '', 'GET', 'int' );  

    // Need to ensure that images are always uploaded into the parent property folder.
    if ($parent_id == 1) {
      // Create the folder path into which we are uploading the images to - This is why they are not copying on test...
      $this->folder = COM_IMAGE_BASE .'/'. JRequest::getVar('id', 'GET', '', 'integer');
    } else {
      $this->folder = COM_IMAGE_BASE .'/'. $parent_id;
    }
    
    
    
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
      $general_error[]['error'][] = JText::_('COM_HELLOWORLD_IMAGES_TOTAL_FILE_SIZE_TOO_LARGE');
      echo json_encode($general_error);
      jexit();
    }
    
    // Input is in the form of an associative array containing numerically indexed arrays - passed in from PHP/Apache in this format?
		// We want a numerically indexed array containing associative arrays
		// Cast each item as array in case the Filedata parameter was not sent as such
		$files = array_map( array($this, 'reformatFilesArray'),
			(array) $files['name']['upload-images'], (array) $files['type']['upload-images'], (array) $files['tmp_name']['upload-images'], (array) $files['error']['upload-images'], (array) $files['size']['upload-images']
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

        // Move the file from the tmp location to the property image folder
        if (!JFile::upload($file['tmp_name'], $file['filepath']))
        {
          // Error in upload
          $file['error'][] = JText::_('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE');
        }
        
        // Add the url to the uploaded files array
        if ($parent_id == 1) {
          $file['image_url'] = JURI::root() . 'images/'. $id . '/' . $file['name'];
        } else {
          $file['image_url'] = JURI::root() . 'images/'. $parent_id . '/' . $file['name'];
          
        }
        $file['caption'] = '';
        $file['image_file_name'] = $file['name'];
      }
    } 
        
    // Lastly, loop over the $files array (again) 
    foreach ($files as &$file) {
      
      // If the image uploaded correctly there won't be any errors
      if (count($file['error']) == 0) {
        
        // Add a success message to the file item and then add this to an images array 
        $file['message'] = JText::_('COM_HELLOWORLD_IMAGES_IMAGE_SUCCESSFULLY_UPLOADED');
        $images[] = $file;
      } 
    }
    
    // Load the relevant model(s) so we can save the data back to the db
    $images_model = $this->getModel('images');
    
    // Get an instance of the images table
    $table = $images_model->getImagesTable(); 
    
    // Save the file details back to the database.
    // Need to ensure that the images are always stored against the parent property ID if this is a leaf node
    if ($parent_id == 1) {
      if (!$table->save_images($id, $images))
      {
        // TODO: This won't return to the user
        JError::raiseWarning(500, $table->getError());
        $general_error[]['error'][] = JText::_('COM_HELLOWORLD_IMAGES_PROBLEM_SAVING_IMAGE_TO_DATABASE');
      } else {
        // Images were uploaded okay, and saved so let's create the profile images.
        $images_model->generateImageProfiles($images, $id);
      }     
    } else {
      // Store the image against the parent property
      if (!$table->save_images($parent_id, $images))
      {
        // TODO: This won't return to the user
        JError::raiseWarning(500, $table->getError());
        $general_error[]['error'][] = JText::_('COM_HELLOWORLD_IMAGES_PROBLEM_SAVING_IMAGE_TO_DATABASE');        
      } else {
        // Images were uploaded okay, and saved so let's create the profile images.
        // Trigger the onContentAfterSave event.
        $images_model->generateImageProfiles($images, $parent_id);
      }
    }
    
    echo json_encode($files);
    
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
			'filepath'	=> JPath::clean(implode('/', array($this->folder, $name))),
		);
	}  
  
}
