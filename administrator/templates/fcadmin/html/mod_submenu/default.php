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
?>
<?php
if ($context == 'com_helloworld') : // If we are in the property manager then we want to output a fancy progress menu type thing. 
  // Get the id of the property
  $id = JRequest::getVar('id', '', 'GET', 'integer');

  $availability = JApplication::getUserState('property.' . $id . '.progress.availability');

  $tariffs = JApplication::getUserState('property.' . $id . '.progress.tariffs');

  // Manipulate list to include the progress for each of the required stages - This relies on the order remaining the same.
  foreach ($list as &$item) {
    $item[3] = false;
  }

  if ($id) { // Id is set therfore is not a new propery and therefore we assume it has been completed correctly
    $list[0][3] = true;
  }

  if ($availability) {
    $list[1][3] = true;
  }

  if ($tariffs) {
    $list[2][3] = true;
  }
  ?>

  <ul id="submenu" class="property-manager">
  <?php foreach ($list as &$item) : ?>
      <li>
      <?php if (strlen($item[1])) : // If there's a url  ?>
          <?php if ($id) : // There is an id so not a new property   ?>
            <?php if (isset($item[2]) && $item[2] == 1) : // Is this the 'active' item ?>
              <a class="active" href="<?php echo JFilterOutput::ampReplace($item[1]); ?>">
              <?php if ($item[3]) : ?>
                  <span title="<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_PROPERTY_DETAILS_COMPLETE'); ?>" class="icon-16-allowed hasTip">
                  <?php echo $item[0]; ?>
                  </span>
                  <?php else : ?>
                  <span title="<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_PLEASE_COMPLETE_PROPERTY_DETAILS_FIRST'); ?>" class="icon-16-notice hasTip">
                  <?php echo $item[0]; ?>
                  </span>
                  <?php endif; ?>
              </a>   
              <?php else : // Not the active menu item  ?>
              <a class="inactive" href="<?php echo JFilterOutput::ampReplace($item[1]); ?>">
              <?php if ($item[3]) : ?>
                  <span class="hasTip icon-16-allowed" title="<?php echo Jtext::_('COM_HELLOWORLD_HELLOWORLD_PLEASE_COMPLETE_') ?>">   
                  <?php echo $item[0]; ?> 
                  </span>
                  <?php else : ?>
                  <span class="hasTip icon-16-notice" title="<?php echo Jtext::_('COM_HELLOWORLD_HELLOWORLD_PLEASE_COMPLETE_') ?>">   
                  <?php echo $item[0]; ?> 
                  </span>
                  <?php endif; ?>
              </a>
              <?php endif; ?>
          <?php else : // A new property   ?>
            <?php if (current($item) == 'Property details') : ?>
              <a class="active" href="<?php echo JFilterOutput::ampReplace($item[1]); ?>">
                <span class="hasTip icon-16-notice" title="<?php echo Jtext::_('COM_HELLOWORLD_HELLOWORLD_PLEASE_COMPLETE_') ?>">   
          <?php echo $item[0]; ?> 
                </span>
              </a>
        <?php else: ?>
              <span class="nolink hasTip icon-16-denyinactive denyinactive" title="<?php echo Jtext::_('COM_HELLOWORLD_HELLOWORLD_PLEASE_COMPLETE_') ?>">               
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



<?php else : // This is any other component  ?>
  <ul id="submenu">
  <?php foreach ($list as $item) : ?>
      <li>
      <?php
      if ($hide) :
        if (isset($item[2]) && $item[2] == 1) :
          ?><span class="nolink active"><?php echo $item[0]; ?></span><?php
      else :
          ?><span class="nolink"><?php echo $item[0]; ?></span><?php
      endif;
    else :
      if (strlen($item[1])) :
        if (isset($item[2]) && $item[2] == 1) :
            ?><a class="active" href="<?php echo JFilterOutput::ampReplace($item[1]); ?>"><?php echo $item[0]; ?></a><?php
        else :
            ?><a href="<?php echo JFilterOutput::ampReplace($item[1]); ?>"><?php echo $item[0]; ?></a><?php
        endif;
      else :
          ?><?php echo $item[0]; ?><?php
      endif;
    endif;
      ?>
      </li>
      <?php endforeach; ?>
  </ul>
  <?php endif; ?>
