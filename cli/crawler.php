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
        $crawler->setURL("www.frenchconnections.co.uk");
        $crawler->addContentTypeReceiveRule("#text/html#");
        
        $crawler->addURLFollowRule("#(accommodation)#");
        $crawler->addURLFilterRule("#(uri)# i");
        $crawler->addURLFilterRule("#(/en/)# i");
        $crawler->addURLFilterRule("#(jpg|jpeg|gif|png|bmp)$# i");
        $crawler->addURLFilterRule("#(css|js)$# i");
        $crawler->addURLFilterRule("#(/en/)# i");
        // match property_wiit_1/property_asdasd_22 type urls
        $crawler->addURLFilterRule("#(property.*?)/(property.*?)#");
        $crawler->addURLFilterRule("#(property.*?)/(property.*?)/(property.*?)#");
        $crawler->addURLFilterRule("#(my-account)#");
        $crawler->addURLFilterRule("#(forsale)#");
        $crawler->addURLFilterRule("#(analytics)#");
        $crawler->addURLFilterRule("#(listing)#");
        $crawler->addURLFilterRule("#(blog)#");
        $crawler->addURLFilterRule("#(forsale-listing)#");
        $crawler->addURLFilterRule("#(internal_)# i");
        $crawler->addURLFilterRule("#(external_)# i");
        $crawler->addURLFilterRule("#(suitability_)# i");
        $crawler->addURLFilterRule("#(activities_)# i");
        $crawler->addURLFilterRule("#(start)# i");
        $crawler->addURLFilterRule("#(kitchen_)# i");
        $crawler->addURLFilterRule("#(viewsite)# i");
        $crawler->addURLFilterRule("#(media)# i");
        $crawler->addURLFilterRule("#(href)# i");
        
        $crawler->goMultiProcessed(7, 1);
        $crawler->setRequestDelay(60/100);
        $crawler->setUrlCacheType(PHPCrawlerUrlCacheTypes::URLCACHE_SQLITE);
        $crawler->obeyRobotsTxt(true);
        
        $crawler->go();
    }
}

JApplicationCli::getInstance('CrawlerCron')->execute();
































