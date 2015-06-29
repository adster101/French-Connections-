<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
JHtml::_('behavior.core');


$property_status_icon = (!$this->items[0]->property_version_review) ? 'publish' : 'warning';
?>
<form name='adminForm' id='adminForm' action="<?php echo 'index.php?option=com_realestate&id=' . (int) $this->id ?>" class='form-validate' method='post'>
  <ul class="nav nav-tabs">
    <li class="active">
      <a href="<?php echo JRoute::_('index.php?option=com_realestate&view=review&layout=property&id=' . (int) $this->id) ?>">
        <i class="icon <?php echo 'icon-' . $property_status_icon ?>"></i>
        Property Detail
      </a>
    </li>
  </ul>
  <div class="row-fluid">
    <div class="span12">
      <table class="table table-bordered table-hover table-striped">
        <thead>
        <th>Field</th>
        <th>Current</th>
        <th>Draft</th>
        </thead>
        <tbody>
          <?php foreach ($this->versions[$layout] as $key => $versions) : ?>
            <?php foreach ($versions as $field => $values) : ?>
              <tr>
                <td width='20%'>
                  <?php echo $this->escape($field); ?>
                  <?php if (strcmp(trim($values[1]), trim($values[0])) != 0 && !empty($values[1])) : ?>
                    <span class="label label-important">*</span>
                  <?php endif; ?>
                </td>
                <td width='40%'>
                  <?php echo strip_tags($values[0]) ?>
                </td>
                <td width='40%'> 
                  <?php if (!empty($values[1])) : ?>
                    <?php echo strip_tags($values[1], "<ins>,<del>") ?>
                  <?php endif; ?>

                  <?php //if (array_key_exists($key, $this->versions[$layout][1])) : ?>
                  <?php //endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php if (array_key_exists('images', $this->versions)) : ?>
        <table class="table table-bordered table-striped">
          <tr>
            <td width="20%">Images</td> 
            <td width="40%">
              <?php foreach ($this->versions['images'][0] as $version) : ?>
                <p>
                  <img src=<?php echo '/images/property/' . $this->id . '/thumbs/' . $version['image_file_name']; ?> />
                  <?php if (!empty($version['deleted'])) : ?>
                    <span class="label label-warning">Deleted</span>
                  <?php endif; ?>
                  <span><?php echo $this->escape($version['caption']) ?></span>
                </p>
              <?php endforeach; ?>
            </td>
            <td width="40%">
              <?php if (!empty($this->versions['images'][1])) : ?>
                <?php foreach ($this->versions['images'][1] as $version) : ?>
                  <p>
                    <img src=<?php echo '/images/property/' . $this->id . '/thumbs/' . $version['image_file_name']; ?> /> 
                    <?php if (!empty($version['added'])) : ?>
                      <span class="label label-success">Added</span>
                    <?php endif; ?>            
                    <span>
                      <?php echo (!empty($version['diff'])) ? $version['diff'] : $version['caption'] ?>
                    </span>
                  </p>
                <?php endforeach; ?>
              <?php endif; ?>
            </td>
          </tr>
        </table>         
      <?php endif; ?>


    </div>
  </div>
  <input type='hidden' name='task' value='' />
  <?php echo JHtml::_('form.token'); ?>
</form>