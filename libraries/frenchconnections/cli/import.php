<?php

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
abstract class Import extends JApplicationCli
{
  /*
   * Get the nearest town or city based on the town/city given and department
   */

  public function nearestcity($latitude = '', $longitude = '')
  {

    try
    {
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);

      $query->select("a.id, b.title");

      $query->from('#__classifications a');
      $query->innerjoin('#__classifications b on b.id = a.parent_id');
      $query->order('
        ( 3959 * acos(cos(radians(' . $latitude . ')) *
          cos(radians(a.latitude)) *
          cos(radians(a.longitude) - radians(' . $longitude . '))
          + sin(radians(' . $latitude . '))
          * sin(radians(a.latitude)))) 
        ');

      $db->setQuery($query, 0, 1);
      $rows = $db->loadObject();
    }
    catch (Exception $e)
    {
      throw new Exception('Problem getting nearest city - ' . $e->getMessage());
    }

    // If there's a nearest city then return it.
    if (!empty($rows))
    {
      return $rows->id;
    }

    return false;
  }

  /**
   * Gets a property version from the relevant property version table
   * 
   * @param type $select The select field
   * @param type $table The table to select from 
   * @param type $field The field to check against
   * @param type $agency_reference - The value of the above field
   * @param type $db
   * @return boolean
   * @throws Exception
   */
  public function getPropertyVersion($table = '', $field = '', $affiliate_reference = '', $db)
  {
    $query = $db->getQuery(true);
    $query->select('*');
    $query->from($db->quoteName($table));
    $query->where($db->quoteName($field) . '=' . $db->quote($affiliate_reference));
    $query->where('review = 0');
    $db->setQuery($query);

    try
    {
      $row = $db->loadObject();
    }
    catch (Exception $e)
    {
      throw new Exception('Problem getting property version - ' . $e->getMessage());
    }

    // Check that we have a result.
    if (empty($row))
    {
      return false;
    }
    // Return the property version ID
    return $row;
  }

  /**
   * Relies on 
   * 
   * @param type $table
   * @param type $data
   * @return type
   * @throws Exception
   */
  public function load($table, $value)
  {

    try
    {
      $table->load($value);
    }
    catch (RuntimeException $e)
    {
      throw new Exception('Problem creating a new real estate property version in Allez Francais XML import createPropertyVersion()');
    }

    return $table;
  }

  /**
   * Relies on 
   * 
   * @param type $table
   * @param type $data
   * @return type
   * @throws Exception
   */
  public function save($table, $data = array())
  {
    try
    {
      $table->save($data);
    }
    catch (Exception $e)
    {
      throw new Exception('Problem saving data )' . $e->getMessage());
    }

    return $table;
  }

  /**
   * TO DO - Make re-usable
   * 
   * @param type $db
   * @param type $data
   * @return type
   * @throws Exception
   */
  public function createImage($db, $data)
  {
    $query = $db->getQuery(true);

    $query->insert('#__property_images_library')
            ->columns(
                    array(
                        $db->quoteName('version_id'), $db->quoteName('unit_id'),
                        $db->quoteName('url'), $db->quoteName('url_thumb'), 
                        $db->quoteName('caption'), $db->quoteName('ordering')
                    )
            )
            ->values(implode(',', $data));

    $db->setQuery($query);

    try
    {
      $db->execute();
    }
    catch (RuntimeException $e)
    {
      throw new Exception($e->getMessage());
    }

    return $db->insertid();
  }

  public function email(Exception $e)
  {
    $mail = JFactory::getMailer();
    $mail->addRecipient('adamrifat@frenchconnections.co.uk');
    $mail->setBody($e->getMessage() . '<br />' . $e->getTraceAsString() . '<br />' . $e->getLine());
    $mail->setSubject($e->getMessage());
    $mail->isHtml(true);
    $mail->send(); 
  }

  public function getUnit()
  {
    return $this->unit;
  }

  public function getPropertyTypes()
  {
    return $this->property_types;
  }

  public function getLocationTypes()
  {
    return $this->location_types;
  }

}

