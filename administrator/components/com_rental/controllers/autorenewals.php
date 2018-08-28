<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class RentalControllerAutoRenewals extends JControllerAdmin
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
      $this->extension = JRequest::getCmd('extension', 'com_rental');
    }
  }

  /**
	 * Function that allows child controller access to model data
	 * after the item has been deleted.
	 *
	 * @param   \JModelLegacy  $model  The data model object.
	 * @param   integer        $id     The validated data.
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	protected function postDeleteHook(\JModelLegacy $model, $id = null)
	{
    $app = JFactory::getApplication();

    // Get the contents of the request data
    $input = $app->input;
    $id = $input->get('id', 'int');

    $app->enqueueMessage(JText::_('COM_CONFIG_SAVE_SUCCESS'), 'message');

    $app->redirect('index.php?option=com_rental&view=autorenewals&id=' . $id);

	}

  /**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'AutoRenewal', $prefix = 'RentalModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
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
  protected function allowEdit($data = array(), $key = 'id')
  {

    // Initialise variables.
    $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
    $user = JFactory::getUser();
    $userId = $user->get('id');

    // This covers the case where the user is creating a new property (i.e. id is 0 or not set
    if ($recordId === 0 && $user->authorise('core.edit.own', $this->extension))
    {
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
        $record = $this->getModel('Property')->getItem($recordId);
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

  /*
   * Autorenewal controller action - checks ownership of record and redirects to listing view
   *
   */

  public function showtransactionlist()
  {

    $app = JFactory::getApplication();

    $context = "$this->option.edit.$this->view_list";

    // Determine the name of the primary key for the data.
    if (empty($key))
    {
      $key = 'id';
    }

    $recordId = $this->input->get('id', '', 'int');

    if (!$this->allowEdit(array($key => $recordId), $key))
    {
      $this->setError(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
      $this->setMessage($this->getError(), 'error');
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option, false)
      );

      return false;
    }

    // Set holdEditID etc
    $this->holdEditId($context, $recordId);
    //$app->setUserState($context . '.data', null);

    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=autorenewals&id=' . (int) $recordId, false)
    );

    return true;
  }

  public function postSaveHook(JModelLegacy $model, $validData = array())
  {

    $this->setMessage(
            JText::sprintf('COM_RENTAL_HELLOWORLD_UPDATED_AUTORENEWAL_DETAILS', $validData['id'])
    );

    // Redirect to the list screen as there is no autorenewals list view...
    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option, false
            )
    );
  }

  public function setDefault()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$cid = $this->input->get('cid', '');
    $id = $this->input->get('id');

    $data = array('id'=> $id, 'VendorTxCode' => $cid[0]);

		$model = $this->getModel('autorenewal');

		if ($model->save($data))
		{

			$msg = JText::_('COM_LANGUAGES_MSG_DEFAULT_LANGUAGE_SAVED');
			$type = 'message';
		}
		else
		{
			$msg = $this->getError();
			$type = 'error';
		}

    $this->setMessage(
            JText::sprintf('COM_RENTAL_HELLOWORLD_UPDATED_AUTORENEWAL_DETAILS', $id)
    );

		$this->setredirect('index.php?option=com_rental&view=autorenewals&id=' . (int) $id);
	}


    public function unsetDefault()
  	{
  		// Check for request forgeries.
  		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

  		$cid = $this->input->get('cid', '');
      $id = $this->input->get('id');

      $data = array('id'=> $id, 'VendorTxCode' => '');

  		$model = $this->getModel('autorenewal');

  		if ($model->save($data))
  		{

  			$msg = JText::_('COM_LANGUAGES_MSG_DEFAULT_LANGUAGE_SAVED');
  			$type = 'message';
  		}
  		else
  		{
  			$msg = $this->getError();
  			$type = 'error';
  		}

      $this->setMessage(
              JText::sprintf('COM_RENTAL_HELLOWORLD_UPDATED_AUTORENEWAL_DETAILS', $id)
      );

  		$this->setredirect('index.php?option=com_rental&view=autorenewals&id=' . (int) $id);
  	}


}
