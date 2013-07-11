<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class HelloWorldControllerUnitVersions extends JControllerForm {

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

    // Set the view list - applies when saving/cancelling rather than 'inflecting' the list view
    $this->view_list = 'listing';
    
    // Assign the tariffs task to run the edit method.
    // This redirects to the tariffs layout of the unitversions view
    $this->registerTask('tariffs', 'edit');
    $this->registerTask('images', 'edit');
    
  }

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
    if ($user->authorise('core.edit', $this->extension)) {
      return true;
    }

    // Fallback on edit.own.
    // First test if the permission is available.
    if ($user->authorise('core.edit.own', $this->extension)) {

      // Now test the owner is the user.
      $ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;
      if (empty($ownerId) && $recordId) {
        // Need to do a lookup from the model.
        // TO DO: Needs to be a more generic getOwnerID method which
        // pull the owner id from the the property table.
        // Therefore the user id in the unit tables is purely informational (and not really needed).
        $record = $this->getModel()->getItem($recordId);

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
   * Augmented getRedirectToItemAppend so we can append the parent_id onto the url
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
        $recordId = JFactory::getApplication()->input->get('parent_id', 0, 'int');
        $urlVar = '&parent_id=';
        if ($recordId) {
          $append .= $urlVar . $recordId;
        }
        break;
      case 'apply':
      case 'save':
        // Derive the parent id from the form data
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');
        $recordId = $data['parent_id'];
        $urlVar = '&parent_id=';
        break;
      case 'reviews':
        $append = '';
        if ($recordId) {
          $append .= '&' . $urlVar . '=' . $recordId;
        }
        break;
      case 'tariffs':
        $layout = $this->input->get('layout', 'tariffs');
        $append = '';
        $append.= '&layout=' . $layout . '&' . $urlVar . '=' . $recordId;
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
   * Augmented getRedirectToItemAppend so we can append the parent_id onto the url
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
        
        $id = $data['parent_id'];

        break;


    endswitch;

    // If parent ID is set in form data also append to the url
    if ($id > 0) {
      $append .= '&id=' . $id;
    }

    return $append;
  }


}
