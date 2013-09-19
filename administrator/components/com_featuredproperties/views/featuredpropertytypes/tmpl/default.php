<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();

$colspan = (isset($this->items[0])) ? count(get_object_vars($this->items[0])) : $colspan = 3;
?>

<form action="<?php echo JRoute::_('index.php?option=com_featuredproperties'); ?>" method="post" name="adminForm" id="adminForm">
  <?php if (!empty($this->sidebar)) : ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>
      <table class="table table-striped" id="articleList">
        <thead>
          <tr>
            <th width="1%" class="hidden-phone">
              <?php echo JHtml::_('grid.checkall'); ?>
            </th>
            <th>
              <?php echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?>
            </th>
            <th class="title">
              <?php echo JText::_('JGLOBAL_TITLE'); ?>
            </th>
            <th>
              <?php echo JText::_('DATE_CREATED'); ?>
            </th>
          </tr>
        </thead>

        <tbody>    
          <?php if (!empty($this->items)) : ?>
            <?php foreach ($this->items as $i => $item): ?>
              <?php $canChange = $user->authorise('core.edit.state', 'com_featuredproperties'); ?>
              <tr class="row<?php echo $i % 2; ?>">
                <td>
                  <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                <td>
                  <?php echo $this->escape($item->id) ?>
                </td>
                <td class="">
                  <a href="<?php echo JRoute::_('index.php?option=com_attributes&task=attributetype.edit&id=' . (int) $item->id); ?>">
                    <?php echo $this->escape($item->title); ?>
                  </a>
                </td>
                <td>
                  <?php echo $item->date_created ?>
                </td>
              </tr>					
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="<?php echo $colspan ?>">
                <p>No featured property types found. :-(</p>
              <td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>


      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <?php echo JHtml::_('form.token'); ?>
    </div>
</form>