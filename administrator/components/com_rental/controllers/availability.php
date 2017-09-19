<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
//jimport('joomla.application.component.controllerform');
jimport('frenchconnections.controllers.property.base');

/**
 * HelloWorld Controller
 */
class RentalControllerAvailability extends RentalControllerBase
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

  public function manage()
  {

    // Check that this is a valid call from a logged in user.
    //JSession::checkToken('GET') or die('Invalid Token');
    // $id is the listing the user is trying to edit
    $id = $this->input->get('unit_id', '', 'int');

    $data['id'] = $id;

    if (!$this->allowEdit($data, 'id'))
    {
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option, false)
      );

      $this->setMessage('There was a problem fetching availability for this property.', 'error');

      return false;
    }


    $this->holdEditId('com_rental.edit.availability', $id);

    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=availability&unit_id=' . (int) $id, false)
    );
    return true;
  }

  public function saveandnext()
  {

    // Get the contents of the request data
    $input = JFactory::getApplication()->input;
    // If the task is save and next
    if ($this->task == 'saveandnext')
    {
      // Check if we have a next field in the request data
      $next = $input->get('next', '', 'base64');
      $url = base64_decode($next);
      // And set the redirect if we have
      if ($next)
      {
        $this->setRedirect(base64_decode($next));
      }
    }
    return true;
  }

  public function cancel($key = null)
  {

    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    // Get the property ID from the form data and redirect 
    $input = JFactory::getApplication()->input;

    $data = $input->get('jform', array(), 'array');

    $property_id = $data['property_id'];

    // Clean the session data and redirect.
    $this->releaseEditId('com_rental.edit.unitversions', (int) $property_id);

    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&task=listing.view&id=' . (int) $property_id, false
            )
    );

    return true;
  }

}
