<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Methods supporting a list of tracks.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 * @since       1.6
 */
class FcadminModelUserCards extends JModelList
{

  /**
   * Build an SQL query to load the list data.
   *
   * @return  JDatabaseQuery
   *
   * @since   1.6
   */
  protected function getListQuery()
  {

    // Create a new query object.
    $db = $this->getDbo();
    $query = $db->getQuery(true);

    // Select the required fields from the table.
    $query->select('
      a.surname as aca_contact_lastname,
      a.firstname as aca_contact_firstname,
      a.user_id as aca_acc_id,
      "N" as status,
      null as CurrencyCode,
      a.address1 as Addr1_1,
      a.address2 as Addr1_2,
      a.address3 as Addr1_3,
      "" as Addr1_4, 
      a.city as aca_town,
      a.region as aca_county,
      a.postal_code as postcode,
      a.country as country,
      a.phone_1 as aca_phone_1,
      a.phone_2 as aca_phone_2,
      a.phone_3 as aca_phone_3,
      "" as aca_fax,
      b.email as aca_email,
      "" as website,
      concat(a.firstname," ", a.surname) as ContactName,
      "" as Salutation,
      "" as Addr2_1,
      "" as Addr2_2,
      "" as Addr2_3,
      "" as Addr2_4,
      "" as Addr2_town,
      "" as Addr2_county,
      "" as Addr2_postcode,
      "" as Addr2_country,
      "" as Addr2_phone_1,
      "" as Addr2_phone_2,
      "" as Addr2_phone_3,
      "" as Addr2_fax,
      "" as Addr2_email,
      "" as Addr2_website,
      "" as Addr2_ContactName,
      "" as Addr2_Salutation,
      "" as Addr3_1,
      "" as Addr3_2,
      "" as Addr3_3,
      "" as Addr3_4,
      "" as Addr3_town,
      "" as Addr3_county,
      "" as Addr3_postcode,
      "" as Addr3_country,
      "" as Addr3_phone_1,
      "" as Addr3_phone_2,
      "" as Addr3_phone_3,
      "" as Addr3_fax,
      "" as Addr3_email,
      "" as Addr3_website,
      "" as Addr3_ContactName,
      "" as Addr3_Salutation,
      "" as Addr4_1,
      "" as Addr4_2,
      "" as Addr4_3,
      "" as Addr4_4,
      "" as Addr4_town,
      "" as Addr4_county,
      "" as Addr4_postcode,
      "" as Addr4_country,
      "" as Addr4_phone_1,
      "" as Addr4_phone_2,
      "" as Addr4_phone_3,
      "" as Addr4_fax,
      "" as Addr4_email,
      "" as Addr4_website,
      "" as Addr4_ContactName,
      "" as Addr4_Salutation,
      "" as Addr5_1,
      "" as Addr5_2,
      "" as Addr5_3,
      "" as Addr5_4,
      "" as Addr5_town,
      "" as Addr5_county,
      "" as Addr5_postcode,
      "" as Addr5_country,
      "" as Addr5_phone_1,
      "" as Addr5_phone_2,
      "" as Addr5_phone_3,
      "" as Addr5_fax,
      "" as Addr5_email,
      "" as Addr5_website,
      "" as Addr5_ContactName,
      "" as Addr5_Salutation,
      "" AS Picture,
      "" AS Notes,
      "" AS identifiers,
      "" AS CustList1,
      "" AS CustList2,
      "" AS CustList3,
      b.username as CustField1,
      a.vat_status as VATcode,
      a.vat_number as VATRegNo,
      "Y" as UseCustomersVATCode      
    ');
    $query->from($db->quoteName('#__user_profile_fc') . 'a');
    $query->leftJoin($db->quoteName('#__users', 'b') . ' on a.user_id = b.id');

    return $query;
  }

  /**
   * Get the content
   *
   * @return  string    The content.
   *
   * @since   1.6
   */
  public function getContent()
  {
    if (!isset($this->content))
    {

      $items = $this->getItems();

      $this->content = '';

      foreach ($items[0] as $key => $value)
      {
        $this->content .= $key . "\t";
      }

      $this->content .= "\r\n";

      foreach ($items as $item)
      {
        $bits = JArrayHelper::fromObject($item);
        $this->content .= implode("\t", $bits) . "\r\n";
      }
    }

    return $this->content;
  }

  public function populateState($ordering = null, $direction = null)
  {

    parent::populateState($ordering, $direction);

    $this->setState('list.limit', 0);
  }

}