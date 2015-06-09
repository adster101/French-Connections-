<?php
defined('_JEXEC') or die('Restricted access');
?>

<!-- Image gallery -->
<!-- Needs go into a separate template -->
<div  role="main">
  <?php if (count($this->images) > 1) : ?>
    <section class="slider">
      <div class="slick-slider">
          <?php if (!empty($this->item->video_url)) : ?>
            
              <div class="embed-responsive embed-responsive-16by9">   
                <iframe class="embed-responsive-item" id="player_1" src="<?php echo $this->escape($this->item->video_url) ?>"></iframe>
              </div> 
           
          <?php endif; ?>
          <?php foreach ($this->images as $images => $image) : ?> 
            <?php $src = (!empty($image->image_file_name)) ? JURI::root() . 'images/property/' . $this->item->unit_id . '/gallery/' . $image->image_file_name : JURI::getInstance()->toString(array('scheme')) . $image->url; ?>
            <div>
              <?php if ($images == 0) : ?>
                <img src="<?php echo $src ?>" />
              <?php else: ?>
                <img src="images/general/ajax-loader-large.gif" data-src="<?php echo $src ?>" />
              <?php endif; ?>
              <p class="flex-caption">
                <?php echo $image->caption; ?>
                <span class="muted small">(<?php echo $images + 1 ?> / <?php echo count($this->images) ?>)</span>
              </p>
            </div>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="slick-slider">
          <?php if (!empty($this->item->video_url)) : ?>
            <li>
              <p class="center lead">
                <br />
                Video
                <span class="icon icon-video" style="width:100%;height:100%;"></span>
              </p>
            </li>
          <?php endif; ?>
          <?php foreach ($this->images as $images => $image) : ?> 
            <?php $src = (!empty($image->image_file_name)) ? JURI::root() . 'images/property/' . $this->item->unit_id . '/thumbs/' . $image->image_file_name : JURI::getInstance()->toString(array('scheme')) . $image->url_thumb; ?>

            <div>
              <img src="<?php echo $src ?>" /> 
            </div>     
          <?php endforeach; ?>
      </div>
    </section>
  <?php else : ?>
    <div class="panel panel-default">
      <ul class="slides">
        <?php if (!empty($this->item->video_url)) : ?>
          <li>
            <img src="<?php JURI::root() . '/images/general/medium-sunflower.png' ?>" />
          </li>
        <?php endif; ?>
        <?php foreach ($this->images as $images => $image) : ?> 
          <li>
            <img src="<?php echo JURI::root() . 'images/property/' . $this->item->unit_id . '/gallery/' . $image->image_file_name; ?>" />
            <p class="flex-caption">
              <?php echo $this->escape($image->caption); ?>
            </p>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
</div>