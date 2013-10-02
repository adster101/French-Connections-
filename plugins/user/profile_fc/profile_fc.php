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
      'firstname',
      'surname',
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
  function onContentPrepareData($context, $data) {
    // Check we are manipulating a valid form.
    if (!in_array($context, array('com_admin.profile', 'com_users.user'))) {

      return true;
    }

    if (is_object($data)) {
      $userId = isset($data->id) ? $data->id : 0;

      if (!isset($data->profile) and $userId > 0) {
        // Load the profile data from the database.
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('user_id,firstname,surname,address1,address2,city,region,country,postal_code,phone_1,phone_2,phone_3,website,aboutme,tos,vat_status,vat_number,company_number,receive_newsletter,where_heard');
        $query->from('#__user_profile_fc');
        $query->where('user_id = ' . (int) $userId);

        $db->setQuery($query);

        $result = $db->loadAssoc();

        // Check for a database error.
        if ($db->getErrorNum()) {
          $this->_subject->setError($db->getErrorMsg());
          return false;
        }

        // Merge the profile data.
        $data->profile = array();

        foreach ($result as $key => $value) {
          $data->$key = json_decode($value, true);
          if ($data->$key == '') {
            $data->$key = $value;
          }
        }
      }
    }

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
    // Require the helloworld helper class
    require_once(JPATH_ADMINISTRATOR . '/components/com_helloworld/helpers/helloworld.php');

    if (!($form instanceof JForm)) {
      $this->_subject->setError('JERROR_NOT_A_FORM');
      return false;
    }

    // Check we are manipulating a valid form.
    $name = $form->getName();

    if (!in_array($name, array('com_admin.profile', 'com_users.user'))) {
      return true;
    }

    // Remove the name field. This is maintained in the onAfterUserSave method by concatenating the first and surnames.
    $form->removeField('name');

    // Add the additional progile fields to the form.
    JForm::addFormPath(dirname(__FILE__) . '/profiles');
    $form->loadFile('profile', false);

    // Add the rule path to the form so we may validate the user profile details a bit.
    JForm::addRulePath('C:\xampp\htdocs\administrator\components\com_helloworld\models\rules');

    return true;
  }

  function onUserAfterSave($data, $isNew, $result, $error) {

    // Get the inputs so we can see whether we need to process anything or not
    $input = JFactory::getApplication()->input;
    $task = $input->get('task', '', 'string');
    $view = $input->get('view', '', 'string');
    $option = $input->get('option', '', 'string');
    $layout = $input->get('layout', '', 'string');

    /*
     * If the option is admin or user and the view matches then process the additional user profile info.
     */
    if (($view == 'profile' && $option == 'com_admin') || ($layout == 'edit' && $option == 'com_users')) {

      $userId = JArrayHelper::getValue($data, 'id', 0, 'int');

      try {

        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_helloworld/tables');
        $table = JTable::getInstance('UserProfileFc', 'HelloWorldTable');

        //$table->delete($userId);
        $data['user_id'] = $data['id'];

        $table->save($data);

        // TO DO - Concatenate the first and last names and update the joomla user 'name' field.
        $user = new JUser($userId);

        $userdata['name'] = $data['firstname'] . ' ' . $data['surname'];
        // Bind the data.
        
        if (!$user->bind($userdata)) {
          $this->setError($user->getError());
          return false;
        }

        // Store the data.
        if (!$user->save()) {
          $this->setError($user->getError());
          return false;
        }
      } catch (JException $e) {
        $this->_subject->setError($e->getMessage());
        return false;
      }
    }
    return true;
  }

  /**
   * Remove all user profile information for the given user ID
   *
   * Method is called after user data is deleted from the database
   *
   * @param	array		$user		Holds the user data
   * @param	boolean		$success	True if user was succesfully stored in the database
   * @param	string		$msg		Message
   */
  function onUserAfterDelete($user, $success, $msg) {
    if (!$success) {
      return false;
    }

    $userId = JArrayHelper::getValue($user, 'id', 0, 'int');

    if ($userId) {
      try {
        $db = JFactory::getDbo();
        $db->setQuery(
                'DELETE FROM #__user_profile_fc WHERE user_id = ' . $userId
        );

        if (!$db->query()) {
          throw new Exception($db->getErrorMsg());
        }
      } catch (JException $e) {
        $this->_subject->setError($e->getMessage());
        return false;
      }
    }

    return true;
  }

}
