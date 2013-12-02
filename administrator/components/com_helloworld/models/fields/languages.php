<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @since		1.6
 */
class JFormFieldLanguages extends JFormFieldList {

  /**
   * The form field type.
   *
   * @var		string
   * @since	1.6
   */
  protected $type = 'Languages';

  /**
   * Method to get the field options.
   *
   * @return	array	The field option objects.
   * @since	1.6
   */
  protected function getOptions() {

    $langs = array(
        0 => 'LANGUAGE_ENGLISH',
        1 => 'LANGUAGE_FRENCH',
        2 => 'LANGUAGE_ITALIAN',
        3 => 'LANGUAGE_GERMAN',
        4 => 'LANGUAGE_SPANISH',
        5 => 'LANGUAGE_DUTCH',
        6 => 'LANGUAGE_GREEK',
        7 => 'LANGUAGE_POLISH',
        8 => 'LANGUAGE_HUNGARIAN',
        9 => 'LANGUAGE_PORTUGESE',
        10 => 'LANGUAGE_RUSSIAN',
        11 => 'LANGUAGE_WELSH',
        12 => 'LANGUAGE_DANISH',
        13 => 'LANGUAGE_CZECH',
        14 => 'LANGUAGE_NORWEGIAN',
        15 => 'LANGUAGE_SWEDISH',
        16 => 'LANGUAGE_IRISH',
        17 => 'LANGUAGE_JAPANESE',
        18 => 'LANGUAGE_FINNISH',
        19 => 'LANGUAGE_CHINESE',
        20 => 'LANGUAGE_CATALAN'
    );
    
    // Loop over each subtree item
    foreach ($langs as $lang) {
      $options[] = JHtml::_('select.option', $lang, JText::_($lang));
    }
    

    // Merge any additional options in the XML definition.
    $options = array_merge(parent::getOptions(), $options);
    return $options;
  }

}


