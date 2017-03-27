<?php

/**
 * @package    Joomla.Cli
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// Initialize Joomla framework
        const _JEXEC = 1;

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php')) {
    require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', dirname(__DIR__));
    require_once JPATH_BASE . '/includes/defines.php';
}

// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

// Import the configuration.
require_once JPATH_CONFIGURATION . '/configuration.php';

// Require the subscriber class
require_once (JPATH_LIBRARIES . '/createsend/csrest_subscribers.php');

/**
 * Cron job to trash expired cache data.
 *
 * @since  2.5
 */
class SyncOwnersWithCampaignMonitor extends JApplicationCli

{
	/**
	 * Entry point for the script
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function doExecute()
	{
    // Get the params so we can find users to ignore
    // Put this into a helper method...probably
    $params = JComponentHelper::getParams('com_fcadmin');

    $ignore = $params->get('users_to_ignore', '');

    $users = explode(',', $ignore);

    foreach ($users as $username)
    {
      $id = JFactory::getUser($username)->id;

      if ($id)
      {
        $ignore_users[] = $id;
      }
    }

    // The API KEY for this list
    $api_key = 'c151240434d33ec21ed6752290e1fe2b';
    $list_id = '649be7aac26ec1449852085855c211ae';

    // Get the list of props taking into account the affiliates
    $props = $this->_getProps($ignore_users);

    $batches = array_chunk($props, 1000);

    $auth = array(
      'api_key' => $api_key);

    // Loop over in batches of 1000 and update the list
    foreach ($batches as $batch => $data)
    {

      $dataArr = array();

      foreach($data as $key => $value)
      {
        $subscriber = array();
        $subscriber['EmailAddress'] = $value->email;
        $subscriber['Name'] = $value->firstname;
        $subscriber['CustomFields'] = array();
        $customFields = array();
        $customField['Key'] = 'STATUS';
        $customField['Value'] = $value->status;

        $subscriber['CustomFields'][] = $customField;

        $dataArr[] = $subscriber;
      }

      // Get the wrapper object
      $wrap = new CS_REST_Subscribers($list_id, $auth);

      $result = $wrap->import($dataArr, false);

      echo "Result of POST /api/v3.1/subscribers/{list id}/import.{format}\n<br />";

      if($result->was_successful()) {
        echo "Subscribed with results <pre>";
        var_dump($result->response);
      } else {
        echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
        var_dump($result->response);
        echo '</pre>';

        if($result->response->ResultData->TotalExistingSubscribers > 0) {
          echo 'Updated '.$result->response->ResultData->TotalExistingSubscribers.' existing subscribers in the list';
        } else if($result->response->ResultData->TotalNewSubscribers > 0) {
          echo 'Added '.$result->response->ResultData->TotalNewSubscribers.' to the list';
        } else if(count($result->response->ResultData->DuplicateEmailsInSubmission) > 0) {
          echo $result->response->ResultData->DuplicateEmailsInSubmission.' were duplicated in the provided array.';
        }

        echo 'The following emails failed to import correctly.<pre>';
        var_dump($result->response->ResultData->FailureDetails);
      }
      echo '</pre>';

    }

	}

  /*
   * Get a list of properties that have expired
   *
   */

  private function _getProps($ignore_users = array())
  {

    $this->out('Getting props...');

    $db = JFactory::getDBO();
    /**
     * Get the date
     */
    $date = JFactory::getDate();

    $query = $db->getQuery(true);

    $query->select('
      DISTINCT c.firstname,
      b.email,
      case
        WHEN datediff(a.expiry_date, now()) >= 0 THEN \'ACTIVE\'
        ELSE \'LAPSED\'
      END as status'
    );

    // Select from the property table
    $query->from('#__property a');

    // Join the users tables
    $query->leftJoin('#__users b ON a.created_by = b.id');
    $query->leftJoin('#__user_profile_fc c ON c.user_id = b.id');

    // Live properties, that are published
    //query->where('expiry_date <= ' . $db->quote($date->calendar('Y-m-d')));
    $query->where('a.published = 1');
    $query->where('a.created_by not in (' . implode(',', $ignore_users) . ')');

    $db->setQuery($query);

    try {
      $rows = $db->loadObjectList();
    }
    catch (Exception $e) {
      $this->out('Problem getting props...');
      return false;
    }

    return $rows;
  }
}

JApplicationCli::getInstance('SyncOwnersWithCampaignMonitor')->execute();
