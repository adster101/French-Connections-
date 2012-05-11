<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * RSForm! Pro system plugin
 */
class plgSystemRSFPRegistration extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatibility we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemRSFPRegistration(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->params = $config;
	}
	
	function canRun()
	{
		if (class_exists('RSFormProHelper')) return true;
		
		$helper = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'helpers'.DS.'rsform.php';
		if (file_exists($helper))
		{
			require_once($helper);
			RSFormProHelper::readConfig();
			return true;
		}
		
		return false;
	}
	
	function rsfp_onFormSave($form)
	{
		$post = JRequest::get('post', JREQUEST_ALLOWRAW);
		$post['form_id'] = $post['formId'];
		
		$row = JTable::getInstance('RSForm_Registration', 'Table');
		$post['published'] = $post['jur_published'];
		if (!$row)
			return;
		if (!$row->bind($post))
		{
			JError::raiseWarning(500, $row->getError());
			return false;
		}
		
		$row->reg_merge_vars = serialize($post['reg_vars']);
		
		$db =& JFactory::getDBO();
		$db->setQuery("SELECT form_id FROM #__rsform_registration WHERE form_id='".(int) $post['form_id']."'");
		if (!$db->loadResult())
		{
			$db->setQuery("INSERT INTO #__rsform_registration SET form_id='".(int) $post['form_id']."'");
			$db->query();
		}
		
		if ($row->store())
		{
			return true;
		}
		else
		{
			JError::raiseWarning(500, $row->getError());
			return false;
		}
	}
	
	function rsfp_bk_onAfterShowFormEditTabs()
	{
		$formId = JRequest::getInt('formId');
		
		$lang =& JFactory::getLanguage();
		$lang->load('plg_system_rsfpregistration');
		
		jimport('joomla.html.pane');
		$tabs =& JPane::getInstance('Tabs', array(), true);
		
		$row = JTable::getInstance('RSForm_Registration', 'Table');
		if (!$row) return;
		$row->load($formId);
		$row->reg_merge_vars = @unserialize($row->reg_merge_vars);
		if ($row->reg_merge_vars === false)
			$row->reg_merge_vars = array();
		
		// Fields
		$fields_array = $this->_getFields($formId);
		$fields = array();
		foreach ($fields_array as $field)
			$fields[] = JHTML::_('select.option', $field, $field);
		
		// Merge Vars
		if ($this->is16())
			$merge_vars = array("name" => JText::_('RSFP_REG_NAME'),"username" => JText::_('RSFP_REG_USERNAME'),"email1" => JText::_('RSFP_REG_EMAIL'),"email2" => JText::_('RSFP_REG_EMAIL2') ,"password1" => JText::_('RSFP_REG_PASSWORD1'),"password2" => JText::_('RSFP_REG_PASSWORD2'));
		else 
			$merge_vars = array("name" => JText::_('RSFP_REG_NAME'),"username" => JText::_('RSFP_REG_USERNAME'),"email" => JText::_('RSFP_REG_EMAIL'),"password" => JText::_('RSFP_REG_PASSWORD1'),"password2" => JText::_('RSFP_REG_PASSWORD2'));
		
		$lists['fields'] = array();
		if (is_array($merge_vars))
			foreach ($merge_vars as $merge_var => $title)
			{
				$lists['fields'][$merge_var] = JHTML::_('select.genericlist', $fields, 'reg_vars['.$merge_var.']', null, 'value', 'text', isset($row->reg_merge_vars[$merge_var]) ? $row->reg_merge_vars[$merge_var] : null);
			}
		
		$lists['published'] = JHTML::_('select.booleanlist','jur_published','class="inputbox"',$row->published);
		if (RSFormProHelper::isJ16())
		{
			$activations = array(
				0 => JText::_('RSFP_REG_NONE'),
				1 => JText::_('RSFP_REG_SELF'),
				2 => JText::_('RSFP_REG_ADMIN')
			);
			$lists['activation'] = JHTML::_('select.genericlist', $activations, 'activation','class="inputbox"', 'value', 'text', $row->activation);
		}
		else
			$lists['activation'] = JHTML::_('select.booleanlist','activation','class="inputbox"',$row->activation);
		$cb = file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_comprofiler'.DS.'admin.comprofiler.php');
		$lists['cb'] = JHTML::_('select.booleanlist','cbactivation','class="inputbox"',$row->cbactivation);
		
		echo '<div id="joomlaregistrationdiv">';
			include(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'helpers'.DS.'registration.php');
		echo '</div>';
	}
	
	function rsfp_bk_onAfterShowFormEditTabsTab()
	{
		$lang =& JFactory::getLanguage();
		$lang->load('plg_system_rsfpregistration');
		
		echo '<li><a href="javascript: void(0);" id="joomlaregistration"><span>'.JText::_('RSFP_REG_JOOMLA_INTEGRATION').'</span></a></li>';
	}
	
	function rsfp_f_onBeforeFormValidation($args)
	{
		$db 	= &JFactory::getDBO();
		$formId = JRequest::getInt('formId');
		$post	= JRequest::getVar('form');
		
		$db->setQuery("SELECT * FROM #__rsform_registration WHERE `form_id`='".$formId."' AND `published`='1'");
		if ($row = $db->loadObject())
		{
			$row->reg_merge_vars = @unserialize($row->reg_merge_vars);
			if ($row->reg_merge_vars === false)
				$row->reg_merge_vars = array();
			
			if (isset($row->reg_merge_vars['name']))
			{
				$field 	= $row->reg_merge_vars['name'];
				
				if (!isset($post[$field]))
					$post[$field] = '';
				
				if (is_array($post[$field]))
				{
					array_walk($post[$field], array('plgSystemRSFPRegistration', '_escapeCommas'));
					$post[$field] = implode(',', $post[$field]);
				}
				
				if ($post[$field] == '')
					$args['invalid'][] = RSFormProHelper::componentNameExists($field, $formId);
			}
			
			if (isset($row->reg_merge_vars['username']))
			{
				$field 	= $row->reg_merge_vars['username'];
				
				if (!isset($post[$field]))
					$post[$field] = '';
				
				if (is_array($post[$field]))
				{
					array_walk($post[$field], array('plgSystemRSFPRegistration', '_escapeCommas'));
					$post[$field] = implode(',', $post[$field]);
				}
				
				if (!plgSystemRSFPRegistration::regValidateUsername($post[$field]))
					$args['invalid'][] = RSFormProHelper::componentNameExists($field, $formId);
			}
			
			if (isset($row->reg_merge_vars['email']) || isset($row->reg_merge_vars['email1']))
			{
				$field1	= isset($row->reg_merge_vars['email1']) ? $row->reg_merge_vars['email1'] : $row->reg_merge_vars['email'];
				$field2	= isset($row->reg_merge_vars['email2']) ? $row->reg_merge_vars['email2'] : $row->reg_merge_vars['email'];
				
				if (!isset($post[$field1]))
					$post[$field1] = '';
				if (!isset($post[$field2]))
					$post[$field2] = '';
					
				if (is_array($post[$field1]))
				{
					array_walk($post[$field1], array('plgSystemRSFPRegistration', '_escapeCommas'));
					$post[$field1] = implode(',', $post[$field1]);
				}
				
				if (is_array($post[$field2]))
				{
					array_walk($post[$field2], array('plgSystemRSFPRegistration', '_escapeCommas'));
					$post[$field2] = implode(',', $post[$field2]);
				}
				
				if ($post[$field1] == '' || $post[$field2] == '' || $post[$field1] != $post[$field2] || !plgSystemRSFPRegistration::regValidateEmail($post[$field1]))
					$args['invalid'][] = RSFormProHelper::componentNameExists($field1, $formId);
			}
			
			if (isset($row->reg_merge_vars['password']) && isset($row->reg_merge_vars['password2']))
			{
				$field1	= $row->reg_merge_vars['password'];
				$field2	= $row->reg_merge_vars['password2'];
				
				if (!isset($post[$field1]))
					$post[$field1] = '';
				if (!isset($post[$field2]))
					$post[$field2] = '';
				
				if (is_array($post[$field1]))
				{
					array_walk($post[$field1], array('plgSystemRSFPRegistration', '_escapeCommas'));
					$post[$field1] = implode(',', $post[$field1]);
				}
				
				if (is_array($post[$field2]))
				{
					array_walk($post[$field2], array('plgSystemRSFPRegistration', '_escapeCommas'));
					$post[$field2] = implode(',', $post[$field2]);
				}
				
				if ($post[$field1] == '' || $post[$field2] == '' || $post[$field1] != $post[$field2])
					$args['invalid'][] = RSFormProHelper::componentNameExists($field2, $formId);
			}
		}
	}
	
	function regValidateUsername($value)
	{
		if ($value == '' || (preg_match( "#[<>\"'%;()&]#i", $value) || strlen(utf8_decode($value)) < 2))
			return false;
		
		$db =& JFactory::getDBO();
		$db->setQuery("SELECT id FROM #__users WHERE `username` LIKE '".$db->getEscaped($value)."'");
		return $db->loadResult() ? false : true;
	}
	
	function regValidateEmail($value)
	{
		if ($value == '' || !RSFormProValidations::email($value))
			return false;
		
		$db =& JFactory::getDBO();
		$db->setQuery("SELECT id FROM #__users WHERE `email` LIKE '".$db->getEscaped($value)."'");
		return $db->loadResult() ? false : true;
	}
	
	function rsfp_f_onBeforeStoreSubmissions($args)
	{
		$db = JFactory::getDBO();
		
		$formId = (int) $args['formId'];
		$post =& $args['post'];
		
		$db->setQuery("SELECT * FROM #__rsform_registration WHERE `form_id`='".$formId."' AND `published`='1'");
		if ($row = $db->loadObject())
		{
			$row->reg_merge_vars = @unserialize($row->reg_merge_vars);
			if ($row->reg_merge_vars === false)
				$row->reg_merge_vars = array();
			
			$vars = array();
			foreach ($row->reg_merge_vars as $tag => $field)
			{				
				if (empty($tag)) continue;
				
				if (!isset($post[$field]))
					$post[$field] = '';
				
				if (is_array($post[$field]))
				{
					array_walk($post[$field], array('plgSystemRSFPRegistration', '_escapeCommas'));
					$post[$field] = implode(',', $post[$field]);
				}
				$vars[$tag] = $post[$field];
				
				if ($tag == 'password')
					$post[$field] = '';
				if ($tag == 'password1')
					$post[$field] = '';
				if ($tag == 'password2')
					$post[$field] = '';
			}
			
			if ($this->is16())
				$this->register16($vars,$row->activation,$row->cbactivation);
			else 
				$this->register($vars,$row->activation,$row->cbactivation);
		}
	}
	
	function _getFields($formId)
	{
		$db =& JFactory::getDBO();
		
		$db->setQuery("SELECT p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.FormId='".(int) $formId."' AND p.PropertyName='NAME' ORDER BY c.Order");
		return $db->loadResultArray();
	}
	
	function _escapeCommas(&$item)
	{
		$item = str_replace(',', '\,', $item);
	}
	
	function is16()
	{
		jimport('joomla.version');
		$version = new JVersion();
		return $version->isCompatible('1.6.0');
	}
	
	function register($vars,$activation,$cbactivation)
	{
		// Get required system objects
		$user 		= clone(JFactory::getUser());
		$config		=& JFactory::getConfig();
		$authorize	=& JFactory::getACL();
		$mainframe  =& JFactory::getApplication();
		$lang		=& JFactory::getLanguage();
		$u			= RSFormProHelper::getURL();
		
		$lang->load('com_user',JPATH_SITE);
		
		//get the clean password
		$password = $vars['password'];
		
		// If user registration is not allowed, show 403 not authorized.
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		if ($usersConfig->get('allowUserRegistration') == '0') {
			JError::raiseError( 403, JText::_( 'Access Forbidden' ));
			return;
		}

		// Initialize new usertype setting
		$newUsertype = $usersConfig->get( 'new_usertype' );
		if (!$newUsertype) {
			$newUsertype = 'Registered';
		}

		// Bind the post array to the user object
		if (!$user->bind( $vars, 'usertype' )) {
			$mainframe->redirect($u,JText::_($user->getError()),'error');
		}

		// Set some initial user values
		$user->set('id', 0);
		$user->set('usertype', $newUsertype);
		$user->set('gid', $authorize->get_group_id( '', $newUsertype, 'ARO' ));

		$date =& JFactory::getDate();
		$user->set('registerDate', $date->toMySQL());

		// If user activation is turned on, we need to set the activation information
		if ($activation == '1')
		{
			jimport('joomla.user.helper');
			$user->set('activation', JUtility::getHash( JUserHelper::genRandomPassword()) );
			$user->set('block', '1');
		}
		
		// If there was an error with registration, set the message and display form
		if ( !$user->save() )
		{
			$mainframe->redirect($u,JText::_( $user->getError()),'error');
			return false;
		}
		
		$this->cbactivate($cbactivation,$user->id);

		// Send registration confirmation mail
		$password = preg_replace('/[\x00-\x1F\x7F]/', '', $password); //Disallow control chars in the email
		$this->_sendMail($user, $password,$activation);
		
		return true;
	}
	
	function _sendMail(&$user, $password,$activation)
	{
		$db				=& JFactory::getDBO();
		$mainframe		=& JFactory::getApplication();
		$lang			=& JFactory::getLanguage();

		$lang->load('com_user',JPATH_SITE);
		
		$name 		= $user->get('name');
		$email 		= $user->get('email');
		$username 	= $user->get('username');

		$usersConfig 	= &JComponentHelper::getParams( 'com_users' );
		$sitename 		= $mainframe->getCfg( 'sitename' );
		$mailfrom 		= $mainframe->getCfg( 'mailfrom' );
		$fromname 		= $mainframe->getCfg( 'fromname' );
		$siteURL		= JURI::base();

		$subject 	= sprintf ( JText::_( 'Account details for' ), $name, $sitename);
		$subject 	= html_entity_decode($subject, ENT_QUOTES);

		if ( $activation == 1 ){
			$message = sprintf ( JText::_( 'SEND_MSG_ACTIVATE' ), $name, $sitename, $siteURL."index.php?option=com_user&task=activate&activation=".$user->get('activation'), $siteURL, $username, $password);
		} else {
			$message = sprintf ( JText::_( 'SEND_MSG' ), $name, $sitename, $siteURL);
		}

		$message = html_entity_decode($message, ENT_QUOTES);

		//get all super administrator
		$query = 'SELECT name, email, sendEmail' .
				' FROM #__users' .
				' WHERE LOWER( usertype ) = "super administrator"';
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		// Send email to user
		if ( ! $mailfrom  || ! $fromname ) {
			$fromname = $rows[0]->name;
			$mailfrom = $rows[0]->email;
		}

		JUtility::sendMail($mailfrom, $fromname, $email, $subject, $message);

		// Send notification to all administrators
		$subject2 = sprintf ( JText::_( 'Account details for' ), $name, $sitename);
		$subject2 = html_entity_decode($subject2, ENT_QUOTES);

		// get superadministrators id
		foreach ( $rows as $row )
		{
			if ($row->sendEmail)
			{
				$message2 = sprintf ( JText::_( 'SEND_MSG_ADMIN' ), $row->name, $sitename, $name, $email, $username);
				$message2 = html_entity_decode($message2, ENT_QUOTES);
				JUtility::sendMail($mailfrom, $fromname, $row->email, $subject2, $message2);
			}
		}
	}
	
	function register16($vars,$activation,$cbactivation)
	{
		$app	=& JFactory::getApplication();
		$lang	=& JFactory::getLanguage();
		$u		= RSFormProHelper::getURL();
		
		$lang->load('com_users',JPATH_SITE);
		
		// If registration is disabled - Redirect to login page.
		if ($params = JComponentHelper::getParams('com_users'))
		{
			if ($params->get('allowUserRegistration') == 0) {
				$app->redirect($u);
				return false;
			}
		}

		//include model
		JModel::addIncludePath(JPATH_SITE.DS.'components'.DS.'com_users'.DS.'models');
		
		// Initialise variables.
		$model	= JModel::getInstance('Registration', 'UsersModel');

		// Get the user data.
		$requestData = $vars;

		JRequest::setVar('jform',$vars);
		
		// Validate the posted data.
		JForm::addFormPath(JPATH_SITE.DS.'components'.DS.'com_users'.DS.'models'.DS.'forms');
		JForm::addFieldPath(JPATH_SITE.DS.'components'.DS.'com_users'.DS.'models'.DS.'fields');
		$form = JForm::getInstance('com_users.registration', 'registration', array('control' => 'jform', 'load_data' => true));
		
		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}
		$form->removeField('captcha');
		$data	= $model->validate($form, $requestData);

		// Check for validation errors.
		if ($data === false) 
		{
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Redirect back to the registration screen.
			$app->redirect($u);
			return false;
		}

		// Attempt to save the data.
		$return	= $this->_register($data,$activation,$cbactivation);

		// Check for errors.
		if ($return === false) 
			return false;
	}
	
	function _register($temp,$useractivation,$cbactivation)
	{
		$config = JFactory::getConfig();
		$params = JComponentHelper::getParams('com_users');
		$app 	= JFactory::getApplication(); 

		// Initialise the table with JUser.
		$user = new JUser;
		$system	= $params->get('new_usertype', 2);
		$data['groups'][] = $system;
		
		// Merge in the registration data.
		foreach ($temp as $k => $v) {
			$data[$k] = $v;
		}

		// Prepare the data for the user object.
		$data['email']		= $data['email1'];
		$data['password']	= $data['password1'];

		// Check if the user needs to activate their account.
		if (($useractivation == 1) || ($useractivation == 2)) {
			jimport('joomla.user.helper');
			$data['activation'] = JUtility::getHash(JUserHelper::genRandomPassword());
			$data['block'] = 1;
		}
		
		// Bind the data.
		if (!$user->bind($data)) {
			JError::raiseError( 500, JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));
			return false;
		}

		// Load the users plugin group.
		JPluginHelper::importPlugin('user');

		// Store the data.
		if (!$user->save()) {
			$app->redirect($u,JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()),'error');
			return false;
		}

		$this->cbactivate($cbactivation,$user->id);
		
		// Compile the notification mail values.
		$data = $user->getProperties();
		$data['fromname']	= $config->get('fromname');
		$data['mailfrom']	= $config->get('mailfrom');
		$data['sitename']	= $config->get('sitename');
		$data['siteurl']	= JUri::base();

		// Handle account activation/confirmation emails.
		if ($useractivation == 2)
		{
			// Set the link to confirm the user email.
			$uri = JURI::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base.JRoute::_('index.php?option=com_users&task=registration.activate&token='.$data['activation'], false);

			$emailSubject	= JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = JText::sprintf(
				'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$data['activation'],
				$data['siteurl'],
				$data['username'],
				$data['password_clear']
			);
		}
		else if ($useractivation == 1)
		{
			// Set the link to activate the user account.
			$uri = JURI::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base.JRoute::_('index.php?option=com_users&task=registration.activate&token='.$data['activation'], false);

			$emailSubject	= JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = JText::sprintf(
				'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$data['activation'],
				$data['siteurl'],
				$data['username'],
				$data['password_clear']
			);
		} else {

			$emailSubject	= JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = JText::sprintf(
				'COM_USERS_EMAIL_REGISTERED_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl']
			);
		}

		// Send the registration email.
		$return = JUtility::sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);

		// Check for an error.
		if ($return !== true) {
			$app->enqueueMessage(JText::_('COM_USERS_REGISTRATION_SEND_MAIL_FAILED'));

			// Send a system message to administrators receiving system mails
			$db = JFactory::getDBO();
			$q = "SELECT id
				FROM #__users
				WHERE block = 0
				AND sendEmail = 1";
			$db->setQuery($q);
			$sendEmail = $db->loadResultArray();
			if (count($sendEmail) > 0) {
				$jdate = new JDate();
				// Build the query to add the messages
				$q = "INSERT INTO `#__messages` (`user_id_from`, `user_id_to`, `date_time`, `subject`, `message`)
					VALUES ";
				$messages = array();
				foreach ($sendEmail as $userid) {
					$messages[] = "(".$userid.", ".$userid.", '".$jdate->toMySQL()."', '".JText::_('COM_USERS_MAIL_SEND_FAILURE_SUBJECT')."', '".JText::sprintf('COM_USERS_MAIL_SEND_FAILURE_BODY', $return, $data['username'])."')";
				}
				$q .= implode(',', $messages);
				$db->setQuery($q);
				$db->query();
			}
			return false;
		}
	}
	
	function cbactivate($cbactivation,$uid)
	{
		$db =& JFactory::getDBO();
		
		$cb = file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_comprofiler'.DS.'admin.comprofiler.php');
		if ($cb && $cbactivation)
		{
			$db->setQuery("INSERT IGNORE INTO #__comprofiler SET `id` = ".$uid." , `user_id` = ".$uid.", `approved` = 1 , `confirmed` = 1");
			$db->query();
		}
		
		return true;
	}
	
	function rsfp_bk_onAfterShowConfigurationTabs()
	{
		if (!$this->canRun()) return;

		$lang =& JFactory::getLanguage();
		$lang->load('plg_system_rsfpregistration');

		jimport('joomla.html.pane');
		$tabs =& JPane::getInstance('Tabs', array(), true);

		echo $tabs->startPanel(JText::_('RSFP_REG_FORM_NAME_LABEL'), 'form-register');
			$this->registerformConfigurationScreen();
		echo $tabs->endPanel();
	}
	
	function registerformConfigurationScreen()
	{
		if (!$this->canRun()) return;		

		$lang =& JFactory::getLanguage();
		$lang->load('plg_system_rsfpregistration');

		$db =& JFactory::getDBO();
		$query = "SELECT f.`FormId` as value, f.`FormName` as text FROM #__rsform_forms f LEFT JOIN #__rsform_registration r ON f.FormId = r.form_id WHERE r.published = 1 ORDER BY f.`FormName` ASC";
		$db->setQuery($query);
		$myforms = $db->loadObjectList();

		$tmp = new stdClass();
		$tmp-> value = '0';
		$tmp-> text = JText::_('Default Joomla User Registration Form');
		array_unshift($myforms, $tmp);
?>
		<div id="page-register">
			<table class="admintable">
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for=""><span class="hasTip" title="<?php echo JText::_('RSFP_REG_FORM_NAME_DESC'); ?>"><?php echo JText::_( 'RSFP_REG_FORM_NAME_LABEL' ); ?></span></label></td>
					<td>
						<?php echo JHTML::_('select.genericlist', $myforms, 'rsformConfig[registration_form]', null, 'value', 'text', RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('registration_form'))); ?>
					</td>
				</tr>
				<tr>
					<td align="right"><strong><?php echo JText::_('RSFP_REG_OR'); ?></strong></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for="redirect_url"><span class="hasTip" title="<?php echo JText::_('RSFP_REDIRECT_URL_DESC'); ?>"><?php echo JText::_( 'RSFP_REDIRECT_URL_LABEL' ); ?></span></label></td>
					<td>
						<input type="text" name="rsformConfig[redirect_url]" id="redirect_url" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('redirect_url')); ?>" size="150" maxlength="150">
					</td>
				</tr>
			</table>
		</div>
		<?php
	}
	
	function onAfterDispatch()
	{
		if (!$this->canRun()) return;
			
		$mainframe = JFactory::getApplication();
		
		$j_option = JRequest::getVar('option');
		$j_view = JRequest::getVar('view');
		$j_task = JRequest::getVar('task');
		
		
		if (($j_option == 'com_user' && ($j_task == 'register' || $j_view == 'register')) || (RSFormProHelper::isJ16() && $j_option == 'com_users' && $j_view == 'registration'))
		{
			$custom_url = RSFormProHelper::getConfig('redirect_url');
			$formid     = RSFormProHelper::getConfig('registration_form');
			$redirect = true;
			
			if (!empty($custom_url) && (strpos($custom_url, 'http://') !== false || strpos($custom_url, 'https://') !== false) && JURI::isInternal($custom_url))
			{
				$url = $custom_url;				
			}
			elseif ($formid != 0)
			{
				$url = JRoute::_('index.php?option=com_rsform&formId='.$formid, false);
				
			} else $redirect = false;
				
			if ($redirect)
				$mainframe->redirect($url);
		}
	}
}