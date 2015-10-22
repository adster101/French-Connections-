<?php
defined('_JEXEC') or die('Restricted access');
$app = JFactory::getApplication();
$menu = $app->getMenu()->getActive();
$cdn = $menu->params->get('cdn', '');

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
        $srcset[] = (!empty($image->image_file_name)) ? $cdn . '/' . $this->item->unit_id . '/330x220_' . $image->image_file_name . $image->url . ' 330w' : JURI::getInstance()->toString(array('scheme')) . $image->url . ' 330w';
        $srcset[] = (!empty($image->image_file_name)) ? $cdn . '/' . $this->item->unit_id . '/408x272_' . $image->image_file_name . $image->url . ' 408w' : JURI::getInstance()->toString(array('scheme')) . $image->url . ' 408w';
        $srcset[] = (!empty($image->image_file_name)) ? $cdn . '/' . $this->item->unit_id . '/617x464_' . $image->image_file_name . $image->url . ' 617w' : JURI::getInstance()->toString(array('scheme')) . $image->url . ' 617w';
        $srcset[] = (!empty($image->image_file_name)) ? $cdn . '/' . $this->item->unit_id . '/770x513_' . $image->image_file_name . $image->url . ' 770w' : JURI::getInstance()->toString(array('scheme')) . $image->url . ' 770w';
        $srcset[] = (!empty($image->image_file_name)) ? $cdn . '/' . $this->item->unit_id . '/900x600_' . $image->image_file_name . $image->url . ' 900w' : JURI::getInstance()->toString(array('scheme')) . $image->url . ' 900w';
        ?>
        <div>
          <?php if ($images == 0) : ?>
            <img src="" class="img-responsive lazyload" data-srcset="<?php echo implode(',', $srcset) ?>" sizes="(max-width: 767px) 100vw, 60vw" />
          <?php else: ?>
            <img src="" class="img-responsive lazyload" data-srcset="<?php echo implode(',', $srcset) ?>" sizes="(max-width: 767px) 100vw, (max-width:991px) 58vw, (min-width: 992px) 66vw, 60vw" />
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
        <?php $src = (!empty($image->image_file_name)) ? $cdn . $this->item->unit_id . '/210x140_' . $image->image_file_name : JURI::getInstance()->toString(array('scheme')) . $image->url_thumb; ?>
        <div>
          <img width="100" src="<?php echo $src ?>" /> 
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
            <img class="lazyload" data-src="<?php echo JURI::root() . 'images/property/' . $this->item->unit_id . '/gallery/' . $image->image_file_name; ?>" />
            <p>
              <?php echo $this->escape($image->caption); ?>
            </p>
          </div>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
</div>