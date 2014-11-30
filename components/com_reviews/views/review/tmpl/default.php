<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

// Below will be useful to be able to use the same params for enquiries as well as reviews
// $cparams = JComponentHelper::getParams('com_media');

$app = JFactory::getApplication();


jimport('joomla.html.html.bootstrap');
?>

<div class="page-header">
  <h1><?php echo $this->escape($this->document->title); ?></h1>
</div>
<p class="lead"><?php echo $this->escape($this->item->unit_title); ?></p>

<?php echo JHtml::_('string.truncate', $this->item->description, 200, true, false); ?>


<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$id = $this->item->id ? $this->item->id : '';
?>

<div class="row">
  <div class="col-xs-12">
    <?php
    if (isset($this->error)) :
      ?>
      <div class="contact-error">
        <?php echo $this->error; ?>
      </div>
    <?php endif; ?>
    <hr />
    <form id="contact-form" action="<?php echo JRoute::_('index.php?option=com_reviews'); ?>" method="post" class="form-validate form-vertical">
      <legend><?php echo JText::_('COM_REVIEWS_TESTIMONIAL_DETAILS'); ?></legend>
      <fieldset class="adminform">
        <?php foreach ($this->form->getFieldset('review') as $field): ?>
          <div class="form-group">
            <?php echo $field->label; ?>
            <div>
              <?php echo $field->input; ?>
            </div>
          </div>         
        <?php endforeach; ?>
        <div class="form-actions">
          <button class="btn btn-primary btn-large validatet" type="submit">
            <?php echo JText::_('COM_REVIEW_REVIEW_SUBMIT'); ?>
          </button>
          <input type="hidden" name="option" value="com_reviews" />
          <input type="hidden" name="task" value="review.submit" />
          <?php echo JHtml::_('form.token'); ?>
        </div>
      </fieldset>
    </form>
  </div>
</div>