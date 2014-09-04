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

$showSubmenu = true;
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
if ($this->params->get('logoFile'))
{
  $logo = JUri::root() . $this->params->get('logoFile');
}
else
{
  $logo = $this->baseurl . '/templates/' . $this->template . '/images/logo.png';
}

// Template Parameters
$displayHeader = $this->params->get('displayHeader', '1');
$statusFixed = $this->params->get('statusFixed', '1');
$stickyToolbar = $this->params->get('stickyToolbar', '1');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <jdoc:include type="head" />
      <meta http-equiv="X-UA-Compatible" content="IE=9">

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
        <!--[if lt IE 9]>
        <script src="../media/jui/js/html5.js"></script>
        <![endif]-->
        </head>
        <body class="admin <?php echo $option . ' view-' . $view . ' layout-' . $layout . ' task-' . $task . ' itemid-' . $itemid; ?>" <?php if ($stickyToolbar) : ?>data-spy="scroll" data-target=".subhead" data-offset="87"<?php endif; ?>>
          <!-- Top Navigation -->
          <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner"> 
              <div class="container-fluid">
                <a class="brand" href="<?php echo $this->baseurl; ?>">
                  <img src="<?php echo $logo; ?>" /><br />
                  <span>Home</span>
                </a>
                <a class="brand hidden-desktop hidden-tablet" href="<?php echo JUri::root(); ?>" title="<?php echo JText::sprintf('TPL_ISIS_PREVIEW', $sitename); ?>" target="_blank"><?php echo JHtml::_('string.truncate', $sitename, 14, false, false); ?>
                  <span class="icon-out-2 small"></span></a>
                <div<?php echo ($this->params->get('admin_menus') != '0') ? ' class="nav-collapse pull-right"' : ''; ?>>
                  <ul class="nav">
                    <li>
                      <a>
                        <span class="icon-user"></span>
                        <?php echo $user->name; ?>
                      </a>
                    </li>
                    <li>
                      <a class="visible-desktop visible-tablet" href="<?php echo JUri::root(); ?>" title="<?php echo JText::sprintf('TPL_ISIS_PREVIEW', $sitename); ?>" target="_blank">
                        <?php echo JHtml::_('string.truncate', $sitename, 14, false, false); ?>
                        <span class="icon-out-2 small"></span>
                      </a>
                    </li>
                    <li class="">
                      <a href="<?php echo JRoute::_('index.php?option=com_login&task=logout&' . JSession::getFormToken() . '=1'); ?>"><?php echo JText::_('Log out'); ?></a>
                    </li>
                  </ul>
                </div>
              </div>  
            </div>
          </nav>
          <?php if ((!$statusFixed) && ($this->countModules('status'))) : ?>
            <!-- Begin Status Module -->
            <div id="status" class="navbar status-top hidden-phone">
              <div class="btn-toolbar">
                <jdoc:include type="modules" name="status" style="no" />
              </div>
              <div class="clearfix"></div>
            </div>
            <!-- End Status Module -->
          <?php endif; ?>
          <!-- container-fluid -->
          <div class="container-fluid">
            <section id="content">
              <!-- Begin Content -->
              <jdoc:include type="modules" name="top" style="xhtml" />
                <?php $help = JToolbar::getInstance('fchelp'); ?>
                <?php echo $help->render(); ?>
              <div class="row-fluid row-offcanvas row-offcanvas-left">
                <?php if ($showSubmenu) : ?>
                  <div class="span3 sidebar-offcanvas">
                    <jdoc:include type="modules" name="fcmenu" style="none" />
                  </div>
                  <div class="span9">
                  <?php else : ?>
                    <div class="span12">
                    <?php endif; ?>
                    <div style="border-left:solid 1px #e5e5e5;padding-left:36px;">
                      <jdoc:include type="modules" name="title" />
                      <jdoc:include type="message" />
                      <?php if (!$cpanel) : ?>
                        <jdoc:include type="modules" name="toolbar" style="no" />
                      <?php endif; ?>   
                      <jdoc:include type="component" />                   
                    </div>
                  </div>
                </div>
                <?php if ($this->countModules('bottom')) : ?>
                  <jdoc:include type="modules" name="bottom" style="xhtml" />
                <?php endif; ?>
                <!-- End Content -->
            </section>
            <?php if ($this->countModules('owner-footer')) : ?> 
              <footer id="status" class=""> 
                <div class="clearfix">
                  <div class="container">
                    <jdoc:include type="modules" name="owner-footer" style="no" />
                  </div>
                </div>
              </footer>  
            <?php endif; ?>
          </div>
          <jdoc:include type="modules" name="debug" style="none" />
          <?php if ($stickyToolbar) : ?>
            <script>
              (function($)
              {
                // fix sub nav on scroll
                var $win = $(window)
                        , $nav = $('.subhead')
                        , navTop = $('.subhead').length && $('.subhead').offset().top - <?php if ($displayHeader || !$statusFixed) : ?>40<?php else: ?>20<?php endif; ?>
                        , isFixed = 0

                processScroll();

                // hack sad times - holdover until rewrite for 2.1
                $nav.on('click', function()
                {
                  if (!isFixed) {
                    setTimeout(function()
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
        </body>
        </html>
