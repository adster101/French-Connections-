<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controlleradmin');

// Import file lib for checking file types of files being uploaded
jimport('joomla.filesystem.file');

// require helper file
require_once('administrator/components/com_media/helpers/media.php');

/**
 * HelloWorld Controller
 */
class HelloWorldControllerImages extends JControllerAdmin {

  protected $extension;

  /**
   * Constructor.
   *
   * @param  array  $config  An optional associative array of configuration settings.
   *
   * @since  1.6
   * @see    JController
   */
  public function __construct($config = array()) {
    parent::__construct($config);

    // Guess the JText message prefix. Defaults to the option.
    if (empty($this->extension)) {
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
  protected function allowEdit($data = array(), $key = 'id') {

    // Initialise variables.
    $recordId = (int) isset($data[$key]) ? $data[$key] : 0;

    $user = JFactory::getUser();

    $userId = $user->get('id');

    // This covers the case where the user is creating a new property (i.e. id is 0 or not set
    if ($recordId === 0 && $user->authorise('core.edit.own', $this->extension)) {
      return true;
    }

    // Check general edit permission first.
    // User can edit anything in this extension
    if ($user->authorise('core.edit', $this->extension)) {
      return true;
    }

    // First test if the permission is available.
    if ($user->authorise('core.edit.own', $this->extension)) {

      // Now test the owner is the user.
      $ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;
      if (empty($ownerId) && $recordId) {
        // Need to do a lookup from the model.
        $model = $this->getModel('Unit');
        $record = $model->getItem($recordId);

        if (empty($record)) {
          return false;
        }

        $ownerId = $record->created_by;
      }

      // If the owner matches 'me' then do the test.
      if ($ownerId == $userId) {
        return true;
      }
    }
    return false;
  }

  function updatecaption() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('get') or die('Invalid Token');

    // Set up some arrays
    // Data is used to check the ownership and also passed to the model save method...
    $data = array();

    // Response is used to send a message back to the client
    $response = array();

    // Get the input data
    $app = JFactory::getApplication();
    $input = $app->input;

    // Build up the data
    $data['property_id'] = $input->get('property_id', '', 'int');
    $data['caption'] = $input->get('caption','','string');
    $data['id'] = $input->get('id','','int');

    // Check that this user is authorised to edit (i.e. owns) this this property
    if (!$this->allowEdit($data, 'property_id'))
    {
      $response = JText::_('NOT_AUTHORISED');
      echo $response;
      jexit(); // Exit this request now as results passed back to client via xhr transport.

    }



    // Need to ensure the caption is filtered at some point

    // Load the relevant model(s) so we can save the data back to the db
    $model = $this->getModel('Image','HelloWorldModel');

    // If we are happy to save and have something to save
    if (!$model->save($data)) {
      // Problem saving, oops
      $response = JText::_('COM_HELLOWORLD_HELLOWORLD_IMAGES_CAPTION_NOT_UPDATED');
      echo $response;
      jexit(); // Exit this request now as results passed back to client via xhr transport.

    }


    $response = JText::_('COM_HELLOWORLD_HELLOWORLD_IMAGES_CAPTION_UPDATED');

    echo $response;

    jexit(); // Exit this request now as results passed back to client via xhr transport.

    // Log out to a file
    // User ID updates caption ID from to on this
  }

  function delete() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('get') or die('Invalid Token');

    // Get the application instance
    $app = JFactory::getApplication();
    $input = $app->input;

    // Build up the data

    $data['property_id'] = $input->get('property_id', '', 'int');
    $data['id'] = $input->get('id','','int');

    // Check that this user is authorised to edit (i.e. owns) this this asset
    if (!$this->allowEdit($data, 'property_id')) {
      $app->enqueueMessage(JText::_('COM_HELLOWORLD_NOT_PERMITTED_TO_EDIT_THIS_PROPERTY'), 'message');
      $this->setRedirect(JRoute::_('index.php?option=com_helloworld&view=images&id=' . (int) $data['property_id'], false));
      return false;
    }

    // Get the image model
    $model = $this->getModel('Image', 'HelloWorldModel');

    // Let's delete this puppy...first we need to get the file details
    $table = $model->getTable();

    if (!$table->delete($data['id'])) {
       $app->enqueueMessage(JText::_('COM_HELLOWORLD_IMAGES_IMAGE_COULD_NOT_BE_DELETED'), 'message');
    } else {
      // Set the message
      $app->enqueueMessage(JText::_('COM_HELLOWORLD_IMAGES_IMAGE_SUCCESSFULLY_DELETED'), 'message');
    }
    // Set the redirection once the delete has completed...
    $this->setRedirect(JRoute::_('index.php?option=com_helloworld&view=images&unit_id=' . (int) $data['property_id'], false));
  }

  /*
   * Action to handle uploading of images from the unit image gallery
   *
   * TODO - Make this code more resuable...
   *
   */

  function upload() {

    // Get the app and user instances
    $app = JFactory::getApplication($initialise = false);
    $user = JFactory::getUser();

    // Get the id, which is the unit ID we are uploading the image against
    $unit_id = $app->input->get('unit_id', '', 'GET', 'int');

    // Get the id, which is the unit ID we are uploading the image against
    $property_id = $app->input->get('property_id', '', 'GET', 'int');

    // Get the version id
    $version_id = $app->input->get('version_id','','GET','int');

    // Set the filepath for the images to be moved into
    $this->folder = JPATH_SITE . '/images/property/' . $property_id . '/';

    // An array to hold the that are good to save against the property
    $images = array();

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('GET') or die('Invalid Token');

    // Check that this user is authorised to upload images here
    if (!$user->authorise('helloworld.images.create', $this->extension)) {
      $app->enqueueMessage(JText::_('COM_HELLOWORLD_IMAGES_NOT_AUTHORISED'), 'message');
      $this->setRedirect(JRoute::_('index.php?option=com_helloworld&view=images' . $this->getRedirectToItemAppend($unit_id, 'id'), false));
    }


    // Get the media component parameters
    $params = JComponentHelper::getParams('com_media');

    // Get some data from the request
    $files = JRequest::getVar('jform', '', 'files', 'array');

    // Input is in the form of an associative array containing numerically indexed arrays - passed in from PHP/Apache in this format?
    // We want a numerically indexed array containing associative arrays
    // Cast each item as array in case the Filedata parameter was not sent as such
    $uploaded_file = array_map(
            array($this, 'reformatFilesArray'), (array) $files['name'], (array) $files['type'], (array) $files['tmp_name'], (array) $files['size']
    );

    // Set FTP credentials, if given
    JClientHelper::setCredentialsFromRequest('ftp');
    JPluginHelper::importPlugin('content');

    $dispatcher = JDispatcher::getInstance();

    foreach ($uploaded_file as &$file) {

      // Initialise an error component of the $upload_file array
      $file['error'] = '';

      // Perform some validation on the file, this would be better wrapped into a simple class file
      // Check the total size of files being uploaded. If it's too large we just exit?
      if (
              $_SERVER['CONTENT_LENGTH'] > ($params->get('upload_maxsize', 0) * 1024 * 1024) ||
              $_SERVER['CONTENT_LENGTH'] > (int) (ini_get('upload_max_filesize')) * 1024 * 1024 ||
              $_SERVER['CONTENT_LENGTH'] > (int) (ini_get('post_max_size')) * 1024 * 1024 ||
              $_SERVER['CONTENT_LENGTH'] > (int) (ini_get('memory_limit')) * 1024 * 1024) {
        // Not acceptable. Too large a total file size.
        // return an error message and ...
        $file['error'] = JText::_('COM_HELLOWORLD_IMAGES_TOTAL_FILE_SIZE_TOO_LARGE');
      }


      // Check that it has a valid name
      if (!isset($file['name'])) {
        // This file doesn't have a filename after running through make path safe
        $file['error'][] = JText::_('COM_HELLOWORLD_IMAGES_IMAGE_NAME_NOT_VALID');
      }

      // The file is valid at this point
      $err = null;

      // canUpload does a further raft of checks to ensure that the image is 'safe' (i.e. checks mime type and that it is an image file etc
      if (!MediaHelper::canUpload($file, $err)) {
        // The file can't be uploaded
        $file['error'][] = JText::_($err);
      }

      // If there are no errors recorded for this file, we move it to the relevant folder for this property

      if (empty($file['error'])) {

        // Move the file from the tmp location to the property image folder
        if (!JFile::upload($file['tmp_name'], $file['filepath'])) {
          // Error in upload
          $file['error'][] = JText::_('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE');
        }
      }

      // If there are no errors recorded for this file, we move it to the relevant folder for this property
      if (empty($file['error'])) {
        // Add the url to the uploaded files array
        $file['url'] = JURI::root() . 'images/property/' . $property_id . '/' . $file['name'];
        $file['caption'] = '';
        $file['image_file_name'] = $file['name'];
        $file['property_id'] = $unit_id;
        $file['version_id'] = $version_id;
        $file['delete_url'] = '';
        $file['delete_type'] = 'DELETE';
        $file['message'] = empty($file['error']) ? JText::_('COM_HELLOWORLD_IMAGES_IMAGE_SUCCESSFULLY_UPLOADED') : '';
        $file['thumbnail_url'] = JURI::root() . '/' . 'images/property/' . $property_id . '/thumb/' . $file['name'];

        // Get an instance of the images model file so we can load the existing images for this unit
        // primarily so we can get the ordering
        $model = $this->getModel('Images');

        $model->setState('version_id',$version_id);

        $existing_images = $model->getItems();

        if (empty($existing_images)) {

          $ordering = 1;

        } else {

          $last = array_pop($existing_images);
          $ordering = $last->ordering + 1;
        }

        // Set the ordering on the image being uploaded
        $file['ordering'] = $ordering;

        // Image has been uploaded, let's create some image profiles...
        $model->generateImageProfile($file['filepath'], (int) $file['property_id'], $file['image_file_name'], 'gallery', 578, 435);
        $model->generateImageProfile($file['filepath'], (int) $file['property_id'], $file['image_file_name'], 'thumbs', 100, 100);
        $model->generateImageProfile($file['filepath'], (int) $file['property_id'], $file['image_file_name'], 'thumb', 210, 120);

        // Load the relevant model(s) so we can save the data back to the db
        $model = $this->getModel('Image');


        // If we are happy to save and have something to save
        if (!$model->save($file)) {
          $file['error'][] = JText::_('COM_MEDIA_ERROR_UNABLE_TO_SAVE_FILE');
        }
      }
    }




    $files = array();

    $files['files'] = $uploaded_file;

    echo json_encode($files);

    jexit(); // Exit this request now as results passed back to client via xhr transport.
  }

  /**
   * Used as a callback for array_map, turns the multi-file input array into a sensible array of files
   * Also, removes illegal characters from the 'name' and sets a 'filepath' as the final destination of the file
   *
   * @param	string	- file name			($upload['name'])
   * @param	string	- file type			($upload['type'])
   * @param	string	- temporary name	($upload['tmp_name'])
   * @param	string	- error info		($upload['error'])
   * @param	string	- file size			($upload['size'])
   *
   * @return	array
   * @access	protected
   */
  protected function reformatFilesArray($name, $type, $tmp_name, $size) {
    // Prepend a unique ID to the filename so that all files have a unique name.
    $name = uniqid() . '-' . JFile::makeSafe($name);
    return array(
        'name' => $name,
        'type' => $type,
        'tmp_name' => $tmp_name,
        'size' => $size,
        'filepath' => JPath::clean(implode('/', array($this->folder, $name))),
    );
  }

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');


		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel('Image');

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}

  /*
   * View action - checks ownership of record sets the edit id in session and redirects to the view
   *
   *
   */

  public function manage() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('GET') or die('Invalid Token');

    // $id is the listing the user is trying to edit
    $id = $this->input->get('unit_id', '', 'int');

    $data['id'] = $id;

    if (!$this->allowEdit($data, 'id')) {
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option, false)
      );

      $this->setMessage('blah', 'error');

      return false;
    }

    $this->holdEditId('com_helloworld.edit.unitversions',$id);

    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=images&unit_id=' . (int) $id, false)
    );
    return true;
  }

}

