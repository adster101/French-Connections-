<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * HelloWorlds Controller
 */
class HelloWorldControllerUnits extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'Unit', $prefix = 'HelloWorldModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}


  public function publish() {

    parent::publish();
		$listing_id = JFactory::getApplication()->input->get('listing_id','','int');

    $extension = $this->input->get('extension');
		$id = ($listing_id) ? '&id=' . $listing_id: '';
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $id, false));

  }

  public function reorder() {

    parent::reorder();
    
		$listing_id = JFactory::getApplication()->input->get('listing_id','','int');

    $extension = $this->input->get('extension');
		$id = ($listing_id) ? '&id=' . $listing_id: '';
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $id, false));

  }




}
