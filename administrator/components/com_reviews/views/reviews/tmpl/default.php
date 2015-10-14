<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');

$listDirn = $this->escape($this->state->get('list.direction'));
$listOrder = $this->escape($this->state->get('list.ordering'));
$user = JFactory::getUser();
$userId = $user->get('id');
$saveOrder = $listOrder == 'r.ordering';
$disableClassName = '';
$disabledLabel = '';
$input = JFactory::getApplication()->input;

// Get the following options from the request.
$option = $input->get('option', '', 'string');
$view = $input->get('view', 'default', 'string');
$unit_id = $input->get('unit_id', '0', 'int');

// If the review is being viewed through the reviews view of hellowolrd then we need to redirect back there
// and not the to the com_reviews component.
$route = ($option == 'com_rental') ? $option . '&view=reviews&unit_id=' . (int) $unit_id : $option;

// Check relevant permissions for the user against the reviews component
$canDo = ReviewsHelper::getActions('com_reviews');

$canChangeState = ($option == 'com_rental') ? false : $canDo->get('core.edit.state');
$canEditOwn = $canDo->get('core.edit.own');
$canEdit = $canDo->get('core.edit');

// Set the data array (for the progress layout) based on the component we are in
$data = ($option == 'com_rental') ? array('item' => $this->unit, 'progress' => $this->progress) : array();
?>

<form action="<?php echo JRoute::_('index.php?option=' . $route); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (!empty($this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
        <?php else : ?>
            <div id="j-main-container">
            <?php endif; ?>
            <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

            <div class="alert alert-info">
                <span class="icon icon-info"></span>
                <strong><?php echo JText::_('COM_REVIEWS_ADDING_REVIEWS'); ?></strong>
            </div>
            <?php if (count($this->items) > 0) : ?> 
                <table class="table table-striped" id="articleList">
                    <thead>
                        <tr>
                            <th width="1%" class="hidden-phone">
                                <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                            </th>
                            <th>
                                <?php echo JText::_('COM_REVIEWS_PROPERTY_ID'); ?>

                            </th>
                            <th>
                                <?php echo JText::_('COM_REVIEWS_REVIEW_TEXT'); ?>
                            </th>
                            <th width="15%">
                                <?php echo JText::_('COM_REVIEWS_REVIEWER'); ?>
                            </th>
                            <th width="15%">
                                <?php echo JText::_('COM_REVIEWS_DATE_STATYED_AT_PROPERTY'); ?>
                            </th>
                            <th>
                                <?php echo JText::_('JSTATUS'); ?>
                            </th>
                            <th>
                                <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>

                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($this->items as $i => $item): ?>
                            <tr>
                                <td>
                                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                </td>
                                <td>
                                    <?php echo $item->unit_title; ?>
                                    (<?php echo $item->property_id; ?>)
                                </td>
                                <td>
                                    <?php echo JHtml::_('string.truncate', $item->review_text, 0, true, false); // Don't allow html here. ?>
                                    <?php if ($canEdit) : ?>
                                        <a href="<?php echo JRoute::_('index.php?option=com_reviews&task=review.edit&id=' . (int) $item->id); ?>">
                                            <?php echo JText::_('JTOOLBAR_EDIT'); ?>
                                        </a>
                                    <?php endif; ?>  
                                </td>
                                <td>
                                    <?php echo $this->escape($item->guest_name); ?>
                                </td>
                                <td>
                                    <?php echo JFactory::getDate($item->date)->calendar('d M Y'); ?>
                                </td>
                                <td>
                                    <?php echo JHtml::_('jgrid.published', $item->published, $i, 'reviews.', $canChangeState, 'cb'); ?>
                                </td>
                                <td>
                                    <?php echo $item->id; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <input type="hidden" name="extension" value="<?php echo 'com_reviews'; ?>" />
                    </tbody>
                </table>
                <?php echo $this->pagination->getListFooter(); ?>

                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />


                <?php echo JHtml::_('form.token'); ?>

            </div>
    </form>
<?php else: ?>
    <div class="alert alert-notice">
        <h4>No Reviews</h4>
        <p>No review were found against your account.</p>
    </div>

<?php endif; ?>
