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
    $html = '
			<div class="formRow">

				<input type="file" id="url" name="url[]" multiple><br>
			</div>

			<div class="formRow">
				<input type="submit" id="_submit" name="_submit" value="Upload">
			</div>
    ';
   
    return $html;
  }

	
}
