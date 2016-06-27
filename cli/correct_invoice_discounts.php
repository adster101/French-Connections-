<?php

/**
 * @package    Joomla.Cli
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// Initialize Joomla framework
        const _JEXEC = 1;

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php')) {
    require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', dirname(__DIR__));
    require_once JPATH_BASE . '/includes/defines.php';
}

// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

// Import the configuration.
require_once JPATH_CONFIGURATION . '/configuration.php';


class UpdateInvoices extends JApplicationCli {

  /**
   * Entry point for the script
   *
   * @return  void
   *
   * @since   2.5
   */

   public $item_costs = array('1007-005', '1007-010', '1007-014', '1007-015', '1007-020', '1007-025', '1007-050');

  public function doExecute()
  {
    $invoices = $this->getInvoices();

    foreach ($invoices as $invoice)
    {
      // Select line from invoice which has discount
      $invoice_line = $this->getInvoiceLine($invoice->id);

      $this->out('Processing ' . $invoice->id);

      // For a discount the vat should be a negative float
      // as we're discounting the whole of the order.
      if ($invoice_line->vat > 0)
      {

        // Correct the sign of the line total net and vat
        $total_net = (double) ($invoice_line->total_net * -1);
        $vat = (double) ($invoice_line->vat * -1);

        // Update value in the database
        $update_line = $this->updateInvoiceLine((double) $total_net, (double) $vat, $invoice_line->id);

        // Determine the correct vat applied to the order
        // Based on the total net cost multiplied by VAT rate
        $total_vat = $invoice->total_net * 0.2;

        // Update total vat on invoice
        $update_invoice = $this->updateInvoice($total_vat, $invoice->id);
      }

    }

    $this->out("Done!");

  }

  public function getInvoices($item_costs = array())
  {

    $db = JFactory::getDBO();

    $query = $db->getQuery(true);
    $sub_query = $db->getQuery(true);

    $sub_query->select('invoice_id')
              ->from('#__invoice_lines')
              ->where('item_code in (' . "'" . implode("','", $this->item_costs) . "'" . ')');

    $query->select('id, total_net')
            ->from('#__invoices')
            ->where('id in ( ' . $sub_query->__toString() . ')');

    $db->setQuery($query);

    $invoices = $db->loadObjectList();

    return $invoices;
  }

  public function getInvoiceLine($id = '')
  {
    $db = JFactory::getDBO();

    $query = $db->getQuery(true);

    $query->select('*')
            ->from('#__invoice_lines')
            ->where('item_code in (' . "'" . implode("','", $this->item_costs) . "'" . ')')
            ->where('invoice_id = ' . $id);

    $db->setQuery($query);

    $invoice_line = $db->loadObject();

    return $invoice_line;
  }

  public function updateInvoiceLine($total_net, $vat, $id = '')
  {

    $db = JFactory::getDBO();

    $query = $db->getQuery(true);

    $query->update('#__invoice_lines');
    $query->set('total_net =' . $total_net . ', vat = ' . $vat);
    $query->where('id = ' . $id);

    $db->setQuery($query);

    $invoice_line = $db->execute();
  }

  public function UpdateInvoice($total_vat = '', $invoice_id = '')
  {
    $db = JFactory::getDBO();

    $query = $db->getQuery(true);

    $query->update('#__invoices');
    $query->set('vat = ' . $total_vat);
    $query->where('id = ' . $invoice_id);

    $db->setQuery($query);

    $invoice_line = $db->execute();
  }
}

JApplicationCli::getInstance('UpdateInvoices')->execute();
