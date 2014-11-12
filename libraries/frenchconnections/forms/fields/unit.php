<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Field to select a user ID from a modal list.
 *
 * @package     Joomla.Libraries
 * @subpackage  Form
 * @since       1.6
 */
class JFormFieldUnit extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6
	 */
	public $type = 'Unit';
  
  public $component;

	/**
	 * Method to get the user field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.6
	 */
	protected function getInput()
	{
		
    $html = array();
    
    $option = (string) $this->element['component'];
    
		$link = 'index.php?option=' . $option . '&amp;view=units&amp;layout=modal&amp;tmpl=component&amp;field=' . $this->id;

		// Initialize some field attributes.
		$attr = !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->required ? ' required' : '';

		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal_' . $this->id);

		// Build the script.
		$script = array();
		$script[] = '	function jSelectUnit_' . $this->id . '(id, title) {';
		$script[] = '		var old_id = document.getElementById("' . $this->id . '_id").value;';
		$script[] = '		if (old_id != id) {';
		$script[] = '			document.getElementById("' . $this->id . '_id").value = id;';
		$script[] = '			document.getElementById("' . $this->id . '").value = title;';
		$script[] = '			document.getElementById("' . $this->id . '").className = document.getElementById("' . $this->id . '").className.replace(" invalid" , "");';
		$script[] = '			' . $this->onchange;
		$script[] = '		}';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

    $row = new stdClass;
    
		if (!empty($this->value))
		{
      
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      $query->select('unit_title')
      ->from('#__unit_versions a')
      ->where('a.unit_id=' . (int) $this->value . ' AND a.review = 0');
      
      $db->setQuery($query);
      
      $row = $db->loadObject();
          
      
		} else {
      $row->unit_title = '';
    }


		// Create a dummy text field with the user name.
		$html[] = '<div class="input-append">';
		$html[] = '	<input type="text" id="' . $this->id . '" value="' . htmlspecialchars($row->unit_title, ENT_COMPAT, 'UTF-8') . '"'
			. ' readonly' . $attr . ' />';

		// Create the user select button.
		if ($this->readonly === false)
		{
			$html[] = '		<a class="btn btn-primary modal_' . $this->id . '" title="' . JText::_('JLIB_FORM_CHANGE_USER') . '" href="' . $link . '"'
				. ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
			$html[] = '<i class="icon-user"></i></a>';
		}

		$html[] = '</div>';

		// Create the real field, hidden, that stored the user id.
		$html[] = '<input type="hidden" id="' . $this->id . '_id" name="' . $this->name . '" value="' . (int) $this->value . '" />';

		return implode("\n", $html);
	}

	/**
	 * Method to get the filtering groups (null means no filtering)
	 *
	 * @return  mixed  array of filtering groups or null.
	 *
	 * @since   1.6
	 */
	protected function getGroups()
	{
		return null;
	}

	/**
	 * Method to get the users to exclude from the list of users
	 *
	 * @return  mixed  Array of users to exclude or null to to not exclude them
	 *
	 * @since   1.6
	 */
	protected function getExcluded()
	{
		return null;
	}
}
