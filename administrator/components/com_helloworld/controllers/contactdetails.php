<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('frenchconnections.controllers.property.base');


// TO DO - extend this controller and other that extend from controller form
// from another generic class which contain canEdit instead of defining in each controller
// Or simply import a utility class with this and other useful methods in
// from the libraries folder

/**
 * HelloWorld Controller
 */
class HelloWorldControllerContactDetails extends HelloWorldControllerBase {

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
  }

  public function postSaveHook(\JModelLegacy $model, $validData = array()) {

    $task = $this->getTask();

    switch ($task) :
      case 'save':
        $this->setRedirect(
                JRoute::_(
                        'index.php?option=' . $this->option . '&view=listing&id=' . $validData['property_id'], false
                )
        );
        break;
      case 'apply':
        $this->setRedirect(
                JRoute::_(
                        'index.php?option=' . $this->option . '&view=' . $this->view_item . '&layout=edit&property_id=' . $validData['property_id'], false
                )
        );
        break;

    endswitch;
    
  }

}

