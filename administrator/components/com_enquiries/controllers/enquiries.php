<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * Classifications Controller
 */
class EnquiriesControllerEnquiries extends JControllerAdmin
{

  /**
   * Proxy for getModel.
   * @since	1.6
   */
  public function getModel($name = 'Enquiry', $prefix = 'EnquiriesModel')
  {
    $model = parent::getModel($name, $prefix, array('ignore_request' => true));
    return $model;
  }

  /**
   * This controller action resends each email that is marked 
   * 
   */
  public function resend()
  {
    // Check for request forgeries
    JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

    // Get items to publish from the request.
    $cid = JFactory::getApplication()->input->get('cid', array(), 'array');

    if (empty($cid))
    {
      JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
    }
    else
    {
      // Get the model.
      $model = $this->getModel();

      // Make sure the item ids are integers
      JArrayHelper::toInteger($cid);

      // Process the items...
      try {
        // Pass the data into the model for processing
        $model->processFailedEnquiries($cid);

        $ntext = $this->text_prefix . '_N_ITEMS_PROCESSED';
        $this->setMessage(JText::plural($ntext, count($cid)));
      } catch (Exception $e) {
        $this->setMessage(JText::_('JLIB_DATABASE_ERROR_ANCESTOR_NODES_LOWER_STATE'), 'error');
      }
    }
    $extension = $this->input->get('extension');
    $extensionURL = ($extension) ? '&extension=' . $extension : '';
    $this->setRedirect(JRoute::_('/administrator/index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false));
  
    return true;
    
  }

}
