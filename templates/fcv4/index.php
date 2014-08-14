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

$menu = $app->getMenu();

$siteHome = ($menu->getActive() == $menu->getDefault('en-GB')) ? 'home' : 'sub';

// Remove all JS from the initial page load...
$this->_scripts = array();
$this->_script = array();
?>

<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
  <head>  
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  <jdoc:include type="head" />

  <?php $doc->addStyleSheet('media/fc/assets/css/styles.css'); ?>
</head>
<body class="<?php echo $siteHome; ?>-page <?php echo $option . " view-" . $view . " itemid-" . $itemid . ""; ?>">
  <header role="banner"> 

    <div class="container"> 
      <?php if ($this->countModules('position-0')) : ?>
        <div class="banner-container">
          <jdoc:include type="modules" name="position-0" style="none" />
        </div>
      <?php endif; ?>
      <div class="navbar-header">
        <!-- Brand and toggle get grouped for better mobile display -->
         <a class="navbar-brand" href="<?php echo $this->baseurl; ?>">
          <img src="/images/general/logo-4.png" alt="' . $sitename . '" />
        </a>      
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#fc-navbar-collapse-1">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>   
        </button> 

      </div>
      <?php if ($this->countModules('position-1')) : ?>  
        <nav class="collapse navbar navbar-default fc-navbar-collapse" role="navigation">
          <jdoc:include type="modules" name="position-1" style="none" />
          <?php if ($menu->getActive() != $menu->getDefault('en-GB')) : ?>
            <form class="navbar-form navbar-right" role="search">
              <div class="form-group">
                <input type="text" class="form-control" placeholder="Search">
              </div>
              <button type="submit" class="btn btn-default">Submit</button>
            </form>
          <?php endif; ?>
        </nav>
      <?php endif; ?>  
    </div>   
  </header>  
  <div class="container">
    <!-- Begin Content -->
    <jdoc:include type="message" /> 
    <?php if ($this->countModules('position-11')) : ?>
      <jdoc:include type="modules" name="position-11" style="no" />
    <?php endif; ?>
    <div class="row">
      <main id="content" role="main">
    </div>
    <jdoc:include type="component" />
  </main>  
  <?php if ($this->countModules('position-12') && $this->countModules('position-13')) : ?>
    <div class="row">
      <div class="col-lg-9">
        <jdoc:include type="modules" name="position-12" style="none" />
        <hr />
        <div class="row">
          <div class="col-lg-6">
            <jdoc:include type="modules" name="position-14" style="none" />
          </div>
          <div class="col-lg-6">
            <jdoc:include type="modules" name="position-15" style="none" />
          </div>
        </div>
      </div>
      <div class="col-lg-3">
        <jdoc:include type="modules" name="position-13" style="none" />
      </div>       
    </div>
  <?php endif; ?>
  <!-- End Content -->
</div>

<jdoc:include type="modules" name="debug" style="html5" />
</body>
</html>
