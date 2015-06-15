<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>

 <header class="" role="banner"> 
    <div class="container"> 
      <?php if ($this->countModules('position-0')) : ?>
        <div class="banner-container">
          <jdoc:include type="modules" name="position-0" style="none" />
        </div>
      <?php endif; ?>
      <!-- Take brand out of navbar as we're not really using the BS default nav correctly -->
      <a class="navbar-brand" href="<?php echo $this->baseurl; ?>">
        <img src="<?php echo '//' . $URI->getHost() . '/images/general/logo-4.png' ?>" alt="<?php echo $sitename ?>" />
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