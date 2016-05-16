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

Class Sitemap extends JApplicationCli
{

    // cUrl handler to ping the Sitemap submission URLs for Search Engines…
    public function myCurl($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpCode;
    }

    function return_code_check($pingedURL, $returnedCode)
    {

        $to = "adamrifat@frenchconnections.co.uk";
        $subject = "Sitemap ping fail: " . $pingedURL;
        $message = "Error code " . $returnedCode . ". Go check it out!";
        $headers = "From: no-reply@frenchconnections.co.uk";

        if ($returnedCode != "200")
        {
            mail($to, $subject, $message, $headers);
        }
    }
    
    
    /*
     * Get a list of locations
     * 
     */
    protected function _getLocations($level = 4)
    {

        $db = JFactory::getDBO();

        $query = $db->getQuery(true);

        $query->select('a.*');

        // Select from the property table 
        $query->from('#__classifications a');
        $query->where('level <=' . $level);
        $query->where('a.parent_id > 0');
        $db->setQuery($query);

        try
        {
            $rows = $db->loadObjectList();
        }
        catch (Exception $e)
        {
            $this->out('Problem getting locations...');
            return false;
        }

        return $rows;
    }

}
