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
$option = $app->input->getCmd('option', '');
$view = $app->input->getCmd('view', '');
$itemid = $app->input->getCmd('Itemid', '');
$sitename = $app->getCfg('sitename');
$listing = false;
$URI = JURI::getInstance();
$menu = $app->getMenu();
$active = $menu->getActive();
$siteHome = ($active == $menu->getDefault('en-GB')) ? 'home' : 'sub';

// Header variable to A/B test a new header
$header = $app->input->get('header', 'default', 'string');

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
} else
{
    $span = "col-lg-12 col-md-12 col-sm-12 col-xs-12";
}
?>

<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1;">
  <jdoc:include type="head" />
  <meta name="google-site-verification" content="gxNKICR23M3sV86ZSmOoGcFZCNH-AvkUI1MTTW3nau4" />
  <style><?php include_once JPATH_SITE . '/media/fc/assets/css/critical.php'; //Inline the 'critical' portions of CSS ?></style>
</head>
<body class="<?php echo $siteHome; ?>-page <?php echo $option . " view-" . $view . " itemid-" . $itemid . ""; ?>" data-spy="scroll" data-target="navbar-property-navigator">

  <!-- Start header -->
  <?php include_once JPATH_THEMES . '/' . $this->template . '/inc/' . $header . '.php'; ?>
  <!-- Finish header -->

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
        <?php if ($this->countModules('before')): ?>
            <jdoc:include type="modules" name="before" style="xhtml" />
        <?php endif; ?>
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
  <div class="container">
    <footer class="clearfix">
      <div class="col-lg-3 col-md-3 col-sm-3">
        <jdoc:include type="modules" name="footer-links" />
      </div>
      <div class="col-lg-9 col-md-9 col-sm-9 ">
        <jdoc:include type="modules" name="footer" />
      </div>
    </footer>
  </div>
  <!-- End Content -->
<jdoc:include type="modules" name="debug" style="html5" />

<noscript>
  <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1060605103975489&ev=PageView&noscript=1" /><img height="1" width="1" style="display:none;" alt="" src="https://analytics.twitter.com/i/adsct?txn_id=l526m&p_id=Twitter" />
<img height="1" width="1" style="display:none;" alt="" src="//t.co/i/adsct?txn_id=l526m&p_id=Twitter" />
</noscript>

<?php
// Include styles - async loads the main CSS file
include_once JPATH_THEMES . '/' . $this->template . '/inc/styles.php';

// Include JS
include_once JPATH_THEMES . '/' . $this->template . '/inc/assets.php';
?>
<script>
  (function (i, s, o, g, r, a, m) {
    i['GoogleAnalyticsObject'] = r;
    i[r] = i[r] || function () {
      (i[r].q = i[r].q || []).push(arguments)
    }, i[r].l = 1 * new Date();
    a = s.createElement(o),
            m = s.getElementsByTagName(o)[0];
    a.async = 1;
    a.src = g;
    m.parentNode.insertBefore(a, m)
  })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

  ga('create', 'UA-2087119-1', 'auto');
  ga('require', 'displayfeatures');
  ga('send', 'pageview');
</script>
</body>
</html>
