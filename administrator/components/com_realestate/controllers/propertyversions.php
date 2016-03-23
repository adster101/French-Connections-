<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorld Controller
 */
class RealEstateControllerPropertyVersions extends JControllerForm
{

    var $view_list = 'listings';

    /**
     * Checks whether the owner is allowed to edit this property version via the PropertyHelper class
     *
     * @param type $data
     * @param type $key
     * @return type boolean
     */
    public function allowEdit($data = array())
    {
        $input = JFactory::getApplication()->input;

        // Get the property id we're trying to edit
        $id = (!empty($data['realestate_property_id'])) ? $data['realestate_property_id'] : $input->get('id', '', 'int');

        // Test whether this user is allowed to edit it.
        return PropertyHelper::allowEditRealestate($id);
    }

    public function __construct($config = array())
    {

        parent::__construct($config);
        $this->registerTask('saveandnext', 'save');
    }

    /**
     * Method to add a new property listing. Runs code to add a new property, property version, unit and unit version
     *
     * @return  mixed  True if the record can be added, a error object if not.
     *
     * @since   12.2
     */
    public function add()
    {
        $app = JFactory::getApplication();
        $context = "$this->option.edit.$this->context";
        // Get an instance of the property versions model
        $model = $this->getModel('PropertyVersions');
        // Get the table associated with the model
        $table = $model->getTable();
        // Get a list of the fields associated with the table
        $data = $table->getProperties();

        // Access check. Check that we are allowed to add a realestate property
        if (!$this->allowAdd())
        {
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
        if (!$model->save($data))
        {

            // Set an error based on like what happened...
            $this->setError(JText::_('COM_RENTAL_HELLOWORLD_CREATE_NEW_PROPERTY_FAILURE'));
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

        // Set a message indicating success...
        $this->setMessage(JText::_('COM_REALESTATE_NEW_PROPERTY_CREATED_SUCCESS'));

        $this->holdEditId('com_realestate.edit.listing', $id);

        // Redirect to the edit screen.
        $this->setRedirect(
                JRoute::_(
                        'index.php?option=com_realestate&task=propertyversions.edit&realestate_property_id=' . (int) $id, false
                )
        );

        return true;
    }

    public function postSaveHook(\JModelLegacy $model, $validData = array())
    {

        // Get the contents of the request data
        $input = JFactory::getApplication()->input;
        // If the task is save and next
        if ($this->task == 'saveandnext')
        {
            // Check if we have a next field in the request data
            $next = $input->get('next', '', 'base64');
            // And set the redirect if we have
            if ($next)
            {
                $this->setRedirect(base64_decode($next));
            }
        }

        // If the property needs to be submitted for review redirect to the
        // submit for review screen when the user hits 'saveandclose'
        if ($validData['review'] && $this->task == 'save')
        {
            $this->setRedirect('index.php?option=com_realestate&task=listing.view&id=' . $validData['realestate_property_id']);
        }
    }
}
