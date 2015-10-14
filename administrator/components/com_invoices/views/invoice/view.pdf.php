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

require_once(JPATH_LIBRARIES . '/tcpdf_6_2_12/config/tcpdf_config_alt.php');

jimport('tcpdf_6_2_12.tcpdf');

/**
 * View class for a list of Invoices.
 */
class InvoicesViewInvoice extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null) {

        $app = JFactory::getApplication();
        $this->id = $app->input->get('id', '', 'int');

        $this->state = $this->get('State');

        $this->items = $this->get('Items');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $html = $this->loadTemplate('pdf');

        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('French Connections');
        $pdf->SetTitle($this->items[0]->id);
        $pdf->SetSubject('Invoice number ' . $this->items[0]->id);

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------
        // set font
        $pdf->SetFont('freesans', '', 10);

        // add a page
        $pdf->AddPage();

        // output the HTML content
        $pdf->writeHTML($html);


        // ---------------------------------------------------------
        $document = JFactory::getDocument();
        $document->setMimeEncoding('application/pdf');

        //JFactory::getApplication()->setHeader('Content-disposition', 'attachment; filename="properties-with-no-availability-' . JFactory::getDate()->calendar('d-m-Y') . '.pdf"; creation-date="' . JFactory::getDate()->toRFC822() . '"', true);
        echo $pdf->Output($this->items[0]->id . '.pdf', 'I');
    }

}
