<?php
/**
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
/**
 * Joomla User plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	User.joomla
 * @since		1.5
 */
class plgLettingTariffs extends JPlugin
{

  
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
    

		// Check we are manipulating a valid form.
		$name = $form->getName();

    if (!in_array($name, array('com_helloworld.tariffs')))
		{
			return true;
		}		
    
     
    
		//$form->removeField('id');works
    
    $form->setValue('id','com_helloworld.tariffs','21');
    return true;
    
	}
}
