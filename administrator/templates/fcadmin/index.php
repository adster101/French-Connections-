<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.isis
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       3.0
 */
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$lang = JFactory::getLanguage();
$this->language = $doc->language;
$this->direction = $doc->direction;
$input = $app->input;
$user = JFactory::getUser();

$livechat_js = "(function(){
  var c = document.createElement('script');
  c.type = 'text/javascript'; c.async = true;
  c.src = '//frenchconnections.smartertrack.com/ChatLink.ashx?config=1&id=stlivechat21';
  var s = document.getElementsByTagName('script')[0];
  s.parentNode.insertBefore(c,s);
})();";

//$doc->addScriptDeclaration($livechat_js);

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');
$doc->addScriptVersion('templates/' . $this->template . '/js/template.js');

// Add Stylesheets
$doc->addStyleSheetVersion('templates/' . $this->template . '/css/template' . ($this->direction == 'rtl' ? '-rtl' : '') . '.css');

// Load specific language related CSS
$file = 'language/' . $lang->getTag() . '/' . $lang->getTag() . '.css';

if (is_file($file))
{
    $doc->addStyleSheetVersion($file);
}

// Detecting Active Variables
$option = $input->get('option', '');
$view = $input->get('view', '');
$layout = $input->get('layout', '');
$task = $input->get('task', '');
$itemid = $input->get('Itemid', '');
$sitename = $app->getCfg('sitename');

$cpanel = ($option === 'com_cpanel');

$showSubmenu = false;
$this->submenumodules = JModuleHelper::getModules('submenu');
foreach ($this->submenumodules as $submenumodule)
{
    $output = JModuleHelper::renderModule($submenumodule);
    if (strlen($output))
    {
        $showSubmenu = true;
        break;
    }
}

// Logo file
$logo = '/' . $this->params->get('logoFile');

// Template Parameters
$displayHeader = $this->params->get('displayHeader', '1');
$statusFixed = $this->params->get('statusFixed', '1');
$stickyToolbar = $this->params->get('stickyToolbar', '1');

// Get an instance of the uri and reset the port and path
$uri = JUri::getInstance();
$uri->setScheme('http');
$uri->setPath('');
$uri->setQuery('');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <jdoc:include type="head" />
      <meta http-equiv="X-UA-Compatible" content="IE=9">
        <?php include_once JPATH_THEMES . '/' . $this->template . '/assets.php'; ?>

        <!-- Template color -->
        <?php if ($this->params->get('templateColor')) : ?>
            <style type="text/css">
              .navbar-inner, .navbar-inverse .navbar-inner, .dropdown-menu li > a:hover, .dropdown-menu .active > a, .dropdown-menu .active > a:hover, .navbar-inverse .nav li.dropdown.open > .dropdown-toggle, .navbar-inverse .nav li.dropdown.active > .dropdown-toggle, .navbar-inverse .nav li.dropdown.open.active > .dropdown-toggle, #status.status-top {
                background: <?php echo $this->params->get('templateColor'); ?>;
              }
              .navbar-inner, .navbar-inverse .nav li.dropdown.open > .dropdown-toggle, .navbar-inverse .nav li.dropdown.active > .dropdown-toggle, .navbar-inverse .nav li.dropdown.open.active > .dropdown-toggle {
                -moz-box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
                -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
                box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
              }
            </style>
        <?php endif; ?>

        <!-- Template header color -->
        <?php if ($this->params->get('headerColor')) : ?>
            <style type="text/css">
              .header {
                background: <?php echo $this->params->get('headerColor'); ?>;
              }
            </style>
        <?php endif; ?>

        <!-- Sidebar background color -->
        <?php if ($this->params->get('sidebarColor')) : ?>
            <style type="text/css">
              .nav-list > .active > a, .nav-list > .active > a:hover {
                background: <?php echo $this->params->get('sidebarColor'); ?>;
              }
            </style>
        <?php endif; ?>
        </head>

        <body class="admin <?php echo $option . ' view-' . $view . ' layout-' . $layout . ' task-' . $task . ' itemid-' . $itemid; ?>" <?php if ($stickyToolbar) : ?>data-spy="scroll" data-target=".subhead" data-offset="87"<?php endif; ?>>
          <!-- Top Navigation -->
          <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
              <div class="container">
                <?php if ($this->params->get('admin_menus') != '0') : ?>
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                      <span class="icon-bar"></span>
                      <span class="icon-bar"></span>
                      <span class="icon-bar"></span>
                    </a>
                <?php endif; ?>
                <a class="brand" href="<?php echo $this->baseurl . '/' ?>">
                  <img src="<?php echo $logo; ?>" /><br />
                  <span>Home</span>
                </a>

                <a class="brand hidden-desktop hidden-tablet" href="<?php echo (string) $uri ?>" title="<?php echo JText::sprintf('TPL_ISIS_PREVIEW', $sitename); ?>" target="_blank"><?php echo JHtml::_('string.truncate', $sitename, 14, false, false); ?>
                  <span class="icon-out-2 small"></span></a>
                <div<?php echo ($this->params->get('admin_menus') != '0') ? ' class="nav-collapse pull-right"' : ''; ?>>
                  <jdoc:include type="modules" name="menu" style="none" />
                  <ul class="nav nav-user">
                    <li>
                      <a class="" href="<?php echo (string) $uri; ?>" title="<?php echo JText::sprintf('TPL_ISIS_PREVIEW', $sitename); ?>" target="_blank"><?php echo JHtml::_('string.truncate', $sitename, 14, false, false); ?>
                        <span class="icon-out-2 small"></span>
                      </a>
                    </li>
                    <li class="dropdown">
                      <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="icon-cog"></span>
                        <b class="caret"></b></a>
                      <ul class="dropdown-menu">
                        <li>
                          <span>
                            <span class="icon-user"></span>
                            <strong><?php echo $user->name; ?></strong>
                          </span>
                        </li>
                        <li class="divider"></li>
                        <!--<li class="">
                          <a href="index.php?option=com_admin&task=profile.edit&id=<?php //echo $user->id;                 ?>"><?php //echo JText::_('TPL_ISIS_EDIT_ACCOUNT');                          ?></a>
                        </li>
                        <li class="divider"></li>-->
                        <li class="">
                          <a href="<?php echo JRoute::_('index.php?option=com_login&task=logout&' . JSession::getFormToken() . '=1'); ?>">
                            <?php echo JText::_('TPL_FRENCHCONNECTIONS_LOGOUT'); ?>
                          </a>
                        </li>
                      </ul>
                    </li>
                  </ul>
                  <!--<a class="brand visible-desktop visible-tablet" href="<?php //echo JUri::root();                          ?>" title="<?php //echo JText::sprintf('TPL_ISIS_PREVIEW', $sitename);                          ?>" target="_blank"><?php //echo JHtml::_('string.truncate', $sitename, 14, false, false);                          ?>
                    <span class="icon-out-2 small"></span></a>-->
                </div>
                <!--/.nav-collapse -->
              </div>
            </div>
          </nav>
          <!-- Header -->
          <?php if ($displayHeader) : ?>
              <header class="header">
                <div class="container">
                  <div class="container-title">
                    <div class="row label-">
                      <div class="span12">
                        <jdoc:include type="modules" name="status" style="no" />

                        <jdoc:include type="modules" name="title" />

                      </div>
                    </div>
                  </div>
                </div>
              </header>
          <?php endif; ?>

          <?php if (!$cpanel) : ?>
              <!-- Subheader -->
              <a class="btn btn-subhead" data-toggle="collapse" data-target=".subhead-collapse"><?php echo JText::_('TPL_ISIS_TOOLBAR'); ?>
                <i class="icon-wrench"></i>
              </a>
              <div class="subhead-collapse collapse">
                <div class="subhead">
                  <div class="container">
                    <div id="container-collapse" class="container-collapse"></div>
                    <div class="row">
                      <div class="span12">
                        <jdoc:include type="modules" name="toolbar" style="no" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
          <?php endif; ?>
          <!-- container-fluid -->
          <div class="container container-main" style="position:relative;left:0;">
            <section id="content">


              <jdoc:include type="modules" name="top" style="xhtml" />
              <div class="row-fluid">
                <!-- Begin Content -->
                <?php if ($showSubmenu) : ?>
                    <div class="span3">
                      <jdoc:include type="modules" name="submenu" style="none" />
                    </div>
                    <div class="span9">
                  <?php else : ?>
                      <div class="span12">
                    <?php endif; ?>
                        <jdoc:include type="message" />
                    <?php
                    // Show the page title here if the header is hidden
                    if (!$displayHeader) :
                        ?>
                        <h1 class="content-title"><?php echo JHtml::_('string.truncate', $app->JComponentTitle, 0, false, false); ?></h1>
                    <?php endif; ?>
                    <jdoc:include type="component" />
                  </div>
                </div>
                <?php if ($this->countModules('bottom')) : ?>
                    <jdoc:include type="modules" name="bottom" style="xhtml" />
                <?php endif; ?>
                <!-- End Content -->
            </section>
          </div>
          <?php if ($this->countModules('owner-footer')) : ?>
              <footer id="status" class="navbar navbar-fixed-bottom">
                <div class="clearfix">
                  <div class="container">

                    <div class="row">
                      <div class="span8">
                        <jdoc:include type="modules" name="owner-footer" style="no" />
                      </div>
                      <div class="span4">
                      </div>
                    </div>
                  </div>
                </div>
              </footer>
          <?php endif; ?>
          <jdoc:include type="modules" name="debug" style="none" />
          <?php if ($stickyToolbar) : ?>
              <script>
                  (function ($)
                  {
                    // fix sub nav on scroll
                    var $win = $(window)
                            , $nav = $('.subhead')
                            , navTop = $('.subhead').length && $('.subhead').offset().top - <?php if ($displayHeader || !$statusFixed) : ?>40<?php else: ?>20<?php endif; ?>
                                          , isFixed = 0

                                  processScroll();

                                  // hack sad times - holdover until rewrite for 2.1
                                  $nav.on('click', function ()
                                  {
                                    if (!isFixed) {
                                      setTimeout(function ()
                                      {
                                        $win.scrollTop($win.scrollTop() - 97)
                                      }, 10)
                                    }
                                  })

                                  $win.on('scroll', processScroll)

                                  function processScroll()
                                  {
                                    var i, scrollTop = $win.scrollTop()
                                    if (scrollTop >= navTop && !isFixed) {
                                      isFixed = 1
                                      $nav.addClass('subhead-fixed')
                                    } else if (scrollTop <= navTop && isFixed) {
                                      isFixed = 0
                                      $nav.removeClass('subhead-fixed')
                                    }
                                  }
                                })(jQuery);
              </script>
          <?php endif; ?>
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
          <div id="stlivechat21"></div>

        </body>
        </html>
