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
  function onContentPrepareData($context, $data) {
    // Check we are manipulating a valid form.
    if (!in_array($context, array('com_users.registration'))) {

      return true;
    }

    if (is_object($data)) {
      $userId = isset($data->id) ? $data->id : 0;

      if (!isset($data->profile) and $userId > 0) {
        // Load the profile data from the database.
        $db = JFactory::getDbo();
        $db->setQuery(
                'SELECT 
             address1, 
             address2,
             city, 
             region,
             country,
             postal_code, 
             phone_1,
             phone_2,
             phone_3, 
             website, 
             aboutme, 
             tos, 
             vat_status, 
             vat_number,
             company_number,
             receive_newsletter,
             where_heard
           FROM #__user_profile_fc' .
                ' WHERE user_id = ' . (int) $userId
        );

        //$db->setQuery('SHOW FULL COLUMNS FROM #__user_profile_fc');

        $results = $db->loadAssoc();

        // Check for a database error.
        if ($db->getErrorNum()) {
          $this->_subject->setError($db->getErrorMsg());
          return false;
        }

        // Merge the profile data.
        $data->profile = array();


        foreach ($results as $key => $value) {
          $data->profile[$key] = json_decode($value, true);
          if ($data->profile[$key] == '') {
            $data->profile[$key] = $value;
          }
        }
      }

      if (!JHtml::isRegistered('users.url')) {
        JHtml::register('users.url', array(__CLASS__, 'url'));
      }
      if (!JHtml::isRegistered('users.calendar')) {
        JHtml::register('users.calendar', array(__CLASS__, 'calendar'));
      }
      if (!JHtml::isRegistered('users.tos')) {
        JHtml::register('users.tos', array(__CLASS__, 'tos'));
      }
    }

    return true;
  }

  public static function url($value) {
    if (empty($value)) {
      return JHtml::_('users.value', $value);
    } else {
      $value = htmlspecialchars($value);
      if (substr($value, 0, 4) == "http") {
        return '<a href="' . $value . '">' . $value . '</a>';
      } else {
        return '<a href="http://' . $value . '">' . $value . '</a>';
      }
    }
  }

  public static function calendar($value) {
    if (empty($value)) {
      return JHtml::_('users.value', $value);
    } else {
      return JHtml::_('date', $value, null, null);
    }
  }

  public static function tos($value) {
    if ($value) {
      return JText::_('JYES');
    } else {
      return JText::_('JNO');
    }
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

    // Below should really be done via a ACL rule
    $isOwner = HelloWorldHelper::isOwner();

    if (!($form instanceof JForm)) {
      $this->_subject->setError('JERROR_NOT_A_FORM');
      return false;
    }

    // Check we are manipulating a valid form.
    $name = $form->getName();

    if (!in_array($name, array('com_users.registration'))) {
      return true;
    }

    // Check which form we are manipulating, we are only interested in the com_admin.profile form
    // just now as this is the one we want the owner/public to fill out. 
    if (in_array($name, array('com_admin.profile'))) {

      // If this user is in the owner user group 
      if ($isOwner) {

        $form->setFieldAttribute('name', 'readonly', 'true');
      }
    }

    // Add the registration fields to the form.
    JForm::addFormPath(dirname(__FILE__) . '/profiles');

    $form->loadFile('profile', false);

    // Add the rule path to the form so we may validate the user profile details a bit.
    JForm::addRulePath(JPATH_ADMINISTRATOR . '\components\com_helloworld\models\rules');

    $tosarticle = $this->params->get('register-tos_article');
    $tosenabled = $this->params->get('register-require_tos', 0);

    // We need to be in the registration form, field needs to be enabled and we need an article ID
    if (!in_array($name, array('com_users.registration', 'com_admin.profile')) || !$tosenabled || !$tosarticle) {
      // We only want the TOS in the registration form
      $form->removeField('tos', 'profile');
    } else {
      // Push the TOS article ID into the TOS field.
      $form->setFieldAttribute('tos', 'article', $tosarticle, 'profile');
    }


    foreach (self::$fields as $field) {

      // Case using the users manager in admin
      if ($name == 'com_users.user') {
        // Remove the field if it is disabled in registration and profile
        if ($this->params->get('register-require_' . $field, 1) == 0 && $this->params->get('profile-require_' . $field, 1) == 0) {
          $form->removeField($field, 'profile');
        }



        $form->setFieldAttribute($field, 'required', ($this->params->get('profile-require_' . $field) == 2) ? 'required' : '', 'profile');
      }

      // Case registration
      elseif ($name == 'com_users.registration') {
        // Toggle whether the field is required.
        if ($this->params->get('register-require_' . $field, 1) > 0) {
          $form->setFieldAttribute($field, 'required', ($this->params->get('register-require_' . $field) == 2) ? 'required' : '', 'profile');
        } else {
          $form->removeField($field, 'profile');
        }
      }

      // Case profile in site or admin
      elseif ($name == 'com_users.profile' || $name == 'com_admin.profile') {

        // Toggle whether the field is required.
        if ($this->params->get('profile-require_' . $field, 1) == 2) {
          $form->setFieldAttribute($field, 'required', ($this->params->get('profile-require_' . $field) == 2) ? 'required' : '', 'profile');
        } elseif ($this->params->get('profile-require_' . $field, 1) == 1) {
          $form->setFieldAttribute($field, 'optional', ($this->params->get('profile-require_' . $field) == 1) ? 'required' : '', 'profile');
        } else {
          $form->removeField($field, 'profile');
        }
      }
    }


    // After all that we only want to show these additional fields to owners when they are updating their profile
    // or when an admin is editing an owners profile. 
    if (in_array($name, array('com_admin.profile', 'com_users.user')) && !$isOwner) {

      // Is the admin user editing a user in the owners user group?
      $editUserID = JRequest::getVar('id', null, 'GET', 'int');
      if (!HelloWorldHelper::isOwner($editUserID)) {
        $form->removeGroup('profile');
      }
    }

    if ($isOwner) {
      // If this is an owner then we remove the params/settings field from the form as we don't want them setting their
      // own editors etc.
      $form->removeGroup('params');
    }

    return true;
  }

  function onUserAfterSave($data, $isNew, $result, $error) {
    
    $userId = JArrayHelper::getValue($data, 'id', 0, 'int');

    $app = JFactory::getApplication();

    if (!$isNew && $app->isSite()) {
      
      if (in_array(10, $data['groups']) && count($data['groups'] == 1) ) {
        
        $task = $app->input->get('task');
        
        $app->redirect('/administrator','Woot');
        
        
        
      }
      
    }
    
    
    
    // Need to hijack the email generation here as per the github plugin...
    
    
    


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
