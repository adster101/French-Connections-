<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$language = JFactory::getLanguage();
$language->load('plg_user_profile_fc', JPATH_ADMINISTRATOR, 'en-GB', true);
$total = '';
$total_vat = '';
?>
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
          <?php foreach ($displayData as $i => $item) : ?>
            <?php $total = $total + $item->line_value; ?>
            <?php $total_vat = $total_vat + $item->vat; ?>
            <tr>
              <td><?php echo $this->escape($item->quantity) ?></td>
              <td><?php echo $this->escape($item->item_description) ?></td>
              <td text-align="right"><?php echo number_format($this->escape($item->cost), 2) ?></td>
              <td text-align="right"><?php echo number_format($this->escape($item->line_value), 2) ?></td>
            </tr>

          <?php endforeach; ?>

          <tr>
            <td colspan="3">
              <strong>VAT</strong>
            </td>
            <td><?php echo number_format($total_vat, 2) ?></td>
          </tr>
          <tr>
            <td colspan="3">&nbsp;</td>
            <td align="right">
              <strong>
                <?php echo number_format($total + $total_vat, 2) ?>
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

