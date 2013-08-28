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
class plgUserProfile_fc extends JPlugin {
  /*
   * ARRAY OF FIELDS AND WHAT NOT
   */

  private static $fields = array(
      'address1',
      'address2',
      'city',
      'region',
      'country',
      'postal_code',
      'phone_1',
      'phone_2',
      'phone_3',
      'website',
      'aboutme',
      'tos',
      'vat_status',
      'vat_number',
      'company_number',
      'receive_newsletter',
      'where_heard'
  );

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
    JFormHelper::addFieldPath(dirname(__FILE__) . '/fields');
  }

  /**
   * @param	string	$context	The context for the data
   * @param	int		$data		The user id
   * @param	object
   *
   * @return	boolean
   * @since	1.6
   */
  function onUserAfterSave($data, $isNew, $result, $error) {

    $userId = JArrayHelper::getValue($data, 'id', 0, 'int');

    // Need to hijack the email generation here as per the github plugin...



    return true;
  }

}
