<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * Classifications Controller
 */
class ClassificationControllerClassifications extends JControllerAdmin {

  /**
   * Proxy for getModel.
   * @since	1.6
   */
  public function getModel($name = 'Classification', $prefix = 'ClassificationModel') {
    $model = parent::getModel($name, $prefix, array('ignore_request' => true));
    return $model;
  }

  /**
   * Rebuild the nested set tree.
   *
   * @return  bool  False on failure or error, true on success.
   *
   * @since   1.6
   */
  public function rebuild() {
    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    $this->setRedirect(JRoute::_('index.php?option=com_classification', false));

    $model = $this->getModel();

    if ($model->rebuild()) {
      // Rebuild succeeded.
      $this->setMessage(JText::_('COM_CLASSIFICATIONS_REBUILD_SUCCESS'));
      return true;
    } else {
      // Rebuild failed.
      $this->setMessage(JText::_('COM_CLASSIFICATIONS_REBUILD_FAILURE'));
      return false;
    }
  }

}