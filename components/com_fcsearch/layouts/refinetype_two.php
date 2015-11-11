<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$displayData = ($displayData) ? $displayData : array();

$uri = ($displayData['uri']) ? $displayData['uri'] : '';
$lang = ($displayData['lang']) ? $displayData['lang'] : 'en-GB';
$offers = ($displayData['offers']) ? $displayData['offers'] : '';
$lwl = ($displayData['lwl']) ? $displayData['lwl'] : '';

$data = $displayData['data'];

// Sort the data array into alphabetical order 
foreach ($data as $key => $row) {
    // replace 0 with the field's index/key
    $sortable[$key]  = $row['title'];
}
array_multisort($sortable, SORT_ASC, $data);

?>

<?php if (!empty($data)) : ?>

    <?php
    foreach ($data as $key => $value) :
        ?>
        <?php
        $remove = false;
        $tmp = explode('/', $uri); // Split the url out on the slash
        $filters = ($lang == 'en-GB') ? array_flip(array_slice($tmp, 3)) : array_flip(array_slice($tmp, 4)); // The filters being applied in the current URL
        if ($value['search_code'] == 'accommodation_')
        {
            $filter_string = $value['search_code'] . JApplication::stringURLSafe($this->escape($value[title])) . '_' . (int) $value[id];
        } else
        {
            $filter_string = $value['search_code'] . JStringNormalise::toUnderscoreSeparated(JApplication::stringURLSafe($this->escape($value[title]))) . '_' . (int) $value[id];
        }

        if (!array_key_exists($filter_string, $filters))
        { // This property filter isn't currently applied
            $new_uri = implode('/', array_flip($filters)); // Take the existing filters 
            $new_uri = (!empty($filters)) ? '/' . $filter_string . '/' . $new_uri : '/' . $filter_string; // And append the new filter only adding new uri it it's not empty
            $remove = false;
        } else
        { // This property type filter is already being applied
            unset($filters[$filter_string]); // Remove it from the filters array
            $new_uri = implode('/', array_flip($filters));  // The new filter part is generated so without this filter which effectively removes the filter from the search
            $new_uri = ($new_uri) ? '/' . $new_uri : '';
            $remove = true;
        }
        $route = 'index.php?option=com_fcsearch&Itemid=' . $displayData['itemid'] . '&s_kwds=' .
                JApplication::stringURLSafe($this->escape($displayData['location'])) . $new_uri . $offers . $lwl;
        ?>

        <p>
          <a href="<?php echo JRoute::_($route) ?>">
            <i class="muted icon <?php echo ($remove ? 'glyphicon glyphicon-remove' : 'glyphicon glyphicon-unchecked'); ?>"> </i>
            <?php echo $this->escape($value[title]); ?> (<?php echo $value[count]; ?>)
          </a>
        </p>          


    <?php endforeach ?>
<?php else: ?>
    <?php echo '...'; ?>
<?php endif; ?>