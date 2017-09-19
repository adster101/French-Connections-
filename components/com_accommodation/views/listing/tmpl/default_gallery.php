<?php
defined('_JEXEC') or die('Restricted access');
?>

<!-- Image gallery -->
<!-- Needs go into a separate template -->
<div role="main">
  <?php if (count($this->images) > 1) : ?>
    <div class="slick-slider">
      <?php if (!empty($this->item->video_url)) : ?>
        <div>
          <div class="embed-responsive embed-responsive-16by9">
            <iframe class="embed-responsive-item" id="player_1" src="<?php echo $this->escape($this->item->video_url) ?>"></iframe>
          </div>
        </div>
      <?php endif; ?>
      <?php foreach ($this->images as $images => $image) : ?>
        <?php $src = (!empty($image->image_file_name)) ? JURI::root() . 'images/property/' . $this->item->unit_id . '/gallery/' . $image->image_file_name : JURI::getInstance()->toString(array('scheme')) . $image->url; ?>
        <div>
          <?php if ($images == 0) : ?>
            <img class="img-responsive" data-lazy="<?php echo $src ?>" />
          <?php else: ?>
            <img class="img-responsive" data-lazy="<?php echo $src ?>" />
          <?php endif; ?>
          <p>
            <?php echo $image->caption; ?>
            <span class="muted small">(<?php echo $images + 1 ?> / <?php echo count($this->images) ?>)</span>
          </p>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="carousel-ribbon hidden-xs">
      <?php if (!empty($this->item->video_url)) : ?>
        <div>
          <p class="center lead">
            <br />
            Video
            <span class="icon icon-video" style="width:100%;height:100%;"></span>
          </p>
        </div>
      <?php endif; ?>
      <?php foreach ($this->images as $images => $image) : ?>
        <?php $src = (!empty($image->image_file_name)) ? JURI::root() . 'images/property/' . $this->item->unit_id . '/thumbs/' . $image->image_file_name : JURI::getInstance()->toString(array('scheme')) . $image->url_thumb; ?>
        <div>
          <img src="<?php echo $src ?>" />
        </div>
      <?php endforeach; ?>
    </div>
  <?php else : ?>
    <div class="panel panel-default">
      <ul class="slides">
        <?php if (!empty($this->item->video_url)) : ?>
          <div>
            <img src="<?php JURI::root() . '/images/general/medium-sunflower.png' ?>" />
          </div>
        <?php endif; ?>
        <?php foreach ($this->images as $images => $image) : ?>
          <div>
            <img src="<?php echo JURI::root() . 'images/property/' . $this->item->unit_id . '/gallery/' . $image->image_file_name; ?>" />
            <p>
              <?php echo $this->escape($image->caption); ?>
            </p>
          </div>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
</div>
