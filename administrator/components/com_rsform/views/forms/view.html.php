<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class RSFormViewForms extends JView
{
	function display($tpl = null)
	{
		$mainframe =& JFactory::getApplication();
		$document =& JFactory::getDocument();
		$document->addCustomTag('<!--[if IE 7]><link href="'.JURI::root().'administrator/components/com_rsform/assets/css/styleie.css" rel="stylesheet" type="text/css" /><![endif]-->');
		
		if (RSFormProHelper::getConfig('global.codemirror'))
		{	
			$document->addScript(JURI::root(true).'/administrator/components/com_rsform/assets/codemirror/lib/codemirror.js');
			$document->addScript(JURI::root(true).'/administrator/components/com_rsform/assets/codemirror/mode/css/css.js');
			$document->addScript(JURI::root(true).'/administrator/components/com_rsform/assets/codemirror/mode/htmlmixed/htmlmixed.js');
			$document->addScript(JURI::root(true).'/administrator/components/com_rsform/assets/codemirror/mode/javascript/javascript.js');
			$document->addScript(JURI::root(true).'/administrator/components/com_rsform/assets/codemirror/mode/php/php.js');
			$document->addScript(JURI::root(true).'/administrator/components/com_rsform/assets/codemirror/mode/clike/clike.js');
			$document->addScript(JURI::root(true).'/administrator/components/com_rsform/assets/codemirror/mode/xml/xml.js');
			
			$document->addStyleSheet(JURI::root(true).'/administrator/components/com_rsform/assets/codemirror/lib/codemirror.css');
			$document->addStyleSheet(JURI::root(true).'/administrator/components/com_rsform/assets/codemirror/theme/default.css');
		}
		
		JToolBarHelper::title('RSForm! Pro','rsform');
		
		if (RSFormProHelper::isJ16())
		{
			$lang =& JFactory::getLanguage();
			$lang->load('com_rsform.sys', JPATH_ADMINISTRATOR);
			
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_MANAGE_FORMS'), 'index.php?option=com_rsform&task=forms.manage', true);
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_MANAGE_SUBMISSIONS'), 'index.php?option=com_rsform&task=submissions.manage');
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_CONFIGURATION'), 'index.php?option=com_rsform&task=configuration.edit');
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_BACKUP_RESTORE'), 'index.php?option=com_rsform&task=backup.restore');
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_UPDATES'), 'index.php?option=com_rsform&task=updates.manage');
			JSubMenuHelper::addEntry(JText::_('COM_RSFORM_PLUGINS'), 'index.php?option=com_rsform&task=goto.plugins');
		}
		
		$layout = $this->getLayout();
		$this->assign('isComponent', JRequest::getVar('tmpl') == 'component');
		if ($layout == 'edit')
		{
			JToolBarHelper::apply('forms.apply');
			JToolBarHelper::save('forms.save');
			JToolBarHelper::spacer();
			JToolBarHelper::custom('forms.preview', 'preview', 'preview', RSFormProHelper::isJ16() ? JText::_('JGLOBAL_PREVIEW') : JText::_('PREVIEW'), false);
			JToolBarHelper::custom('submissions.manage', 'forward', 'forward', JText::_('RSFP_SUBMISSIONS'), false);
			JToolBarHelper::custom('components.copy', 'copy', 'copy', JText::_('RSFP_COPY_TO_FORM'), false);
			JToolBarHelper::custom('components.duplicate', 'copy', 'copy', JText::_('RSFP_DUPLICATE'), false);
			JToolBarHelper::deleteList(JText::_('VALIDDELETEITEMS'), 'components.remove', RSFormProHelper::isJ16() ? JText::_('JTOOLBAR_DELETE') : JText::_('DELETE'));
			JToolBarHelper::publishList('components.publish', RSFormProHelper::isJ16() ? JText::_('JTOOLBAR_PUBLISH') : JText::_('Publish'));
			JToolBarHelper::unpublishList('components.unpublish', RSFormProHelper::isJ16() ? JText::_('JTOOLBAR_UNPUBLISH') : JText::_('Unpublish'));
			JToolBarHelper::spacer();
			JToolBarHelper::cancel('forms.cancel');
			
			$this->assignRef('tabposition', JRequest::getInt('tabposition', 0));
			$this->assignRef('tab', JRequest::getInt('tab', 0));
			$this->assignRef('form', $this->get('form'));
			
			$this->assign('hasSubmitButton', $this->get('hasSubmitButton'));
			
			JToolBarHelper::title('RSForm! Pro <small>['.JText::sprintf('RSFP_EDITING_FORM', $this->form->FormTitle).']</small>','rsform');
			
			$this->assignRef('fields', $this->get('fields'));
			$this->assignRef('quickfields', $this->get('quickfields'));
			$this->assignRef('pagination', $this->get('fieldspagination'));
			
			$lists['Published'] = JHTML::_('select.booleanlist','Published','class="inputbox"',$this->form->Published);
			$lists['keepdata'] = JHTML::_('select.booleanlist','Keepdata','class="inputbox"',$this->form->Keepdata);
			$lists['confirmsubmission'] = JHTML::_('select.booleanlist','ConfirmSubmission','class="inputbox"',$this->form->ConfirmSubmission);
			$lists['ShowThankyou'] = JHTML::_('select.booleanlist','ShowThankyou','class="inputbox" onclick="enableThankyou(this.value);"',$this->form->ShowThankyou);
			$lists['ShowContinue'] = JHTML::_('select.booleanlist', 'ShowContinue', 'class="inputbox"'.(!$this->form->ShowThankyou ? 'disabled="disabled"' : ''), $this->form->ShowContinue);
			$lists['UserEmailMode'] = JHTML::_('select.booleanlist', 'UserEmailMode', 'class="inputbox" onclick="enableEmailMode(\'User\', this.value)"', $this->form->UserEmailMode, JText::_('HTML'), JText::_('RSFP_COMP_FIELD_TEXT'));
			$lists['UserEmailAttach'] = JHTML::_('select.booleanlist', 'UserEmailAttach', 'class="inputbox" onclick="enableAttachFile(this.value)"', $this->form->UserEmailAttach);
			$lists['AdminEmailMode'] = JHTML::_('select.booleanlist', 'AdminEmailMode', 'class="inputbox" onclick="enableEmailMode(\'Admin\', this.value)"', $this->form->AdminEmailMode, JText::_('HTML'), JText::_('RSFP_COMP_FIELD_TEXT'));
			$lists['MetaTitle'] = JHTML::_('select.booleanlist', 'MetaTitle', 'class="inputbox"', $this->form->MetaTitle);
			$lists['TextareaNewLines'] = JHTML::_('select.booleanlist', 'TextareaNewLines', 'class="inputbox"', $this->form->TextareaNewLines);
			$lists['AjaxValidation'] = JHTML::_('select.booleanlist', 'AjaxValidation', 'class="inputbox"', $this->form->AjaxValidation);
			
			$this->assignRef('themes', $this->get('themes'));
			
			$this->assignRef('lang', $this->get('lang'));
			$lists['Languages'] = JHTML::_('select.genericlist', $this->get('languages'), 'Language', 'onchange="submitbutton(\'changeLanguage\')"', 'value', 'text', $this->lang);
			
			$this->assignRef('mappings',$this->get('mappings'));
			$this->assignRef('mpagination',$this->get('mpagination'));
			$this->assignRef('conditions',$this->get('conditions'));
			$this->assignRef('formId',$this->form->FormId);
			$this->assignRef('emails',$this->get('emails'));
			
			$this->assignRef('lists', $lists);
		}
		elseif ($layout == 'new')
		{
			JToolBarHelper::custom('forms.new.steptwo', 'forward', 'forward', RSFormProHelper::isJ16() ? JText::_('JNEXT') : JText::_('Next'), false);
			JToolBarHelper::cancel('forms.cancel');
		}
		elseif ($layout == 'new2')
		{
			JToolBarHelper::custom('forms.new.stepthree', 'forward', 'forward', RSFormProHelper::isJ16() ? JText::_('JNEXT') : JText::_('Next'), false);
			JToolBarHelper::cancel('forms.cancel');
			
			$lists['AdminEmail'] = JHTML::_('select.booleanlist', 'AdminEmail', 'class="inputbox" onclick="changeAdminEmail(this.value)"', 1);
			$lists['UserEmail'] = JHTML::_('select.booleanlist', 'UserEmail', 'class="inputbox"', 1);
			$actions = array(
				JHTML::_('select.option', 'refresh', JText::_('RSFP_SUBMISSION_REFRESH_PAGE')),
				JHTML::_('select.option', 'thankyou', JText::_('RSFP_SUBMISSION_THANKYOU')),
				JHTML::_('select.option', 'redirect', JText::_('RSFP_SUBMISSION_REDIRECT_TO'))				
			);
			$lists['SubmissionAction'] = JHTML::_('select.genericlist', $actions, 'SubmissionAction', 'onclick="changeSubmissionAction(this.value)"');
			
			$this->assignRef('adminEmail', $this->get('adminEmail'));
			$this->assignRef('lists', $lists);
			$this->assignRef('editor', JFactory::getEditor());
		}
		elseif ($layout == 'new3')
		{
			JToolBarHelper::custom('forms.new.stepfinal', 'forward', 'forward', JText::_('Finish'), false);
			JToolBarHelper::cancel('forms.cancel');
			
			$lists['predefinedForms'] = JHTML::_('select.genericlist', $this->get('predefinedforms'), 'predefinedForm', '');
			$this->assignRef('lists', $lists);
		}
		elseif ($layout == 'component_copy')
		{
			JToolBarHelper::custom('components.copy.process', 'copy', 'copy', 'Copy', false);
			JToolBarHelper::cancel('components.copy.cancel');
			
			$formlist = $this->get('formlist');
			$lists['forms'] = JHTML::_('select.genericlist', $formlist, 'toFormId', '', 'value', 'text');
			
			$this->assign('formId', JRequest::getInt('formId'));
			$this->assign('cids', JRequest::getVar('cid', array()));
			$this->assignRef('lists', $lists);
		}
		elseif ($layout == 'richtext')
		{
			$this->assignRef('editor', JFactory::getEditor());
			$this->assign('noEditor', JRequest::getInt('noEditor'));
			$this->assign('formId', JRequest::getInt('formId'));
			$this->assign('editorName', JRequest::getCmd('opener'));
			$this->assign('editorText', $this->get('editorText'));
		}
		elseif ($layout == 'edit_mappings')
		{
			$formId = JRequest::getInt('formId');
			$this->assignRef('mappings',$this->get('mappings'));
			$this->assignRef('mpagination',$this->get('mpagination'));
			$this->assignRef('formId',$formId);
		}
		elseif ($layout == 'edit_conditions')
		{
			$formId = JRequest::getInt('formId');
			$this->assignRef('conditions',$this->get('conditions'));
			$this->assignRef('formId',$formId);
		}
		elseif ($layout == 'edit_emails')
		{
			$this->assignRef('emails',$this->get('emails'));
		}
		elseif ($layout == 'show')
		{
			$db =& JFactory::getDBO();
			$lang =& JFactory::getLanguage();
			$lang->load('com_rsform', JPATH_SITE);
			$formId = JRequest::getInt('formId');
			
			$db->setQuery("SELECT FormTitle FROM #__rsform_forms WHERE FormId = ".$formId." ");
			JToolBarHelper::title($db->loadResult(),'rsform');
			
			$this->assignRef('formId',$formId);
		}
		elseif ($layout == 'emails')
		{
			$this->assignRef('row', $this->get('email'));
			$lists['mode'] = JHTML::_('select.booleanlist', 'mode', 'class="inputbox" onclick="showMode(this.value);"', $this->row->mode, JText::_('HTML'), JText::_('Text'));
			$this->assignRef('lists', $lists);
			$this->assignRef('editor', JFactory::getEditor());
			$this->assignRef('quickfields', $this->get('quickfields'));
			$this->assignRef('lang', $this->get('emaillang'));
			$lists['Languages'] = JHTML::_('select.genericlist', $this->get('languages'), 'ELanguage', 'onchange="submitbutton(\'changeEmailLanguage\')"', 'value', 'text', $this->lang);
		}
		else
		{
			JToolBarHelper::addNewX('forms.add', RSFormProHelper::isJ16() ? JText::_('JTOOLBAR_NEW') : JText::_('New'));
			JToolBarHelper::spacer();
			JToolBarHelper::custom('forms.copy', 'copy.png', 'copy_f2.png', JText::_('RSFP_DUPLICATE'), false);
			JToolBarHelper::spacer();
			JToolBarHelper::deleteList(JText::_('VALIDDELETEITEMS'), 'forms.delete', RSFormProHelper::isJ16() ? JText::_('JTOOLBAR_DELETE') : JText::_('DELETE'));
			JToolBarHelper::spacer();
			JToolBarHelper::publishList('forms.publish', RSFormProHelper::isJ16() ? JText::_('JTOOLBAR_PUBLISH') : JText::_('Publish'));
			JToolBarHelper::unpublishList('forms.unpublish', RSFormProHelper::isJ16() ? JText::_('JTOOLBAR_UNPUBLISH') : JText::_('Unpublish'));
		
			$this->assignRef('forms', $this->get('forms'));
			$this->assignRef('pagination', $this->get('pagination'));
		
			$this->assignRef('sortColumn', $this->get('sortColumn'));
			$this->assignRef('sortOrder', $this->get('sortOrder'));
		}
		
		parent::display($tpl);
	}
	
	function triggerEvent($event)
	{
		$mainframe =& JFactory::getApplication();
		$mainframe->triggerEvent($event);
	}
}