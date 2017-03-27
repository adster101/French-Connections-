<?php
/**
 * @version     1.0.0
 * @package     com_invoices
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();
$userId = $user->get('id');
?>



<p class="small">
  VAT Reg. Number: GB 801 2299 61<br />
  Company Registration Number: 3216862<br />
  French Connections <br />
  Suite 3,<br />
  5 Battalion Court,<br />
  Colburn Business Park,<br />
  Catterick Garrison,<br />
  North Yorkshire.<br />
  DL9 4QN.
</p>
<p><strong>INVOICE <?php echo $this->escape($this->id); ?></strong></p>
<hr />
<p><strong>Invoice to:</strong></p>
<p><?php echo $this->escape($this->items[0]->first_name . ' ' . $this->items[0]->surname); ?><br />
    <?php echo $this->escape($this->items[0]->address1); ?><br />
    <?php echo ($this->items[0]->address2) ? $this->escape($this->items[0]->address2) . "<br />" : ''; ?>
    <?php echo ($this->items[0]->address3) ? $this->escape($this->items[0]->address3) . "<br />" : ''; ?>
    <?php echo $this->escape($this->items[0]->town); ?><br />
    <?php echo $this->escape($this->items[0]->county); ?><br />
    <?php echo $this->escape($this->items[0]->postcode); ?><br />
</p>
<p>Invoice No
    <strong>
        <?php echo $this->escape($this->items[0]->id) ?>
    </strong>
</p>

<p>Date issued: <strong><?php echo $this->escape($this->items[0]->date_created) ?></strong></p>

<table class="table table-striped" id="invoiceList">
    <thead>
        <tr>
            <th width="10%"><strong>Qty</strong></th>
            <th width="60%"><strong>Description</strong></th>
            <th align="right" width="15%"><strong>Unit cost</strong></th>
            <th align="right" width="15%"><strong>Total(GBP)</strong></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->items as $i => $item) : ?>
            <tr>
                <td width="10%"><?php echo $this->escape($item->quantity) ?></td>
                <td width="60%"><?php echo $this->escape($item->item_description) ?></td>
                <td align="right" width="15%"><?php echo $this->escape($item->line_value) ?></td>
                <td align="right" width="15%"><?php echo number_format($this->escape($item->line_value * $item->quantity), 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3">VAT</td>
            <td align="right"><?php echo $this->escape($item->vat) ?></td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
            <td align="right">
                <strong><?php echo number_format($this->escape($this->items[0]->total_net) + $this->escape($this->items[0]->vat), 2) ?></strong>
            </td>
        </tr>
    </tbody>

</table>
