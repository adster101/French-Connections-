<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('frenchconnections.controllers.property.base');

/**
 * HelloWorld Controller
 */
class RentalControllerTariffs extends RentalControllerBase
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
    $this->registerTask('saveandnext', 'save');
    $this->registerTask('copy', 'save');
  }

  /*
   * Augmented getRedirectToItemAppend so we can append the property_id onto the url
   * MAkes more sense to override this than the individual save/edit methods
   *
   */

  public function getRedirectToListAppend($recordId = null, $urlVar = 'id')
  {

    // Get the default append string
    $append = '';

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
    if ($id > 0)
    {
      $append .= '&view=listing&id=' . $id;
    }

    return $append;
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
  }

  public function cancel($key = null)
  {

    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    // Get the property ID from the form data and redirect 
    $input = JFactory::getApplication()->input;

    $data = $input->get('jform', array(), 'array');

    $property_id = $data['property_id'];    $context = "$this->option.edit.$this->context";

    // Clean the session data and redirect.
    $this->releaseEditId($context, $property_id);
    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&task=listing.view&id=' . (int) $property_id, false
            )
    );

    return true;
  }
}
