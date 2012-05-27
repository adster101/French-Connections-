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
	<jdoc:include type="head" />
	<meta charset="utf-8">
	<!-- Use the .htaccess and remove these lines to avoid edge case issues. More info: h5bp.com/b/378 -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<!-- Set the viewport width to device width for mobile -->
	<meta name="viewport" content="width=device-width initial-scale=1.0" />
	<!-- CSS: implied media=all -->
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/bootstrap.css">
	
	<!--[if lt IE 9]>
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/ie.css">
	<![endif]-->
	<!-- IE Fix for HTML5 Tags -->
	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

<!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/bootstrap-carousel.js"></script>
</head>

<body>
	<div class="container">
		<div class="row">
			<div class="span12">
				<p class="logo pull-left">
					<a class="brand " title="<?php echo htmlspecialchars($siteName); ?>" href="<?php echo $this->baseurl ?>">
						<img 
							alt="<?php echo htmlspecialchars($siteName); ?>"
							src="<?php echo $this->baseurl ?>/<?php echo htmlSpecialChars($this->params->get('logo')); ?>" />
					</a>	
				</p>
				<?php if($this->countModules('site-search')) : ?>
					<jdoc:include type="modules" name="site-search" style="xhtml" />
				<?php endif; ?>	
			</div>
		
			<div class="span12">
				<?php if($this->countModules('main-menu')) : ?>
					<jdoc:include type="modules" name="main-menu" style="nav" />
				<?php endif; ?>
			</div>
	
			<?php if ($menu->getActive() !== $menu->getDefault()) { // If this isn't the homepage  ?>
				<?php if ($this->countModules('left') && !$this->countModules('right')) { // and there is a module published to the left position only ?>			
					<div class="span4">
						<jdoc:include type="modules" name="left" style="xhtml" />
					</div>
				<?php } else if ($this->countModules('right') && $this->countModules('left')) { ?>
					<div class="span4">
						<jdoc:include type="modules" name="left" style="xhtml" />
					</div>
				<?php } ?>		
	 		<?php } ?>
			
			<?php if (!$this->countModules('left') && !$this->countModules('right')) { // If no modules published left or right output a full width column ?>
				<div class="span12">
			<?php } else if ($this->countModules('left') && $this->countModules('right')) { // If modules published to both side postions output a narrowish column?>
				<div class="span4">
			<?php } else if ($this->countModules('right')) { // If there is a module published only to the right output an eight column wide column ?>
				<div class="span8">
			<?php } else if ($this->countModules('left')) { // If there is a module published only to the left output an ten column wide column ?>
				<div class="span8">
			<?php } ?>
			<?php if($this->countModules('slider')) { ?>
				<jdoc:include type="modules" name="slider" style="xhtml" />
			<?php } // EOF is this homepage ?>		
			<?php if ($menu->getActive() !== $menu->getDefault() && $this->countModules('breadcrumbs')) { // If not the homepage and module is published to breadcrumbs ?>
					<jdoc:include type="modules" name="breadcrumbs" />	
			<?php } ?>
			<?php if($this->countModules('sub-menu-horizontal')) { ?>
				<jdoc:include type="modules" name="sub-menu-horizontal" style="xhtml" />
			<?php } ?>
				<jdoc:include type="component" />
				</div>
			<?php if ($menu->getActive() !== $menu->getDefault()) { // If this isn't the homepage  ?>
				<?php if ($this->countModules('left') && !$this->countModules('right')) { // and there is a module published to the left position only ?>			
				

				<?php } else if ($this->countModules('right') && $this->countModules('left')) { ?>
					<div class="span4">
						<jdoc:include type="modules" name="right" style="xhtml" />
					</div>
				<?php } else if ($this->countModules('right')) { ?>
					<div class="span4">
						<jdoc:include type="modules" name="right" style="xhtml" />
					</div>				
				<?php } ?>
			<?php } ?>
		
	<div class="span12">
		<jdoc:include type="modules" name="footer" style="xhtml" />
	</div>
	
	

	</div>
</div>
<!-- JavaScript at the bottom for fast page loading -->






</body>
</html>
