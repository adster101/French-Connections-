<?php

/**
 * @version     1.0.0
 * @package     com_invoices
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */
// No direct access
defined('_JEXEC') or die;

class InvoicesController extends JControllerLegacy
{

  /**
   * Method to display a view.
   *
   * @param	boolean			$cachable	If true, the view output will be cached
   * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
   *
   * @return	JController		This object to support chaining.
   * @since	1.5
   */
  public function display($cachable = false, $urlparams = false)
  {
    require_once JPATH_COMPONENT . '/helpers/invoices.php';

    // Get the GET params for this view
    $view = JFactory::getApplication()->input->getCmd('view', 'invoices');
    JFactory::getApplication()->input->set('view', $view);
    $user = JFactory::getUser();

    $option = $this->input->getCmd('option', 'com_invoices');

    // Basic check to ensure user is allowed to access this view.
    if ($view == 'import' && !$user->authorise('com_invoices.import.view', $option))
    {
      $this->setRedirect('index.php');
      return false;
    }





    parent::display($cachable, $urlparams);

    return $this;
  }

}
