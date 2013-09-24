<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('frenchconnections.controllers.property.base');

/**
 * HelloWorld Controller
 */
class HelloWorldControllerPropertyVersions extends HelloWorldControllerBase {
 

  public function cancel($key = null) {
    parent::cancel($key);
    
    $id = JFactory::getApplication()->input->get('property_id','','int');
    
    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=listing&id=' . (int) $id, false
            )
    );
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

  /**
   * Method to add a new property listing. Runs code to add a new property, property version, unit and unit version
   *
   * @return  mixed  True if the record can be added, a error object if not.
   *
   * @since   12.2
   */
  public function add() {
    $app = JFactory::getApplication();
    $context = "$this->option.edit.$this->context";
    $model = $this->getModel('PropertyVersions');
    $table = $model->getTable();

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
     * Pump the dummy data into propertyversions model. 
     * 
     */
    if (!$model->save($data)) {

      // Set an error based on like what happened...
      $this->setError(JText::_('COM_HELLOWORLD_HELLOWORLD_CREATE_NEW_PROPERTY_FAILURE'));
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
     * Get the id of the newly created listing
     */

    $id = $model->getState($model->getName() . '.id');

    /*
     * Get the model and table properties for a unit
     */
    $model = $this->getModel('UnitVersions');

    $table = $model->getTable();

    $data = $table->getProperties();

    /*
     * Pump the dummy unit data into the unit model...
     */
    $data['property_id'] = $id;

    if (!$model->save($data)) {

      // Set an error based on like what happened...
      $this->setError(JText::_('COM_HELLOWORLD_HELLOWORLD_CREATE_NEW_PROPERTY_FAILURE'));
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

    // Set a message indicating success...
    $this->setError(JText::_('COM_HELLOWORLD_HELLOWORLD_CREATE_NEW_PROPERTY_SUCCESS'));
    $this->setMessage($this->getError(), 'message');

    $this->holdEditId('com_helloworld.view.listing', $id);

    // Redirect to the edit screen.
    $this->setRedirect(
            JRoute::_(
                    'index.php?option=com_helloworld&task=propertyversions.edit&property_id=' . (int) $id, false
            )
    );

    return true;
  }

  public function saveandnext($key = null, $urlVar = null) {

    $return = parent::save($key, $urlVar);

    $id = JFactory::getApplication()->input->get('property_id','','int');
    
    if ($return && (int) $id && $id > 0) {

      // Derive the first unit for this property and redirect
      // getDefaultUnit - useful method for a property view also
      // Get unit where ordering = 1 for this property id
      // ensure that it is the latest version though, unless doing a preview

      
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=com_helloworld&task=unitversions.edit&unit_id=' . (int) $unit_id, false
              )
      );
    }

    return $return;
  }

}
