<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import file lib for checking file types of files being uploaded
jimport('joomla.filesystem.file');

jimport('frenchconnections.controllers.property.images');

/**
 * HelloWorld Controller
 */
class RealEstateControllerImages extends PropertyControllerImages {

  /**
   * Proxy for getModel.
   * @since	1.6
   */
  public function getModel($name = 'Image', $prefix = 'RealEstateModel') {
    $model = parent::getModel($name, $prefix, array('ignore_request' => true));
    return $model;
  }
  
}

