<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<div class="span3">
  <div class="panel panel-default">
    <div class="panel-heading">Page views</div>
    <div class="panel-body">
      This property received <span class="lead"><?php echo (int) $this->data['views'][0] ?></span> views
      <hr />
      <p class="align-right"><a href="#">Get more page views</a>
    </div>
  </div>
</div>
<div class="span3">
  <div class="panel panel-default">
    <div class="panel-heading">Website click throughs</div>
    <div class="panel-body">This property received <span class="lead"><?php echo (int) $this->data['clicks'][0] ?></span> views</div>
  </div>
</div>
<div class="span3">
  <div class="panel panel-default">
    <div class="panel-heading">Enquiries</div>
    <div class="panel-body">This property received <span class="lead"><?php echo (int) $this->data['enquiries'][0] ?></span> enquiries</div>
  </div>
</div>
<div class="span3">
  <div class="panel panel-default">
    <div class="panel-heading">Reviews</div>
    <div class="panel-body">This property has <span class="lead"><?php echo (int) $this->data['reviews'][0] ?></span> reviews</div>
  </div>
</div>
<?php echo JHtml::_('form.token'); ?>