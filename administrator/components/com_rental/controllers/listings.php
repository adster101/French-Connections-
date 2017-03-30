<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * HelloWorlds Controller
 */
class RentalControllerListings extends JControllerAdmin
{

  /**
   * Proxy for getModel.
   * @since	1.6
   */
  public function getModel($name = 'Property', $prefix = 'RentalModel')
  {
    $model = parent::getModel($name, $prefix, array('ignore_request' => true));
    return $model;
  }

  /*
   *
   */

  public function submit()
  {
    $this->setMessage('Submitted for review', 'notice');
    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option, false)
    );
  }

  public function admin()
  {
    // Get items to publish from the request.
    $cid = JFactory::getApplication()->input->get('cid', array(), 'array');
    $task = $this->getTask();

    // Get property model and update snooze date/status
    // Get note model and save notes field

    if (empty($cid))
    {
      JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
    }
    else
    {

      // Make sure the item ids are integers
      JArrayHelper::toInteger($cid);

      $id = ($cid[0]) ? $cid[0] : '';
      
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option . '&view=' . $task . '&id=' . (int) $id, false)
      );
    }
   
    return true;
  }

}
