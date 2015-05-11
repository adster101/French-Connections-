<?php

// No direct access to this file
defined('_JEXEC') or die;

/**
 * HelloWorld component helper.
 */
abstract class RealEstateHelper
{
  /**
   * @param	int $value
   * @param	int $i
   */
  public static function progressButton($listing_id = '', $unit_id = '', $controller = '', $action = 'edit', $icon = '', $button_text = '', $item = '', $urlParam = 'property_id', $btnClass = '', $current_view = '')
  {

    $progress_icon = 'warning';
    $okay_icon = 'ok';

    $html = '';

    $id = ($controller == 'propertyversions') ? $listing_id : $unit_id;

    if (!empty($item->latitude) && ($controller == 'propertyversions'))
    {
      $progress_icon = $okay_icon;
      $id = $listing_id;
    }
    elseif ($controller == 'contactdetails')
    {
      if ($item->use_invoice_details)
      {
        $progress_icon = $okay_icon;
      }
      elseif (!$item->use_invoice_details && !empty($item->first_name) && !empty($item->surname) && !empty($item->email_1) && !empty($item->phone_1))
      {
        $progress_icon = $okay_icon;
      }
      $id = $listing_id;
    }
    elseif (empty($item->title) && ($controller == 'propertyversions' ))
    {
      $progress_icon = $progress_icon;
      $id = $listing_id;
    }
    elseif (empty($item->unit_title) && $controller == 'unitversions' && !empty($listing_id) && $action == 'edit')
    {
      // This property has no unit, or unit details not completed...
      $progress_icon = $progress_icon;
    }
    elseif (!empty($unit_id) && $controller == 'images')
    {
      $progress_icon = ($item->images > 0) ? $okay_icon : $progress_icon;
    }
    elseif (!empty($unit_id) && $controller == 'availability')
    {
      $progress_icon = ($item->availability > 0) ? $okay_icon : $progress_icon;
    }
    elseif (!empty($unit_id) && $controller == 'unitversions' && !empty($listing_id))
    {
      $progress_icon = $okay_icon;
    }
    elseif (!empty($unit_id) && $controller == 'tariffs')
    {
      $progress_icon = ($item->tariffs > 0) ? $okay_icon : $progress_icon;
    }
    else if ($controller == 'reviews')
    {
      $id = $unit_id;
      $progress_icon = '';
    }
    elseif (!empty($unit_id) && $controller == 'unitversions')
    {
      $progress_icon = '';
    }
    $active = ($controller == $current_view) ? 'active' : '';
    if (!$btnClass)
    {
      $html .= '<li id="' . $controller . '" class="' . $active . '">';
    }
    $html .='<a class="' . $btnClass . '"'
            . ' href="' . JRoute::_('index.php?option=com_rental&task=' . $controller . '.' . $action . '&' . $urlParam . '=' . (int) $id . '&' . JSession::getFormToken() . '=1') . '"'
            . ' rel="tooltip">';
    if ($icon)
    {
      $html .= '<span class="icon icon-' . $icon . '"></span>';
    }
    $html .= '&nbsp;' . Jtext::_($button_text);
    if (!empty($progress_icon) && $icon)
    {
      $html .= '&nbsp;<span class="icon icon-' . $progress_icon . '"></span>';
    }
    $html .= '</a>';

    if (!$btnClass)
    {
      '</li>';
    }

    return $html;
  } 

  /*
   * Determines a list of notices to display for a property notifying the user of which units and which sections need attention
   */

  public static function getProgressNotices($progress = array())
  {

    $notices = array();
    // The sections we want to check for. Tariffs needs expanding for the more detailed tariff data (changeover day etc)
    $sections = array('images' => array(), 'availability' => array(), 'tariffs' => array());

    if (empty($progress))
    {
      return false;
    }

    foreach ($progress as $unit)
    {
      if (empty($unit->unit_title))
      {
        $notices['Accommodation']['units'][] = (!empty($unit->unit_title)) ? $unit->unit_title : 'New Unit';
      }
    }

    if (!$progress[0]->use_invoice_details &&
            empty($progress[0]->first_name) &&
            empty($progress[0]->surname) &&
            empty($progress[0]->email_1) &&
            empty($progress[0]->phone_1)
    )
    {
      $notices['contact_details']['units'] = false;
    }

    foreach ($sections as $section => $value)
    {

      foreach ($progress as $key => $unit)
      {
        if ($unit->$section == 0)
        { // If the unit doesn't have this section completed
          if (!array_key_exists($section, $notices))
          {
            $notices[$section]['units'] = '';
          }

          // Add the unit that is failing to the list
          $notices[$section]['units'][] = (!empty($unit->unit_title)) ? $unit->unit_title : 'New Unit';
        }
      }
    }



    return $notices;
  }

  /*
   * Get a list of filter options for the review state of a property
   *
   * @return  array An array of JHtmlOption elements
   */

  public static function getReviewOptions()
  {
    // Build the filter options.
    $options = array();
    $options[] = JHtml::_('select.option', '1', JText::_('COM_RENTAL_HELLOWORLD_UPDATED'));
    $options[] = JHtml::_('select.option', '2', JText::_('COM_RENTAL_HELLOWORLD_FOR_REVIEW'));
    return $options;
  }

  /*
   * Get a list of filter options for the snooze state of a property
   *
   * @return  array An array of JHtmlOption elements
   */

  public static function getSnoozeOptions()
  {
    // Build the filter options.
    $options = array();
    $options[] = JHtml::_('select.option', '1', JText::_('COM_RENTAL_HELLOWORLD_HIDE_SNOOZED'));
    $options[] = JHtml::_('select.option', '2', JText::_('COM_RENTAL_HELLOWORLD_SHOW_SNOOZED'));
    return $options;
  }

  /*
   * Get a list of filter options for the snooze state of a property
   *
   * @return  array An array of JHtmlOption elements
   */

  public static function getDateFilterOptions()
  {
    // Build the filter options.
    $options = array();
    $options[] = JHtml::_('select.option', '', JText::_('JSELECT'));
    $options[] = JHtml::_('select.option', 'expiry_date', 'Expiry date');
    $options[] = JHtml::_('select.option', 'created_on', 'Date created');
    return $options;
  }


  /**
   * Get the actions
   */
  public static function getActions($assetName = 'com_realestate')
  {
    $user = JFactory::getUser();
    $result = new JObject;

    $actions = array(
        'core.admin',
        'core.manage',
        'core.create',
        'core.delete',
        'core.edit',
        'core.edit.state',
        'core.edit.own',
        'realestate.propertyversions.view',
        'realestate.images.view',
        'realestate.listings.filter',
        'realestate.listings.showowner',
        'realestate.listing.review',
        'realestate.listing.submit',
        'realestate.listing.admin',
        'realestate.listings.showowner',
        'realestate.notes.view',
        'realestate.listing.snooze24'
    );


    foreach ($actions as $action)
    {
      $result->set($action, $user->authorise($action, $assetName));
    }
    return $result;
  }


  /*
   * Get the default language
   */

  public static function getDefaultLanguage()
  {
    $lang = & JFactory::getLanguage()->getTag();
    return $lang;
  }


 

}
