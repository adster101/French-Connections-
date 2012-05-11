<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSFormControllerSubmissions extends RSFormController
{
	function __construct()
	{
		parent::__construct();
		
		$this->registerTask('apply', 		'save');
		$this->registerTask('exportCSV', 	'export');
		$this->registerTask('exportExcel', 	'export');
		$this->registerTask('exportXML', 	'export');
		
		$this->_db =& JFactory::getDBO();
	}

	function manage()
	{
		JRequest::setVar('view', 	'submissions');
		JRequest::setVar('layout', 	'default');
		
		parent::display();
	}
	
	function edit()
	{
		JRequest::setVar('view', 	'submissions');
		JRequest::setVar('layout', 	'edit');
		
		parent::display();
	}
	
	function columns()
	{
		$mainframe 	=& JFactory::getApplication();
		$formId 	= JRequest::getInt('formId');
		
		$this->_db->setQuery("DELETE FROM #__rsform_submission_columns WHERE FormId='".$formId."'");
		$this->_db->query();
		
		$staticcolumns = JRequest::getVar('staticcolumns', array());
		foreach ($staticcolumns as $column)
		{
			$this->_db->setQuery("INSERT INTO #__rsform_submission_columns SET FormId='".$formId."', ColumnName='".$this->_db->getEscaped($column)."', ColumnStatic='1'");
			$this->_db->query();
		}
		
		$columns = JRequest::getVar('columns', array());
		foreach ($columns as $column)
		{
			$this->_db->setQuery("INSERT INTO #__rsform_submission_columns SET FormId='".$formId."', ColumnName='".$this->_db->getEscaped($column)."', ColumnStatic='0'");
			$this->_db->query();
		}
		
		$this->setRedirect('index.php?option=com_rsform&task=submissions.manage&formId='.$formId);
	}
	
	function save()
	{
		// Get the model
		$model = $this->getModel('submissions');
		
		// Save
		$model->save();
		
		$cid = $model->getSubmissionId();
		
		$task = $this->getTask();
		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option=com_rsform&task=submissions.edit&cid[]='.$cid;
			break;
		
			case 'save':
				$link = 'index.php?option=com_rsform&task=submissions.manage';
			break;
		}
		
		$this->setRedirect($link, JText::_('RSFP_SUBMISSION_SAVED'));
	}
	
	function resend()
	{
		$formId = JRequest::getInt('formId');
		$cid 	= JRequest::getVar('cid', array(), 'post');
		JArrayHelper::toInteger($cid);
		
		foreach ($cid as $SubmissionId)
			RSFormProHelper::sendSubmissionEmails($SubmissionId);
		
		$this->setRedirect('index.php?option=com_rsform&task=submissions.manage&formId='.$formId, JText::_('RSFP_SUBMISSION_MAILS_RESENT'));
	}
	
	function cancel()
	{
		$this->setRedirect('index.php?option=com_rsform');
	}
	
	function cancelForm()
	{
		$formId = JRequest::getInt('formId');
		$this->setRedirect('index.php?option=com_rsform&task=forms.edit&formId='.$formId);
	}
	
	function clear()
	{
		$formId = JRequest::getInt('formId');
		$model 	= $this->getModel('submissions');
		
		$model->deleteSubmissionFiles($formId);
		$total = $model->deleteSubmissions($formId);
		
		$this->setRedirect('index.php?option=com_rsform&task=forms.manage', JText::sprintf('RSFP_SUBMISSIONS_CLEARED', $total));
	}
	
	function delete()
	{
		$formId = JRequest::getInt('formId');
		$cid 	= JRequest::getVar('cid', array(), 'post');
		JArrayHelper::toInteger($cid);
		
		$model = $this->getModel('submissions');
		$model->deleteSubmissionFiles($cid);
		$model->deleteSubmissions($cid);
		
		$this->setRedirect('index.php?option=com_rsform&task=submissions.manage&formId='.$formId);
	}
	
	function export()
	{
		$config = JFactory::getConfig();
		$tmp_path = $config->getValue('config.tmp_path');
		if (!is_writable($tmp_path))
		{
			JError::raiseWarning(500, JText::sprintf('RSFP_EXPORT_ERROR_MSG', $tmp_path));
			$this->setRedirect('index.php?option=com_rsform&task=submissions.manage');
		}
		
		JRequest::setVar('view', 	'submissions');
		JRequest::setVar('layout', 	'export');
		
		parent::display();
	}
	
	function exportProcess()
	{
		$mainframe =& JFactory::getApplication();
		$option    =  JRequest::getVar('option', 'com_rsform');
		
		$config = JFactory::getConfig();
	
		// Get post
		$session = JFactory::getSession();
		$post = $session->get($option.'.export.data', serialize(array()));
		$post = unserialize($post);
		
		// Limit
		$start = JRequest::getInt('exportStart');
		$mainframe->setUserState($option.'.submissions.limitstart', $start);
		$limit = JRequest::getInt('exportLimit', 500);
		$mainframe->setUserState($option.'.submissions.limit', $limit);
		
		// Tmp path
		$tmp_path = $config->getValue('config.tmp_path');
		$file = $tmp_path.DS.$post['ExportFile'];
		
		$formId = $post['formId'];
		
		// Type
		$type = strtolower($post['exportType']);
		
		// Selected rows or all rows
		$rows = !empty($post['ExportRows']) ? explode(',', $post['ExportRows']) : '';
		
		// Use headers ?
		$use_headers = (int) $post['ExportHeaders'];
		
		// Headers and ordering
		$staticHeaders = $post['ExportSubmission'];
		$headers = $post['ExportComponent'];
		$order = $post['ExportOrder'];
		
		// Remove headers that we're not going to export
		foreach ($order as $name => $id)
		{
			if (!isset($staticHeaders[$name]) && !isset($headers[$name]))
				unset($order[$name]);
		}
		
		// Adjust order array
		$order = array_flip($order);
		ksort($order);
		
		$model = $this->getModel('submissions');
		$model->export = true;
		$model->rows = $rows;
		$model->_query = $model->_buildQuery();
		$submissions = $model->getSubmissions();
		
		// CSV Options
		if ($type == 'csv')
		{
			$delimiter = str_replace(array('\t', '\n', '\r'), array("\t","\n","\r"), $post['ExportDelimiter']);
			$enclosure = str_replace(array('\t', '\n', '\r'), array("\t","\n","\r"), $post['ExportFieldEnclosure']);
			
			// Create and open file for writing if this is the first call
			// If not, just append to the file
			// Using fopen() because JFile::write() lacks such options
			$handle = fopen($file, $start == 0 ? 'w' : 'a');
			
			if ($start == 0 && $use_headers)
			{
				fwrite($handle, $enclosure.implode($enclosure.$delimiter.$enclosure,$order).$enclosure);
				fwrite($handle, "\n");
			}
			
			if (empty($submissions))
			{
				fclose($handle);
				// Adjust pagination
				$mainframe->setUserState($option.'.submissions.limitstart', 0);
				$mainframe->setUserState($option.'.submissions.limit', $mainframe->getCfg('list_limit'));
				echo 'END';
			}
			else
			{
				foreach ($submissions as $submissionId => $submission)
				{
					foreach ($order as $orderId => $header)
					{
						if (isset($submission['SubmissionValues'][$header]))
						{
							$submission['SubmissionValues'][$header]['Value'] = ereg_replace("\015(\012)?", "\012", $submission['SubmissionValues'][$header]['Value']);
							// Is this right ?
							if (strpos($submission['SubmissionValues'][$header]['Value'],"\n") !== false)
								$submission['SubmissionValues'][$header]['Value'] = str_replace("\n",' ',$submission['SubmissionValues'][$header]['Value']);
						}
						fwrite($handle, $enclosure.(isset($submission['SubmissionValues'][$header]) ? str_replace(array('\\r','\\n','\\t',$enclosure), array("\015","\012","\011",$enclosure.$enclosure), $submission['SubmissionValues'][$header]['Value']) : (isset($submission[$header]) ? $submission[$header] : '')).$enclosure.($header != end($order) ? $delimiter : ""));
					}
					fwrite($handle, "\n");
				}
				fclose($handle);
			}
		}
		// Excel Options
		elseif ($type == 'excel')
		{
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'helpers'.DS.'excel.php');
			
			$xls = new RSFormProXLS();
			$xls->use_headers = $use_headers;
			$xls->open($file, $start == 0 ? 'w' : 'a', $start);
			
			if ($start == 0 && $use_headers)
				$xls->write_headers($order);
			
			if (empty($submissions))
			{
				$xls->close();
				// Adjust pagination
				$mainframe->setUserState($option.'.submissions.limitstart', 0);
				$mainframe->setUserState($option.'.submissions.limit', $mainframe->getCfg('list_limit'));
				echo 'END';
			}
			else
			{
				$array = array();
				foreach ($submissions as $submissionId => $submission)
				{
					$item = array();
					foreach ($order as $orderId => $header)
					{
						if (isset($submission['SubmissionValues'][$header]))
							$item[$header] = $submission['SubmissionValues'][$header]['Value'];
						elseif (isset($submission[$header]))
							$item[$header] = $submission[$header];
						else
							$item[$header] = '';
					}
					
					$array[] = $item;
				}
				$xls->write($array);
				$xls->close();
			}
		}
		// XML Options
		elseif ($type == 'xml')
		{
			$handle = fopen($file, $start == 0 ? 'w' : 'a');
			
			if ($start == 0)
			{
				$buffer = '';
				$buffer .= '<?xml version="1.0" encoding="utf-8"?>'."\n";
				$buffer .= '<form>'."\n";
				$buffer .= '<title><![CDATA['.$model->getFormTitle().']]></title>'."\n";
				$buffer .= "\t".'<submissions>'."\n";
				fwrite($handle, $buffer);
			}
			
			if (empty($submissions))
			{
				$buffer = '';
				$buffer .= "\t".'</submissions>'."\n";
				$buffer .= '</form>';
				fwrite($handle, $buffer);
				fclose($handle);
				// Adjust pagination
				$mainframe->setUserState($option.'.submissions.limitstart', 0);
				$mainframe->setUserState($option.'.submissions.limit', $mainframe->getCfg('list_limit'));
				echo 'END';
			}
			else
			{
				foreach ($submissions as $submissionId => $submission)
				{
					fwrite($handle, "\t\t".'<submission>'."\n");
					$buffer = '';
					foreach ($order as $orderId => $header)
					{
						if (isset($submission['SubmissionValues'][$header]))
							$item = $submission['SubmissionValues'][$header]['Value'];
						elseif (isset($submission[$header]))
							$item = $submission[$header];
						else
							$item = '';
						
						if (!is_numeric($item))
							$item = '<![CDATA['.$item.']]>';
						
						$header = preg_replace('#\s+#', '', $header);
						
						$buffer .= "\t\t\t".'<'.$header.'>'.$item.'</'.$header.'>'."\n";
					}
					fwrite($handle, $buffer);
					fwrite($handle, "\t\t".'</submission>'."\n");
				}
				fclose($handle);
			}
		}
		
		exit();
	}
	
	function exportTask()
	{
		JRequest::setVar('view', 'submissions');
		JRequest::setVar('layout', 'exportprocess');
		
		parent::display();
		
		$session = JFactory::getSession();
		$option  = JRequest::getVar('option', 'com_rsform');
		
		$session->set($option.'.export.data', serialize(JRequest::get('post', JREQUEST_ALLOWRAW)));
	}
	
	function exportFile()
	{
		$config = JFactory::getConfig();
		$file = JRequest::getCmd('ExportFile');
		$file = $config->getValue('config.tmp_path').DS.$file;
		
		$type = JRequest::getCmd('ExportType');
		$extension = 'csv';
		if ($type == 'csv')
			$extension = 'csv';
		elseif ($type == 'excel')
			$extension = 'xls';
		elseif ($type == 'xml')
			$extension = 'xml';
		
		RSFormProHelper::readFile($file, date('Y-m-d').'_rsform.'.$extension);
	}
	
	function viewFile()
	{
		$id = JRequest::getInt('id');
		$this->_db->setQuery("SELECT * FROM #__rsform_submission_values WHERE SubmissionValueId='".$id."'");
		$result = $this->_db->loadObject();
		
		// Not found
		if (empty($result))
			return $this->setRedirect('index.php?option=com_rsform&task=submissions.manage');
		
		// Not an upload field
		$this->_db->setQuery("SELECT c.ComponentTypeId FROM #__rsform_properties p LEFT JOIN #__rsform_components c ON (p.ComponentId=c.ComponentId) WHERE p.PropertyName='NAME' AND p.PropertyValue='".$this->_db->getEscaped($result->FieldName)."'");
		$type = $this->_db->loadResult();
		if ($type != 9)
			return $this->setRedirect('index.php?option=com_rsform&task=submissions.manage', JText::_('RSFP_VIEW_FILE_NOT_UPLOAD'));
		
		jimport('joomla.filesystem.file');
		
		if (JFile::exists($result->FieldValue))
			RSFormProHelper::readFile($result->FieldValue);
		
		$this->setRedirect('index.php?option=com_rsform&task=submissions.manage', JText::_('RSFP_VIEW_FILE_NOT_FOUND'));
	}
}