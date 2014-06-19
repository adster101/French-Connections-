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

class TicketsController extends JControllerLegacy
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
    $user = JFactory::getUser();
    $input = JFactory::getApplication()->input;
    $option = $input->getCmd('option', 'com_tickets');
    $view = $input->getCmd('view', 'tickets');
    
    JFactory::getApplication()->input->set('view', $view);
    
    if ($user->authorise($option . '.view.' . $view, $option))
    {
      parent::display($cachable, $urlparams);
    }
    else
    {
      $this->setRedirect('/');
      $this->redirect();
    }

		return $this;
	}
}
