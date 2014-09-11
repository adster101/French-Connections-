<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla formrule library
jimport('joomla.form.formrule');
 
/**
 * Form Rule class for the Joomla Framework.
 */
class JFormRuleOccupancy extends JFormRule
{
	/**
	 * The regular expression.
	 *
	 * @access	protected
	 * @var		string
	 * @since	1.6
	 */
	protected $regex = '^[1-9]{1,2}+$';
}
