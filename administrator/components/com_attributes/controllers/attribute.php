<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class AttributesControllerAttribute extends JControllerForm {

  public function language() {
    $id = JRequest::getInt('id');
    JApplication::setUserState('com_attributes.edit.lang', JRequest::getVar('Language'));
    $view = JRequest::getVar('view');
    $this->setRedirect('index.php?option=com_attributes&task=' . $view . '.edit&id=' . $id);
  }

}
