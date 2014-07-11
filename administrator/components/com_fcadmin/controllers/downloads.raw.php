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
class FcadminControllerDownloads extends JControllerAdmin
{

  public function usercards()
  {
    // Set the view to the raw view
    $view = $this->getView('usercards', 'raw');

    // Get the model for the view.
    $model = $this->getModel('usercards');

    // Push the model into the view (as default).
    $view->setModel($model, true);

    $view->display();
  }

  public function jobfiles()
  {
    // Set the view to the raw view
    $view = $this->getView('jobfile', 'raw');

    // Get the model for the view.
    $model = $this->getModel('jobfile');

    // Push the model into the view (as default).
    $view->setModel($model, true);

    $view->display();
  }

}