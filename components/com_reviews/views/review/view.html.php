<?php    

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
 
/**
 * HTML View class for the HelloWorld Component
 */
class ReviewsViewReview extends JViewLegacy
{
  protected $state;
	protected $form;
	protected $item;
	protected $return_page;
  
	// Overwriting JView display method
	function display($tpl = null) 
	{
           
    $this->form = $this->get('Form');
    
    $this->item = $this->get('Item');
    
		// Set the document
		$this->setDocument();		
    parent::display($tpl);

	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
    $this->document->setTitle(JText::sprintf('COM_REVIEW_SUBMIT_REVIEW'));
	}	
}
