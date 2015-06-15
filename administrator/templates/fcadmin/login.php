<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.isis
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$lang = JFactory::getLanguage();

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');
JHtml::_('bootstrap.tooltip');

// Add Stylesheets
//$doc->addStyleSheet('templates/' . $this->template . '/css/template.css');
// Load optional RTL Bootstrap CSS
JHtml::_('bootstrap.loadCss', false, $this->direction);

// Load specific language related CSS
$file = 'language/' . $lang->getTag() . '/' . $lang->getTag() . '.css';
if (is_file($file))
{
  $doc->addStyleSheet($file);
}

// Detecting Active Variables
$option = $app->input->getCmd('option', '');
$view = $app->input->getCmd('view', '');
$layout = $app->input->getCmd('layout', '');
$task = $app->input->getCmd('task', '');
$itemid = $app->input->getCmd('Itemid', '');
$sitename = $app->getCfg('sitename');

$cookieName = md5('autologin');
$uri = JURI::getInstance();

// If Cookie is set then we know this owner can be autologged in
$cookie = $app->input->cookie->get($cookieName);

// Check if debug is on
$config = JFactory::getConfig();
$debug = (boolean) $config->get('debug');
?>

<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
  <head>  
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  <jdoc:include type="head" />
  <?php $doc->addStyleSheet('//' . $uri->getHost() . '/media/fc/assets/css/styles.css'); ?>
  <?php if ($cookie) : ?> 
    <script type="text/javascript">
      jQuery(function($) {
        document.forms[0].submit();
      });
    </script>
  <?php endif; ?>
  <script type="text/javascript">
    jQuery(function($) {
      $("#form-login input[name='username']").focus();
    });
  </script>
</head>

<body class="site <?php echo $option . " view-" . $view . " layout-" . $layout . " task-" . $task . " itemid-" . $itemid . " "; ?>">
  <header class="" role="banner"> 
    <div class="container"> 
      <?php if ($this->countModules('position-1')) : ?>
        <div class="banner-container">
          <jdoc:include type="modules" name="position-1" style="xhtml" />
        </div>
      <?php endif; ?>
      <!-- Take brand out of navbar as we're not really using the BS default nav correctly -->
      <a class="navbar-brand" href="<?php echo $this->baseurl; ?>">
        <img src="<?php echo '//' . $uri->getHost() . '/images/general/logo-4.png' ?>" alt="<?php echo $sitename ?>" />
      </a> 
    </div>

  </header> 
  <p class="lead text-center <?php echo (!$cookie) ? "hide" : '' ?>">
    One moment while we create your account...<br /><br />
    <img src="/images/general/ajax-loader.gif" alt="Please wait..." />      
  </p>

  <div id="content" <?php echo ($cookie) ? "class='hide'" : '' ?>>
    <!-- Begin Content -->
    <div id="element-box" class="">
      <div class="container">
        <jdoc:include type="message" />
      </div>
      <jdoc:include type="component" />
    </div>
    <noscript>
    <?php echo JText::_('JGLOBAL_WARNJAVASCRIPT') ?>
    </noscript>
    <!-- End Content -->
  </div>
</div>
<div class="container">
  <p>
    &copy; <?php echo date('Y'); ?> <?php echo $sitename; ?>
  </p>
</div>
<jdoc:include type="modules" name="debug" style="none" />
</body>
</html>
