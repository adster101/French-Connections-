<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Renders a popup window button
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
class JToolbarButtonImageupload extends JToolbarButton
{
	/**
	 * Button type
	 *
	 * @var    string
	 */
	protected $_name = 'Imageupload';

	/**
	 * Fetch the HTML for the button
	 *
	 * @param   string   $type     Unused string, formerly button type.
	 * @param   string   $name     Button name
	 * @param   string   $text     The link text
	 * @param   string   $url      URL for popup
	 * @param   integer  $width    Width of popup
	 * @param   integer  $height   Height of popup
	 * @param   integer  $top      Top attribute.
	 * @param   integer  $left     Left attribute
	 * @param   string   $onClose  JavaScript for the onClose event.
	 *
	 * @return  string  HTML string for the button
	 *
	 * @since   11.1
	 */
	public function fetchButton($type = 'Popup', $name = '', $text = '', $url = '', $width = 600, $height = 400, $top = 0, $left = 0, $onClose = '')
	{
		JHtml::_('behavior.modal');

		$text = JText::_($text);
		$class = $this->fetchIconClass($name);
		$doTask = $this->_getCommand($name, $url, $width, $height, $top, $left);


		$html = "<button data-toggle=\"collapse\" data-target=\"#collapseUpload\" class=\"modal btn btn-small\">";
    //function(){
        //;
        //$('helloworld-choose-parent-form').addEvent('submit', function() {
          //Joomla.submitbutton('helloworld.woot');
          //return false;
        //})
      //}}\">";
		$html .= "<i class=\"$class\">";
		$html .= "</i>";
		$html .= "$text";
		$html .= "</button>";

		return $html;
	}

	/**
	 * Get the button id
	 *
	 * Redefined from JButton class
	 *
	 * @param   string  $type  Button type
	 * @param   string  $name  Button name
	 *
	 * @return  string	Button CSS Id
	 *
	 * @since   11.1
	 */
	public function fetchId($type, $name)
	{
		return $this->_parent->getName() . '-' . "popup-$name";
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @param   string   $name    Button name
	 * @param   string   $url     URL for popup
	 * @param   integer  $width   Unused formerly width.
	 * @param   integer  $height  Unused formerly height.
	 * @param   integer  $top     Unused formerly top attribute.
	 * @param   integer  $left    Unused formerly left attribure.
	 *
	 * @return  string   JavaScript command string
	 *
	 * @since   11.1
	 */
	protected function _getCommand($name, $url, $width, $height, $top, $left)
	{
		if (substr($url, 0, 4) !== 'http')
		{
			$url = JURI::base() . $url;
		}

		return $url;
	}
}
