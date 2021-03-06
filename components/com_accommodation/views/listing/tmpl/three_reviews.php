<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();
$logged_in = ($user->guest) ? false : true;

$params = JComponentHelper::getParams('com_reviews');

// Get the item ID and work out the SEF route to the property listing
$Itemid = SearchHelper::getItemid(array('component', 'com_accommodation'));
$route = JRoute::_('index.php?option=com_accommodation&Itemid=' . $Itemid . '&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id);
?>
<div id="reviews">
  <?php if ($this->item->unit_title) : ?>
    <h2 class="page-header">
      <?php echo htmlspecialchars(JText::sprintf('COM_ACCOMMODATION_REVIEWS_AT', $this->item->unit_title)) ?>
    </h2>
  <?php endif; ?>
  <?php
  $Itemid_review = SearchHelper::getItemid(array('component', 'com_reviews'));
  $review_route = JRoute::_('index.php?option=com_reviews&task=review.add&Itemid=' . $Itemid_review . '&unit_id=' . $this->item->unit_id, false);

  if ($logged_in) :
    ?>
    <p>
      <a href="<?php echo $review_route ?>">
        <?php echo JText::_('COM_ACCOMMODATION_SITE_ADD_REVIEW'); ?>
      </a>
    </p>
    <?php
  else:
    // Get the review item id and set the review route (only works if user logged in)
    $Itemid_login = SearchHelper::getItemid(array('component', 'com_reviews'));
    $login_route = JRoute::_('index.php?option=com_users&view=login&Itemid=' . $Itemid_login);
    ?>
    <a class="login" href="<?php echo $login_route . '?return=' . base64_encode($review_route) ?>">
      <?php echo JText::_('COM_ACCOMMODATION_SITE_ADD_REVIEW') ?>
    </a>
  <?php endif; ?>

  <?php if ($this->reviews) : ?>
    <div class="well well-sm well-light-blue">
      <?php foreach ($this->reviews as $review) : ?>
        <figure>
          <blockquote class="quote">
            <?php echo strip_tags($review->review_text, '<p>,<br>'); ?>
            <?php echo JHtmlProperty::rating($review->rating); ?>
          </blockquote>
          <figcaption>
            <cite>
              <?php echo $review->guest_name; ?>
              <?php echo JFactory::getDate($review->date)->calendar('D, d M Y'); ?>
            </cite>
          </figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p>
      <strong>
        <?php echo JText::_('COM_ACCOMMODATION_SITE_NO_REVIEWS'); ?>
      </strong>
    </p>
  <?php endif; ?>


</div>
