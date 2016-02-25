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
class RealestateViewListings extends JViewLegacy
{

  /**
   * Display the view
   */
  public function display($tpl = null)
  {
    $this->get('State');
    $content = $this->get('Content');

    // Check for errors.
    if (count($errors = $this->get('Errors')))
    {
      throw new Exception(implode("\n", $errors));
    }

    $document = JFactory::getDocument();
    $document->setMimeEncoding('text/csv');
    
    JFactory::getApplication()->setHeader('Content-disposition', 'attachment; filename="realestate-listings-' . JFactory::getDate()->calendar('d-m-Y') .  '.csv"; creation-date="' . JFactory::getDate()->toRFC822() . '"', true);
    echo $content;
  }

}

