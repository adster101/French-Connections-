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

        $crawler = new Crawler();
        $crawler->setURL("dev.frenchconnections.co.uk");
        $crawler->addContentTypeReceiveRule("#text/html#");
        $crawler->addURLFilterRule("#(jpg|jpeg|gif|png|bmp)$# i");
        $crawler->addURLFilterRule("#(css|js)$# i");
        $crawler->addURLFilterRule("#(+\'uri\'+)$# i");
        $crawler->goMultiProcessed(10, 1);
        $crawler->setUrlCacheType(PHPCrawlerUrlCacheTypes::URLCACHE_SQLITE);
        $crawler->obeyRobotsTxt(true);
        
        $crawler->go();
    }

}

JApplicationCli::getInstance('CrawlerCron')->execute();
