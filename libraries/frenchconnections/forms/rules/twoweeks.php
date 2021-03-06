<?php

/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_PLATFORM') or die;

/**
 * Form Rule class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormRuleTwoWeeks extends JFormRule {

  /**
   * Method to test if two values are equal. To use this rule, the form
   * XML needs a validate attribute of equals and a field attribute
   * that is equal to the field to test against.
   *
   * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
   * @param   mixed             $value    The form field value to validate.
   * @param   string            $group    The field name group control value. This acts as as an array container for the field.
   *                                      For example if the field has name="foo" and the group value is set to "bar" then the
   *                                      full field name would end up being "bar[foo]".
   * @param   JRegistry         $input    An optional JRegistry object with the entire data set to validate against the entire form.
   * @param   JForm             $form     The form object for which the field is being tested.
   *
   * @return  boolean  True if the value is valid, false otherwise.
   *
   * @since   11.1
   * @throws  InvalidArgumentException
   * @throws  UnexpectedValueException
   */
  public function test(SimpleXMLElement $element, $value, $group = null, JRegistry $input = null, JForm $form = null) {

    $matches = array();
    $regex = "/^(\d{2})-(\d{2})-(\d{4})$/";
    $field = (string) $element['field'];

    // Check that a validation field is set.
    if (!$field) {
      throw new UnexpectedValueException(sprintf('$field empty in %s::test', get_class($this)));
    }

    if (is_null($form)) {
      throw new InvalidArgumentException(sprintf('The value for $form must not be null in %s', get_class($this)));
    }

    if (is_null($input)) {
      throw new InvalidArgumentException(sprintf('The value for $input must not be null in %s', get_class($this)));
    }

    // Firstly check that the start date is in the correct format.
    preg_match($regex, $value, $matches);

    if (!checkdate($matches[2], $matches[1], $matches[3])) {
      return false;
    }

    // So far so good, check that the end date is in the correct format
    preg_match($regex, $input->get($field), $matches);

    if (!checkdate($matches[2], $matches[1], $matches[3])) {
      return false;
    }
    
    // Dates are at least in the correct format.
    $start_date = new DateTime($value);
    //$start_date->format('Y-m-d');
    $end_date = new DateTime($input->get($field));
    //$end_date->format('Y-m-d');
    $interval = $start_date->diff($end_date);
    $length = $interval->format('%a');
    // Test the two values against each other.
    if ($length < 15) {
      return true;
    }

    return false;
  }

}
