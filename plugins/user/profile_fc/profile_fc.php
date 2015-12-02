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
class plgUserProfile_fc extends JPlugin
{
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
      'state',
      'postal_code',
      'email_alt',
      'phone_1',
      'phone_2',
      'phone_3',
      'aboutme',
      'tos',
      'vat_status',
      'vat_number',
      'company_number',
      'receive_newsletter',
      'where_heard',
      'exchange_rate_eur',
      'exchange_rate_usd',
      'sms_alert_number',
      'dummy_validation_code',
      'sms_status',
      'sms_valid',
      'sms_inactive',
      'sms_nightwatchman'
  );

  /**
   * Affects constructor behavior. If true, language files will be loaded automatically.
   *
   * @var    boolean
   * @since  3.1
   */
  protected $autoloadLanguage = true;

  /**
   * Constructor
   *
   * @access      protected
   * @param       object  $subject The object to observe
   * @param       array   $config  An array that holds the plugin configuration
   * @since       1.5
   */
  public function __construct(& $subject, $config)
  {
    parent::__construct($subject, $config);
    JFormHelper::addFieldPath(dirname(__FILE__) . '/fields');
    JText::script('JGLOBAL_VALIDATION_FORM_FAILED');
  }

  /**
   * @param	string	$context	The context for the data
   * @param	int		$data		The user id
   * @param	object
   *
   * @return	boolean
   * @since	1.6
   */
  function onContentPrepareData($context, $data)
  {
    // Check we are manipulating a valid form.
    if (!in_array($context, array('com_admin.profile', 'com_users.user')))
    {
      return true;
    }

    if (is_object($data))
    {
      $userId = isset($data->id) ? $data->id : 0;

      if (!isset($data->profile) and $userId > 0)
      {
        // Load the profile data from the database.
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('
          user_id,
          firstname,
          surname,
          address1,
          address2,
          city,
          region,
          country,
          state,
          postal_code,
          email_alt,
          phone_1,
          phone_2,
          phone_3,
          aboutme,
          tos,
          vat_status,
          vat_number,
          company_number,
          receive_newsletter,
          where_heard,
          exchange_rate_eur,
          exchange_rate_usd,
          sms_alert_number,
          sms_status,
          sms_valid,
          sms_inactive,
          sms_nightwatchman      
        ');
        $query->from('#__user_profile_fc');
        $query->where('user_id = ' . (int) $userId);

        $db->setQuery($query);

        $result = $db->loadAssoc();

        // Check for a database error.
        if ($db->getErrorNum())
        {
          $this->_subject->setError($db->getErrorMsg());
          return false;
        }

        // Merge the profile data.
        $data->profile = array();

        foreach ($result as $key => $value)
        {
          $data->$key = json_decode($value, true);
          if ($data->$key == '')
          {
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
  function onContentPrepareForm($form, $data)
  {
    if (!($form instanceof JForm))
    {
      $this->_subject->setError('JERROR_NOT_A_FORM');
      return false;
    }

    $name = $form->getName();

    // Check we are manipulating a valid form.
    if (!in_array($name, array('com_admin.profile', 'com_users.user')))
    {
      return true;
    }

    require_once(JPATH_ADMINISTRATOR . '/components/com_rental/helpers/rental.php');
    $user = JFactory::getUser();
    $input = JFactory::getApplication()->input;
    $form_data = $input->get('jform', array(), 'array');
    $vat_status = '';
    $lang = JFactory::getLanguage();
    $lang->load('com_rental');


    JText::script('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
    JText::script('COM_RENTAL_HELLOWORLD_UNSAVED_CHANGES');

    // Remove the name field. This is maintained in the onAfterUserSave method by concatenating the first and surnames.
    $form->setFieldAttribute('name', 'required', 'false');
    $form->setFieldAttribute('name', 'hidden', 'true');

    // Add the additional progile fields to the form.
    JForm::addFormPath(dirname(__FILE__) . '/profiles');
    $form->loadFile('profile', false);

    // Add the rule path to the form so we may validate the user profile details a bit.
    JForm::addRulePath(JPATH_LIBRARIES . '/frenchconnections/forms/rules');
    JForm::addFieldPath(JPATH_LIBRARIES . '/frenchconnections/forms/fields');

    if (!empty($data))
    {
      $vat_status = (isset($data->vat_status)) ? $data->vat_status : '';
    }
    else if (!empty($form_data))
    {
      $vat_status = $form_data['vat_status'];
    }

    if ($vat_status == 'ZA')
    {
      $form->setFieldAttribute('company_number', 'required', 'required');
    }

    if ($vat_status == 'ECS')
    {
      $form->setFieldAttribute('vat_number', 'required', 'required');
    }

    if (!$data->sms_valid)
    {
      $form->removeField('sms_valid_message');
    }

    if ($data->sms_valid)
    {
      $form->removeField('dummy_validation_code');
    }

    /* If the user doesn't have manage users permissions and no active or live properties
      we don't want them to update some of their account settings...
     * */
    if (!$user->authorise('core.manage', 'com_users'))
    {
      $form->setFieldAttribute('firstname', 'required', 'false');
      $form->setFieldAttribute('firstname', 'readonly', 'true');

      $form->setFieldAttribute('surname', 'required', 'false');
      $form->setFieldAttribute('surname', 'readonly', 'true');
    }



    return true;
  }

  function onUserAfterSave($data, $isNew, $result, $error)
  {

    // Get the inputs so we can see whether we need to process anything or not
    $input = JFactory::getApplication()->input;
    $task = $input->get('task', '', 'string');
    $view = $input->get('view', '', 'string');
    $option = $input->get('option', '', 'string');
    $layout = $input->get('layout', '', 'string');
    $params = JComponentHelper::getParams('com_rental');
    $userdata = array();
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/tables');
    $table = JTable::getInstance('UserProfileFc', 'RentalTable');

    jimport('clickatell.SendSMS');

    /*
     * If the option is admin or user and the view matches then process the additional user profile
     * info.
     */
    if (($view == 'profile' && $option == 'com_admin') || ($layout == 'edit' && $option == 'com_users'))
    {

      $userId = JArrayHelper::getValue($data, 'id', 0, 'int');

      try
      {
        // Load the existing user profile data for this user.
        $table->load($data['id']);

        /*
         * Get the SMS related values from the validated form data
         */
        $isValid = $data['sms_valid'];
        $sms_number = $data['sms_alert_number'];
        $sms_verification_code = $data['dummy_validation_code'];
        $sms_status = $data['sms_status'];

        // Set the nightwatchman flag if unset in the form
        if (empty($data['sms_nightwatchman']))
        {
          $data['sms_nightwatchman'] = 0;
        }
        /*
         * If we have an sms number but it's not been validated and there we haven't send a verification code
         * OR
         * The sms number that has been passed is different to the one on record.
         */
        if (($sms_number && !$isValid && !$sms_status) || (!empty($sms_number) && strcmp($sms_number, $table->sms_alert_number) != 0))
        {
          $code = rand(10000, 100000);
          $data['sms_validation_code'] = $code;
          $data['sms_status'] = 'VALIDATION';
          $data['sms_valid'] = 0;
          $data['sms_alert_number'] = $sms_number;

          // Clickatel baby
          $sendsms = new SendSMS($params->get('username'), $params->get('password'), $params->get('id'));

          /*
           *  if the login return 0, means that login failed, you cant send sms after this 
           */
          if (($sendsms->login()))
          {
            $login = true;
          }

          /*
           * Send sms using the simple send() call 
           */
          if ($login)
          {
            $sendsms->send($sms_number, JText::sprintf('COM_RENTAL_HELLOWORLD_SMS_VERIFICATION_CODE', $code));
          }
        }
        else if (($sms_number) && !$isValid && $sms_status == 'VALIDATION')
        {

          // The number hasn't been validated but we might have a validation code to verify
          // Get the validation code from the data base and compare it to that passed in via the form
          $data['sms_validation_code'] = $table->sms_validation_code;
          $data['sms_valid'] = 0;

          if ($sms_verification_code == $table->sms_validation_code)
          {
            $data['sms_status'] = 'ACTIVE';
            $data['sms_valid'] = 1;
          }
        }
        else if (empty($sms_number))
        {
          // Opt out of alerts
          $data['sms_validation_code'] = '';
          $data['sms_status'] = '';
          $data['sms_valid'] = 0;
          $data['sms_alert_number'] = '';
        }

        // Unset id which is the user id and set user_id in $data
        $data['user_id'] = $data['id'];

        unset($data['id']);

        // Set the table primary key to ID. As this is unset a new record will be created, if $isNew
        if ($isNew)
        {
          $table->set('_tbl_keys', array('id'));
        }
        // Save the data back to the user profile table
        if (!$table->save($data))
        {
          $this->setError($table->getError());
          return false;
        }

        // Concatenate the first and last names and update the joomla user 'name' field.
        $user = JTable::getInstance('User', 'JTable');

        // Load the existing user details
        $user->load($userId);

        $userdata['name'] = $data['firstname'] . ' ' . $data['surname'];

        /* Store the data. This is triggering a double call of this plugin! */
        if (!$user->save($userdata))
        {
          $this->setError($user->getError());
          return false;
        }
      }
      catch (JException $e)
      {
        $this->_subject->setError($e->getMessage());
        return false;
      }
    }
    return true;
  }

  /**
   * Method is called before user data is stored in the database
   *
   * @param   array    $user   Holds the old user data.
   * @param   boolean  $isnew  True if a new user is stored.
   * @param   array    $data   Holds the new user data.
   *
   * @return    boolean
   *
   * @since   3.1
   * @throws    InvalidArgumentException on invalid date.
   */
  public function onUserBeforeSave($user, $isnew, $data)
  {

    // Check that the date is valid.
    if (!empty($data['firstname']) && !empty($data['surname']))
    {
      try
      {
        // Concatenate the ffirst and surname fields and save into the name field.
        $data['name'] = $data['firstname'] . ' ' . $data['surname'];
      }
      catch (Exception $e)
      {
        // Throw an exception if date is not valid.
        throw new InvalidArgumentException(JText::_('PLG_USER_PROFILE_ERROR_INVALID_NAME'));
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
  function onUserAfterDelete($user, $success, $msg)
  {
    if (!$success)
    {
      return false;
    }

    $userId = JArrayHelper::getValue($user, 'id', 0, 'int');

    if ($userId)
    {
      try
      {
        $db = JFactory::getDbo();
        $db->setQuery(
                'DELETE FROM #__user_profile_fc WHERE user_id = ' . $userId
        );

        if (!$db->query())
        {
          throw new Exception($db->getErrorMsg());
        }
      }
      catch (JException $e)
      {
        $this->_subject->setError($e->getMessage());
        return false;
      }
    }

    return true;
  }

}
