<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Quickicon.Joomlaupdate
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Joomla! udpate notification plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Quickicon.Joomlaupdate
 * @since       2.5
 */
class plgQuickiconFc extends JPlugin {

  /**
   * Constructor
   *
   * @param       object  $subject The object to observe
   * @param       array   $config  An array that holds the plugin configuration
   *
   * @since       2.5
   */
  public function __construct(& $subject, $config) {
    parent::__construct($subject, $config);
    $this->loadLanguage();
  }

  /**
   * This method is called when the Quick Icons module is constructing its set
   * of icons. You can return an array which defines a single icon and it will
   * be rendered right after the stock Quick Icons.
   *
   * @param  $context  The calling context
   *
   * @return array A list of icon definition associative arrays, consisting of the
   * 				 keys link, image, text and access.
   *
   * @since       2.5
   */
  public function onGetIcons($context) {
    if ($context != $this->params->get('context', 'mod_quickicon') || !JFactory::getUser()->authorise('core.manage', 'com_helloworld')) {
      return;
    }

    return array(
        array(
            'link' => JRoute::_('index.php?option=com_helloworld'),
            'image' => 'home',
            'text' => JText::_('MOD_QUICK_ICON_FC_PROPERTY_MANAGER'),
            'id' => 'woot'
        ),
        array(
            'link' => JRoute::_('index.php?option=com_classification'),
            'image' => 'folder',
            'text' => JText::_('MOD_QUICKICON_FC_CLASSIFICATION'),
            'access' => array('core.manage', 'com_classification')
        ),
        array(
            'link' => JRoute::_('index.php?option=com_reviews'),
            'image' => 'folder',
            'text' => JText::_('MOD_QUICKICON_FC_REVIEWS'),
            'access' => array('core.manage', 'com_reviews')
        ),
        array(
            'link' => JRoute::_('index.php?option=com_specialoffers'),
            'image' => 'folder',
            'text' => JText::_('MOD_QUICKICON_FC_SPECIALOFFERS'),
            'access' => array('core.manage', 'com_specialoffers')
        ),
        array(
            'link' => JRoute::_('index.php?option=com_enquiries'),
            'image' => 'folder',
            'text' => JText::_('MOD_QUICKICON_FC_ENQUIRIES'),
            'access' => array('core.manage', 'com_enquiries')
        ),
        array(
            'link' => JRoute::_('index.php?option=com_itemcosts'),
            'image' => 'folder',
            'text' => JText::_('MOD_QUICKICON_FC_ITEMCOSTS'),
            'access' => array('core.manage', 'com_itemcosts')
        ),
        array(
            'link' => JRoute::_('index.php?option=com_payments'),
            'image' => 'folder',
            'text' => JText::_('MOD_QUICKICON_FC_PAYMENTS'),
            'access' => array('core.manage', 'com_payments')
        )
    );
  }

}
