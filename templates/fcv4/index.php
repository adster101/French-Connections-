<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

// Load template settings
$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$option = $app->input->getCmd('option', '');
$view = $app->input->getCmd('view', '');
$itemid = $app->input->getCmd('Itemid', '');
$sitename = $app->getCfg('sitename');
$listing = false;

$menu = $app->getMenu();
$active = $menu->getActive();
$siteHome = ($active == $menu->getDefault('en-GB')) ? 'home' : 'sub';
if ($active)
{
  $listing = ($active->component == 'com_accommodation') ? true : false;
}
// Remove all JS from the initial page load...
$this->_scripts = array();
$this->_script = array();

// Adjusting content width
if ($this->countModules('position-7'))
{
  $span = "col-lg-8 col-md-8 col-sm-8 col-xs-12";
}
else
{
  $span = "col-lg-12 col-md-12 col-sm-12 col-xs-12";
}
?>

<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
  <head>  
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  <jdoc:include type="head" />
  <?php $doc->addStyleSheet('//www.frenchconnections.co.uk/media/fc/assets/css/styles.css'); ?>
  <?php $doc->addScript('//www.frenchconnections.co.uk/media/fc/assets/js/scripts.min.js', 'text/javascript', false, true); ?>
</head>
<body class="<?php echo $siteHome; ?>-page <?php echo $option . " view-" . $view . " itemid-" . $itemid . ""; ?>" data-spy="scroll" data-target="navbar-property-navigator">
  <header class="" role="banner"> 
    <div class="container"> 
      <?php if ($this->countModules('position-0')) : ?>
        <div class="banner-container">
          <jdoc:include type="modules" name="position-0" style="none" />
        </div>
      <?php endif; ?>
      <!-- Take brand out of navbar as we're not really using the BS default nav correctly -->
      <a class="navbar-brand" href="<?php echo $this->baseurl; ?>">
        <img src="//www.frenchconnections.co.uk/images/general/logo-4.png" alt="' . $sitename . '" />
      </a> 
    </div>
    <div class="container">
      <nav class="navbar navbar-default" role="navigation">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".fc-navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>   
          </button> 
        </div>
        <?php if ($this->countModules('position-1')) : ?>  
          <div class="collapse navbar-collapse fc-navbar-collapse">
            <jdoc:include type="modules" name="position-1" style="none" />
          </div>
        <?php endif; ?>  
      </nav>       
    </div>
  </header>  

  <div class="container">
    <jdoc:include type="message" /> 
    <!-- Begin Content -->
    <?php if ($this->countModules('position-11')) : ?>
      <jdoc:include type="modules" name="position-11" style="no" />
    <?php endif; ?>
    <jdoc:include type="modules" name="position-3" style="html5" />
  </div>
  <?php if (!$listing): ?>
    <div class="container">
    <?php endif; ?>
    <div class="row">
      <div class="<?php echo $span; ?>">
        <jdoc:include type="component" />
        <?php if ($this->countModules('position-2')): ?>
          <jdoc:include type="modules" name="position-2" style="xhtml" />
        <?php endif; ?>
      </div>
      <?php if ($this->countModules('position-7')): ?>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
          <jdoc:include type="modules" name="position-7" style="xhtml" />
        </div>
      <?php endif; ?>
    </div>
    <?php if (!$listing): ?>
    </div>
  <?php endif; ?>
  <?php if ($this->countModules('position-12') && $this->countModules('position-13')) : ?>
    <div class="container">
      <div class="row">
        <div class="col-lg-9 col-md-7">
          <jdoc:include type="modules" name="position-12" style="none" />
          <hr />
          <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
              <jdoc:include type="modules" name="position-14" style="none" />
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
              <jdoc:include type="modules" name="position-15" style="none" />
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <jdoc:include type="modules" name="position-10" style="html5" />
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-5">
          <jdoc:include type="modules" name="position-13" style="none" />
        </div>       
      </div>
    </div>
  <?php endif; ?>
  <footer>
    <div class="container">
      <jdoc:include type="modules" name="footer" style="" />
    </div>
  </footer>
  <!-- End Content -->
<jdoc:include type="modules" name="debug" style="html5" />
<script>(function() {
    var _fbq = window._fbq || (window._fbq = []);
    if (!_fbq.loaded) {
      var fbds = document.createElement('script');
      fbds.async = true;
      fbds.src = '//connect.facebook.net/en_US/fbds.js';
      var s = document.getElementsByTagName('script')[0];
      s.parentNode.insertBefore(fbds, s);
      _fbq.loaded = true;
    }
    _fbq.push(['addPixelId', '528120040655478']);
  })();
  window._fbq = window._fbq || [];
  window._fbq.push(['track', 'PixelInitialized', {}]);
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=528120040655478&amp;ev=PixelInitialized" /></noscript>

<script src="//platform.twitter.com/oct.js" type="text/javascript"></script>
<script type="text/javascript">
  twttr.conversion.trackPid('l526m');</script>
<noscript>
<img height="1" width="1" style="display:none;" alt="" src="https://analytics.twitter.com/i/adsct?txn_id=l526m&p_id=Twitter" />
<img height="1" width="1" style="display:none;" alt="" src="//t.co/i/adsct?txn_id=l526m&p_id=Twitter" /></noscript>
</body>
</html>
