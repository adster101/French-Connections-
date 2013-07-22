<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

jimport('joomla.utilities.date');

/**
 * An example custom profile plugin.
 *
 * @package		Joomla.Plugin
 * @subpackage	User.profile
 * @version		1.6
 */
class plgContentAirport extends JPlugin {

  /**
   * Constructor
   *
   * @access      protected
   * @param       object  $subject The object to observe
   * @param       array   $config  An array that holds the plugin configuration
   * @since       1.5
   */
  public function __construct(& $subject, $config) {
    parent::__construct($subject, $config);
    $this->loadLanguage();
  }

  /**
   * @param	string	$context	The context for the data
   * @param	int		$data		The user id
   * @param	object
   *
   * @return	boolean
   * @since	1.6
   */
  function onContentPrepareData($context, $data) {

    $data = '';
    
    return true;
  }

  /**
   * @param	JForm	$form	The form to be altered.
   * @param	array	$data	The associated data for the form.
   *
   * @return	boolean
   * @since	1.6
   */
  function onContentPrepareForm($form, $data) {

    if (!($form instanceof JForm)) {
      $this->_subject->setError('JERROR_NOT_A_FORM');
      return false;
    }

    // Add the extra fields to the form.
    JForm::addFormPath(dirname(__FILE__) . '/forms');
    $form->loadFile('airport', false);
    return true;
  }

}
