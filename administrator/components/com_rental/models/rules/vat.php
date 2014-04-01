<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla formrule library
jimport('joomla.form.formrule');

/**
 * Form Rule class for the Joomla Framework.
 */
class JFormRuleVat extends JFormRule {

  /**
   * The regular expression.
   *
   * @access	protected
   * @var		string
   * @since	1.6
   */
  protected $regex = '^([a-zA-Z]{2})([1-9]{7,13})$';

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
