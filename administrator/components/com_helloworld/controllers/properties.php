<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * HelloWorlds Controller
 */
class HelloWorldControllerProperties extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'Property', $prefix = 'HelloWorldModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

  /*
   * 
   */
  public function submit()
  {
    $this->setMessage('Submitted for review', 'notice');
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option, false)
      );
  }

}
