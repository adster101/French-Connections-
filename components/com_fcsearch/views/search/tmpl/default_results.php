<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

// Activate the highlighter if enabled.
if (!empty($this->query->highlight) && $this->params->get('highlight_terms', 1)) {
  JHtml::_('behavior.highlighter', $this->query->highlight);
}

// Get the application object.
$app = JFactory::getApplication(); ?>


