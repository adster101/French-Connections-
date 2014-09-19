<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();
$logged_in = ($user->guest) ? false : true;

$params = JComponentHelper::getParams('com_reviews');

// Get the item ID and work out the SEF route to the property listing
$Itemid = FCSearchHelperRoute::getItemid(array('component', 'com_accommodation'));
$route = JRoute::_('index.php?option=com_accommodation&Itemid=' . $Itemid . '&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id);
?>
<?php if ($this->reviews) : ?>
  <figure>
    <blockquote class="quote">
      <?php echo strip_tags(JHtml::_('string.truncate', $this->reviews[0]->review_text, 100)); ?>
      <p>
        <a href="<?php echo $route ?>#reviews">
          <?php echo JText::sprintf('COM_ACCOMMODATION_SITE_READ_MORE_REVIEWS', count($this->reviews)); ?>
        </a>
      </p>
    </blockquote> 
    <figcaption>
      <cite>
        <?php echo $this->reviews[0]->guest_name; ?>
        <?php
        $date = new DateTime($this->reviews[0]->date);
        echo $date->format('D, d M Y');
        ?>
      </cite> 
    </figcaption>
  </figure>
<?php else: ?>
  <p>
    <?php echo JText::_('COM_ACCOMMODATION_SITE_NO_REVIEWS'); ?>
  </p>
<?php endif; ?>
<?php
if ($logged_in) :
  $Itemid_review = FCSearchHelperRoute::getItemid(array('component', 'com_reviews'));
  $review_route = JRoute::_('index.php?option=com_reviews&task=review.add&Itemid=' . $Itemid_review . '&unit_id=' . $this->item->unit_id);
  ?>
  <p>
    <a href="<?php echo $review_route ?>">
      <?php echo JText::_('COM_ACCOMMODATION_SITE_ADD_REVIEW'); ?>
    </a>
  </p>
  <?php
else:
  // Get the review item id and set the review route (only works if user logged in)
  $Itemid_login = FCSearchHelperRoute::getItemid(array('component', 'com_users'));
  $login_route = JRoute::_('index.php?option=com_users&view=login&Itemid=' . $Itemid_login);

  $Itemid_review = $params->get('item_id_review');
  
  $review_route = JRoute::_('index.php?option=com_reviews&task=review.add&Itemid=' . $Itemid_review . '&unit_id=' . $this->item->unit_id);
  ?>
  <a class="login" href="<?php echo $login_route . '?return=' . base64_encode($review_route) ?>">
    <?php echo JText::_('COM_ACCOMMODATION_SITE_ADD_REVIEW') ?>
  </a>    
<?php endif; ?>
<hr />