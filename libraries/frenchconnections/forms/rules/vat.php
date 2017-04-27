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
  //protected $regex = '^([a-zA-Z]{2})([0-9]{7,13})([a-zA-Z]{1})?$';
  protected $regex = '^((AT)?U[0-9]{8}|(BE)?0[0-9]{9}|(BG)?[0-9]{9,10}|(CY)?[0-9]{8}L|(CZ)?[0-9]{8,10}|(DE)?[0-9]{9}|(DK)?[0-9]{8}|(EE)?[0-9]{9}|(EL|GR)?[0-9]{9}|(ES)?[0-9A-Z][0-9]{7}[0-9A-Z]|(FI)?[0-9]{8}|(FR)?[0-9A-Z]{2}[0-9]{9}|(GB)?([0-9]{9}([0-9]{3})?|[A-Z]{2}[0-9]{3})|(HU)?[0-9]{8}|(IE)?[0-9]S[0-9]{5}L|(IT)?[0-9]{11}|(LT)?([0-9]{9}|[0-9]{12})|(LU)?[0-9]{8}|(LV)?[0-9]{11}|(MT)?[0-9]{8}|(NL)?[0-9]{9}B[0-9]{2}|(PL)?[0-9]{10}|(PT)?[0-9]{9}|(RO)?[0-9]{2,10}|(SE)?[0-9]{12}|(SI)?[0-9]{8}|(SK)?[0-9]{10})$';

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
