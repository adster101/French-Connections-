<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

jimport('simplediff.simplediff');

/**
 * HelloWorld Model
 */
class RealestateModelReview extends JModelAdmin
{

  /**
   * Method to get the record form.
   *
   * @param	array	$data		Data for the form.
   * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
   * @return	mixed	A JForm object on success, false on failure
   * @since	1.6
   */
  public function getForm($data = array(), $loadData = true)
  {

    // Get the form.
    JForm::addFormPath(JPATH_LIBRARIES . '/frenchconnections/forms');
    $form = $this->loadForm('com_realestate.approve_draft', 'approve_draft', array('control' => 'jform', 'load_data' => $loadData));

    if (empty($form))
    {
      return false;
    }

    return $form;
  }

  
  /**
   * Get the message text to bind with the form
   * TO DO - Move most of below to getItem or preprocessForm?
   */
  public function loadFormData()
  {
    $input = JFactory::getApplication()->input;
    $recordId = $input->getInt('id');
    $layout = $input->getCmd('layout');

    // Get the owner details etc
    $table = $this->getTable('Property', 'RealestateTable');

    $property = $table->load($recordId);

    if (!$property)
    {
      Throw new Exception('Problem loading property details', 500);
    }

    $userId = ($table->created_by) ? $table->created_by : 0;

    $user = JFactory::getUser($userId);

    $uri = JUri::getInstance();
    $domain = $uri->toString(array('scheme', 'host'));


    if (empty($table->expiry_date))
    {
      $data['body'] = JText::sprintf('COM_REALESTATE_REVIEW_GOING_LIVE_LETTER', $user->name, $recordId, $domain, $recordId, $domain, $recordId);
      $data['subject'] = JText::sprintf('COM_RENTAL_REVIEW_CHANGES_GOING_LIVE_EMAIL_SUBJECT', $user->name, $recordId);
    }
    else if ($layout == 'reject')
    {
      $data['body'] = JText::sprintf('COM_RENTAL_REVIEW_CHANGES_REJECTED_EMAIL_BODY', $user->name, $recordId, $domain, $recordId, $domain, $recordId);
      $data['subject'] = JText::sprintf('COM_RENTAL_REVIEW_CHANGES_REJECTED_EMAIL_SUBJECT', $user->name, $recordId);
    }
    else if ($layout == 'approve')
    {
      $data['body'] = JText::sprintf('COM_RENTAL_HELLOWORLD_APPROVE_CHANGES_EMAIL_BODY', $user->name, $recordId, $domain, $recordId, $domain, $recordId);
      $data['subject'] = JText::sprintf('COM_RENTAL_APPROVE_CHANGES_CONFIRMATION_SUBJECT', $user->name, $recordId);
    }

    return $data;
  }

  /**
   * Returns a reference to the a Table object, always creating it.
   *
   * @param	type	The table type to instantiate
   * @param	string	A prefix for the table class name. Optional.
   * @param	array	Configuration array for model. Optional.
   * @return	JTable	A database object
   * @since	1.6
   */
  public function getTable($type = 'PropertyVersions', $prefix = 'RealestateTable', $config = array())
  {
    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * 
   * 
   * 
   */
  public function getListingDiff($recordId = null)
  {
    $input = JFactory::getApplication()->input;

    // Get the primary key set in the model state
    $recordId = (!empty($recordId)) ? $recordId : (int) $this->getState($this->getName() . '.id');
    $propertyId = $input->get('id', '', 'int');
    $versions = array();

    // Fetch the latest two version of this property.
    if (!$property_versions = $this->getPropertyVersionDetail($propertyId))
    {
      Throw new Exception('Problem fetching property version detail', 500);
    }

    foreach ($property_versions as $key => $value)
    {
      // Get the images based on the version id we are looking at
      $images = (array_key_exists('id', $value)) ? $this->getImages($value['id']) : array();
      $versions['images'][] = $images;
    }
    if (count($versions['images']) == 2)
    {


      $simplediff = new simplediff();

      // Contains all images in the new version
      $new_version_images = array();
      $old_version_images = array();

      foreach ($versions['images'][1] as $key => $image)
      {
        $new_version_images[$image['image_file_name']] = array();
        $new_version_images[$image['image_file_name']]['position'] = $key;
        $new_version_images[$image['image_file_name']]['caption'] = $image['caption'];
      }

      foreach ($versions['images'][0] as $key => $image)
      {
        $old_version_images[$image['image_file_name']] = array();
        $old_version_images[$image['image_file_name']]['position'] = $key;
        $old_version_images[$image['image_file_name']]['caption'] = $image['caption'];
      }

      // $v contains an array of images 
      foreach ($versions['images'][0] as $key => $image)
      {
        // Deals with diffing the captions
        if (array_key_exists($image['image_file_name'], $new_version_images))
        {
          $image['deleted'] = false;
          // Image is present in both versions
          $old_caption = $image['caption'];
          $new_caption = $new_version_images[$image['image_file_name']]['caption'];

          // Get a diff on the two captions
          $diff = $simplediff->htmldiff($old_caption, $new_caption);

          // Store the diff against the new image array
          $versions['images'][1][$new_version_images[$image['image_file_name']]['position']]['diff'] = $diff;
        }

        if (!array_key_exists($image['image_file_name'], $new_version_images))
        {
          $image['deleted'] = true;
          // Image has been deleted, need to add it to new version images for completeness
          $versions['images'][0][$key] = $image;
        }
      }

      foreach ($versions['images'][1] as $key => $image)
      {
        if (!array_key_exists($image['image_file_name'], $old_version_images))
        {
          $image['added'] = true;
          $versions['images'][1][$key] = $image;
        }
      }
    }
    // Get any html diffs if we have a new version
    if (!empty($property_versions[1]))
    {
      // Get an array holding the two version of the property part of the listing      
      $property_versions = $this->getHtmlDiff($property_versions);
    }

    // TO DO - The below needs to be a method
    foreach ($property_versions[0] as $k => $v)
    {
      $new_versions[$k] = array();
      $new_versions[$k][] = $property_versions[0][$k];
      $new_versions[$k][] = (!empty($property_versions[1][$k])) ? $property_versions[1][$k] : '';
      $other_array[] = $new_versions;
      $new_versions = array();
    }

    $versions['property'] = $other_array;

    return $versions;
  }

  /**
   * getPropertyVersionDetail = returns published and update versions of either a unit or a property listing
   * 
   * @param type $recordId
   * @param type $table1
   * @param type $table2
   * @param type $join_field
   * @return mixed 
   * 
   */
  public function getPropertyVersionDetail($recordId)
  {

    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    // Initialise the query.
    $query->select('
      b.*,
      c.title,
      d.title,
      e.title,
      f.title,
      g.title,
      u.name
    ');

    $query->from($db->quoteName('#__realestate_property') . ' as a');
    $query->join('left', $db->quoteName('#__realestate_property_versions') . ' as b on a.id = b.realestate_property_id');
    $query->join('left', $db->quoteName('#__classifications') . ' c on c.id = b.country');
    $query->join('left', $db->quoteName('#__classifications') . ' d on d.id = b.area');
    $query->join('left', $db->quoteName('#__classifications') . ' e on e.id = b.region');
    $query->join('left', $db->quoteName('#__classifications') . ' f on f.id = b.department');
    $query->join('left', $db->quoteName('#__classifications') . ' g on g.id = b.city');
    $query->join('left', $db->quoteName('#__users') . ' u on u.id = b.modified_by');
    $query->where('a.id = ' . (int) $recordId);
    $query->where('b.review in (0,1)');

    $db->setQuery($query);

    $rows = $db->loadAssocList();

// Check that we have a result.
    if (empty($rows))
    {
      return false;
    }

    return $rows;
  }

  /**
   * Method takes an array containing one or two elements corresponding to different versions of
   * either a property or unit and generates an 'html' diff of the two with insertions wrapped in 
   * <ins> and deletions wrapped in <del>. If no new version then it's created but all elements
   * are set to null.
   * 
   * @param array $versions
   * @param type $keys_to_check
   * @return type
   */
  public function getHtmlDiff($versions = array())
  {

    // Get an instance of our simple diff class
    $simplediff = new simplediff();

    // 
    $old_version = $versions[0];
    $new_version = (!empty($versions[1])) ? $versions[1] : array();

    // Loop over the old version array
    foreach ($old_version as $key => $value)
    {
      if (empty($new_version[$key]))
      {
        // If we're not looking at a new version, just set it to empty
        $new_version[$key] = '';
      }
      else
      {
        $diff = $simplediff->htmldiff(strip_tags($old_version[$key]), strip_tags($new_version[$key]));
        $new_version[$key] = trim($diff);
        $old_version[$key] = strip_tags($old_version[$key]);
      }
    }

    // Update the 'diffed' versions in the version array
    $versions[1] = $new_version;
    $versions[0] = $old_version;
    return $versions;
  }

  /**
   * getImages = gets a list of images based on the version ID passed
   * @param type $version_id
   * @return type
   */
  public function getImages($version_id = '')
  {

    $db = $this->getDbo();
    $query = $db->getQuery(true);

    // Get a list of the images uploaded against this listing
    $query->select('
      id,
      realestate_property_id,
      image_file_name,
      caption,
      ordering,
      version_id
    ');
    $query->from('#__realestate_property_images_library');

    $query->where('version_id = ' . (int) $version_id);

    $query->order('ordering', 'asc');

    $db->setQuery($query);

    $images = $db->loadAssocList();

    if (empty($images))
    {
      return false;
    }

    return $images;
  }

}