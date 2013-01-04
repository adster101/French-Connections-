<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JFormHelper::loadRuleClass('email');

/**
 * JFormRule for com_contact to make sure the E-Mail adress is not blocked.
 *
 * @package     Joomla.Site
 * @subpackage  com_contact
 */
class JFormRuleDate extends JFormRuleEmail
{
	/**
	 * Method to test for a valid color in hexadecimal.
	 *
	 * @param   SimpleXMLElement  &$element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value     The form field value to validate.
	 * @param   string            $group     The field name group control value. This acts as as an array container for the field.
	 *                                       For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                       full field name would end up being "bar[foo]".
	 * @param   object            &$input    An optional JRegistry object with the entire data set to validate against the entire form.
	 * @param   object            &$form     The form object for which the field is being tested.
	 *
	 * @return  boolean  True if the value is valid, false otherwise.
	 */
	public function test(& $element, $value, $group = null, &$input = null, &$form = null)
	{

   if (preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $value, $matches)) {

     if (checkdate($matches[2], $matches[3], $matches[1])) {
            return true;
     }
   }

    
		return false;
	}
}
