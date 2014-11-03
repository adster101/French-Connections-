<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import file lib for checking file types of files being uploaded
jimport('joomla.filesystem.file');

jimport('frenchconnections.controllers.property.images');

/**
 * HelloWorld Controller
 */
class RealEstateControllerImages extends PropertyControllerImages
{

  var $folder = '';
  var $property_id;
  var $id;
  var $review;
  var $unit_id;

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

    // Get the input and initialise our properties based on that
    $input = JFactory::getApplication()->input;

    $this->property_id = $input->get('property_id', '', 'int');

    $this->folder = JPATH_SITE . '/images/property/' . $this->property_id . '/';

    // Get the version id 
    $this->id = $input->get('id', '', 'int');

    // Get the review state for the property
    $this->review = $input->get('review', '', 'boolean');

    parent::__construct($config);



    //$this->registerTask('saveandnext', 'save');
  }

  /**
   * Checks whether the owner is allowed to edit this property version via the PropertyHelper class 
   * 
   * @param type $data
   * @param type $key
   * @return type boolean
   */
  public function allowEdit($data = array())
  {
    // Get the property id we're trying to edit
    $id = $data['realestate_property_id'];

    // Test whether this user is allowed to edit it.
    return PropertyHelper::allowEditRealestate($id);
  }

  function delete()
  {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('get') or die('Invalid Token');

    $app = JFactory::getApplication();
    $input = $app->input;

    $model = $this->getModel('Image', 'RealestateModel');
    $table = $this->getModel()->getTable();


    // Build up the data
    $recordId = $input->get('realestate_property_id', '', 'int');
    $id = $input->get('id', '', 'int');

    // Check that this user is authorised to 'edit' this property
    if (!$this->allowEdit($data))
    {
      
    }

    if (!$model->delete($id))
    {
      $app->enqueueMessage(JText::_('COM_RENTAL_IMAGES_IMAGE_COULD_NOT_BE_DELETED'), 'error');
    }
    else
    {
      // Set the message
      $app->enqueueMessage(JText::_('COM_RENTAL_IMAGES_IMAGE_SUCCESSFULLY_DELETED'), 'message');
    }
    // Set the redirection once the delete has completed...
    $this->setRedirect(JRoute::_('index.php?option=com_rental&view=images&unit_id=' . (int) $unit_id, false));
  }

  /*
   * View action - checks ownership of record sets the edit id in session and redirects to the view
   *
   * TO DO - Figure out a way to share this action between realestate and rental components
   *
   */

  public function manage()
  {

    // $id is the listing the user is trying to edit
    $id = $this->input->get('realestate_property_id', '', 'int');

    $data['realestate_property_id'] = $id;

    if (!$this->allowEdit($data))
    {
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option, false)
      );

      $this->setMessage('blah', 'error');

      return false;
    }

    $this->holdEditId($this->option . '.edit.' . $this->context, $id);

    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=images&realestate_property_id=' . (int) $id, false)
    );
    return true;
  }

  function updatecaption()
  {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('get') or die('Invalid Token');

    $app = JFactory::getApplication();
    $input = $app->input;

    $model = $this->getModel('Caption', 'RealestateModel');
    $data = array();
    $response = array();

    // Build up the data
    $data['realestate_property_id'] = $input->get('realestate_property_id', '', 'int');
    $data['caption'] = $input->get('caption', '', 'string');
    $data['id'] = $input->get('id', '', 'int');

    // Check that this user is authorised to edit (i.e. owns) this this property
    if (!$this->allowEdit($data, 'unit_id'))
    {
      $response['message'] = JText::_('NOT_AUTHORISED');
      //echo $response;
      //jexit(); // Exit this request now as results passed back to client via xhr transport.
    }

    // Consider running this through $model->validate to more carefully check the caption details
    $form = $model->getForm();

    $validData = $model->validate($form, $data);

    if (!$validData)
    {
      // Problem saving, oops
      $response['message'] = JText::_('COM_RENTAL_HELLOWORLD_IMAGES_CAPTION_IS_INVALID');
      $response['error'] = 1;
      //echo $response;
      //jexit(); // Exit this request now as results passed back to client via xhr transport.     
    }

    // Need to ensure the caption is filtered at some point
    // If we are happy to save and have something to save
    // Also, need to amend the save method so that it triggers a new version
    if (!$model->save($validData))
    {
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

}

