<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modelform');

class FcadminModelInvoices extends JModelForm
{

  /**
   * Method to get the menu item form.
   *
   * @param   array      $data        Data for the form.
   * @param   boolean    $loadData    True if the form is to load its own data (default case), false if not.
   * @return  JForm    A JForm object on success, false on failure
   * @since   1.6
   */
  public function getForm($data = array(), $loadData = false)
  {
    // Get the form.
    $form = $this->loadForm('com_invoices.importinvoices', 'importinvoices', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form))
    {
      return false;
    }

    return $form;
  }

  public function import($file = array())
  {

    // Set up variables
    $db = JFactory::getDbo();

    // Include the invoice table path
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_invoices/tables');

    // Get the invoice table
    $table = $this->getTable('Invoice', 'InvoicesTable');

    // Get the invoice lines table
    $table_invoice_lines = $this->getTable('Invoice_lines', 'InvoicesTable');

    // Get a logger instance so we can add log out if there is a problem or warning.
    JLog::addLogger(array('text_file' => 'invoices.import.php'), JLog::ALL, array('invoices_import'));

    // Get an instance of the application, so we can queue up messages
    $app = JFactory::getApplication();

    $warnings = array();

    try {

      // This reads the uploaded invoice file and processed it into an array keyed by invoice ID
      $invoices = $this->processFile($file);

      // Start a transaction
      $db->transactionStart();

      // Essentially just need to insert this lot into the db now.
      foreach ($invoices as $invoice)
      {

        // Do an upfront check of whether the invoice already exists or not...need to do this before
        // invoice data is bound to the table object...
        if (!$table->load($invoice['id']))
        {
          // Do something?
        }

        if (!empty($table->id))
        {
          $message = 'Ignoring import of invoice which already exists ' . (int) $invoice['id'] . ' for user ' . (int) $invoice['user_id'];
          JLog::add($message, JLog::WARNING, 'invoices_import');
          $warnings[] = $message;
          continue;
        }


        if (!$table->save($invoice))
        {
          $message = $table->getError();
          //Throw new Exception($message);
          JLog::add($message, JLog::WARNING, 'invoices_import');
        }

        // Proceed and save the invoice line...
        foreach ($invoice['lines'] as $line)
        {
          if (!$table_invoice_lines->save($line))
          {
            $message = JText::sprintf('COM_INVOICE_LINE_IMPORT_PROBLEM', $invoice['id']);
            Throw new Exception($message);
          }
          $table_invoice_lines->reset();
        }

        // Add the discount line
        if (!empty($invoice['discount']))
        {
          $line = $invoice['discount'];

          if (!$table_invoice_lines->save($line))
          {
            $message = JText::sprintf('COM_INVOICE_LINE_IMPORT_PROBLEM', $invoice['id']);
            Throw new Exception($message);
          }
          $table_invoice_lines->reset();
        }

        $table->reset();
      }
    }
    catch (Exception $e) {
      $db->transactionRollback();
      // Add the error message back to the message queue so we know what's causing the problem...
      $app->enqueueMessage($e->getMessage(), 'error');
      // Bail
      return false;
    }

    // Commit the transactions, go and have a cup of tea
    $db->transactionCommit();

    $message = JText::_('COM_FCADMIN_INVOICE_IMPORT_SUCESS');
    $app->enqueueMessage($message, 'success');

    if (!empty($warnings))
    {
      $message = implode('<br />', $warnings);
      $app->enqueueMessage($message, 'warning');
    }

    return true;
  }

  public function processFile($file)
  {
    $filter = new JFilterInput;
    $net_total = '';
    $vat_total = '';
    $invoices = array();
    $handle = fopen($file['tmp_name'], 'r');
    // Do...invoice...while...we have invoices
    while (($line = fgetcsv($handle, 0, ",")) !== FALSE)
    {

      // If invoice number not set then continue
      if (empty($line[6]))
      {
        continue;
      }

      //iconv("UTF-8", "ASCII//IGNORE", $line[24]) Removes odd unicode characters from the MYOB file
      $invoice_id = (int) $line[9];

      $description = $line[12];
      $quantity = $line[17];
      $item_cost = $filter->clean($line[18], 'float');

      //$net_line_total = $filter->clean($line[16], 'float');
      $vat_line = $filter->clean($line[16], 'float');
      $user_id = $filter->clean($line[0], 'int');
      $date_created = JFactory::getDate(str_replace('/', '-', $line[6]))->calendar('Y-m-d');

      $first_name = '';
      //$delivery_date = (!empty($line[6])) ? JFactory::getDate(str_replace('/', '-',$line[6]))->calendar('Y-m-d') : null;
      $surname = $filter->clean($line[11], 'string');
      $address1 = $filter->clean($line[1], 'string');
      $address2 = $filter->clean($line[2], 'string');
      $address3 = '';
      $town = $filter->clean($line[3], 'string');
      $county = $filter->clean($line[4], 'string');
      $postcode = $filter->clean($line[5], 'string');

      //$town = $filter->clean($line[5], 'string');

      // Get the user address details from the user profile details
      $address = $this->getAddress($user_id);

      // Add this invoice to the array if it's not already present
      if (!array_key_exists($invoice_id, $invoices))
      {
        $invoices[$invoice_id] = array();
        $invoices[$invoice_id]['id'] = $invoice_id;
        $invoices[$invoice_id]['user_id'] = $user_id;
        $invoices[$invoice_id]['date_created'] = $date_created;

        $invoices[$invoice_id]['total_net'] = $filter->clean($line[8], 'float');;
        $invoices[$invoice_id]['vat'] = $filter->clean($line[10], 'float');'';
        $invoices[$invoice_id]['state'] = '1';

        $invoices[$invoice_id]['first_name'] = $first_name;
        $invoices[$invoice_id]['surname'] = $surname;
        $invoices[$invoice_id]['address1'] = $address1;
        $invoices[$invoice_id]['address2'] = (!empty($address2)) ? $address2 : '';
        $invoices[$invoice_id]['address3'] = '';
        $invoices[$invoice_id]['town'] = $town;
        $invoices[$invoice_id]['county'] = $county;
        $invoices[$invoice_id]['postcode'] = $postcode;
      }

      // Generate a new line for this invoice
      $invoice_line = array();
      $invoice_line['invoice_id'] = $invoice_id;
      //$invoice_line['vat_status'] = $vat_status;
      //$invoice_line['item_code'] = $item_code;
      $invoice_line['item_description'] = $description;
      $invoice_line['quantity'] = $quantity;
      $invoice_line['total_net'] = $item_cost;
      $invoice_line['vat'] = $vat_line;
      $invoices[$invoice_id]['lines'][] = $invoice_line;

      // Generate a discount line, if one has been applied...
      if (round(($line[19])) && round(($line[20])) and !array_key_exists('discount', $invoices[$invoice_id]))
      {
        $discount_line = array();
        $discount_line['invoice_id'] = $invoice_id;
        $discount_line['item_description'] = JText::sprintf('COM_FCADMIN_INVOICE_DISCOUNT_PERCENT', (int) $line[19], '%');
        $discount_line['quantity'] = 1;
        $discount_line['total_net'] = -$line[20];
        $discount_line['vat'] = $vat_line;
        $invoices[$invoice_id]['discount'] = $discount_line;
      }
    }
    return $invoices;
  }

  public function getAddress($user_id = '')
  {
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    $query->select('address1,address2,address3,city,region,country,postal_code');
    $query->from('#__user_profile_fc');
    $query->where('user_id=' . (int) $user_id);

    $db->setQuery($query);

    try {
      $row = $db->loadObject();
    }
    catch (Exception $e) {
      Throw new Exception('User ' . $user_id . 'not found in system. Please add this user and try again.' . $e->getMessage());
    }
    return $row;
  }

}
