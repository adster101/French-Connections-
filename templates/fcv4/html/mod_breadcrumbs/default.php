<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_breadcrumbs
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$app = JApplicationSite::getInstance('site');
$lang = $app->input->get('lang', 'en');
$search_url = $app->getUserState('user.search');
$menu = $app->getMenu();
$active = $menu->getActive();
$items = $menu->getItems(array('component','access'), array('com_fcsearch', array(1,2,3)));
$Itemid = is_array($items) ? $items[0]->id : array();
$isListing = ($active->component == 'com_accommodation') ? true : false;
$isShortlist = ($active->component == 'com_shortlist') ? true : false;
$isRealestate = ($active->component == 'com_realestate') ? true : false;
$layout = $app->input->getCmd('layout','');
?>
<?php if (!empty($search_url) && ($isListing || $isShortlist || $isRealestate) && $layout != 'one') : ?>
  <div class="visible-lg-inline-block visible-md-inline-block visible-sm-inline-block visible-xs-inline-block">
    <p>
      <a class="btn btn-primary btn-xs" href="<?php echo $search_url ?>" title="">    
        <span class="glyphicon glyphicon-circle-arrow-left"></span>
        <?php echo JText::_('COM_ACCOMMODATION_BACK_TO_SEARCH_RESULTS'); ?>
      </a>
    </p>
  </div>
<?php endif; ?>

<ol class="breadcrumb<?php echo $moduleclass_sfx; ?> visible-lg-inline-block visible-md-inline-block visible-sm-inline-block hidden-xs hidden-sm">
  <?php
  if ($params->get('showHere', 1))
  {
    echo '<li class="active">' . JText::_('MOD_BREADCRUMBS_HERE') . '&#160;</li>';
  }
  else
  {
    echo '<li class="active"><span class="glyphicon glyphicon-map-marker"></span></li>';
  }

// Get rid of duplicated entries on trail including home page when using multilanguage
  for ($i = 0; $i < $count; $i++)
  {
    if ($i == 1 && !empty($list[$i]->link) && !empty($list[$i - 1]->link) && $list[$i]->link == $list[$i - 1]->link)
    {
      unset($list[$i]);
    }
  }

// Find last and penultimate items in breadcrumbs list
  end($list);
  $last_item_key = key($list);
  prev($list);
  $penult_item_key = key($list);

// Generate the trail
  foreach ($list as $key => $item) :
    // Make a link if not the last item in the breadcrumbs
    $show_last = $params->get('showLast', 1);
    if ($key != $last_item_key)
    {
      // Render all but last item - along with separator
      echo '<li>';
      if (!empty($item->link))
      {
        echo '<a href="' . $item->link . '">' . $item->name . '</a>';
      }
      else
      {
        echo $item->name;
      }
      echo '</li>';
    }
    elseif ($show_last)
    {
      // Render last item if reqd.
      echo '<li>';
      echo $item->name;
      echo '</li>';
    }
  endforeach;
  ?>
</ol>
