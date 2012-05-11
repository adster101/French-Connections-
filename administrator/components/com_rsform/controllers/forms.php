<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSFormControllerForms extends RSFormController
{
	function __construct()
	{
		parent::__construct();
		
		$this->registerTask('apply', 	 'save');
		$this->registerTask('new', 	 	 'add');
		$this->registerTask('publish',   'changestatus');
		$this->registerTask('unpublish', 'changestatus');
		
		$this->_db =& JFactory::getDBO();
	}

	function manage()
	{
		JRequest::setVar('view', 'forms');
		JRequest::setVar('layout', 'default');
		
		parent::display();
	}
	
	function edit()
	{
		JRequest::setVar('view', 	'forms');
		JRequest::setVar('layout', 	'edit');
		
		parent::display();
	}
	
	function add()
	{
		JRequest::setVar('view', 	'forms');
		JRequest::setVar('layout', 	'new');
		
		parent::display();
	}
	
	function emails()
	{
		JRequest::setVar('view', 	'forms');
		JRequest::setVar('layout', 	'emails');
		
		parent::display();
	}
	
	function menuAddScreen()
	{
		JRequest::setVar('view', 	'menus');
		JRequest::setVar('layout', 	'default');
		
		parent::display();
	}
	
	function menuAddBackend()
	{
		$db		=& JFactory::getDBO();
		$app	=& JFactory::getApplication();
		$formId	= JRequest::getInt('formId',0);
		
		if (!$formId)
			$app->redirect('index.php?option=com_rsform&task=forms.manage');
		
		$db->setQuery("SELECT FormTitle FROM #__rsform_forms WHERE FormId = ".$formId." ");
		$formname = $db->loadResult();
		
		if (RSFormProHelper::isJ16())
		{
			$db->setQuery("SELECT extension_id FROM #__extensions WHERE `type` = 'component' AND `element` = 'com_rsform' AND `name` = 'rsform' ");
			$componentid = $db->loadResult();
			
			$db->setQuery("INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES('', 'main', '".$db->getEscaped($formname)."', '".$db->getEscaped($formname)."', '', 'rsform', 'index.php?option=com_rsform&view=forms&layout=show&formId=".$formId."', 'component', 0, 1, 1, ".(int) $componentid.", 0, 0, '0000-00-00 00:00:00', 0, 1, 'class:component', 0, '', 0, 0, 0, '', 1)");
			$db->query();
		}
		else 
		{
			$db->setQuery("INSERT INTO `#__components` (`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES('', '".$db->getEscaped($formname)."', '', 0, 0, 'option=com_rsform&view=forms&layout=show&formId=".$formId."', '".$db->getEscaped($formname)."', 'com_rsform_menu', 0, 'js/ThemeOffice/component.png', 1, '', 1)");
			$db->query();
		}
		
		$db->setQuery("UPDATE #__rsform_forms SET `Backendmenu` = 1 WHERE FormId = ".$formId." ");
		$db->query();
		
		$app->redirect('index.php?option=com_rsform&task=forms.manage', JText::_('RSFP_FORM_ADDED_BACKEND'));
	}
	
	/**
	 * Forms Menu Remove Backend
	 */
	function menuRemoveBackend()
	{
		$db		=& JFactory::getDBO();
		$app	=& JFactory::getApplication();
		$formId	= JRequest::getInt('formId',0);
		
		if (!$formId)
			$app->redirect('index.php?option=com_rsform&task=forms.manage');
		
		if (RSFormProHelper::isJ16())
			$db->setQuery("DELETE FROM `#__menu` WHERE `client_id` = '1' AND link = 'index.php?option=com_rsform&view=forms&layout=show&formId=".$formId."' ");
		else
			$db->setQuery("DELETE FROM `#__components` WHERE `option` = 'com_rsform_menu' AND admin_menu_link = 'option=com_rsform&view=forms&layout=show&formId=".$formId."' ");
		$db->query();
		
		$db->setQuery("UPDATE #__rsform_forms SET `Backendmenu` = 0 WHERE FormId = ".$formId." ");
		$db->query();
		
		$app->redirect('index.php?option=com_rsform&task=forms.manage', JText::_('RSFP_FORM_REMOVED_BACKEND'));
	}
	
	function newStepTwo()
	{
		JRequest::setVar('view', 'forms');
		JRequest::setVar('layout', 'new2');
		
		parent::display();
	}
	
	function newStepThree()
	{
		$session = JFactory::getSession();
		$session->set('com_rsform.wizard.FormTitle', JRequest::getVar('FormTitle', '', 'post', 'none', JREQUEST_ALLOWRAW));
		$session->set('com_rsform.wizard.FormLayout', JRequest::getVar('FormLayout', '', 'post', 'none', JREQUEST_ALLOWRAW));		
		$session->set('com_rsform.wizard.AdminEmail', JRequest::getInt('AdminEmail'));
		$session->set('com_rsform.wizard.AdminEmailTo', JRequest::getVar('AdminEmailTo', '', 'post', 'none', JREQUEST_ALLOWRAW));
		$session->set('com_rsform.wizard.UserEmail', JRequest::getInt('UserEmail'));
		$session->set('com_rsform.wizard.SubmissionAction', JRequest::getVar('SubmissionAction', '', 'post', 'word'));
		$session->set('com_rsform.wizard.Thankyou', JRequest::getVar('Thankyou', '', 'post', 'none', JREQUEST_ALLOWRAW));
		$session->set('com_rsform.wizard.ReturnUrl', JRequest::getVar('ReturnUrl', '', 'post', 'none', JREQUEST_ALLOWRAW));
		
		JRequest::setVar('view', 'forms');
		JRequest::setVar('layout', 'new3');
		
		parent::display();
	}
	
	function newStepFinal()
	{
		$session = JFactory::getSession();
		$config = JFactory::getConfig();
		
		$row = JTable::getInstance('RSForm_Forms', 'Table');
		$row->FormTitle = $session->get('com_rsform.wizard.FormTitle');
		if (empty($row->FormTitle))
			$row->FormTitle = JText::_('RSFP_FORM_DEFAULT_TITLE');
		$row->FormName = JFilterOutput::stringURLSafe($row->FormTitle);
		$row->FormLayoutName = $session->get('com_rsform.wizard.FormLayout');		
		if (empty($row->FormLayoutName))
			$row->FormLayoutName = 'inline';
		
		$AdminEmail = $session->get('com_rsform.wizard.AdminEmail');
		if ($AdminEmail)
		{
			$row->AdminEmailTo = $session->get('com_rsform.wizard.AdminEmailTo');
			$row->AdminEmailFrom = $config->getValue('config.mailfrom');
			$row->AdminEmailFromName = $config->getValue('config.fromname');
			$row->AdminEmailSubject = JText::sprintf('RSFP_ADMIN_EMAIL_DEFAULT_SUBJECT', $row->FormTitle);
			$row->AdminEmailText = JText::_('RSFP_ADMIN_EMAIL_DEFAULT_MESSAGE');
		}
		
		$UserEmail = $session->get('com_rsform.wizard.UserEmail');
		if ($UserEmail)
		{
			$row->UserEmailFrom = $config->getValue('config.mailfrom');
			$row->UserEmailFromName = $config->getValue('config.fromname');
			$row->UserEmailSubject = JText::_('RSFP_USER_EMAIL_DEFAULT_SUBJECT');
			$row->UserEmailText = JText::_('RSFP_USER_EMAIL_DEFAULT_MESSAGE');
		}
		
		$action = $session->get('com_rsform.wizard.SubmissionAction');
		if ($action == 'thankyou')
			$row->Thankyou = $session->get('com_rsform.wizard.Thankyou');
		elseif ($action == 'redirect')
			$row->ReturnUrl = $session->get('com_rsform.wizard.ReturnUrl');
		
		$filter = JFilterInput::getInstance();
		
		$layout = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'layouts'.DS.$filter->clean($row->FormLayoutName, 'path').'.php';
		
		if (file_exists($layout))
		{
			$quickfields = array();
			$requiredfields = array();
			$this->_form = $row;
			$row->FormLayout = include($layout);
		}
		
		if ($row->store())
		{
			$predefinedForm = JRequest::getVar('predefinedForm');
			if ($predefinedForm)
			{
				$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'assets'.DS.'forms'.DS.$filter->clean($predefinedForm);
				if (file_exists($path.DS.'install.xml'))
				{
					$GLOBALS['q_FormId'] = $row->FormId;
					JRequest::setVar('formId', $row->FormId);
					
					$options = array();
					$options['cleanup'] = 0;
					
					require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'helpers'.DS.'restore.php');
					
					$restore = new RSFormProRestore($options);
					$restore->installDir = $path;
					
					if ($restore->restore())
					{
						$model = $this->getModel('forms');
						$quickfields = $model->getQuickFields();
						
						if ($AdminEmail && !empty($quickfields))
							foreach ($quickfields as $quickfield)
								$row->AdminEmailText .= "\n".'<p>{'.$quickfield.':caption}: {'.$quickfield.':value}</p>';
						
						if ($UserEmail)
						{
							$row->UserEmailTo = '{Email:value}';
							
							if (!empty($quickfields))
								foreach ($quickfields as $quickfield)
									$row->UserEmailText .= "\n".'<p>{'.$quickfield.':caption}: {'.$quickfield.':value}</p>';
						}
						
						$row->store();
					}
				}
			}
		}
		
		$session->clear('com_rsform.wizard.FormTitle');
		$session->clear('com_rsform.wizard.FormLayout');
		$session->clear('com_rsform.wizard.AdminEmail');
		$session->clear('com_rsform.wizard.AdminEmailTo');
		$session->clear('com_rsform.wizard.UserEmail');
		$session->clear('com_rsform.wizard.SubmissionAction');
		$session->clear('com_rsform.wizard.Thankyou');
		$session->clear('com_rsform.wizard.ReturnUrl');
		
		$this->setRedirect('index.php?option=com_rsform&task=forms.edit&formId='.$row->FormId);
	}
	
	function save()
	{
		$formId = JRequest::getInt('formId');
		
		$model = $this->getModel('forms');
		$saved = $model->save();
		
		$task = $this->getTask();
		switch ($task)
		{
			case 'save':
				$link = 'index.php?option=com_rsform&task=forms.manage';
			break;
			
			case 'apply':
				$tabposition = JRequest::getInt('tabposition', 0);
				$tab		 = JRequest::getInt('tab', 0);
				$link		 = 'index.php?option=com_rsform&task=forms.edit&formId='.$formId.'&tabposition='.$tabposition.'&tab='.$tab;
			break;
		}
		
		$this->setRedirect($link, JText::_('RSFP_FORM_SAVED'));
	}
	
	function cancel()
	{
		$this->setRedirect('index.php?option=com_rsform&task=forms.manage');
	}
	
	function delete()
	{
		$db = JFactory::getDBO();
		
		// Get the selected items
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		
		// Force array elements to be integers
		JArrayHelper::toInteger($cid, array(0));
		
		$model = $this->getModel('submissions');
		
		$total = count($cid);
		foreach($cid as $formId)
		{
			$model->deleteSubmissionFiles($formId);
			$model->deleteSubmissions($formId);

			//Delete Components
			$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE FormId = '".$formId."'");
			$componentIds = $db->loadResultArray();
			
			if (!empty($componentIds))
			{
				$components = implode(',',$componentIds);
				$db->setQuery("DELETE FROM #__rsform_properties WHERE ComponentId IN (".$components.")");
				$db->query();
				$db->setQuery("DELETE FROM #__rsform_components WHERE ComponentId IN (".$components.")");
				$db->query();
			}

			//delete mappings
			$db->setQuery("DELETE FROM #__rsform_mappings WHERE formId = '".$formId."'");
			$db->query();
			
			//delete extra emails
			$db->setQuery("DELETE FROM #__rsform_emails WHERE formId = '".$formId."'");
			$db->query();
			
			//Delete Forms
			$db->setQuery("DELETE FROM #__rsform_forms WHERE FormId = '".$formId."'");
			$db->query();
			
			//Delete Translations
			$db->setQuery("DELETE FROM #__rsform_translations WHERE form_id = '".$formId."'");
			$db->query();
			
			// delete conditions
			$db->setQuery("SELECT `id` FROM #__rsform_conditions WHERE form_id = '".$formId."'");
			if ($condition_ids = $db->loadResultArray())
			{
				$db->setQuery("DELETE FROM #__rsform_conditions WHERE form_id = '".$formId."'");
				$db->query();
				$db->setQuery("DELETE FROM #__rsform_condition_details WHERE condition_id IN (".implode(',', $condition_ids).")");
				$db->query();
			}
			
			if (RSFormProHelper::isJ16())
				$db->setQuery("DELETE FROM `#__menu` WHERE `path` = 'rsform' AND link = 'index.php?option=com_rsform&view=forms&layout=show&formId=".$formId."' ");
			else
				$db->setQuery("DELETE FROM `#__components` WHERE `option` = 'com_rsform_menu' AND admin_menu_link = 'option=com_rsform&view=forms&layout=show&formId=".$formId."' ");
			$db->query();			
		}
		
		$this->setRedirect('index.php?option=com_rsform&task=forms.manage', JText::sprintf('RSFP_FORMS_DELETED', $total));
	}
	
	function changeStatus()
	{
		$task = $this->getTask();
		$db   = JFactory::getDBO();
		
		// Get the selected items
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		
		// Force array elements to be integers
		JArrayHelper::toInteger($cid, array(0));
		
		$value = $task == 'publish' ? 1 : 0;
		
		$total = count($cid);
		if ($total > 0)
		{
			$formIds = implode(',', $cid);
			$db->setQuery("UPDATE #__rsform_forms SET Published = '".$value."' WHERE FormId IN (".$formIds.")");
			$db->query();
		}
		
		$msg = $value ? JText::sprintf('RSFP_FORMS_PUBLISHED', $total) : JText::sprintf('RSFP_FORMS_UNPUBLISHED', $total);

		$this->setRedirect('index.php?option=com_rsform&task=forms.manage', $msg);
	}
	
	function copy()
	{
		$db = JFactory::getDBO();
		
		// Get the selected items
		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		
		// Force array elements to be integers
		JArrayHelper::toInteger($cid, array(0));
		
		$total = 0;
		foreach ($cid as $formId)
		{
			if (empty($formId))
				continue;
				
			$total++;
			
			$original =& JTable::getInstance('RSForm_Forms', 'Table');
			$original->load($formId);
			$original->FormName .= ' copy';
			$original->FormTitle .= ' copy';
			$original->FormId = null;
			
			$copy =& JTable::getInstance('RSForm_Forms', 'Table');
			$copy->bind($original);
			$copy->store();
			
			$copy->FormLayout = str_replace('rsform_'.$formId.'_page', 'rsform_'.$copy->FormId.'_page', $copy->FormLayout);
			if ($copy->FormLayout != $original->FormLayout)
				$copy->store();
			
			$newFormId = $copy->FormId;
			
			// copy language
			$db->setQuery("SELECT * FROM #__rsform_translations WHERE `reference`='forms' AND `form_id`='".$formId."'");
			$translations = $db->loadObjectList();
			foreach ($translations as $translation)
			{
				$db->setQuery("INSERT INTO #__rsform_translations SET `form_id`='".$newFormId."', `lang_code`='".$db->getEscaped($translation->lang_code)."', `reference`='forms', `reference_id`='".$db->getEscaped($translation->reference_id)."', `value`='".$db->getEscaped($translation->value)."'");
				$db->query();
			}
			
			$componentRelations = array();
			$conditionRelations = array();
			
			$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE FormId='".$formId."' ORDER BY `Order`");
			$components = $db->loadResultArray();
			foreach ($components as $r)
				$componentRelations[$r] = RSFormProHelper::copyComponent($r, $newFormId);
			
			// copy conditions
			$db->setQuery("SELECT * FROM #__rsform_conditions WHERE form_id='".$formId."'");
			if ($conditions = $db->loadObjectList())
			{
				foreach ($conditions as $condition)
				{
					$new_condition =& JTable::getInstance('RSForm_Conditions', 'Table');
					$new_condition->bind($condition);
					$new_condition->id = null;
					$new_condition->form_id = $newFormId;
					$new_condition->component_id = $componentRelations[$condition->component_id];
					$new_condition->store();
					
					$conditionRelations[$condition->id] = $new_condition->id;
				}
				
				$db->setQuery("SELECT * FROM #__rsform_condition_details WHERE condition_id IN (".implode(',', array_keys($conditionRelations)).")");
				if ($details = $db->loadObjectList())
				{
					foreach ($details as $detail)
					{
						$new_detail =& JTable::getInstance('RSForm_Condition_Details', 'Table');
						$new_detail->bind($detail);
						$new_detail->id = null;
						$new_detail->condition_id = $conditionRelations[$detail->condition_id];
						$new_detail->component_id = $componentRelations[$detail->component_id];
						$new_detail->store();
					}
				}
			}
			
			//foreach ($relations as $oldComponentId => $newComponentId)
		}
		
		$this->setRedirect('index.php?option=com_rsform&task=forms.manage', JText::sprintf('RSFP_FORMS_COPIED', $total));
	}
	
	function changeAutoGenerateLayout()
	{
		$formId 		= JRequest::getInt('formId');
		$formLayoutName = JRequest::getVar('formLayoutName');
		$db 			= JFactory::getDBO();
		
		$db->setQuery("UPDATE #__rsform_forms SET `FormLayoutAutogenerate` = ABS(FormLayoutAutogenerate-1), `FormLayoutName`='".$db->getEscaped($formLayoutName)."' WHERE `FormId` = '".$formId."'");
		$db->query();
		
		jexit();
	}
}