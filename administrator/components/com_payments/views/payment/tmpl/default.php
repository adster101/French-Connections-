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
$total = '';
$vat_total = '';
?>

<form action="<?php echo JRoute::_('index.php?option=com_payments'); ?>" method="post" name="adminForm" id="adminForm">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>

      <p>
        <?php echo $this->escape($this->items[0]->InvoiceName); ?> |
        <strong>PRN: </strong><?php echo $this->escape($this->items[0]->property_id); ?> |
        <strong>User ID: </strong><?php echo $this->escape($this->items[0]->user_id); ?> |
        <strong>Expires: </strong><?php echo $this->escape($this->items[0]->expiry_date); ?> |

      </p>

      <p>
        Date created
        <strong>
          <?php echo $this->escape($this->items[0]->DateCreated) ?>
        </strong>
      </p>

      <table class="table table-striped" id="invoiceList">
        <thead>
          <tr>
            <th>Qty</th>
            <th>Description</th>
            <th align="right">Unit cost</th>
            <th align="right">Total(GBP)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($this->items as $i => $item) : ?>
          <?php
            $line_total = number_format($this->escape($item->line_total),2);
            $total = $line_total + $total;
            $line_vat_total = number_format($this->escape($item->vat),2);
            $vat_total = $vat_total + $line_vat_total;
          ?>
            <tr>
              <td><?php echo $this->escape($item->quantity) ?></td>
              <td><?php echo '[' . $this->escape($item->code) . '] ' . $this->escape($item->description) ?></td>
              <td text-align="right"><?php echo $this->escape($item->cost) ?></td>
              <td text-align="right"><?php echo $line_total; ?></td>
            </tr>

          <?php endforeach; ?>

          <tr>
            <td colspan="3">
              <strong>VAT</strong>
            </td>
            <td><?php echo number_format($this->escape($vat_total),2) ?></td>
          </tr>
          <tr>
            <td colspan="3">&nbsp;</td>
            <td align="right">
              <strong>
                <?php echo number_format($this->escape($total) + $this->escape($vat_total),2) ?>
              </strong>
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="4"></td>
          </tr>
        </tfoot>
      </table>

    </div>

    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="task" value="" />

</form>



