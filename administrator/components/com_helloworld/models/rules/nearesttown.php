<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla formrule library
jimport('joomla.form.formrule');
 
/**
 * Form Rule class for the Joomla Framework.
 */
class JFormRuleNearesttown extends JFormRule
{
	/**
	* The regular expression.
	*
	* @access	protected
	* @var		string
	* @since	1.6
	*/
	// For some reason the regex doesn't like the 'french' characters...
	//protected $regex = '^[a-zA-Z0-9<>/\s!()#@.,%&;-����]+$';
	protected $regex = '^[0-9]+$';
}
