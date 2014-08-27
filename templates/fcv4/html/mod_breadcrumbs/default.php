<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_breadcrumbs
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$lang = $app->input->get('lang', 'en');
$search_url = $app->getUserState('user.search');
$menu = $app->getMenu();
$active = $menu->getActive();
$items = $menu->getItems('component', 'com_fcsearch');
$Itemid = is_array($items) ? $items[0]->id : array();
$isListing = ($active->component == 'com_accommodation') ? true : false;
$isShortlist = ($active->component == 'com_shortlist') ? true : false;
?>
<?php if (!empty($search_url) && ($isListing || $isShortlist)) : ?>
  <div class="visible-lg-inline-block visible-md-inline-block visible-sm-inline-block visible-xs-inline-block">
    <a class="btn btn-primary btn-xs" href="<?php echo $search_url ?>" title="">    
      <span class="glyphicon glyphicon-circle-arrow-left"></span>
      <?php echo JText::_('COM_ACCOMMODATION_BACK_TO_SEARCH_RESULTS'); ?>
    </a>
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

<?php if ($isListing) : ?>
  <div class="visible-lg-inline-block visible-md-inline-block visible-sm-inline-block pull-right">
    <form class="form-inline" id="property-search" method="POST" action="<?php echo JRoute::_('index.php?option=com_fcsearch&lang=' . $lang . '&Itemid=' . (int) $Itemid . '&s_kwds=' . JText::_('COM_FCSEARCH_S_KWDS_DEFAULT')) ?>">
      <?php echo JHtml::_('form.token'); ?>
      <label class="sr-only" for="q">
        <?php echo JText::_('COM_FCSEARCH_SEARCH_QUERY_LABEL'); ?>
      </label>
      <input id="s_kwds" class="typeahead search-box form-control" type="text" name="s_kwds" autocomplete="Off" value="" placeholder="<?php echo JText::_('COM_ACCOMMODATION_SEARCH_DESTINATION_OR_PROPERTY') ?>" />
      <button class="property-search-button btn btn-primary">
        <span class="glyphicon glyphicon-search"><span class="sr-only"><?php echo JText::_('JSEARCH') ?></span></span>
      </button>
    </form>
  </div>
<?php endif; ?>
