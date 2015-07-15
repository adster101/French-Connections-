<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * JFormRule for com_contact to make sure the subject contains no banned word.
 *
 * @package     Joomla.Site
 * @subpackage  com_contact
 */
class JFormRuleTelephone extends JFormRule
{

  /**
   * Method to test for a valid.
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
    // If the field is empty and not required, the field is valid.
    $required = ((string) $element['required'] == 'true' || (string) $element['required'] == 'required');

    if (!$required && empty($value))
    {
      return true;
    }

    // $value is prefiltered by Jform and will only contain alpha numeric chars (+ and spaces are stripped).
    // Test that the string matches the pattern
    if (preg_match('/^[0-9+\s+()]{11,25}$/', $value, $matches))
    {
      // Yay
      return true;
    }
    else
    {
      // Boo
      return false;
    }
  }

}
