<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * Hello World Component Controller
 */

class ReviewsController extends JControllerLegacy
{
  
  public function display($cachable = false, $urlparams = array()) {
    
    
  	$user	= JFactory::getUser();
    
		if (!$user->authorise('review.submit.new', 'com_reviews')) {
         
      throw new Exception(JText::_('COM_REVIEW_URL_INCORRECT'), 404);
  
    }

    $safeurlparams = array(
			'id'				=> 'INT',
			'lang'				=> 'CMD'
        
		);

  	parent::display($cachable, $safeurlparams);

    return $this;
  }
  
}
