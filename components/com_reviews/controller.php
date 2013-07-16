<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Hello World Component Controller
 */
class ReviewsController extends JControllerLegacy {

  public function display($cachable = false, $urlparams = array()) {
    $input = JFactory::getApplication()->input;


    $user = JFactory::getUser();

    // Not really the appropriate place to check create permissions for reviews
    if (!$user->authorise('review.submit.new', 'com_reviews')) {

      //throw new Exception(JText::_('COM_REVIEW_URL_INCORRECT'), 404);
    }

    $safeurlparams = array(
        'id' => 'INT',
        'lang' => 'CMD'
    );

    // Set the default view name and format from the Request.
    $viewName = $input->get('view', 'reviews', 'word');
    $input->set('view', $viewName);
    parent::display($cachable, $safeurlparams);

    return $this;
  }

}
