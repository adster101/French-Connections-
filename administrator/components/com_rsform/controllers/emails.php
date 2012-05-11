<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSFormControllerEmails extends RSFormController
{
	function __construct()
	{
		parent::__construct();
		
		$this->registerTask('apply', 'save');
		
		$this->_db =& JFactory::getDBO();
	}
	
	function save()
	{
		$model	= $this->getModel('forms');
		$row	= $model->saveemail();
		
		if ($this->getTask() == 'apply')
			return $this->setRedirect('index.php?option=com_rsform&task=forms.emails&cid='.$row->id.'&formId='.$row->formId.'&tmpl=component&update=1');
		
		$close = RSFormProHelper::isJ16() ? 'window.parent.SqueezeBox.close();' : 'window.parent.document.getElementById(\'sbox-window\').close();';
		$document =& JFactory::getDocument();
		$document->addScriptDeclaration('window.parent.updateemails('.$row->formId.');'.$close);
	}
	
	function remove()
	{
		$db		=& JFactory::getDBO();
		$cid	= JRequest::getInt('cid');
		$formId = JRequest::getInt('formId');
		
		if ($cid)
		{
			$db->setQuery("DELETE FROM #__rsform_emails WHERE id = ".$cid." ");
			$db->query();
			$db->setQuery("DELETE FROM #__rsform_translations WHERE reference_id IN ('".$cid.".fromname','".$cid.".subject','".$cid.".message') ");
			$db->query();
		}
		
		JRequest::setVar('view', 'forms');
		JRequest::setVar('layout', 'edit_emails');
		JRequest::setVar('tmpl', 'component');
		JRequest::setVar('formId', $formId);
		
		parent::display();
		jexit();
	}
	
	function update()
	{
		$formId = JRequest::getInt('formId');
		
		JRequest::setVar('view', 'forms');
		JRequest::setVar('layout', 'edit_emails');
		JRequest::setVar('tmpl', 'component');
		JRequest::setVar('formId', $formId);
		
		parent::display();
		jexit();
	}
}