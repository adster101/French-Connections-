<?php defined('_JEXEC') or die;
/**
* @package		Little Donkey - Mobile and HTML5 enabled Joomla! template
* @Type			Joomla 1.7 
* @version		v0.1
* @author		Adam Rifat 
* @copyright	Copyright (C) 2012 French Connections
* @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/
$app			= JFactory::getApplication();
$doc			= JFactory::getDocument();
$templateparams	= $app->getTemplate(true)->params;
// Get the menu item type which we use to determine if we are on the homepage or not.
$menu 			= JSite::getMenu();
$siteName = $app->getCfg( 'sitename' );
?>


<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="<?php echo $this->language; ?>"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="<?php echo $this->language; ?>"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="<?php echo $this->language; ?>"> <![endif]-->
<!--[if gt IE 8]><!--> 
<html lang="<?php echo $this->language; ?>"> <!--<![endif]-->
<head>	
	<meta charset="utf-8">
	<!-- Use the .htaccess and remove these lines to avoid edge case issues. More info: h5bp.com/b/378 -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<!-- Set the viewport width to device width for mobile -->
	<meta name="viewport" content="width=device-width" />
	<!-- CSS: implied media=all -->
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/foundation.css">
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/app.css">
	
	<!--[if lt IE 9]>
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/ie.css">
	<![endif]-->
	<!-- IE Fix for HTML5 Tags -->
	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<jdoc:include type="head" />
</head>

<body>
	<div class="container">
		<div class="row">
			<div class="twelve columns">
				<span class="logo">
					<a title="<?php echo htmlspecialchars($siteName); ?>" href="<?php echo $this->baseurl ?>">
						<img 
							alt="<?php echo htmlspecialchars($siteName); ?>"
							src="<?php echo $this->baseurl ?>/<?php echo htmlSpecialChars($this->params->get('logo')); ?>" />
					</a>	
				</span>
				<?php if($this->countModules('site-search')) : ?>
					<jdoc:include type="modules" name="site-search" style="xhtml" />
				<?php endif; ?>		
			</div>
		</div>
	</div>
	<div id="navBar" class="container">
		<div class="row">
			<div class="twelve columns">
				<?php if($this->countModules('main-menu')) : ?>
					<jdoc:include type="modules" name="main-menu" style="xhtml" />
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="container component">
		<div class="row">
			
			<!-- TO DO - Sort this mess out -->
			
			<?php if (!$this->countModules('left') && !$this->countModules('right')) { ?>
				<div class="twelve columns">
			<?php } else if ($this->countModules('right')) { ?>
				<div class="eight columns">
			<?php }?>
			<?php if ($menu->getActive() == $menu->getDefault()) { ?>
				<?php if($this->countModules('slider')) : ?>
					<jdoc:include type="modules" name="slider" style="xhtml" />
				<?php endif; ?>
			<?php } ?>
				
				<?php if ($menu->getActive() !== $menu->getDefault() && $this->countModules('breadcrumbs')) { // If not the homepage and module is published to breadcrumbs?>
						<jdoc:include type="modules" name="breadcrumbs" />	
				<?php } ?>
				<?php if($this->countModules('sub-menu-horizontal')) : ?>
					<jdoc:include type="modules" name="sub-menu-horizontal" style="xhtml" />
				<?php endif; ?>
				<jdoc:include type="component" />
			</div>
			<?php 
				// If this isn't the homepage and there is a module published to the left position
				if ($menu->getActive() !== $menu->getDefault() && $this->countModules('left')) { ?>
				<?php if($this->countModules('left')) : ?>
					<div class="three columns pull-seven">
						<jdoc:include type="modules" name="left" style="xhtml" />
					</div>
				<?php endif; ?>
			<?php } ?>		
			<?php if($this->countModules('right')) : ?>
				<div class="four columns">
					<jdoc:include type="modules" name="right" style="xhtml" />
				</div>
			<?php endif; ?>
		</div>
	</div>
	
	<div id="footer" class="container">
		<div class="row">
			<hr />
			<jdoc:include type="modules" name="footer" style="xhtml" />
		</div>
	</div>
	
	
	<div id="navModal" class="reveal-modal">
		<?php if($this->countModules('main-menu-mobile')) : ?>
			<jdoc:include type="modules" name="main-menu-mobile" style="xhtml" />
		<?php endif; ?>	
		<a class="close-reveal-modal">�</a>
	</div>
	

		

<!-- JavaScript at the bottom for fast page loading -->

<script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/javascripts/modernizr.foundation.js"></script>
<!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/javascripts/foundation.js"></script>

<script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/javascripts/app.js"></script>



	
<!-- Change UA-XXXXX-X to be your site's ID -->
<script>
	window._gaq = [['_setAccount','UA-31032891-1'],['_trackPageview'],['_trackPageLoadTime']];
    Modernizr.load({
      load: ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js'
    });
  </script>


  <!-- Prompt IE 6 users to install Chrome Frame. Remove this if you want to support IE 6.
       chromium.org/developers/how-tos/chrome-frame-getting-started -->
  <!--[if lt IE 7 ]>
    <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
    <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
  <![endif]-->
	<jdoc:include type="modules" name="debug" />

</body>
</html>
