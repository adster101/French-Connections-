<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla formrule library
jimport('joomla.form.formrule');

/**
 * Form Rule class for the Joomla Framework.
 */
class JFormRuleNonzero extends JFormRule {

  /**
   * The regular expression.
   *
   * @access	protected
   * @var		string
   * @since	1.6
   */
  protected $regex = '^0*[1-9]\d*$';
  // ^[1-9]\d*$ would also work (starts with any of 1-9 followed by any number
  // As would ^0*[1-9]\d*$ 

  public function test(\SimpleXMLElement $element, $value, $group = null, \JRegistry $input = null, \JForm $form = null) {

    // If the field is empty and not required, the field is valid.
    $required = ((string) $element['required'] == 'true' || (string) $element['required'] == 'required');

    if (!$required && empty($value)) {
      return true;
    }

    // If the value is an int greater than 0 then proceed
    $return = ((int) $value > 0) ? true : false;
      
    

    return $return;
  }

}
