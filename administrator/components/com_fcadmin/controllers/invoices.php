<?php

/**
 * @version     1.0.0
 * @package     com_invoices
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */
// No direct access.
defined('_JEXEC') or die;

// Extend this from JControllerLegacy?
jimport('joomla.application.component.controlleradmin');

/**
 * Invoices list controller class.
 */
class FcadminControllerInvoices extends JControllerLegacy
{

  protected $default_view = 'invoices';

  /**
   * Constructor.
   *
   * @param   array  $config  An optional associative array of configuration settings.
   *
   * @see     JControllerLegacy
   * @since   12.2
   * @throws  Exception
   */
  public function __construct($config = array())
  {
    parent::__construct($config);

    // Guess the option as com_NameOfController
    if (empty($this->option))
    {
      $this->option = 'com_' . strtolower($this->getName());
    }
  }

  /**
   * Method to import a list of invoices from a tab separated file
   * Includes an auth check and calls the relevant model to perform the actual import.
   *  
   * @return boolean
   */
  public function import()
  {
    JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

    $params = JComponentHelper::getParams('com_fcadmin');
    $data = $this->input->files->get('jform', '', 'array');
    $file = $data['invoices'];
    $model = $this->getModel('invoices');
    $user = JFactory::getUser();

    // Check the auth permissions for creating invoices
    if (!$user->authorise('core.create', 'com_invocies'))
    {
      return false;
    }

    // Total length of post back data in bytes.
    $contentLength = (int) $_SERVER['CONTENT_LENGTH'];

    // Maximum allowed size of post back data in MB.
    $postMaxSize = (int) ini_get('post_max_size');

    // Maximum allowed size of script execution in MB.
    $memoryLimit = (int) ini_get('memory_limit');

    // Check for the total size of post back data.
    if (($postMaxSize > 0 && $contentLength > $postMaxSize * 1024 * 1024) || ($memoryLimit != -1 && $contentLength > $memoryLimit * 1024 * 1024))
    {
      JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_WARNUPLOADTOOLARGE'));
      return false;
    }

    $uploadMaxSize = $params->get('upload_maxsize', 0) * 1024 * 1024;

    if (($file['error'] == 1) || ($uploadMaxSize > 0 && $file['size'] > $uploadMaxSize))
    {
      // File size exceed either 'upload_max_filesize' or 'upload_maxsize'.
      JError::raiseWarning(100, JText::_('COM_INVOICES_ERROR_WARNFILETOOLARGE'));
      return false;
    }

    // Call media helper so we can use canUpload to check whether the upload is palatable or not.
    $mediaHelper = new JHelperMedia;

    if (!$mediaHelper->canUpload($file, 'com_fcadmin'))
    {
      return false;
    }

    // Try and import the invoices using the invoices model
    if (!$model->import($file))
    {
      $this->setRedirect('index.php?option=' . $this->option . '&tmpl=component');
      return false;
    }

    
    $this->setRedirect('index.php?option=' . $this->option . '&view=' . $this->default_view . '&tmpl=component');
    return true;
  }

}