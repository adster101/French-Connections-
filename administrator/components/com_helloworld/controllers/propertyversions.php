<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class HelloWorldControllerPropertyVersions extends JControllerForm {

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


        // Set the view list - applies when saving rather than 'inflecting' the list view
        $this->view_list = 'listing';
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
        
        // Redirect to the edit screen.
        $this->setRedirect(
                JRoute::_(
                        'index.php?option=com_helloworld&task=propertyversions.edit&property_id=' . (int) $id, false
                )
        );

        return true;
    }

}
