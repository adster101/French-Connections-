<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla formrule library
jimport('joomla.form.formrule');

/**
 * Form Rule class for the Joomla Framework.
 */
class JFormRuleExchange extends JFormRule {

  /**
   * The regular expression.
   *
   * @access	protected
   * @var		string
   * @since	1.6
   */
  protected $regex = '^[0-9.]+$';

  public function test(\SimpleXMLElement $element, $value, $group = null, \JRegistry $input = null, \JForm $form = null) {

    // If the field is empty and not required, the field is valid.
    $required = ((string) $element['required'] == 'true' || (string) $element['required'] == 'required');

    if (!$required && empty($value)) {
      return true;
    }

    // Test the value against the regular expression.
    $return = parent::test($element, $value, $group, $input, $form);

    return $return;
  }

}
