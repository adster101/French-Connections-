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
  <style>.btn,.input-group-addon,.social-icon,img{vertical-align:middle}.btn,button{cursor:pointer}.breadcrumb,.list-unstyled,.nav{list-style:none}.clearfix:after,.container:after,.nav:after,.navbar-collapse:after,.navbar-header:after,.navbar:after,.panel-body:after,.row:after{clear:both}.btn,.input-group-addon,.view-search .search-result .rate-per{white-space:nowrap}header,nav{display:block}strong{font-weight:700}.img-responsive{display:block;width:100%\9;max-width:100%;height:auto}h1,h3,h4,h5{font-weight:500;line-height:1.1;color:inherit;font-family:"District Thin";text-transform:uppercase}h3 small{font-weight:400;line-height:1;color:#d6dae3;font-size:65%}h4,h5{margin-top:10px;margin-bottom:10px}h5{font-size:14px}.lead{margin-bottom:20px;font-size:16px;font-weight:300;line-height:1.4}.view-search .search-result .rates>.lead,label{font-weight:700}@media (min-width:768px){.lead{font-size:21px}}.small,small{font-size:85%}ol,ul{margin-top:0;margin-bottom:10px}.col-lg-12,.col-lg-3,.col-lg-9,.col-md-12,.col-md-3,.col-md-9,.col-sm-12,.col-sm-3,.col-sm-9,.col-xs-12{position:relative;min-height:1px;padding-left:15px;padding-right:15px}@media (min-width:768px){.col-sm-12,.col-sm-3,.col-sm-9{float:left}.col-sm-12{width:100%}.col-sm-9{width:75%}.col-sm-3{width:25%}}@media (min-width:992px){.col-md-12,.col-md-3,.col-md-9{float:left}.col-md-12{width:100%}.col-md-9{width:75%}.col-md-3{width:25%}}@media (min-width:1200px){.col-lg-12,.col-lg-3,.col-lg-9{float:left}.col-lg-12{width:100%}.col-lg-9{width:75%}.col-lg-3{width:25%}}.panel-heading{padding:10px 15px;border-bottom:1px solid transparent;border-top-right-radius:3px;border-top-left-radius:3px}.panel-default{border-color:#ddd}.panel-default>.panel-heading{color:#8592ac;background-color:#f5f5f5;border-color:#ddd}.btn-default{color:#333;background-color:#fff;border-color:#ccc}.btn-sm{padding:5px 10px;font-size:12px;line-height:1.5;border-radius:3px}.nav>li>a>img{max-width:none}.view-search #map-search-tab>li>a>img.map,label{max-width:100%}.nav-stacked>li{float:none}.nav-stacked>li+li{margin-top:2px;margin-left:0}.breadcrumb{padding:8px 15px;background-color:transparent;border-radius:4px;margin-bottom:10px}.breadcrumb>li{display:inline-block}.breadcrumb>li+li:before{content:"\00a0/\00a0";padding:0 5px;color:#ccc}.breadcrumb>.active{color:#d6dae3}.view-search .search-result h3>small,body{color:#525f79}.clearfix:after,.clearfix:before,.container:after,.container:before,.nav:after,.nav:before,.navbar-collapse:after,.navbar-collapse:before,.navbar-header:after,.navbar-header:before,.navbar:after,.navbar:before,.panel-body:after,.panel-body:before,.row:after,.row:before{content:" ";display:table}.pull-right{float:right!important}.pull-left{float:left!important}.visible-lg-block,.visible-md-block,.visible-sm-block,.visible-sm-inline-block,.visible-xs-inline-block{display:none!important}@media (max-width:767px){.visible-xs-inline-block{display:inline-block!important}}@media (min-width:768px) and (max-width:991px){.visible-sm-block{display:block!important}.visible-sm-inline-block{display:inline-block!important}}@media (min-width:992px) and (max-width:1199px){.visible-md-block{display:block!important}}@font-face{font-family:'Glyphicons Halflings';src:url(/media/fc/fonts/glyphicons-halflings-regular.eot);src:url(/media/fc/fonts/glyphicons-halflings-regular.eot?#iefix) format('embedded-opentype'),url(/media/fc/fonts/glyphicons-halflings-regular.woff) format('woff'),url(/media/fc/fonts/glyphicons-halflings-regular.ttf) format('truetype'),url(/media/fc/fonts/glyphicons-halflings-regular.svg#glyphicons_halflingsregular) format('svg')}.glyphicon-heart:before{content:"\e005"}.glyphicon-list:before{content:"\e056"}.glyphicon-map-marker:before{content:"\e062"}.glyphicon-filter:before{content:"\e138"}.well.well-light-blue{background:#eaf3fe;border:1px solid #b9d7fc}@font-face{font-family:'District Thin';src:url(/media/fc/fonts/DistTh.eot);src:url(/media/fc/fonts/DistTh.eot?#iefix) format('embedded-opentype'),url(/media/fc/fonts/DistTh.woff) format('woff'),url(/media/fc/fonts/DistTh.ttf) format('truetype'),url(/media/fc/fonts/DistTh.svg#glyphicons_halflingsregular) format('svg')}.view-search h1.page-header{font-size:14px;margin-top:20px}@media (max-width:480px){.view-search h1.page-header{margin-top:0}}@media (min-width:768px){.view-search h1.page-header{font-size:18px}}@media (min-width:992px){.view-search h1.page-header{font-size:24px}}@media (min-width:1200px){.visible-lg-block{display:block!important}.view-search h1.page-header{font-size:30px}}.search-field{display:inline-block}.view-search #map-search-tab>li>a[href='#mapsearch']{padding:0}.view-search select.sort-order{height:inherit;border:none;box-shadow:none;padding:0 2px}.view-search .search-results{margin-top:20px}.view-search .search-results .search-result:nth-child(2n+1){background:#eaf3fe}.view-search .search-result{padding:10px;border-top:1px solid #d2e5fd}hr,img{border:0}.view-search .search-result h3{margin-top:0}.view-search .search-result .shortlist-button{float:right}html{font-family:sans-serif;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-size:10px;-webkit-tap-highlight-color:transparent}header,nav,section{display:block}a{background:0 0;color:#031b5a;text-decoration:none}.form-control,.panel,body{background-color:#fff}h1{margin:.67em 0;font-size:36px}hr{-moz-box-sizing:content-box;box-sizing:content-box;height:0;margin-top:20px;margin-bottom:20px;border-top:1px solid #f2f5f1}button,input,select{color:inherit;font:inherit;margin:0;font-family:inherit;font-size:inherit;line-height:inherit}button{overflow:visible;-webkit-appearance:button}button,select{text-transform:none}.navbar,h1,h3,h4{text-transform:uppercase}button::-moz-focus-inner,input::-moz-focus-inner{border:0;padding:0}*,:after,:before{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}body{margin:0;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:14px;line-height:1.42857143}.carousel-inner>.item>img{display:block;width:100%\9;max-width:100%;height:auto}.sr-only{position:absolute;width:1px;height:1px;margin:-1px;padding:0;overflow:hidden;clip:rect(0,0,0,0);border:0}h1,h3,h4{margin-bottom:10px;font-weight:500;line-height:1.1;color:inherit}h1,h3{margin-top:20px}h4{margin-top:10px;font-size:18px}h3{font-size:24px}p{margin:0 0 10px}.small{font-size:85%}.btn,.form-control{font-size:14px;line-height:1.42857143;background-image:none}.page-header{padding-bottom:9px;margin:40px 0 20px;border-bottom:1px solid #d6dae3}ul{margin-top:0;margin-bottom:10px}.list-unstyled{padding-left:0}@media (min-width:768px){.view-search .search-result .rates,.view-search .search-result .shortlist-button,.view-search .search-result .view-property-button{text-align:right}.view-search .search-result .shortlist-button{float:none}}.row{margin-left:-15px;margin-right:-15px}.col-lg-12,.col-lg-3,.col-lg-6,.col-lg-9,.col-md-12,.col-md-5,.col-md-6,.col-md-7,.col-sm-12,.col-sm-6,.col-xs-12{position:relative;min-height:1px;padding-left:15px;padding-right:15px}.col-xs-12{float:left;width:100%}@media (min-width:768px){.col-sm-12,.col-sm-6{float:left}.col-sm-12{width:100%}.col-sm-6{width:50%}}@media (min-width:992px){.col-md-12,.col-md-5,.col-md-6,.col-md-7{float:left}.col-md-12{width:100%}.col-md-7{width:58.33333333%}.col-md-6{width:50%}.col-md-5{width:41.66666667%}}@media (min-width:1200px){.col-lg-12,.col-lg-3,.col-lg-6,.col-lg-9{float:left}.col-lg-12{width:100%}.col-lg-9{width:75%}.col-lg-6{width:50%}.col-lg-3{width:25%}}label{display:inline-block;margin-bottom:5px}.btn,.glyphicon{font-weight:400}.form-control{display:block;width:100%;height:34px;padding:6px 12px;color:#525f79;border:1px solid #ccc;border-radius:4px;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075);box-shadow:inset 0 1px 1px rgba(0,0,0,.075);-webkit-transition:border-color ease-in-out .15s,box-shadow ease-in-out .15s;-o-transition:border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition:border-color ease-in-out .15s,box-shadow ease-in-out .15s}.form-control::-moz-placeholder{color:#abb4c6;opacity:1}.form-control:-ms-input-placeholder{color:#abb4c6}.form-control::-webkit-input-placeholder{color:#abb4c6}.form-group{margin-bottom:15px}@media (min-width:768px){.form-inline .form-group{display:inline-block;margin-bottom:0;vertical-align:middle}.form-inline .form-control{display:inline-block;width:auto;vertical-align:middle}.form-inline .input-group{display:inline-table;vertical-align:middle}.form-inline .input-group .form-control,.form-inline .input-group .input-group-addon{width:auto}.form-inline .input-group>.form-control{width:100%}}.panel,.well{margin-bottom:20px}.panel{border:1px solid transparent;border-radius:4px;-webkit-box-shadow:0 1px 1px rgba(0,0,0,.05);box-shadow:0 1px 1px rgba(0,0,0,.05)}.panel-body{padding:15px}.well{min-height:20px;padding:19px;background-color:#f2f5f1;border:1px solid #dfe6dc;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.05);box-shadow:inset 0 1px 1px rgba(0,0,0,.05)}.btn,.nav{margin-bottom:0}.well-sm{padding:9px;border-radius:3px}.btn{display:inline-block;text-align:center;border:1px solid transparent;padding:6px 12px;border-radius:4px;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}.nav>li,.nav>li>a{display:block;position:relative}.btn-primary{color:#fff;background-color:#647cb6;border-color:#536dae}.nav{padding-left:0}.nav>li>a{padding:10px 15px}.nav-tabs{border-bottom:1px solid #ddd}.nav-tabs>li{float:left;margin-bottom:-1px}.nav-tabs>li>a{margin-right:2px;line-height:1.42857143;border:1px solid transparent;border-radius:4px 4px 0 0}.nav-tabs>li.active>a{color:#abb4c6;background-color:#fff;border:1px solid #ddd;border-bottom-color:transparent;cursor:default}.nav-pills>li{float:left}.nav-pills>li>a{border-radius:4px}.nav-pills>li+li{margin-left:2px}.tab-content>.tab-pane{display:none}.tab-content>.active{display:block}.navbar{position:relative;min-height:50px;margin-bottom:20px;border:1px solid transparent}.navbar-collapse{overflow-x:visible;padding-right:15px;padding-left:15px;border-top:1px solid transparent;box-shadow:inset 0 1px 0 rgba(255,255,255,.1);-webkit-overflow-scrolling:touch}@media (min-width:768px){.navbar{border-radius:4px}.navbar-header{float:left}.navbar-collapse{width:auto;border-top:0;box-shadow:none}.navbar-collapse.collapse{display:block!important;height:auto!important;padding-bottom:0;overflow:visible!important}}.navbar-brand{float:left;padding:15px;font-size:18px;line-height:20px}.navbar-toggle{position:relative;float:right;margin-right:15px;padding:9px 10px;margin-top:8px;margin-bottom:8px;background-color:transparent;background-image:none;border-radius:4px}.navbar-toggle .icon-bar{display:block;width:22px;height:2px;border-radius:1px}.navbar-toggle .icon-bar+.icon-bar{margin-top:4px}.navbar-nav{margin:7.5px -15px}.homepage-fp h1.page-header,.panel-home-page-search>.panel-body>h4,.well h3,.well h4{margin-top:0}.navbar-nav>li>a{padding-top:10px;padding-bottom:10px;line-height:20px}.carousel-inner>.item>img,.glyphicon{line-height:1}@media (min-width:768px){.navbar-toggle{display:none}.navbar-nav{float:left;margin:0}.navbar-nav>li{float:left}.navbar-nav>li>a{padding-top:15px;padding-bottom:15px}}.navbar-default{background-color:#f2f5f1;border-color:#e0e7de}.navbar-default .navbar-nav>li>a{color:#525f79}.navbar-default .navbar-nav>.active>a{color:#555;background-color:#e0e7de}.navbar-default .navbar-toggle{border-color:#ddd}.navbar-default .navbar-toggle .icon-bar{background-color:#888}.navbar-default .navbar-collapse{border-color:#e0e7de}.carousel{position:relative}.carousel-inner{position:relative;overflow:hidden;width:100%}.carousel-inner>.item{display:none;position:relative;-webkit-transition:.6s ease-in-out left;-o-transition:.6s ease-in-out left;transition:.6s ease-in-out left}.carousel-inner>.active{display:block;left:0}.carousel-caption{position:absolute;left:15%;right:15%;bottom:20px;z-index:10;padding-top:20px;padding-bottom:20px;color:#fff;text-align:center;text-shadow:0}@media screen and (min-width:768px){.carousel-caption{left:20%;right:20%;padding-bottom:30px}}.collapse{display:none}.container:after,.container:before,.nav:after,.nav:before,.navbar-collapse:after,.navbar-collapse:before,.navbar-header:after,.navbar-header:before,.navbar:after,.navbar:before,.panel-body:after,.panel-body:before,.row:after,.row:before{content:" ";display:table}@media (max-width:767px){.hidden-xs{display:none!important}}.input-group{position:relative;display:table;border-collapse:separate}.input-group .form-control{position:relative;z-index:2;float:left;width:100%;margin-bottom:0}.input-group .form-control,.input-group-addon{display:table-cell}.input-group-addon{width:1%;padding:6px 12px;font-size:14px;font-weight:400;line-height:1;color:#525f79;text-align:center;background-color:#f2f5f1;border:1px solid #ccc;border-radius:4px}.input-group .form-control:first-child{border-bottom-right-radius:0;border-top-right-radius:0}.input-group-addon:last-child{border-bottom-left-radius:0;border-top-left-radius:0;border-left:0}.input-group.date .input-group-addon i{cursor:pointer}@font-face{font-family:'Glyphicons Halflings';src:url(/media/fc/fonts/glyphicons-halflings-regular.eot);src:url(/media/fc/fonts/glyphicons-halflings-regular.eot?#iefix) format('embedded-opentype'),url(/media/fc/fonts/glyphicons-halflings-regular.woff) format('woff'),url(/media/fc/fonts/glyphicons-halflings-regular.ttf) format('truetype'),url(/media/fc/fonts/glyphicons-halflings-regular.svg#glyphicons_halflingsregular) format('svg')}.glyphicon{position:relative;top:1px;display:inline-block;font-family:'Glyphicons Halflings';font-style:normal;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.glyphicon-chevron-left:before{content:"\e079"}.glyphicon-chevron-right:before{content:"\e080"}.glyphicon-calendar:before{content:"\e109"}.container{margin-right:auto;margin-left:auto;padding-left:15px;padding-right:15px}@media (min-width:768px){.container{width:750px}.banner-container{float:right}}@media (min-width:992px){.container{width:970px}}@media (min-width:1200px){.container{width:1200px}}@media (min-width:1400px){.container{width:1400px}}@font-face{font-family:'District Thin';src:url(/media/fc/fonts/DistTh.eot);src:url(/media/fc/fonts/DistTh.eot?#iefix) format('embedded-opentype'),url(/media/fc/fonts/DistTh.woff) format('woff'),url(/media/fc/fonts/DistTh.ttf) format('truetype'),url(/media/fc/fonts/DistTh.svg#glyphicons_halflingsregular) format('svg')}@font-face{font-family:Social;src:url(/media/fc/fonts/Social.eot);src:url(/media/fc/fonts/Social.eot?#iefix) format('embedded-opentype'),url(/media/fc/fonts/Social.woff) format('woff'),url(/media/fc/fonts/Social.ttf) format('truetype'),url(/media/fc/fonts/Social.svg#glyphicons_halflingsregular) format('svg')}.social-icon{font-size:21px;margin-right:10px}.facebook:before{font-family:Social;content:"\e601";color:#647cb7}h1,h3,h4{font-family:"District Thin"}.well{border-radius:0}.navbar-toggle{border:1px solid #525f79}.navbar-toggle:before{content:"Menu";padding-left:30px;float:left;line-height:14px}.navbar-toggle .icon-bar{background-color:#525f79}.navbar-brand{height:auto}.navbar-nav>li>a{font-size:85%}.nav-login{float:right;margin-bottom:9px}.nav-login>li>a{text-align:center;background-color:#eaf3fe;color:#525f79}.nav-login>li>a:before{padding-right:3px}.nav-login>li>a.holidaymaker-login:before{content:"\e008";font-family:"Glyphicons Halflings"}.nav-login>li>a.owner-login:before{content:"\e033";font-family:"Glyphicons Halflings"}@media (max-width:768px){.nav-login{float:none;width:100%}.nav-login li:nth-of-type(1){padding-right:2px}.nav-login>li{float:none;display:table-cell;width:1%}.nav-login>li>a{margin-bottom:0}}.panel-home-page-search{background-color:transparent;border-radius:0;border:10px solid rgba(242,245,241,.75)}@media (min-width:768px){.panel-home-page-search .form-inline .search-control-date{width:90px}.panel-home-page-search .form-inline .search-control-occupancy{width:95px}}@media (min-width:992px){.navbar-nav>li>a{font-size:100%}.panel-home-page-search{left:80px;width:510px;position:absolute;z-index:20;top:20px}}@media (min-width:1200px){.panel-home-page-search{left:120px;top:60px}}.panel-home-page-search .form-inline .search-box{display:block;margin-bottom:15px;width:100%}.panel-home-page-search>.panel-body{background-color:rgba(242,245,241,.95)}.homepage-offers{background-color:#fff}.homepage-offers .tab-content{padding:9px}.homepage-offers .nav-tabs>li>a{border-radius:0;margin-right:0;border:0;text-transform:uppercase;padding:10px 6px}.homepage-offers .nav-tabs>li.active>a{border-right:1px solid #ddd;border-bottom-color:transparent}#homepageCarousel{margin-bottom:20px}#homepageCarousel .carousel-left,#homepageCarousel .carousel-right{position:absolute;top:45%;background-image:none;padding:8px 10px 5px;font-size:20px;background-color:rgba(0,0,0,.6);color:#fff;border-radius:4px}#homepageCarousel .carousel-left{left:1%}#homepageCarousel .carousel-right{right:1%}#homepageCarousel .carousel-caption{background-color:rgba(242,245,241,.95);color:#525f79;text-align:left;text-indent:120px}@media screen and (min-width:768px){#homepageCarousel .carousel-caption{left:0;right:0;padding:0;text-indent:80px}}@media screen and (min-width:992px){#homepageCarousel .carousel-caption{text-indent:80px}}@media screen and (min-width:1200px){#homepageCarousel .carousel-caption{text-indent:120px}}</style>
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
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=528120040655478&amp;ev=PixelInitialized" /></noscript>

<script src="//platform.twitter.com/oct.js" type="text/javascript"></script>
<script type="text/javascript">
    twttr.conversion.trackPid('l526m');
</script>
<noscript>
<img height="1" width="1" style="display:none;" alt="" src="https://analytics.twitter.com/i/adsct?txn_id=l526m&p_id=Twitter" />
<img height="1" width="1" style="display:none;" alt="" src="//t.co/i/adsct?txn_id=l526m&p_id=Twitter" />
</noscript>
<?php include_once JPATH_THEMES . '/' . $this->template . '/inc/styles.php'; ?>
<?php include_once JPATH_THEMES . '/' . $this->template . '/inc/assets.php'; ?>
</body>
</html>
