<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

// Getting params from template
$params = JFactory::getApplication()->getTemplate(true)->params;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$this->language = $doc->language;
$this->direction = $doc->direction;

// Detecting Active Variables
$option = $app->input->getCmd('option', '');
$view = $app->input->getCmd('view', '');
$layout = $app->input->getCmd('layout', '');
$task = $app->input->getCmd('task', '');
$itemid = $app->input->getCmd('Itemid', '');
$sitename = $app->getCfg('sitename');

if ($task == "edit" || $layout == "form")
{
  $fullWidth = 1;
}
else
{
  $fullWidth = 0;
}

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');

// Add current user information
$user = JFactory::getUser();


// Logo file
if ($params->get('logoFile'))
{
  $logo = JUri::root() . $params->get('logoFile');
}
else
{
  $logo = $this->baseurl . "/images/general/logo-4.png";
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title><?php echo $this->title; ?> <?php echo htmlspecialchars($this->error->getMessage()); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/template.css" type="text/css" />

      <?php
      $debug = JFactory::getConfig()->get('debug_lang');
      if ((defined('JDEBUG') && JDEBUG) || $debug)
      {
        ?>
        <link rel="stylesheet" href="<?php echo $this->baseurl ?>/media/cms/css/debug.css" type="text/css" />
        <?php
      }
      ?>
      <link rel="stylesheet" href="<?php echo $this->baseurl ?>/media/fc/assets/css/styles.css" type="text/css" />

      <link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />

  </head>

  <body class="site <?php
  echo $option
  . ' view-' . $view
  . ($layout ? ' layout-' . $layout : ' no-layout')
  . ($task ? ' task-' . $task : ' no-task')
  . ($itemid ? ' itemid-' . $itemid : '')
  . ($params->get('fluidContainer') ? ' fluid' : '');
  ?>">

    <!-- Body -->
    <div class="body">
      <div class="container">
        <!-- Header -->
        <div class="header">
          <div class="header-inner clearfix">
            <a class="navbar-brand pull-left" href="/">
              <img src="<?php echo $logo; ?>" alt="<?php echo $sitename; ?>" />
            </a>
            <div class="header-search pull-right">
              <?php
              // Display position-0 modules
              echo $doc->getBuffer('modules', 'position-0', array('style' => 'none'));
              ?>
            </div>
          </div>
        </div>
        <nav class="navbar navbar-default" role="navigation">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".fc-navbar-collapse">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>   
            </button> 
          </div>
          <div class="collapse navbar-collapse fc-navbar-collapse">
            <?php
            // Display position-1 modules
            echo $doc->getBuffer('modules', 'position-1', array('style' => 'none'));
            ?> 
          </div>
        </nav>       

        <!-- Banner -->
        <div class="banner">
          <?php echo $doc->getBuffer('modules', 'banner', array('style' => 'xhtml')); ?>
        </div>
        <div class="row-fluid">
          <div id="content" class="span12">
            <!-- Begin Content -->
            <h1 class="page-header"><?php echo JText::_('JERROR_LAYOUT_PAGE_NOT_FOUND'); ?></h1>
            <div class="well">
              <div class="row-fluid">
                <div class="span6">
                  <p><strong><?php echo JText::_('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST'); ?></strong></p>
                  <p><?php echo JText::_('JERROR_LAYOUT_NOT_ABLE_TO_VISIT'); ?></p>
                  <ul>
                    <li><?php echo JText::_('JERROR_LAYOUT_AN_OUT_OF_DATE_BOOKMARK_FAVOURITE'); ?></li>
                    <li><?php echo JText::_('JERROR_LAYOUT_MIS_TYPED_ADDRESS'); ?></li>
                    <li><?php echo JText::_('JERROR_LAYOUT_SEARCH_ENGINE_OUT_OF_DATE_LISTING'); ?></li>
                    <li><?php echo JText::_('JERROR_LAYOUT_YOU_HAVE_NO_ACCESS_TO_THIS_PAGE'); ?></li>
                  </ul>
                </div>
                <div class="span6">
                  <?php if (JModuleHelper::getModule('search')) : ?>
                    <p><strong><?php echo JText::_('JERROR_LAYOUT_SEARCH'); ?></strong></p>
                    <p><?php echo JText::_('JERROR_LAYOUT_SEARCH_PAGE'); ?></p>
                    <?php echo $doc->getBuffer('module', 'search'); ?>
                  <?php endif; ?>
                  <p><?php echo JText::_('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?></p>
                  <p><a href="/" class="btn"><i class="icon-home"></i> <?php echo JText::_('JERROR_LAYOUT_HOME_PAGE'); ?></a></p>
                </div>
              </div>
              <hr />
              <p><?php echo JText::_('JERROR_LAYOUT_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR'); ?></p>
              <blockquote>
                <?php echo $this->error->getCode(); ?></span> <?php echo $this->error->getMessage(); ?>
              </blockquote>
            </div>
            <!-- End Content -->
          </div>
        </div>
      </div>
    </div>
    <!-- Footer -->
    <div class="footer">
      <div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?>">
        <hr />
        <?php echo $doc->getBuffer('modules', 'footer', array('style' => 'none')); ?>

        <p class="pull-right"><a href="#top" id="back-top"><?php echo JText::_('TPL_PROTOSTAR_BACKTOTOP'); ?></a></p>
        <p>&copy; <?php echo $sitename; ?> <?php echo date('Y'); ?></p>
      </div>
    </div>
    <?php echo $doc->getBuffer('modules', 'debug', array('style' => 'none')); ?>
    </div>
  </body>
</html>
