<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

jimport('joomla.utilities.date');

/**
 * An example custom profile plugin.
 *
 * @package		Joomla.Plugin
 * @subpackage	User.profile
 * @version		1.6
 */
class plgContentAirport extends JPlugin {

  /**
   * catid, the catid to process articles for
   * 
   * @var type 
   */
  var $catid = '';

  /**
   * Constructor
   *
   * @access      protected
   * @param       object  $subject The object to observe
   * @param       array   $config  An array that holds the plugin configuration
   * @since       1.5
   */
  public function __construct(& $subject, $config) {
    parent::__construct($subject, $config);
    $this->loadLanguage();

    // Set the cat id property
    $this->catid = $this->params->get('catid', '');
  }

  /**
   * @param	string	$context	The context for the data
   * @param	int		$data		The user id
   * @param	object
   *
   * @return	boolean
   * @since	1.6
   */
  function onContentPrepareData($context, $data) {

    // Check if we're in the site app, otherwise, do nothing
    if (JFactory::getApplication()->isSite()) {
      return true;
    }

    $catid = $this->params->get('catid', '');



    // Check the context 
    if ($context == 'com_content.article' && isset($data->catid)) {

      if (is_object($data) && $data->catid == $catid) {
        $articleId = isset($data->id) ? $data->id : 0;
        if ($articleId > 0 && $data->catid == $catid) {
          // Load the data from the database.
          $db = JFactory::getDbo();
          $query = $db->getQuery(true);
          $query->select('latitude, longitude, department, code, name');
          $query->from('#__airports');
          $query->where('id = ' . $db->Quote($articleId));
          $db->setQuery($query);
          $results = $db->loadAssoc();

          // Check for a database error.
          if ($db->getErrorNum()) {
            $this->_subject->setError($db->getErrorMsg());
            return false;
          }


          // Merge the data
          $data->attribs['latitude'] = $results['latitude'];
          $data->attribs['longitude'] = $results['longitude'];
          $data->attribs['department'] = $results['department'];
          $data->attribs['code'] = $results['code'];
          $data->attribs['name'] = $results['name'];
        }
      }
    }

    return true;
  }

  /**
   * @param	JForm	$form	The form to be altered.
   * @param	array	$data	The associated data for the form.
   *
   * @return	boolean
   * @since	1.6
   */
  function onContentPrepareForm($form, $data) {

    // Check if we're in the site app, otherwise, do nothing
    if (JFactory::getApplication()->isSite()) {
      return true;
    }


    $name = $form->getName();

    $append_form = false;

    if (!($form instanceof JForm)) {
      $this->_subject->setError('JERROR_NOT_A_FORM');
      return false;
    }

    // Check we are manipulating a valid form.
    if (!in_array($name, array('com_content.article'))) {
      return true;
    }

    // Only going to work if we cludge the additional data into the form here....

    $catid = $this->params->get('catid', '');

    // If data is empty, then we test to see if the additional fields are set in the application input.
    if (empty($data)) {

      $raw = JFactory::getApplication()->input->get('jform', array(), 'array');
      // Additional fields present for this article
      $lat = $raw['attribs']['latitude'];
      $lon = $raw['attribs']['longitude'];
      $dep = $raw['attribs']['department'];

      if ((!empty($lat) && !empty($lon) && !empty($dep))) {
        $append_form = true;
      }
    }

    // Only show the additional airport fields if the category ids match
    if ($data->catid == $catid || ($append_form)) {



      // Add the extra fields to the form.
      JForm::addFormPath(dirname(__FILE__) . '/forms');
      $form->loadFile('airport', false);
      return true;
    }

    return true;
  }

  public function onContentAfterDelete($context, $article) {


    // The catid specified in the plugin parameters
    $catid = $this->params->get('catid', '');


    if ($context == 'com_content.article' && ($article->catid == $catid)) {

      $articleId = $article->id;

      if ($articleId) {

        try {
          $db = JFactory::getDbo();

          $query = $db->getQuery(true);
          $query->delete('#__airports');
          $query->where('id = ' . $db->Quote($articleId));

          $db->setQuery($query);
          if (!$db->query()) {
            throw new Exception($db->getErrorMsg());
          }
        } catch (Exception $e) {
          $this->_subject->setError($e->getMessage());
          return false;
        }
      }
    }
    return true;
  }

  /**
   * onContentAfterSave - saves out the additional airport info to a separate table.
   * 
   * @param type $context
   * @param type $article
   * @param type $isNew
   * @return boolean
   * @throws Exception
   */
  public function onContentAfterSave($context, $article, $isNew) {

// The catid specified in the plugin parameters
    $catid = $this->params->get('catid', '');


    if ($context == 'com_content.article' && ($article->catid == $catid)) {

      $articleId = $article->id;

      $attribs = json_decode($article->attribs);


      if ($articleId) {

        try {
          $db = JFactory::getDbo();

          $query = $db->getQuery(true);
          $query->delete('#__airports');
          $query->where('id = ' . $db->Quote($articleId));

          $db->setQuery($query);
          if (!$db->query()) {
            throw new Exception($db->getErrorMsg());
          }

          $query->clear();
          $query->insert('#__airports');
          $query->columns('id,department,latitude,longitude,code,name');

          $query->values($articleId . ', ' . $db->quote($attribs->department) . ', ' . $db->quote($attribs->latitude) . ', ' . $db->quote($attribs->longitude) . ', ' . $db->quote($attribs->code) . ', ' . $db->quote($attribs->name));

          $db->setQuery($query);

          if (!$db->query()) {
            throw new Exception($db->getErrorMsg());
          }
        } catch (Exception $e) {
          $this->_subject->setError($e->getMessage());
          return false;
        }
      }
    }
    return true;
  }

}

