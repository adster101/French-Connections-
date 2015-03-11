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

jimport('joomla.application.component.view');

/**
 * View class for a list of Invoices.
 */
class FcadminViewNoavailability extends JViewLegacy
{

  /**
   * Display the view
   */
  public function display($tpl = null)
  {

    $content = $this->get('Content');
		$mimetype		= $this->get('MimeType');

    // Check for errors.
    if (count($errors = $this->get('Errors')))
    {
      throw new Exception(implode("\n", $errors));
    }

    $document = JFactory::getDocument();
    $document->setMimeEncoding('text/csv');
    
    JFactory::getApplication()->setHeader('Content-disposition', 'attachment; filename="properties-with-no-availability-' . JFactory::getDate()->calendar('d-m-Y') .  '.txt"; creation-date="' . JFactory::getDate()->toRFC822() . '"', true);
    echo $content;
  }

}

