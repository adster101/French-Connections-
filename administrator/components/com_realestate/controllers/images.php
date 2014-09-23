<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import file lib for checking file types of files being uploaded
jimport('joomla.filesystem.file');

jimport('frenchconnections.controllers.property.images');

/**
 * HelloWorld Controller
 */
class RealEstateControllerImages extends PropertyControllerImages
{

  var $folder = '';
  var $property_id;
  var $id;
  var $review;

  /**
   * Constructor.
   *
   * @param  array  $config  An optional associative array of configuration settings.
   *
   * @since  1.6
   * @see    JController
   */
  public function __construct($config = array())
  {

    // Get the input and initialise our properties based on that
    $input = JFactory::getApplication()->input;

    $this->property_id = $input->get('property_id', '', 'int');

    $this->folder = JPATH_SITE . '/images/property/' . $this->property_id . '/';

    // Get the version id 
    $this->id = $input->get('id', '', 'int');

    // Get the review state for the property
    $this->review = $input->get('review', '', 'boolean');

    parent::__construct($config);



    //$this->registerTask('saveandnext', 'save');
  }

  /*
   * View action - checks ownership of record sets the edit id in session and redirects to the view
   *
   * TO DO - Figure out a way to share this action between realestate and rental components
   *
   */

  public function manage()
  {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken() or die('Invalid Token');

    // $id is the listing the user is trying to edit
    $id = $this->input->get('realestate_property_id', '', 'int');

    $data['id'] = $id;

    if (!$this->allowEdit($data, 'id'))
    {
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option, false)
      );

      $this->setMessage('blah', 'error');

      return false;
    }

    $this->holdEditId($this->option . '.edit.' . $this->context, $id);

    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=images&realestate_property_id=' . (int) $id, false)
    );
    return true;
  }

}

