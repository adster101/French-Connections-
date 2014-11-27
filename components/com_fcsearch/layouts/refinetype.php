<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$displayData = ($displayData) ? $displayData : array();

$uri = ($displayData['uri']) ? $displayData['uri'] : '';
$lang = ($displayData['lang']) ? $displayData['lang'] : 'en-GB';
$type = ($displayData['type']) ? $displayData['type'] : '';
$offers = ($displayData['offers']) ? $displayData['offers'] : ''; 
$lwl = ($displayData['lwl']) ? $displayData['lwl'] : ''; 
?>

<?php if (!empty($displayData['data'])) : ?>

  <?php
  $counter = 0;
  $hide = true;
  foreach ($displayData['data'] as $key => $value) :
    ?>
    <?php
    $remove = false;
    $tmp = explode('/', $uri); // Split the url out on the slash
    $filters = ($lang == 'en-GB') ? array_flip(array_slice($tmp, 3)) : array_flip(array_slice($tmp, 4)); // The filters being applied in the current URL
    $filter_string = $type . JApplication::stringURLSafe($this->escape($value->title)) . '_' . (int) $value->id;

    if (!array_key_exists($filter_string, $filters)) { // This property filter isn't currently applied
      $new_uri = implode('/', array_flip($filters)); // Take the existing filters 
      $new_uri = (!empty($filters)) ? '/' . $filter_string . '/' . $new_uri : '/' . $filter_string; // And append the new filter only adding new uri it it's not empty
      $remove = false;
    } else { // This property type filter is already being applied
      unset($filters[$filter_string]); // Remove it from the filters array
      $new_uri = implode('/', array_flip($filters));  // The new filter part is generated so without this filter which effectively removes the filter from the search
      $new_uri = ($new_uri) ? '/' . $new_uri : '';
      $remove = true;
    }
    $route = 'index.php?option=com_fcsearch&Itemid=' . $displayData['itemid'] . '&s_kwds=' .
            JApplication::stringURLSafe($this->escape($displayData['location'])) . $new_uri . $offers . $lwl;
    ?>
    <?php if ($counter >= 5 && $hide) : ?>
      <?php $hide = false; ?>
      <div class="hide ">
    <?php endif; ?>
      <p>
        <a href="<?php echo JRoute::_($route) ?>">
          <i class="muted icon <?php echo ($remove ? 'icon-checkbox' : 'icon-checkbox-unchecked'); ?>"> </i>
    <?php echo $this->escape($value->title); ?> (<?php echo $value->count; ?>)
        </a>
      </p>          
          <?php $counter++; ?>
    <?php if ($counter == count($displayData['data']) && !$hide) : ?>
      </div>
      <?php endif; ?>
      <?php if ($counter == count($displayData['data']) && !$hide) : ?>
      <hr class="condensed" />
      <a href="#" class="show" title="<?php echo JText::_('COM_FCSEARCH_SEARCH_SHOW_MORE_OPTIONS') ?>"><?php echo JText::_('COM_FCSEARCH_SEARCH_SHOW_MORE_OPTIONS'); ?></a>
    <?php endif; ?>
  <?php endforeach ?>
<?php else: ?>
  <?php echo '...'; ?>
<?php endif; ?>