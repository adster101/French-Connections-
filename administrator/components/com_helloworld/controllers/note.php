<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class HelloWorldControllerNote extends JControllerForm {

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

    $this->view_list = 'listings';
  }


  /**
   * Method to check if you can update the snooze status for a property...
   *
   * @param   array   $data  An array of input data.
   * @param   string  $key   The name of the key for the primary key.
   *
   * @return  boolean
   *
   * @since   1.6
   */
  protected function allowAdd($data = array(), $key = 'id') {
    return JFactory::getUser()->authorise('helloworld.notes.add', $this->extension);
  }

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $key       The name of the primary key variable.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   2.5
	 */
	protected function getRedirectToItemAppend($recordId = null, $key = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId, $key);

		$userId = JFactory::getApplication()->input->get('property_id', 0, 'int');
		if ($userId)
		{
			$append .= '&property_id=' . $userId;
		}

		return $append;
	}

}
