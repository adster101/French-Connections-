<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
JFormHelper::loadFieldClass('checkbox');

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Single check box field.
 * This is a boolean field with null for false and the specified option for true
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @link        http://www.w3.org/TR/html-markup/input.checkbox.html#input.checkbox
 * @see         JFormFieldCheckboxes
 * @since       11.1
 */
class JFormFieldTermsandconditions extends JFormFieldCheckbox
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'Termsandconditions';

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   11.1
	 */
	protected function getLabel()
	{
		$label = '';
    $tandc_link = '';
    $params = JComponentHelper::getParams('com_rental');
    
		if ($this->hidden)
		{
			return $label;
		}
    
    $tandc_link = JRoute::_('/index.php?option=com_content&Itemid=' . (int) $params->get('termsandconditions', ''));

		// Get the label text from the XML element, defaulting to the element name.
		$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$text = $this->translateLabel ? JText::sprintf($text, $tandc_link) : $text;

		// Build the class for the label.
		$class = !empty($this->description) ? 'hasTooltip' : '';
		$class = $this->required == true ? $class . ' required' : $class;
		$class = !empty($this->labelclass) ? $class . ' ' . $this->labelclass : $class;

		// Add the opening label tag and main attributes attributes.
		$label .= '<label id="' . $this->id . '-lbl" for="' . $this->id . '" class="' . $class . '"';

		// If a description is specified, use it to build a tooltip.
		if (!empty($this->description))
		{
			// Don't translate discription if specified in the field xml.
			$description = $this->translateDescription ? JText::_($this->description) : $this->description;
			JHtml::_('bootstrap.tooltip');
			$label .= ' title="' . JHtml::tooltipText(trim($text, ':'), $description, 0) . '"';
		}

		// Add the label text and closing tag.
		if ($this->required)
		{
			$label .= '>' . $text . '<span class="star">&#160;*</span></label>';
		}
		else
		{
			$label .= '>' . $text . '</label>';
		}

		return $label;
	}

}

