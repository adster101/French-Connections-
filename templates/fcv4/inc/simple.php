<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>

<header class="simple" role="banner"> 
  <div class="container">
    <nav class="navbar navbar-default simple" role="navigation">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".fc-navbar-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>   
        </button> 
        <!-- Take brand out of navbar as we're not really using the BS default nav correctly -->
        <a class="navbar-brand simple" href="<?php echo $this->baseurl; ?>">
          <img src="<?php echo '//' . $URI->getHost() . '/images/general/logo-4.png' ?>" alt="<?php echo $sitename ?>" />
        </a> 
      </div>
      <?php if ($this->countModules('owner-login')) : ?>
        <jdoc:include type="modules" name="owner-login" style="none" />
      <?php endif; ?>     
      <?php if ($this->countModules('nav-simple')) : ?>  
        <div class="collapse navbar-collapse fc-navbar-collapse">
          <jdoc:include type="modules" name="nav-simple" style="none" />
        </div>
      <?php endif; ?>  
    </nav>       
  </div>
</header>  