<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Hello World Component Controller
 */
class ReviewsController extends JControllerLegacy
{

  public function display($cachable = true, $urlparams = array())
  {

    $input = JFactory::getApplication()->input;


    $user = JFactory::getUser();

    $safeurlparams = array(
        'id' => 'INT',
        'lang' => 'CMD',
    );


    
    if ($user->get('guest') == 1)
    {
      // Redirect to login page.
      $this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
      return;
    }

    // Set the default view name and format from the Request.
    $viewName = $input->get('view', 'reviews', 'word');
    $input->set('view', $viewName);

    parent::display($cachable, $safeurlparams);

    return $this;
  }

}
