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

if ($task == "edit" || $layout == "form") {
  $fullWidth = 1;
} else {
  $fullWidth = 0;
}

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');
$doc->addScript('templates/' . $this->template . '/js/template.js');

// Add Stylesheets
$doc->addStyleSheet('templates/' . $this->template . '/css/template.css');

// Load optional RTL Bootstrap CSS
JHtml::_('bootstrap.loadCss', false, $this->direction);

// Add current user information
$user = JFactory::getUser();

// Adjusting content width
if ($this->countModules('position-7') && $this->countModules('position-8')) {
  $span = "span6";
} elseif ($this->countModules('position-7') && !$this->countModules('position-8')) {
  $span = "span9";
} elseif (!$this->countModules('position-7') && $this->countModules('position-8')) {
  $span = "span9";
} else {
  $span = "span12";
}

// Logo file or site title param
if ($this->params->get('logoFile')) {
  $logo = '<img src="' . JUri::root() . $this->params->get('logoFile') . '" alt="' . $sitename . '" />';
} elseif ($this->params->get('sitetitle')) {
  $logo = '<span class="site-title" title="' . $sitename . '">' . htmlspecialchars($this->params->get('sitetitle')) . '</span>';
} else {
  $logo = '<span class="site-title" title="' . $sitename . '">' . $sitename . '</span>';
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <jdoc:include type="head" />
    <?php
// Use of Google Font
    if ($this->params->get('googleFont')) {
      ?>
      <link href='//fonts.googleapis.com/css?family=<?php echo $this->params->get('googleFontName'); ?>' rel='stylesheet' type='text/css' />
      <style type="text/css">
        h1,h2,h3,h4,h5,h6,.site-title{
          font-family: '<?php echo str_replace('+', ' ', $this->params->get('googleFontName')); ?>', sans-serif;
        }
      </style>
      <?php
    }
    ?>
    <?php
// Template color
    if ($this->params->get('templateBackgroundColor')) {
      ?>
      <style type="text/css">
        body.site
        {
          background-color: <?php echo $this->params->get('templateBackgroundColor'); ?>
        }
      </style>
      <?php
    }
    ?>
    <!--[if lt IE 9]>
      <script src="<?php echo $this->baseurl ?>/media/jui/js/html5.js"></script>
    <![endif]-->
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
      <div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?>">
        <!-- Header -->
        <header class="header" role="banner">
          <div class="row-fluid">
            <div class="span5">
              <a class="brand pull-left" href="<?php echo $this->baseurl; ?>">
                <?php echo $logo; ?> <?php
                if ($this->params->get('sitedescription')) {
                  echo '<div class="site-description">' . htmlspecialchars($this->params->get('sitedescription')) . '</div>';
                }
                ?>
              </a>
            </div>

            <div class="span7 header-search">
              <jdoc:include type="modules" name="position-0" style="none" />
            </div>
          </div>
        </header>
        <?php if ($this->countModules('position-1')) : ?>
          <nav class="navigation" role="navigation">
            <jdoc:include type="modules" name="position-1" style="nav" />
          </nav>
        <?php endif; ?>
        <jdoc:include type="modules" name="banner" style="xhtml" />
        <?php if ($this->countModules('position-11') && $this->countModules('position-12')) : ?>
          <div class="row-fluid">
            <div class="span6">
              <jdoc:include type="modules" name="position-11" style="none" />
            </div>
            <div class="span6">
              <jdoc:include type="modules" name="position-12" style="none" />
            </div>       
          </div>
        <?php elseif ($this->countModules('position-11') && !$this->countModules('position-12')) : ?>
          <div class="span12">
            <jdoc:include type="modules" name="position-11" style="html5" />
          </div>
        <?php elseif (!$this->countModules('position-11') && $this->countModules('position-12')) : ?>
          <div class="span12">
            <jdoc:include type="modules" name="position-12" style="html5" />
          </div>
        <?php endif; ?>
        <div class="row-fluid">
          <?php if ($this->countModules('position-8')) : ?>
            <!-- Begin Sidebar -->
            <div id="sidebar" class="span3">
              <div class="sidebar-nav">
                <jdoc:include type="modules" name="position-8" style="xhtml" />
              </div>
            </div>
            <!-- End Sidebar -->
          <?php endif; ?>
          <main id="content" role="main" class="<?php echo $span; ?>">
            <!-- Begin Content -->
            <jdoc:include type="modules" name="position-3" style="xhtml" />
            <jdoc:include type="message" />

            <jdoc:include type="component" />

            <?php if ($this->countModules('position-2') && $this->countModules('position-13')) : ?>
              <div class="row-fluid">
                <div class="span6">
                  <jdoc:include type="modules" name="position-2" style="none" />
                </div>
                <div class="span6">
                  <jdoc:include type="modules" name="position-13" style="none" />
                </div>       
              </div>
            <?php elseif ($this->countModules('position-2') && !$this->countModules('position-13')) : ?>
              <div class="span12">
                <jdoc:include type="modules" name="position-2" style="html5" />
              </div>
            <?php elseif (!$this->countModules('position-2') && $this->countModules('position-13')) : ?>
              <div class="span12">
                <jdoc:include type="modules" name="position-13" style="html5" />
              </div>
            <?php endif; ?>

            <!-- End Content -->
          </main>
          <?php if ($this->countModules('position-7')) : ?>
            <div id="aside" class="span3">
              <!-- Begin Right Sidebar -->
              <jdoc:include type="modules" name="position-7" style="well" />
              <!-- End Right Sidebar -->
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <!-- Footer -->
    <footer class="footer" role="contentinfo">
      <div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?>">
        <hr />
        <jdoc:include type="modules" name="footer" style="none" />
        <p class="pull-right"><a href="#top" id="back-top"><?php echo JText::_('TPL_PROTOSTAR_BACKTOTOP'); ?></a></p>
        <p>&copy; <?php echo $sitename; ?> <?php echo date('Y'); ?></p>
      </div>
    </footer>
    <jdoc:include type="modules" name="debug" style="none" />
  </body>
</html>
