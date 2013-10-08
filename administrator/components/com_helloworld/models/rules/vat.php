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
  protected $regex = '^([GB])*(([1-9]\d{8})|([1-9]\d{11})|(GD[1-9]\d{2})|(HA[1-9]\d{2}))$';

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
