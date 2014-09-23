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
class RentalControllerImages extends JControllerForm {

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
      $this->extension = JRequest::getCmd('extension', 'com_rental');
    }

    //$this->registerTask('saveandnext', 'save');
  }

  /**
   * Changes the order of one or more records.
   *
   * @return  boolean  True on success
   *
   * @since   12.2
   */
  public function reorder() {
    // Check for request forgeries.
    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    $unit_id = JFactory::getApplication()->input->get('unit_id', '', 'int');
    $ids = JFactory::getApplication()->input->post->get('cid', array(), 'array');
    $inc = ($this->getTask() == 'orderup') ? -1 : +1;

    $model = $this->getModel();
    $return = $model->reorder($ids, $inc);
    if ($return === false) {
      // Reorder failed.
      $message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
      $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . '&unit_id=' . (int) $unit_id, false), $message, 'error');
      return false;
    } else {
      // Reorder succeeded.
      $message = JText::_('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED');
      $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . '&unit_id=' . (int) $unit_id, false), $message);
      return true;
    }
  }

  /**
   * Proxy for getModel.
   * @since	1.6
   */
  public function getModel($name = 'Image', $prefix = 'RentalModel') {
    $model = parent::getModel($name, $prefix, array('ignore_request' => true));
    return $model;
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

    $app = JFactory::getApplication();
    $input = $app->input;
    $model = $this->getModel('Caption', 'RentalModel');
    $data = array();
    $response = array();

    // Build up the data
    $data['unit_id'] = $input->get('unit_id', '', 'int');
    $data['caption'] = $input->get('caption', '', 'string');
    $data['id'] = $input->get('id', '', 'int');

    // Check that this user is authorised to edit (i.e. owns) this this property
    if (!$this->allowEdit($data, 'unit_id')) {
      $response['message'] = JText::_('NOT_AUTHORISED');
      //echo $response;
      //jexit(); // Exit this request now as results passed back to client via xhr transport.
    }

    // Consider running this through $model->validate to more carefully check the caption details
    $form = $model->getForm();

    $validData = $model->validate($form, $data);

    if (!$validData) {
      // Problem saving, oops
      $response['message'] = JText::_('COM_RENTAL_HELLOWORLD_IMAGES_CAPTION_IS_INVALID');
      $response['error'] = 1;
      //echo $response;
      //jexit(); // Exit this request now as results passed back to client via xhr transport.     
    }

    // Need to ensure the caption is filtered at some point
    // If we are happy to save and have something to save
    // Also, need to amend the save method so that it triggers a new version
    if (!$model->save($data)) {
      // Problem saving, oops
      $response['message'] = JText::_('COM_RENTAL_HELLOWORLD_IMAGES_CAPTION_NOT_UPDATED');
      $response['error'] = 1;
      //echo $response;
      //jexit(); // Exit this request now as results passed back to client via xhr transport.
    }

    $response['message'] = JText::_('COM_RENTAL_HELLOWORLD_IMAGES_CAPTION_UPDATED');
    $response['error'] = 0;
    
    echo json_encode($response);

    jexit(); // Exit this request now as results passed back to client via xhr transport.
    // Log out to a file
    // User ID updates caption ID from to on this
  }

  function delete() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('get') or die('Invalid Token');

    $app = JFactory::getApplication();
    $input = $app->input;
    $model = $this->getModel('Image', 'RentalModel');

    // Build up the data

    $unit_id = $input->get('unit_id', '', 'int');
    $id = $input->get('id', '', 'int');

    // Check that this user is authorised to edit (i.e. owns) this this asset
    // TO DO - Need to validate robustly that the user is authorised to delete (this) image(s).
    //if (!$this->allowEdit($data, 'property_id')) {
    //$app->enqueueMessage(JText::_('COM_RENTAL_NOT_PERMITTED_TO_EDIT_THIS_PROPERTY'), 'message');
    //$this->setRedirect(JRoute::_('index.php?option=com_rental&view=images&id=' . (int) , false));
    //return false;
    //}

    if (!$model->delete($id)) {
      $app->enqueueMessage(JText::_('COM_RENTAL_IMAGES_IMAGE_COULD_NOT_BE_DELETED'), 'error');
    } else {
      // Set the message
      $app->enqueueMessage(JText::_('COM_RENTAL_IMAGES_IMAGE_SUCCESSFULLY_DELETED'), 'message');
    }
    // Set the redirection once the delete has completed...
    $this->setRedirect(JRoute::_('index.php?option=com_rental&view=images&unit_id=' . (int) $unit_id, false));
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
    $filter = new JFilterInput();
    
    // Load the relevant model(s) so we can save the data back to the db
    $model = $this->getModel('Image');
    
    // Initialise an array to return the info about the uploaded image
    $return = array();

    // Get the id, which is the unit ID we are uploading the image against
    $unit_id = $app->input->get('unit_id', '', 'int');

    // Get the id, which is the unit ID we are uploading the image against
    $property_id = $app->input->get('property_id', '', 'GET', 'int');

    // Get the unit version id
    $id = $app->input->get('id', '', 'int');

    // Get the unit version id
    $review = $app->input->get('review', '', 'boolean');

    // Set the filepath for the images to be moved into
    $this->folder = JPATH_SITE . '/images/property/' . $unit_id . '/';

    // An array to hold the that are good to save against the property
    $images = array();

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('GET') or die('Invalid Token');

    // Check that this user is authorised to upload images here
    if (!$user->authorise('core.create', $this->extension)) {
      $app->enqueueMessage(JText::_('COM_RENTAL_IMAGES_NOT_AUTHORISED'), 'message');
      $this->setRedirect(JRoute::_('index.php?option=com_rental&view=images' . $this->getRedirectToItemAppend($unit_id, 'id'), false));
    }

    // Get the media component parameters
    $params = JComponentHelper::getParams('com_media');

    // Get some data from the request
    $files = JRequest::getVar('jform', array(), 'files', 'array');
          

    // Input is in the form of an associative array containing numerically indexed arrays - passed in from PHP/Apache in this format?
    // We want a numerically indexed array containing associative arrays
    // Cast each item as array in case the Filedata parameter was not sent as such
    $uploaded_file = array_map(
            array($this, 'reformatFilesArray'), (array) $files['name'], (array) $files['type'], (array) $files['tmp_name'], (array) $files['size']
    );

    foreach ($uploaded_file as $key => &$file) {

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
        $file['error'] = JText::_('COM_RENTAL_IMAGES_TOTAL_FILE_SIZE_TOO_LARGE');
      }

      // Check that it has a valid name
      if (!isset($file['name'])) {
        // This file doesn't have a filename after running through make path safe
        $file['error'][] = JText::_('COM_RENTAL_IMAGES_IMAGE_NAME_NOT_VALID');
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
        $file['caption'] = '';
        $file['image_file_name'] = $file['name'];
        $file['unit_id'] = $unit_id;
        $file['id'] = $id;
        $file['property_id'] = $property_id;
        $file['review'] = $review;
        $file['delete_url'] = '';
        $file['delete_type'] = 'DELETE';
        $file['message'] = empty($file['error']) ? JText::_('COM_RENTAL_IMAGES_IMAGE_SUCCESSFULLY_UPLOADED') : '';
        $file['thumbnail_url'] = JURI::root() . '/' . 'images/property/' . $unit_id . '/thumb/' . $file['name'];

        // If we are happy to save and have something to save
        if (!$model->save($file)) {
          $file['error'][] = JText::_('COM_MEDIA_ERROR_UNABLE_TO_SAVE_FILE');
        }

        // Update the file array so we can in turn update the form so that subsequent images are upload to a new version
        $file['version_id'] = $model->getState($model->getName() . '.version_id');
        $file['review'] = $model->getState($model->getName() . '.review');
      }
    }

    $return['files'][] = $file;

    echo json_encode($return);

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
  protected function reformatFilesArray($name, $type, $tmp_name, $size, $caption = '') {
    // Prepend a unique ID to the filename so that all files have a unique name.
    $name = uniqid() . '-' . JFile::makeSafe(str_replace(' ', '-', $name));
    return array(
        'name' => $name,
        'type' => $type,
        'tmp_name' => $tmp_name,
        'size' => $size,
        'filepath' => JPath::clean(implode('/', array($this->folder, $name))),
        'caption' => $caption
    );
  }

  /**
   * Method to save the submitted ordering values for records via AJAX.
   *
   * @return  void
   *
   * @since   3.0
   */
  public function saveOrderAjax() {
    
    // Check that this is a valid call from a logged in user.
    JSession::checkToken() or die('Invalid Token');  
    
    $input = JFactory::getApplication()->input;
    
    $sort = $input->get('sort', array(), 'array');
    
    $order = array();
    
    foreach ($sort as $k => $v) {
      $order[] = $k + 1;
    }
    
    //$pks = $this->input->post->get('cid', array(), 'array');
    //$order = $this->input->post->get('order', array(), 'array');

    // Sanitize the input
    JArrayHelper::toInteger($sort);
    JArrayHelper::toInteger($order);

    // Get the model
    $model = $this->getModel('Image');

    // Save the ordering
    $return = $model->saveorder($sort, $order);

    if ($return) {
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
    $id = $this->input->get('realestate_property_id', '', 'int');

    $data['id'] = $id;

    if (!$this->allowEdit($data, 'id')) {
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option, false)
      );

      $this->setMessage('', 'error');

      return false;
    }

    $this->holdEditId($this->option . '.edit.' .$this->context, $id);

    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=images&realestate_property_id=' . (int) $id, false)
    );
    return true;
  }

  public function saveandnext() {

    // Get the contents of the request data
    $input = JFactory::getApplication()->input;
    // If the task is save and next
    if ($this->task == 'saveandnext') {
      // Check if we have a next field in the request data
      $next = $input->get('next', '', 'base64');
      $url = base64_decode($next);
      // And set the redirect if we have
      if ($next) {
        $this->setRedirect(base64_decode($next));
      }
    }
    return true;
  }

  public function cancel($key = null) {
    
    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    // Get the property ID from the form data and redirect 
    $input = JFactory::getApplication()->input;

    $property_id = $input->get('property_id', '', 'int');

    // Clean the session data and redirect.
    $this->releaseEditId('com_rental.edit.unitversions', $property_id);

    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=listing&id=' . (int) $property_id, false
            )
    );
    
    return true;
  }

}

