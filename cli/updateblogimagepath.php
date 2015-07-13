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

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class UpdateBlogImagePathCron extends JApplicationCli
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

    $articles = $this->getArticles();
    
    $db = JFactory::getDBO();
    
    $query = $db->getQuery(true);
    
    

    foreach ($articles as $article)
    {

      if ((strpos($article->introtext, '/blog/') > 0) && (strpos($article->introtext, '/images/') === false))
      {

        $article->introtext = str_replace('/blog/', '/images/blog/', $article->introtext);
          
        
        $query->update('#__content')
                ->set('introtext = ' . $db->quote($article->introtext))
                ->where('id = ' . $article->id);
        $db->setQuery($query);
        
        $db->execute();

        $query->clear();
               
      }
    }
  }

  public function getArticles()
  {

    $db = JFactory::getDBO();

    $query = $db->getQuery(true);

    $query->select('*')
            ->from('#__content')
            ->where('catid in ( 149,145,144,143,142,140,139)');

    $db->setQuery($query);

    $articles = $db->loadObjectList($key = 'id');

    return $articles;
  }

}

JApplicationCli::getInstance('UpdateBlogImagePathCron')->execute();
