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
class plgUserRegistration extends JPlugin
{

  /**
   * Affects constructor behavior. If true, language files will be loaded automatically.
   *
   * @var    boolean
   * @since  3.1
   */
  protected $autoloadLanguage = true;

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
    if (!in_array($name, array('com_users.registration', 'com_users.login')))
    {
      return true;
    }

    $fieldsets = $form->getFieldsets();

    foreach ($fieldsets as $fieldset)
    {
      foreach ($form->getFieldset($fieldset->name) as $field)
      {
        // Filter out the jform[] bit which is appended to the form field name by this stage
        $field_name = preg_match('/\[(.*?)\]/', $field->name, $matches);
        if ($field_name)
        {
          if (!$field->hidden && $field->type != 'Spacer')
            $form->setFieldAttribute($matches[1], 'class', 'form-control');
        }
        else
        {
          $form->setFieldAttribute($field->name, 'class', 'form-control');
        }
      }
    }


    return true;
  }

}

