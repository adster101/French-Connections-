<?php

// No direct access to this file
defined('_JEXEC') or die;

/**
 * HelloWorld component helper.
 */
abstract class FeaturedPropertiesHelper {

  /**
   * Get the actions
   */
  public static function getActions() {
    $user = JFactory::getUser();
    $result = new JObject;
    $assetName = 'com_featuredproperties';

    $actions = array(
        'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.delete', 'core.edit.state'
    );

    foreach ($actions as $action) {
      $result->set($action, $user->authorise($action, $assetName));
    }

    return $result;
  }

  public function addSubmenu($vName = '') {
    JHtmlSidebar::addEntry(JText::_('COM_FEATUREDPROPERTIES_MANAGE_FEATUREDPROPERTIES'), 'index.php?option=com_featuredproperties', ($vName == 'featuredproperties' || $vName == 'featuredproperty'));
    //JHtmlSidebar::addEntry(JText::_('COM_FEATUREDPROPERTIES_MANAGE_FEATURED_PROPERTY_TYPE'), 'index.php?option=com_featuredproperties&view=featuredpropertytypes', ($vName == 'featuredpropertytype' || $vName == 'featuredpropertytypes'));
    JHtmlSidebar::addEntry(JText::_('COM_FEATUREDPROPERTIES_MANAGE_FEATURED_PROPERTY_TYPE'), 'index.php?option=com_categories&view=categories&extension=com_featuredproperties', ($vName == 'categories'));
  }

  /**
   * Get a list of filter options for the state of a module. As below, used mainly to override default labels.
   *
   * @return	array	An array of JHtmlOption elements.
   */
  public static function getStateOptions() {
    // Build the filter options.
    $options = array();
    $options[] = JHtml::_('select.option', '1', JText::_('COM_FEATUREDPROPERTIES_PAID'));
    $options[] = JHtml::_('select.option', '0', JText::_('COM_FEATUREDPROPERTIES_UNPAID'));
    $options[] = JHtml::_('select.option', '-2', JText::_('JTRASHED'));
    return $options;
  }

  public static function getFeaturedPropertyTypeOptions() {

		$options = JHtml::_('category.options', 'com_featuredproperties');
    
    return $options;
    
  }

  /**
   * This simply returns an array of states for the list view. Essentially, it just overrides the default 'published', 'unpublished' labels
   * so we can tailor the labels to the component.
   * @return array
   */
  public static function getPaidStates() {
    $states = array(
        1 => array(
            'unpublish',
            'COM_BANNERS_BANNERS_PINNED',
            'COM_BANNERS_BANNERS_HTML_PIN_BANNER',
            'COM_BANNERS_BANNERS_PINNED',
            true,
            'publish',
            'publish'
        ),
        0 => array(
            'publish',
            'COM_BANNERS_BANNERS_UNPINNED',
            'COM_BANNERS_BANNERS_HTML_UNPIN_BANNER',
            'COM_BANNERS_BANNERS_UNPINNED',
            true,
            'unpublish',
            'unpublish'
        ), -2 => array(
            'publish',
            'COM_BANNERS_BANNERS_UNPINNED',
            'COM_BANNERS_BANNERS_HTML_UNPIN_BANNER',
            'COM_BANNERS_BANNERS_UNPINNED',
            true,
            'trash',
            'trash'
        ),
    );

    return $states;
  }

}