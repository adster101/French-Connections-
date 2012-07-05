<?php
// No direct access to this file
defined('_JEXEC') or die;
 
// import the list field type
jimport('joomla.form.helper');

JFormHelper::loadFieldClass('list');

/**
 * HelloWorld Form Field class for the HelloWorld component
 */
class JFormFieldAjaxUpload extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'ajaxupload';
 
  /**
   *  Outputs HTML for the AJAX file uploader used to upload images to the image library for a property
   */
  
  public function getInput () {
    // Define the HTML to output
    $html = '<div id="upload"><div style="display: none; " class="drop-upload">Drop files here</div></div>';
    
    return $html;
  }

	
}
