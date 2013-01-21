<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
 
/**
 * HelloWorld Controller
 */
class HelloWorldControllerHelloWorld extends JControllerForm
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
			$this->extension = JRequest::getCmd('extension', 'com_helloworld');
		}
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
    if ($recordId === 0 && $user->authorise('core.edit.own', $this->extension)) {
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
				$record = $this->getModel()->getItem($recordId);

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
  
  
  
  public function woot() {
    
    $parent_id = 1;
    
    // Check that this is a valid call from a logged in user.
    JSession::checkToken( 'GET' ) or die( 'Invalid Token' );
    
    $data = JRequest::getVar( 'jform', '', 'POST', 'array' ); 
    
    if (array_key_exists('parent_id', $data)) {
      $parent_id = $data['parent_id'];
      JApplication::setUserState('parent_id', $data['parent_id']);
    }
    
    
    
    $app = JFactory::getApplication();  
    if ($parent_id === 1) {

      $app->enqueueMessage(JText::_('COM_HELLOWORLD_HELLOWORLD_NEW_PROPERTY_TO_BE_ADDED'), 'warning');
    } else {
      $app->enqueueMessage(JText::_('COM_HELLOWORLD_HELLOWORLD_NEW_UNIT_TO_BE_ADDED'), 'warning');

    }

    $this->setRedirect(JRoute::_('index.php?option=com_helloworld&task=helloworld.edit', false));

  }
  
  
  
 
}
