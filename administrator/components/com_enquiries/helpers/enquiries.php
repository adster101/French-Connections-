<?php

// No direct access to this file
defined('_JEXEC') or die;

/**
 * HelloWorld component helper.
 */
abstract class EnquiriesHelper
{

  public static function addSubmenu($view = '')
  {

    //Get the current user id
    $user = JFactory::getUser();

    JHtmlSidebar::addEntry(JText::_('COM_RENTAL_SUBMENU_OWNERS_AREA_HOME'), '#');
    JHtmlSidebar::addEntry(JText::_('COM_RENTAL_PROPERTY_SUBMENU_MENU'), 'index.php');
    JHtmlSidebar::addEntry(JText::_('Property'), '#');

    JHtmlSidebar::addEntry(JText::_('COM_RENTAL_MENU'), 'index.php', ($view == 'rental'));
    JHtmlSidebar::addEntry(JText::_('COM_SPECIALOFFERS_MENU'), 'index.php?option=com_specialoffers', ($view == 'specialoffers'));
    JHtmlSidebar::addEntry(JText::_('COM_REVIEWS_MENU'), 'index.php?option=com_reviews', ($view == 'reviews'));

    JHtmlSidebar::addEntry(JText::_('Marketing'), '#');
    JHtmlSidebar::addEntry(JText::_('COM_ENQUIRIES_MENU'), 'index.php?option=com_enquiries', ($view == 'enquiries'));
    JHtmlSidebar::addEntry(JText::_('COM_STATS_MENU'), 'index.php?option=com_stats', ($view == 'stats'));
    JHtmlSidebar::addEntry(JText::_('Additional marketing'), 'index.php', ($view == 'marketing'));
  }

  /**
   * Get the actions
   */
  public static function getActions()
  {
    $user = JFactory::getUser();
    $result = new JObject;
    $assetName = 'com_enquiries';

    $actions = array(
        'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.delete', 'core.edit.state'
    );

    foreach ($actions as $action)
    {
      $result->set($action, $user->authorise($action, $assetName));
    }

    return $result;
  }

  /**
   * Get a list of filter options for the state of a module.
   *
   * @return	array	An array of JHtmlOption elements.
   */
  public static function getStateOptions()
  {
    // Build the filter options.
    $options = array();
    $options[] = JHtml::_('select.option', '1', JText::_('COM_MESSAGES_OPTION_READ'));
    $options[] = JHtml::_('select.option', '0', JText::_('COM_MESSAGES_OPTION_UNREAD'));
    $options[] = JHtml::_('select.option', '-2', JText::_('JTRASHED'));
    return $options;
  }

}
