<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if (!empty($displayData))
{

  $input = JFactory::getApplication()->input;
  $preview = $input->get('preview', '', 'int');

  $link = 'index.php?option=com_accommodation&Itemid=' . (int) $displayData->itemid . '&id=' . (int) $displayData->property_id . '&unit_id=' . (int) $displayData->unit_id;

  if ((int) $preview && $preview == 1)
  {
    $link .= '&preview=1';
  }

  $route = JRoute::_($link);
}

