<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

$data = $displayData;

// Load the form filters
$filters = $data['view']->filterForm->getGroup('filter');
?>
<?php if ($filters) : ?>
  <fieldset>
    <legend>Filter options</legend>
    <?php foreach ($filters as $fieldName => $field) : ?>
      <div class="filter-container">
        <?php echo $field->label; ?>
        <?php echo $field->input; ?>
      </div>
    <?php endforeach; ?>
    <div class="filter-container">
      <button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
        <i class="icon-search"></i> <?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
      </button>
    </div>
  </fieldset>
<?php endif; ?>
