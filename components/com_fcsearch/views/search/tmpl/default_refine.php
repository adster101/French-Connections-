<?php
/**
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHtml::_('behavior.core');
var_dump($this->attribute_options);die;

$app = JFactory::getApplication();
$pathway = $app->getPathway();
$items = $pathway->getPathWay();

$lang = $app->getLanguage()->getTag();

$uri = str_replace(array('http://', 'https://'), '', JUri::current());
$refine_budget_min = $this->getBudgetFields();
$refine_budget_max = $this->getBudgetFields(250, 5000, 250, 'max_');

$min_budget = $this->state->get('list.min_price');
$max_budget = $this->state->get('list.max_price');
$offers = ($this->state->get('list.offers')) ? '?offers=true' : '';
$lwl = ($this->state->get('list.lwl')) ? '?lwl=true' : '';

$Itemid_search = SearchHelper::getItemid(array('component', 'com_fcsearch'));

// The layout for the anchor based navigation on the property listing
$refine_type_layout = new JLayoutFile('refinetype', $basePath = JPATH_SITE.'/components/com_fcsearch/layouts');
?>

<h4 id="refine"><?php echo JText::_('COM_FCSEARCH_SEARCH_REFINE_SEARCH'); ?></h4>
<div class="panel panel-default">
  <div class="panel-heading">
    <?php echo JText::_('COM_FCSEARCH_REFINE_EXTRAS'); ?>
  </div>
  <div class="panel-body">
    <?php if (!empty($this->lwl) || !empty($this->so)) : ?>
      <?php
      $link = JURI::getInstance();
        $query_string_original = $link->getQuery(true);
      $query_string_new = $query_string_original;
      ?>
      <?php
      if (!empty($this->lwl)) :
        if ($query_string_new['lwl']) {
            unset($query_string_new['lwl']);
        } else {
            $query_string_new['lwl'] = 'true';
        }
        $link->setQuery($query_string_new);
        ?>
        <p>
          <a href="<?php echo JRoute::_($link->toString()) ?>">
            <i class="muted <?php echo ($lwl) ? 'glyphicon glyphicon-remove' : 'glyphicon glyphicon-unchecked'; ?>"> </i>
            <?php echo JText::_(COM_FCSEARCH_SEARCH_FILTER_LWL); ?> (<?php echo $this->lwl; ?>)
          </a>
        </p>
      <?php endif; ?>
      <?php
      if (!empty($this->so)) :
        $query_string_new = $query_string_original;

        if ($query_string_new['offers']) {
            unset($query_string_new['offers']);
        } else {
            $query_string_new['offers'] = 'true';
        }
        $link->setQuery($query_string_new);
        ?>
        <p>
          <a href="<?php echo JRoute::_($link->toString()) ?>">
            <i class="muted <?php echo ($offers) ? 'glyphicon glyphicon-remove' : 'glyphicon glyphicon-unchecked'; ?>"> </i>
            <?php echo JText::_(COM_FCSEARCH_SEARCH_FILTER_OFFERS); ?> (<?php echo $this->so; ?>)
          </a>
        </p>
      <?php endif; ?>
    <?php else : ?>
      <?php echo '...'; ?>
    <?php endif; ?>
  </div>
</div>
<div class="panel panel-default">
  <div class="panel-heading">
    <?php echo JText::_('COM_FCSEARCH_REFINE_PRICE'); ?>
  </div>
  <div class="panel-body">
    <div class="search-field">
      <label class="" for="min_price"><?php echo JText::_('COM_FCSEARCH_SEARCH_MINIMUM_PRICE_RANGE'); ?></label>
      <select id="min_price" name="min" class="span12">
        <?php echo JHtml::_('select.options', $refine_budget_min, 'value', 'text', 'min_'.$min_budget); ?>
      </select>
    </div>
    <div class="search-field">
      <label class="" for="max_price"><?php echo JText::_('COM_FCSEARCH_SEARCH_MAXIMUM_PRICE_RANGE'); ?></label>
      <select id="max_price" name="max" class="span12">
        <?php echo JHtml::_('select.options', $refine_budget_max, 'value', 'text', 'max_'.$max_budget); ?>
      </select>
    </div>
    <div class="search-field">
      <button class="property-search-button btn btn-warning btn-small pull-right" href="#">
        <?php echo JText::_('COM_FCSEARCH_UPDATE') ?>
      </button>
    </div>
  </div>
</div>
<?php if ($this->localinfo->level) : ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      Location
      <?php //echo JText::_($this->escape($this->localinfo->title));      ?>
    </div>
    <div class="panel-body">
      <?php foreach ($items as $key => $value) : ?>
        <?php if ($key > 0) : ?>
          <?php
          // TO DO - Make this into a function or sommat as it's repeated below.
          $tmp = explode('/', $uri); // Split the url out on the slash
          $filters = ($lang == 'en-GB') ? array_slice($tmp, 3) : array_slice($tmp, 4); // Remove the first 3 value of the URI
          $filters = (!empty($filters)) ? '/'.implode('/', $filters) : '';
          ?>
          <p>
            <a class="btn btn-sm btn-default" href="<?php echo JRoute::_($items[$key - 1]->link.$filters.$offers.$lwl); ?>">
              <span class="close"> &times;</span>
              <?php echo $value->name = stripslashes(htmlspecialchars($value->name, ENT_COMPAT, 'UTF-8')); ?>
            </a>
          </p>
          <?php if (($key + 1) == count($items)) : ?>
            <hr />
          <?php endif; ?>
        <?php endif; ?>

      <?php endforeach; ?>
      <?php if ($this->localinfo->level < 10) : ?>
        <p>Refine location</p>
        <?php if (!empty($this->location_options)) : ?>

          <?php
          $counter = 0;
          $hide = true;
          foreach ($this->location_options as $key => $value) :
            ?>
            <?php
            $remove = false;
            $tmp = explode('/', $uri); // Split the url out on the slash
            $filters = ($lang == 'en-GB') ? array_slice($tmp, 3) : array_slice($tmp, 4); // Remove the first 3 value of the URI
            $filters = (!empty($filters)) ? '/'.implode('/', $filters) : '';
            $route = 'index.php?option=com_fcsearch&Itemid='.$Itemid_search.'&s_kwds='.JApplication::stringURLSafe($this->escape($value->title)).$filters.$offers.$lwl;
            ?>

            <?php if ($counter >= 10 && $hide) : ?>
              <?php $hide = false; ?>
              <div class="hide ">
              <?php endif; ?>
              <p>
                <a href="<?php echo JRoute::_($route) ?>">
                  <?php echo $this->escape($value->title); ?> (<?php echo $value->count; ?>)
                </a>
              </p>
              <?php ++$counter; ?>
              <?php if ($counter == count($this->location_options) && !$hide) : ?>
              </div>
            <?php endif; ?>
            <?php if ($counter == count($this->location_options) && !$hide) : ?>
              <hr class="condensed" />
              <a href="#" class="show align-right" title="<?php echo JText::_('COM_FCSEARCH_SEARCH_SHOW_MORE_OPTIONS') ?>">
                <?php echo JText::_('COM_FCSEARCH_SEARCH_SHOW_MORE_OPTIONS'); ?></a>
            <?php endif; ?>
          <?php endforeach ?>
        <?php else : ?>
          <?php echo '...'; ?>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<div class="panel panel-default">
  <div class="panel-heading">
    <?php echo JText::_('COM_FCSEARCH_SEARCH_REFINE_SEARCH_ACCOMMODATION_TYPE'); ?>
  </div>
  <div class="panel-body">
    <?php
    echo $refine_type_layout->render(
            array(
                'data' => $this->accommodation_options,
                'location' => $this->localinfo->title,
                'itemid' => $Itemid_search,
                'uri' => $uri,
                'lang' => $lang,
                'type' => 'accommodation_',
                'offers' => $offers,
                'lwl' => $lwl,
    ));
    ?>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">
    <?php echo JText::_('COM_FCSEARCH_SEARCH_REFINE_SEARCH_PROPERTY_TYPE'); ?>
  </div>
  <div id="property" class="panel-body">
    <?php if (!empty($this->property_options)) : ?>

      <?php
      $counter = 0;
      $hide = true;
      foreach ($this->property_options as $key => $value) :
        ?>
        <?php
        $remove = false;
        $tmp = explode('/', $uri); // Split the url out on the slash
        $filters = ($lang == 'en-GB') ? array_flip(array_slice($tmp, 3)) : array_flip(array_slice($tmp, 4)); // The filters being applied in the current URL
        $filter_string = 'property_'.JApplication::stringURLSafe($this->escape($value->title)).'_'.(int) $value->id;

        if (!array_key_exists($filter_string, $filters)) { // This property filter isn't currently applied
          $new_uri = implode('/', array_flip($filters)); // Take the existing filters
          $new_uri = (!empty($filters)) ? '/'.$filter_string.'/'.$new_uri : '/'.$filter_string; // And append the new filter only adding new uri it it's not empty
          $remove = false;
        } else { // This property type filter is already being applied
          unset($filters[$filter_string]); // Remove it from the filters array
          $new_uri = implode('/', array_flip($filters));  // The new filter part is generated so without this filter which effectively removes the filter from the search
          $new_uri = ($new_uri) ? '/'.$new_uri : '';
            $remove = true;
        }
        $route = 'index.php?option=com_fcsearch&Itemid='.$Itemid_search.'&s_kwds='.
                JApplication::stringURLSafe($this->escape($this->localinfo->title)).$new_uri.$offers.$lwl;
        ?>
        <?php if ($counter >= 10 && $hide) : ?>
          <?php $hide = false; ?>
          <div class="hide ">
          <?php endif; ?>
          <p>
            <a href="<?php echo JRoute::_($route) ?>">
              <i class="muted icon <?php echo $remove ? 'glyphicon glyphicon-check' : 'glyphicon glyphicon-unchecked'; ?>"> </i>
              <?php echo $this->escape($value->title); ?> (<?php echo $value->count; ?>)
            </a>
          </p>
          <?php ++$counter; ?>
          <?php if ($counter == count($this->property_options) && !$hide) : ?>
          </div>
        <?php endif; ?>
        <?php if ($counter == count($this->property_options) && !$hide) : ?>
          <hr class="condensed" />
          <a href="#" class="show align-right" title="<?php echo JText::_('COM_FCSEARCH_SEARCH_SHOW_MORE_OPTIONS') ?>">
            <?php echo JText::_('COM_FCSEARCH_SEARCH_SHOW_MORE_OPTIONS'); ?>
          </a>
        <?php endif; ?>
      <?php endforeach ?>
    <?php else: ?>
      <?php echo '...'; ?>
    <?php endif; ?>
  </div>
</div>
<?php foreach ($this->attribute_options as $key => $values) : ?>
  <?php
  $counter = 0;
  $hide = true // Init a counter so we don't show all the options at once
  ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <?php echo JTEXT::_($this->escape($key)); ?>
    </div>
    <div class="panel-body">
      <?php if (!empty($values)) : ?>
        <?php
        foreach ($values as $key => $value) :
          $new_uri = '';
          $tmp = array_flip(explode('/', $uri));
          $remove = '';

          $filter_string = $value['search_code'].JStringNormalise::toUnderscoreSeparated(JApplication::stringURLSafe($value['title'])).'_'.$key;
          // If the filter string doesn't already exist in the url, then append it to the end
          if (!array_key_exists($filter_string, $tmp)) {
              $new_uri = implode('/', array_flip($tmp));
              $new_uri = $new_uri.'/'.$filter_string;
              $remove = false;
          } else {
              unset($tmp[$filter_string]);
              $new_uri = implode('/', array_flip($tmp));
              $remove = true;
          }
          ?>
          <?php if ($counter >= 10 && $hide) : ?>
            <?php $hide = false; ?>
            <div class="hide ">
            <?php endif; ?>
            <p>
              <a href="<?php echo JRoute::_('http://'.$new_uri.$offers.$lwl) ?>">
                <i class="muted icon <?php echo $remove ? 'glyphicon glyphicon-check' : 'glyphicon glyphicon-unchecked'; ?>"> </i>&nbsp;<?php echo $value['title']; ?> (<?php echo $value['count']; ?>)
              </a>
            </p>

            <?php ++$counter; ?>

            <?php if ($counter == count($values) && !$hide) : ?>

            </div>
          <?php endif; ?>
          <?php if ($counter == count($values) && !$hide) : ?>
            <hr class="condensed" />
            <a href="#" class="show align-right" title="<?php echo JText::_('COM_FCSEARCH_SEARCH_SHOW_MORE_OPTIONS') ?>">
              <?php echo JText::_('COM_FCSEARCH_SEARCH_SHOW_MORE_OPTIONS'); ?>
            </a>
          <?php endif; ?>

        <?php endforeach; ?>
      <?php else: ?>
        <?php echo '...'; ?>
      <?php endif; ?>
    </div>
  </div>

<?php endforeach; ?>
