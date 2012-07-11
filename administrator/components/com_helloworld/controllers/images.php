<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

// Import file lib for checking file types of files being uploaded
jimport('joomla.filesystem.file');

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

    // Check that this is a valid call from a logged in user.
    JSession::checkToken( 'get' ) or die( 'Invalid Token' );
    
    // Check that this user is authorised to edit (i.e. owns) this this property
    $this->allowEdit();
    
    // Get the component parameters
 		$params = JComponentHelper::getParams('com_helloworld');

 		// Get some data from the request
		$files			= JRequest::getVar('jform_upload_images', '', 'files', 'array');
    
    // Create the folder path into which we are uploading the images to 
    $this->folder = 'C:XAMPP/htdocs/images/' . JRequest::getVar('id', 'GET', '', 'integer');

    
    if (
			$_SERVER['CONTENT_LENGTH']>($params->get('upload_maxsize', 0) * 1024 * 1024) ||
			$_SERVER['CONTENT_LENGTH']>(int)(ini_get('upload_max_filesize'))* 1024 * 1024 ||
			$_SERVER['CONTENT_LENGTH']>(int)(ini_get('post_max_size'))* 1024 * 1024 ||
			$_SERVER['CONTENT_LENGTH']>(int)(ini_get('memory_limit'))* 1024 * 1024
		)
		{
      // Not acceptable. Too large a total file size.
			// return an error object somehow...
		}
    

    // Input is in the form of an associative array containing numerically indexed arrays
		// We want a numerically indexed array containing associative arrays
		// Cast each item as array in case the Filedata parameter was not sent as such
		$files = array_map( array($this, 'reformatFilesArray'),
			(array) $files['name'], (array) $files['type'], (array) $files['tmp_name'], (array) $files['error'], (array) $files['size']
		);
    
   
    
  

   

    
    
    
    foreach ($files as &$file)
		{
      
			// The request is valid
			$err = null;

    // Merge this into a custom canUpload function here incorporating any other checks we think we need (XSS check, dimensions etc)
    // Have it return false on any one error and set an error message or run through all the checks first and then spit out all errors?
		foreach ($files as &$file)
		{ 
			if ($file['size']>((int)(ini_get('post_max_size')) * 1024 * 1024))
			{
        $file['error'][] = JText::_('COM_HELLOWORLD_IMAGES_IMAGE_TOO_LARGE');
			}
			
			if (JFile::exists($file['filepath']))
			{
				// A file with this name already exists
        $file['error'][] = JText::_('COM_HELLOWORLD_IMAGES_IMAGE_ALREADY_EXISTS');
      }

			if (!isset($file['name']))
			{
				// This file doesn't have a filename after running through make path safe
				$file['error'][] = JText::_('COM_HELLOWORLD_IMAGES_IMAGE_NAME_NOT_VALID');
      }
		}
      // As per the media helper need to check file type
      if (!$this->canUpload($file, $err))
			{
				// The file can't be upload
				$file['error'][] = $err;
        die;
			}

			// Trigger the onContentBeforeSave event.
			$object_file = new JObject($file);

      $dispatcher	= JDispatcher::getInstance();

			$result = $dispatcher->trigger('onContentBeforeSave', array('com_media.file', &$object_file));
			if (in_array(false, $result, true))
			{
				// There are some errors in the plugins
				$error[$file['name']][] = JText::_('COM_HELLOWORLD_IMAGES_IMAGE_PROBLEM_WITH_UPLOAD');
			}

			if (!JFile::upload($file['tmp_name'], $file['filepath']))
			{
				// Error in upload
				$error[$file['name']][] = JText::_('COM_HELLOWORLD_IMAGES_IMAGE_UNSPECIFIED_ERROR');
			}
			else
			{
				// Trigger the onContentAfterSave event.
				$dispatcher->trigger('onContentAfterSave', array('com_media.file', &$object_file, true));
				$this->setMessage(JText::sprintf('COM_MEDIA_UPLOAD_COMPLETE', substr($file['filepath'], strlen(1))));
			}
		}
    
    print_r($files);
    die;
    // At this point we have an array of images 
    
    // Load the relevant table model(s) so we can save the data back to the db
    $images = $this->getModel('images');
    
    // Get an instance of the helloworld table
    $table = $images->getTable();
    
    // Get the ID of the property being edited and set the table instance to that.
    $table->id = JRequest::getVar( 'id' );
    
    // Bind images to table object
    $array = array();
    
    $poop['images'][0]['path'] = 'path/to/image';
    $poop['images'][0]['en-GB'] = 'English caption';
    $poop['images'][0]['fr-FR'] = 'French caption';
    $poop['images'][1]['path'] = 'path/to/image';
    $poop['images'][1]['en-GB'] = 'English caption';
    $poop['images'][1]['fr-FR'] = 'French caption';  
    
    $array['images'] = json_encode($poop);
    
    // Bind the translated fields to the JTAble instance	
    if (!$table->bind($array))
    {
      JError::raiseWarning(500, $table->getError());
      return false;
    }	
 
    // And update or create depending on whether any translations already exist
		if (!$table->store())
		{
			JError::raiseWarning(500, $table->getError());
			return false;
		}	
    
    //$table->save($id);
    
    
    
    
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
			'filepath'	=> JPath::clean(implode(DS, array($this->folder, $name)))
		);
	}  
  
  
	function canUpload($file, &$err)
	{
    echo "Woot!";die;
		$params = JComponentHelper::getParams('com_media');

		$format = JFile::getExt($file['name']);

		$allowable = explode(',', $params->get('upload_extensions'));

		if (!in_array($format, $allowable)) {
			$err = JText('COM_MEDIA_ERROR_WARNFILETYPE');
			return false;
		}

		$maxSize = (int) ($params->get('upload_maxsize', 0) * 1024 * 1024);

		if ($maxSize > 0 && (int) $file['size'] > $maxSize) {
			$err = JText('COM_MEDIA_ERROR_WARNFILETOOLARGE');

			return false;
		}

		return true;
	}
  
}
