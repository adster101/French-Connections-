<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

$input = JFactory::getApplication()->input;
$field = $input->getCmd('field');
$function = 'jSelectUnit_' . $field;

?>

<form action="<?php echo JRoute::_('index.php?option=com_specialoffers&view=units&layout=modal&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm">
  <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

  <table class="table table-striped table-condensed">
    <thead>
      <tr>
        <th class="left">
          Unit title
        </th>
        <th class="nowrap" width="25%">
          Property ID
        </th>
        <th class="nowrap" width="25%">
          Unit ID
        </th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="15">
          <?php echo $this->pagination->getListFooter(); ?>
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php
      $i = 0;

      foreach ($this->items as $item) :
        ?>
        <tr class="row<?php echo $i % 2; ?>">
          <td>
            <a class="pointer" onclick="if (window.parent)
                  window.parent.<?php echo $this->escape($function); ?>('<?php echo $item->unit_id; ?>', '<?php echo $this->escape(addslashes($item->unit_title)); ?>');">
  <?php echo $item->unit_title; ?></a>
          </td>
          <td align="center">
  <?php echo $item->property_id; ?>
          </td>
          <td align="left">
  <?php echo nl2br($item->unit_id); ?>
          </td>
        </tr>
<?php endforeach; ?>
    </tbody>
  </table>
  <div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="field" value="<?php echo $this->escape($field); ?>" />
    <input type="hidden" name="boxchecked" value="0" />
<?php echo JHtml::_('form.token'); ?>
  </div>
</form>
