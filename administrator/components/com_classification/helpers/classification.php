<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * HelloWorld component helper.
 */
abstract class ClassificationHelper
{

	/**
	 * Get the actions
	 */
	public static function getActions($classificationId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
 
		if (empty($classificationId)) {
			$assetName = 'com_classification';
		}
		else {
			$assetName = 'com_classification.classification.'.(int) $classificationId;
		}
 
		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.delete', 'core.edit.own', 'core.edit.state'
		);
 
		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}
 
		return $result; 
	}  
}
