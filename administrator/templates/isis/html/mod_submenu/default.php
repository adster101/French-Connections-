<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	mod_submenu
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

$hide = JRequest::getInt('hidemainmenu');

//Get the context
$context = JRequest::getVar('option', '', 'GET', 'string');

if ($context == 'com_helloworld') : // If we are in the property manager then we want to output a fancy progress menu type thing. 
  // Manipulate list to include the progress for each of the required stages - This relies on the order remaining the same.
  // Assumption is that they are all false to start off with.
  foreach ($list as &$item) {
    $item[3] = false;
  }

  // Get the id of the property
  $id = JRequest::getVar('id', '', 'GET', 'integer');

  // Get the app instance
  $app = JFactory::getApplication();

  // Would these be better tested and set in the view.html.php file?
  $availability = $app->getUserState('com_helloworld.availability.progress', false);
  $tariffs = $app->getUserState('com_helloworld.tariffs.progress', false);
  $images = $app->getUserState('com_helloworld.images.progress', false);
  $published = $app->getUserState('com_helloworld.published.progress', false);

  if ($id) { // Id is set therfore is not a new propery and therefore we assume it has been completed correctly
    $list[0][3] = true;
  }

  if ($availability) {
    $list[1][3] = true;
  }

  if ($tariffs) {
    $list[2][3] = true;
  }

  if ($images) {
    $list[3][3] = true;
  }
  ?>
  <div id="sidebar">
    <div class="sidebar-nav">
      <ul id="submenu" class="property-manager nav nav-list">
        <?php foreach ($list as &$item) : ?>
          <?php if (isset($item[2]) && $item[2] == 1) :
            ?><li class="active"><?php
    else :
            ?><li><?php
      endif;
          ?>          <?php if (strlen($item[1])) : // If there's a url  ?>
                <?php if ($id) : // There is an id so not a new property    ?>
                  <?php if (isset($item[2]) && $item[2] == 1) : // Is this the 'active' item ?>
                    <?php if (current($item) != 'Special offers') : ?>
                      <?php if ($item[3]) : ?>
                      <a title="<?php echo JText::sprintf('COM_HELLOWORLD_HELLOWORLD_DETAILS_COMPLETE', $item[0], $item[0]) ?>" 
                         class="active hasTip" 
                         href="<?php echo JFilterOutput::ampReplace($item[1]); ?>">
                        <i class="icon-save"> </i>
                        <?php echo $item[0]; ?>
                      </a>
                    <?php else : ?>
                      <a title="<?php echo JText::sprintf('COM_HELLOWORLD_HELLOWORLD_PLEASE_COMPLETE_DETAILS', $item[0], $item[0]) ?>" 
                         class="active hasTip" href="<?php echo JFilterOutput::ampReplace($item[1]); ?>">
                        <i  class="icon-warning"> </i>                          
                        <?php echo $item[0]; ?>
                      </a>
                    <?php endif; ?>
                  <?php elseif ($published) : ?>
                    <a title="<?php echo JText::sprintf('COM_HELLOWORLD_HELLOWORLD_DETAILS_NOT_REQUIRED', $item[0], $item[0]) ?>"
                       class="active hasTip" 
                       href="<?php echo JFilterOutput::ampReplace($item[1]); ?>">
                      <span class="">
                        <?php echo $item[0]; ?>
                      </span>               
                    </a>
                  <?php endif; ?>
                  </a>   
                <?php else : // Not the active menu item  ?>
                  <?php if (current($item) != 'Special offers') : ?>
                    <?php if ($item[3]) : ?>
                      <a title="<?php echo JText::sprintf('COM_HELLOWORLD_HELLOWORLD_DETAILS_COMPLETE', $item[0], $item[0]) ?>"
                         class="inactive hasTip" 
                         href="<?php echo JFilterOutput::ampReplace($item[1]); ?>">
                        <i class="hasTip icon-save"> </i>
                        <?php echo $item[0]; ?> 

                      </a>
                    <?php else : ?>
                      <a title="<?php echo JText::sprintf('COM_HELLOWORLD_HELLOWORLD_PLEASE_COMPLETE_DETAILS', $item[0], $item[0]) ?>"
                         class="inactive hasTip" href="<?php echo JFilterOutput::ampReplace($item[1]); ?>">
                        <i class="icon-warning hasTip"> </i>   
                        <?php echo $item[0]; ?> 
                      </a>
                    <?php endif; ?>     
                  <?php elseif ($published) : ?>
                    <a title="<?php echo JText::sprintf('COM_HELLOWORLD_HELLOWORLD_DETAILS_NOT_REQUIRED', $item[0], $item[0]) ?>"
                       class="inactive hasTip" 
                       href="<?php echo JFilterOutput::ampReplace($item[1]); ?>">

                      <i class="hasTip"> </i>
                      <?php echo $item[0]; ?>
                    </a>
                  <?php endif; ?>      

                <?php endif; ?>
              <?php else : // A new property    ?>
                <?php if (current($item) == 'Property details') : ?>
                  <a title="<?php echo Jtext::_('COM_HELLOWORLD_HELLOWORLD_PLEASE_COMPLETE_PROPERTY_DETAILS_FIRST') ?>"
                     class="active hasTip" 
                     href="#">
                    <i class="icon-warning"> </i> 
                    <?php echo $item[0]; ?> 
                  </a>
                <?php else: ?>
                  <span 
                    class="nolink hasTip"
                    title="<?php echo Jtext::_('COM_HELLOWORLD_HELLOWORLD_PLEASE_COMPLETE_PROPERTY_DETAILS') ?>">
                    <i class="nolink hasTip icon-lock"> </i>  
                    <?php echo $item[0]; ?> 
                  </span>

                <?php endif; ?>

              <?php endif; ?>
            <?php else : ?>
              <?php echo $item[0]; ?> 
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>



<?php else : // This is any other component   ?>
  <div id="sidebar">
    <div class="sidebar-nav">
      <?php if ($displayMenu) : ?>
        <ul id="submenu" class="nav nav-list">
          <div id="sidebar">
            <div class="sidebar-nav">	<?php foreach ($list as $item) : ?>
                <?php if (isset($item[2]) && $item[2] == 1) :
                  ?><li class="active"><?php
        else :
                  ?><li><?php
          endif;
                ?>
                    <?php
                    if ($hide) :
                      ?><a class="nolink"><?php echo $item[0]; ?><?php
          else :
            if (strlen($item[1])) :
                        ?><a href="<?php echo JFilterOutput::ampReplace($item[1]); ?>"><?php echo $item[0]; ?></a><?php
            else :
                        ?><?php echo $item[0]; ?><?php
            endif;
          endif;
                    ?>
                </li>
              <?php endforeach; ?>
              </ul>
            <?php endif; ?>
            <?php if ($displayMenu && $displayFilters) : ?>
              <hr />
            <?php endif; ?>
            <?php if ($displayFilters) : ?>
              <div class="filter-select hidden-phone">
                <h4 class="page-header"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></h4>
                <form action="<?php echo JRoute::_($action); ?>" method="post">
                  <?php foreach ($filters as $filter) : ?>
                    <label for="<?php echo $filter['name']; ?>" class="element-invisible"><?php echo $filter['label']; ?></label>
                    <select name="<?php echo $filter['name']; ?>" id="<?php echo $filter['name']; ?>" class="span12 small" onchange="this.form.submit()">
                      <?php if (!$filter['noDefault']) : ?>
                        <option value=""><?php echo $filter['label']; ?></option>
                      <?php endif; ?>
                      <?php echo $filter['options']; ?>
                    </select>
                    <hr class="hr-condensed" />
                  <?php endforeach; ?>
                </form>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
