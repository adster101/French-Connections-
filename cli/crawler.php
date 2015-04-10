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
if (file_exists(dirname(__DIR__) . '/defines.php'))
{
  require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
  define('JPATH_BASE', dirname(__DIR__));
  require_once JPATH_BASE . '/includes/defines.php';
}

// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

require_once JPATH_BASE . '/administrator/components/com_rental/models/property.php';

jimport('frenchconnections.cli.crawler');

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class CrawlerCron extends JApplicationCli
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
    $app = JFactory::getApplication('site');
    $from = $app->getCfg('mailfrom');
    $sender = $app->getCfg('fromname');
    $lang = JFactory::getLanguage();

    $lang->load('frenchconnections', JPATH_SITE . '/libraries/frenchconnections');

    $status = array();

    // Get the debug setting
    $debug = (bool) $app->getCfg('debug');
    define('DEBUG', $debug);

    $props = $this->_getProps();
    $model = JModelLegacy::getInstance('Property', 'RentalModel', $config = array('table_path' => JPATH_BASE . '/administrator/components/com_rental/tables/'));

    foreach ($props as $prop)
    {
      echo "Checking " . $prop->id . ' ' . $prop->website . "\n";

      $modified = JHtml::_('date', 'now', 'Y-m-d h:i:s');

      $crawler = new Crawler($prop->website);
      $crawler->crawl();

      $subject = ($crawler->found) ? JText::sprintf('COM_FRENCHCONNECTIONS_BACKLINK_FOUND_SUBJECT', $crawler->domain) : JText::sprintf('COM_FRENCHCONNECTIONS_BACKLINK_NOT_FOUND_SUBJECT', $crawler->domain);
      $body = ($crawler->found) ? JText::sprintf('COM_FRENCHCONNECTIONS_BACKLINK_FOUND_BODY', $crawler->page) : JText::sprintf('COM_FRENCHCONNECTIONS_BACKLINK_NOT_FOUND_BODY', $prop->firstname, $crawler->domain);
      $email = (DEBUG) ? 'adamrifat@frenchconnections.co.uk' : $prop->email;
      $data = array('id' => $prop->id, 'website_visible' => $crawler->found, 'subject' => $subject, 'body' => $body, 'modified' => $modified);

      // Update the property listing whic also does the notes, nice!
      if (!$model->save($data))
      {
        return false;
      }

      // If no link back is found then send an email to the owner...
      if (!$crawler->found)
      {
        JFactory::getMailer()->sendMail(
                $from, $sender, $email, $subject, $body);
      }

      unset($crawler);
    }

    // All websites checked, spit out a CSV file?
  }

  private function _getProps()
  {


    $db = JFactory::getDBO();
    /**
     * Get the date now
     */
    $date = JHtml::_('date', 'now', 'Y-m-d');

    $query = $db->getQuery(true);

    $query->select('
      DISTINCT a.id,
      a.expiry_date, 
      b.website,
      c.email,
      d.firstname'
    );

    $query->from('#__property a');
    $query->leftJoin($db->quoteName('#__property_versions', 'b') . ' on a.id = b.property_id');
    $query->leftJoin($db->quoteName('#__users', 'c') . ' on c.id = a.created_by');
    $query->leftJoin($db->quoteName('#__user_profile_fc', 'd') . ' on d.user_id = c.id');
    $query->where('b.review = 0');
    $query->where('b.website <> \'\'');
    $query->where($db->quoteName('expiry_date') . ' >= ' . $db->quote($date));
    $query->order('expiry_date');

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

JApplicationCli::getInstance('CrawlerCron')->execute();
