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
        <?php $src = (!empty($image->image_file_name)) ? JURI::root() . 'images/property/' . $this->item->unit_id . '/profiles/' . $image->image_file_name : JURI::getInstance()->toString(array('scheme')) . $image->url; ?>
        <?php
        $srcset = array();
        $srcset[] = (!empty($image->image_file_name)) ? JURI::root() . 'images/property/' . $this->item->unit_id . '/profiles/' . '330x248_' . $image->image_file_name . $image->url . ' 400w' : JURI::getInstance()->toString(array('scheme'));
        $srcset[] = (!empty($image->image_file_name)) ? JURI::root() . 'images/property/' . $this->item->unit_id . '/profiles/' . '770x580_' . $image->image_file_name . $image->url . ' 767w' : JURI::getInstance()->toString(array('scheme'));
        $srcset[] = (!empty($image->image_file_name)) ? JURI::root() . 'images/property/' . $this->item->unit_id . '/profiles/' . '408x307_' . $image->image_file_name . $image->url . ' 768w' : JURI::getInstance()->toString(array('scheme'));
        $srcset[] = (!empty($image->image_file_name)) ? JURI::root() . 'images/property/' . $this->item->unit_id . '/profiles/' . '617x464_' . $image->image_file_name . $image->url . ' 992w' : JURI::getInstance()->toString(array('scheme'));
        $srcset[] = (!empty($image->image_file_name)) ? JURI::root() . 'images/property/' . $this->item->unit_id . '/profiles/' . '770x580_' . $image->image_file_name . $image->url . ' 1200w' : JURI::getInstance()->toString(array('scheme'));
        $srcset[] = (!empty($image->image_file_name)) ? JURI::root() . 'images/property/' . $this->item->unit_id . '/profiles/' . '903x586_' . $image->image_file_name . $image->url . ' 1900w' : JURI::getInstance()->toString(array('scheme'));
        ?>
        <div>
          <?php if ($images == 0) : ?>
            <img class="img-responsive center-block" srcset="<?php echo implode(',', $srcset) ?>"/>
          <?php else: ?>
            <img class="img-responsive center-block" srcset="<?php echo implode(',', $srcset) ?>"/>
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
        <?php $src = (!empty($image->image_file_name)) ? JURI::root() . 'images/property/' . $this->item->unit_id . '/profiles/100x100_' . $image->image_file_name : JURI::getInstance()->toString(array('scheme')) . $image->url_thumb; ?>
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