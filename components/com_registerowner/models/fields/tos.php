<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('checkbox');

/**
 * Provides input for TOS
 *
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 * @since       2.5.5
 */
class JFormFieldTos extends JFormFieldCheckbox
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  2.5.5
	 */
	public $type = 'Tos';

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   2.5.5
	 */
	protected function getLabel()
	{

		return '';
	}
  
	/**
	 * Method to get the field input markup.
	 * The checked element sets the field to selected.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$value = $this->element['value'] ? (string) $this->element['value'] : '1';
		$required = $this->required ? ' required="required" aria-required="true"' : '';
    $text = $this->element['label'];
		$label = '';
    // Build the class for the label.
		
    $labelclass = !empty($this->element['labelclass']) ? $this->element['labelclass']: '';

		// Add the opening label tag and main attributes attributes.
		$label .= '<label id="' . $this->id . '-lbl" for="' . $this->id . '" class="' . $labelclass . ' required"';

	
		
		$tosarticle = $this->element['article'] ? (int) $this->element['article'] : 1;
		$link = JText::_($text);

		// Add the label text and closing tag.
		$label .= '>' . $link . '<span class="star">&#160;*</span>';
    
		if (empty($this->value))
		{
			$checked = (isset($this->element['checked'] )) ? ' checked="checked"' : '';
		}
		else
		{
			$checked = ' checked="checked"';
		}

		// Initialize JavaScript field attributes.
		$onclick = $this->element['onclick'] ? ' onclick="' . (string) $this->element['onclick'] . '"' : '';
    $label .='<input type="checkbox" name="' . $this->name . '" id="' . $this->id . '" value="'
			. htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"' . $class . $checked . $disabled . $onclick . $required . ' /></label>';
    return $label;
	}
}
