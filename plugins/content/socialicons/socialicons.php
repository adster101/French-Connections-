<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Content.pagebreak
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.utilities.utility');

/**
 * Page break plugin
 *
 * <b>Usage:</b>
 * <code><hr class="system-pagebreak" /></code>
 * <code><hr class="system-pagebreak" title="The page title" /></code>
 * or
 * <code><hr class="system-pagebreak" alt="The first page" /></code>
 * or
 * <code><hr class="system-pagebreak" title="The page title" alt="The first page" /></code>
 * or
 * <code><hr class="system-pagebreak" alt="The first page" title="The page title" /></code>
 *
 * @package     Joomla.Plugin
 * @subpackage  Content.pagebreak
 * @since       1.6
 */
class PlgContentSocialIcons extends JPlugin
{

  /**
   * Load the language file on instantiation.
   *
   * @var    boolean
   * @since  3.1
   */
  protected $autoloadLanguage = true;

  /**
   * Plugin that adds a pagebreak into the text and truncates text at that point
   *
   * @param   string   $context  The context of the content being passed to the plugin.
   * @param   object   &$row     The article object.  Note $article->text is also available
   * @param   mixed    &$params  The article params
   * @param   integer  $page     The 'page' number
   *
   * @return  mixed  Always returns void or true
   *
   * @since   1.6
   */
  public function onContentPrepare($context, &$row, &$params, $page = 0)
  {

    $allowed_contexts = array('com_content.article');

    if (!in_array($context, $allowed_contexts))
    {
      return true;
    }

    // Return if we don't have a valid article id
    if (!isset($row->id) || !(int) $row->id)
    {
      return true;
    }

    if (!in_array($row->catid, $this->params->get('catid')))
    {
      return true;
    }

    // Build the Route for this article
    $route = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catid));

    // Social icons button thingy
    $socialicons = new JLayoutFile('frenchconnections.general.socialicons');
    $displayData = new StdClass;
    $displayData->appid = $params->get('facebook_appid', '');
    $displayData->route = $route;
    $displayData->title = $row->title;
    $displayData->header = true;
    $displayData->class = 'glyphicon-xxlarge visible-lg-inline-block visible-md-inline-block visible-sm-inline-block visible-xs-inline-block';
            
    $row->text .= $socialicons->render($displayData);

    return true;
  }

}
