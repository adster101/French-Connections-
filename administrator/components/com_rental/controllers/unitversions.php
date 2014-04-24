<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
//jimport('joomla.application.component.controllerform');
jimport('frenchconnections.controllers.property.base');

/**
 * HelloWorld Controller
 */
class RentalControllerUnitVersions extends RentalControllerBase {

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

    // Set the view list - applies when saving/cancelling rather than 'inflecting' the list view
    $this->view_list = 'listing';

    // Assign the tariffs task to run the edit method.
    // This redirects to the tariffs layout of the unitversions view
    $this->registerTask('tariffs', 'edit');
    $this->registerTask('images', 'edit');
    $this->registerTask('saveandnext', 'save');
  }

  /*
   * Method to check that the user can view the reviews, and if so redirect to the correct view
   *
   */

  public function reviews($key = null, $urlVar = null) {

    $model = $this->getModel();
    $table = $model->getTable();

    // Set the context
    $context = "$this->option.view.$this->context";

    // Determine the name of the primary key for the data.
    if (empty($key)) {
      $key = $table->getKeyName();
    }

    // To avoid data collisions the urlVar may be different from the primary key.
    if (empty($urlVar)) {
      $urlVar = $key;
    }

    // $id is the listing the user is trying to edit
    $recordId = $this->input->getInt($urlVar);

    if (!$this->allowEdit(array($key => $recordId), $key)) {
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option, false)
      );

      $this->setMessage('Not authorised...', 'error');

      return false;
    }

    // Hold the edit ID once the id and user have been authorised.
    $this->holdEditId($context, $recordId);
    $this->set('view_item', 'reviews');
    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar), false
            )
    );
    return true;
  }

  /*
   * Augmented getRedirectToItemAppend so we can append the property_id onto the url
   * This is mostly useful for when a new unit is being created and the validation fails...
   *
   * Makes more sense to override this than the individual save/edit methods
   */

  public function getRedirectToItemAppend($recordId = null, $urlVar = 'unit_id') {

    // Get the default append string
    $append = parent::getRedirectToItemAppend($recordId, $urlVar);

    // Get the task, if we are 'editing' then the parent id won't be set in the form scope
    $task = $this->getTask();

    switch ($task) :
      case 'add':
      case 'edit':
        $recordId = JFactory::getApplication()->input->get('property_id', 0, 'int');
        $urlVar = '&property_id=';
        if ($recordId) {
          $append .= $urlVar . $recordId;
        }
        break;
      case 'apply':
      case 'save':
        // Derive the parent id from the form data
        //$data = JFactory::getApplication()->input->get('jform', array(), 'array');
        //$recordId = $data['property_id'];
        //$urlVar = '&property_id=';
        break;
      case 'reviews':
        $append = '';
        if ($recordId) {
          $append .= '&' . $urlVar . '=' . $recordId;
        }
        break;
      case 'images':
        $layout = $this->input->get('layout', 'images');
        $append = '';
        $append.= '&layout=' . $layout . '&' . $urlVar . '=' . $recordId;
        break;
    endswitch;

    return $append;
  }

  /*
   * Augmented getRedirectToItemAppend so we can append the property_id onto the url
   * MAkes more sense to override this than the individual save/edit methods
   *
   */

  public function getRedirectToListAppend($recordId = null, $urlVar = 'id') {

    // Get the default append string
    $append = parent::getRedirectToListAppend($recordId, $urlVar);

    // Get the task, if we are 'editing' then the parent id won't be set in the form scope
    $task = $this->getTask();

    switch ($task) :
      case 'save':
      case 'cancel':
        // Derive the parent id from the form data
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');
        $id = $data['property_id'];
        break;


    endswitch;

    // If parent ID is set in form data also append to the url
    if ($id > 0) {
      $append .= '&id=' . $id;
    }

    return $append;
  }

  public function add() {
    $app = JFactory::getApplication();
    $context = "$this->option.edit.$this->context";
    $model = $this->getModel('UnitVersions');
    $table = $model->getTable();
    $id = JFactory::getApplication()->input->getInt('property_id');
    $data = $table->getProperties();

    // Access check.
    if (!$this->allowAdd()) {
      // Set the internal error and also the redirect error.
      $this->setError(JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'));
      $this->setMessage($this->getError(), 'error');

      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option . '&view=' . $this->view_list
                      . $this->getRedirectToListAppend(), false
              )
      );

      return false;
    }

    /*
     * Pump the dummy unit data into the unit model...
     */
    $data['property_id'] = $id;

    if (!$model->save($data)) {

      // Set an error based on like what happened...
      $this->setError(JText::_('COM_RENTAL_HELLOWORLD_CREATE_NEW_PROPERTY_FAILURE'));
      $this->setMessage($this->getError(), 'error');

      // Redirect to the edit screen.
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option . '&view=' . $this->view_list
                      . $this->getRedirectToListAppend(), false
              )
      );

      return false;
    }
    $unit_id = $model->getState($model->getName() . '.id');

    // Set a message indicating success...
    $this->setError(JText::_('COM_RENTAL_HELLOWORLD_CREATE_NEW_UNIT_SUCCESS'));
    $this->setMessage($this->getError(), 'message');

    $this->holdEditId('com_rental.view.listing', $id);

    $unit_id = $model->getState($model->getName() . '.unit_id');

    // Redirect to the edit screen.
    $this->setRedirect(
            JRoute::_(
                    'index.php?option=com_rental&task=unitversions.edit&unit_id=' . (int) $unit_id, false
            )
    );

    return true;
  }

  public function postSaveHook(\JModelLegacy $model, $validData = array()) {

    // Get the contents of the request data
    $input = JFactory::getApplication()->input;
    // If the task is save and next
    if ($this->task == 'saveandnext') {
      // Check if we have a next field in the request data
      $next = $input->get('next', '', 'base64');
      // And set the redirect if we have
      if ($next) {
        $this->setRedirect(base64_decode($next));
      }
    }
  }

}
