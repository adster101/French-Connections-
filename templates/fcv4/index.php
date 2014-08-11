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

$menu = $app->getMenu();

$siteHome = ($menu->getActive() == $menu->getDefault()) ? 'home' : 'sub';

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
<?php $doc->addStyleSheet('media/fc/assets/css/styles.css'); ?>
  <jdoc:include type="head" />
</head>
<body class="<?php echo $siteHome; ?>-page <?php echo $option . " view-" . $view . " itemid-" . $itemid . ""; ?>">
  <div class="container">
    <jdoc:include type="modules" name="debug" style="html5" />
  </div>
</body>
</html>
