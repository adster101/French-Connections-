<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JLoader::register('FinderHelperLanguage', JPATH_ADMINISTRATOR . '/components/com_finder/helpers/language.php');

/**
 * Filter HTML Behaviors for Finder.
 *
 * @package     Joomla.Site
 * @subpackage  com_finder
 * @since       2.5
 */
abstract class JHtmlFilter
{
	/**
	 * Method to generate filters using the slider widget and decorated
	 * with the FinderFilter JavaScript behaviors.
	 *
	 * @param   array  $options  An array of configuration options. [optional]
	 *
	 * @return  mixed  A rendered HTML widget on success, null otherwise.
	 *
	 * @since   2.5
	 */
	public static function slider($options = array())
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$html = '';
		$filter = null;

		// Get the configuration options.
		$filterId = array_key_exists('filter_id', $options) ? $options['filter_id'] : null;
		$activeNodes = array_key_exists('selected_nodes', $options) ? $options['selected_nodes'] : array();
		$classSuffix = array_key_exists('class_suffix', $options) ? $options['class_suffix'] : '';
		$loadMedia = array_key_exists('load_media', $options) ? $options['load_media'] : true;

		// Load the predefined filter if specified.
		if (!empty($filterId))
		{
			$query->select($db->quoteName('f.data') . ', ' . $db->quoteName('f.params'));
			$query->from($db->quoteName('#__finder_filters') . ' AS f');
			$query->where($db->quoteName('f.filter_id') . ' = ' . (int) $filterId);

			// Load the filter data.
			$db->setQuery($query);

			try
			{
				$filter = $db->loadObject();
			}
			catch (RuntimeException $e)
			{
				return null;
			}

			// Initialize the filter parameters.
			if ($filter)
			{
				$registry = new JRegistry;
				$registry->loadString($filter->params);
				$filter->params = $registry;
			}
		}

		// Build the query to get the branch data and the number of child nodes.
		$query->clear();
		$query->select('t.*, count(c.id) AS children');
		$query->from($db->quoteName('#__finder_taxonomy') . ' AS t');
		$query->join('INNER', $db->quoteName('#__finder_taxonomy') . ' AS c ON c.parent_id = t.id');
		$query->where($db->quoteName('t.parent_id') . ' = 1');
		$query->where($db->quoteName('t.state') . ' = 1');
		$query->where($db->quoteName('t.access') . ' IN (' . $groups . ')');
		$query->where($db->quoteName('c.state') . ' = 1');
		$query->where($db->quoteName('c.access') . ' IN (' . $groups . ')');
		$query->group('t.id, t.parent_id, t.state, t.access, t.ordering, t.title, c.parent_id');
		$query->order('t.ordering, t.title');

		// Limit the branch children to a predefined filter.
		if ($filter)
		{
			$query->where('c.id IN(' . $filter->data . ')');
		}

		// Load the branches.
		$db->setQuery($query);

		try
		{
			$branches = $db->loadObjectList('id');
		}
		catch (RuntimeException $e)
		{
			return null;
		}

		// Check that we have at least one branch.
		if (count($branches) === 0)
		{
			return null;
		}

		// Load the CSS/JS resources.
		if ($loadMedia)
		{
			JHtml::stylesheet('com_finder/sliderfilter.css', false, true, false);
			JHtml::script('com_finder/sliderfilter.js', false, true);
		}

		// Load plug-in language files.
		FinderHelperLanguage::loadPluginLanguage();

		// Start the widget.
		$html .= '<div id="finder-filter-container">';
		$html .= '<dl id="branch-selectors">';
		$html .= '<dt>';
		$html .= '<label for="tax-select-all" class="checkbox">';
		$html .= '<input type="checkbox" id="tax-select-all" />';
		$html .= JText::_('COM_FINDER_FILTER_SELECT_ALL_LABEL');
		$html .= '</label>';
		$html .= '</dt>';
		$html .= '<div class="control-group">';

		// Iterate through the branches to build the branch selector.
		foreach ($branches as $bk => $bv)
		{
			// If the multi-lang plug-in is enabled then drop the language branch.
			if ($bv->title == 'Language' && JLanguageMultilang::isEnabled())
			{
				continue;
			}

			$html .= '<label for="tax-' . $bk . '" class="checkbox">';
			$html .= '<input type="checkbox" class="toggler" id="tax-' . $bk . '"/>';
			$html .= JText::sprintf('COM_FINDER_FILTER_BRANCH_LABEL', JText::_(FinderHelperLanguage::branchSingular($bv->title)));
			$html .= '</label>';
		}

		$html .= '</div>';
		$html .= '</dl>';
		$html .= '<div id="finder-filter-container">';

		// Iterate through the branches and build the branch groups.
		foreach ($branches as $bk => $bv)
		{
			// If the multi-lang plug-in is enabled then drop the language branch.
			if ($bv->title == 'Language' && JLanguageMultilang::isEnabled())
			{
				continue;
			}

			// Build the query to get the child nodes for this branch.
			$query->clear();
			$query->select('t.*');
			$query->from($db->quoteName('#__finder_taxonomy') . ' AS t');
			$query->where($db->quoteName('t.parent_id') . ' = ' . (int) $bk);
			$query->where($db->quoteName('t.state') . ' = 1');
			$query->where($db->quoteName('t.access') . ' IN (' . $groups . ')');
			$query->order('t.ordering, t.title');

			// Load the branches.
			$db->setQuery($query);

			try
			{
				$nodes = $db->loadObjectList('id');
			}
			catch (RuntimeException $e)
			{
				return null;
			}

			// Translate node titles if possible.
			$lang = JFactory::getLanguage();
			foreach ($nodes as $nk => $nv)
			{
				$key = FinderHelperLanguage::branchPlural($nv->title);
				if ($lang->hasKey($key))
				{
					$nodes[$nk]->title = JText::_($key);
				}
			}

			// Start the group.
			$html .= '<dl class="checklist" rel="tax-' . $bk . '">';
			$html .= '<dt>';
			$html .= '<label for="tax-' . JFilterOutput::stringUrlSafe($bv->title) . '" class="checkbox">';
			$html .= '<input type="checkbox" class="branch-selector filter-branch' . $classSuffix . '" id="tax-' . JFilterOutput::stringUrlSafe($bv->title) . '" />';
			$html .= JText::sprintf('COM_FINDER_FILTER_BRANCH_LABEL', JText::_(FinderHelperLanguage::branchSingular($bv->title)));
			$html .= '</label>';
			$html .= '</dt>';
			$html .= '<div class="control-group">';

			// Populate the group with nodes.
			foreach ($nodes as $nk => $nv)
			{
				// Determine if the node should be checked.
				$checked = in_array($nk, $activeNodes) ? ' checked="checked"' : '';

				// Build a node.
				$html .= '<label for="tax-' . $nk . '" class="checkbox">';
				$html .= '<input class="selector filter-node' . $classSuffix . '" type="checkbox" value="' . $nk . '" name="t[]" id="tax-' . $nk . '"' . $checked . ' />';
				$html .= $nv->title;
				$html .= '</label>';
			}

			// Close the group.
			$html .= '</div>';
			$html .= '</dl>';
		}

		// Close the widget.
		$html .= '<div class="clr"></div>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Method to generate filters using select box drop down controls.
	 *
	 * @param   FinderIndexerQuery  $query    A FinderIndexerQuery object.
	 * @param   array               $options  An array of options.
	 *
	 * @return  mixed  A rendered HTML widget on success, null otherwise.
	 *
	 * @since   2.5
	 */
	public static function select($query, $options)
	{
		$user 	= JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$filter = null;

		// Get the configuration options.
		$classSuffix = $options->get('class_suffix', null);
		$loadMedia = $options->get('load_media', true);
		$showDates = $options->get('show_date_filters', false);

		// Try to load the results from cache.
		$cache = JFactory::getCache('com_finder', '');
		$cacheId = 'filter_select_' . serialize(array($query->filter, $options, $groups, JFactory::getLanguage()->getTag()));

		// Check the cached results.
		if (!($branches = $cache->get($cacheId)))
		{
			$db = JFactory::getDBO();
			$sql = $db->getQuery(true);

			// Load the predefined filter if specified.
			if (!empty($query->filter))
			{
				$sql->select($db->quoteName('f.data') . ', '. $db->quoteName('f.params'));
				$sql->from($db->quoteName('#__finder_filters') . ' AS f');
				$sql->where($db->quoteName('f.filter_id') . ' = ' . (int) $query->filter);

				// Load the filter data.
				$db->setQuery($sql);

				try
				{
					$filter = $db->loadObject();
				}
				catch (RuntimeException $e)
				{
					return null;
				}

				// Initialize the filter parameters.
				if ($filter)
				{
					$registry = new JRegistry;
					$registry->loadString($filter->params);
					$filter->params = $registry;
				}
			}

			// Build the query to get the branch data and the number of child nodes.
			$sql->clear();
			$sql->select('t.*, count(c.id) AS children');
			$sql->from($db->quoteName('#__finder_taxonomy') . ' AS t');
			$sql->join('INNER', $db->quoteName('#__finder_taxonomy') . ' AS c ON c.parent_id = t.id');
			$sql->where($db->quoteName('t.parent_id') . ' = 1');
			$sql->where($db->quoteName('t.state') . ' = 1');
			$sql->where($db->quoteName('t.access') . ' IN (' . $groups . ')');
			$sql->where($db->quoteName('c.state') . ' = 1');
			$sql->where($db->quoteName('c.access') . ' IN (' . $groups . ')');
			$sql->group($db->quoteName('t.id'));
			$sql->order('t.ordering, t.title');

			// Limit the branch children to a predefined filter.
			if (!empty($filter->data))
			{
				$sql->where('c.id IN(' . $filter->data . ')');
			}

			// Load the branches.
			$db->setQuery($sql);

			try
			{
				$branches = $db->loadObjectList('id');
			}
			catch (RuntimeException $e)
			{
				return null;
			}

			// Check that we have at least one branch.
			if (count($branches) === 0)
			{
				return null;
			}

			// Iterate through the branches and build the branch groups.
			foreach ($branches as $bk => $bv)
			{
				// If the multi-lang plug-in is enabled then drop the language branch.
				if ($bv->title == 'Language' && JLanguageMultilang::isEnabled())
				{
					continue;
				}

				// Build the query to get the child nodes for this branch.
				$sql->clear();
				$sql->select('t.*');
				$sql->from($db->quoteName('#__finder_taxonomy') . ' AS t');
				$sql->where($db->quoteName('t.parent_id') . ' = ' . (int) $bk);
				$sql->where($db->quoteName('t.state') . ' = 1');
				$sql->where($db->quoteName('t.access') . ' IN (' . $groups . ')');
				$sql->order('t.ordering, t.title');

				// Limit the nodes to a predefined filter.
				if (!empty($filter->data))
				{
					$sql->where('t.id IN(' . $filter->data . ')');
				}

				// Load the branches.
				$db->setQuery($sql);

				try
				{
					$branches[$bk]->nodes = $db->loadObjectList('id');
				}
				catch (RuntimeException $e)
				{
					return null;
				}

				// Translate branch nodes if possible.
				$language = JFactory::getLanguage();
				foreach ($branches[$bk]->nodes as $node_id => $node)
				{
					$key = FinderHelperLanguage::branchPlural($node->title);
					if ($language->hasKey($key))
					{
						$branches[$bk]->nodes[$node_id]->title = JText::_($key);
					}
				}

				// Add the Search All option to the branch.
				array_unshift($branches[$bk]->nodes, array('id' => null, 'title' => JText::_('COM_FINDER_FILTER_SELECT_ALL_LABEL')));
			}

			// Store the data in cache.
			$cache->store($branches, $cacheId);
		}

		$html = '';

		// Add the dates if enabled.
		if ($showDates)
		{
			$html .= JHtml::_('filter.dates', $query, $options);
		}

		$html .= '<div id="finder-filter-select-list" class="form-horizontal">';

		// Iterate through all branches and build code.
		foreach ($branches as $bk => $bv)
		{
			// If the multi-lang plug-in is enabled then drop the language branch.
			if ($bv->title == 'Language' && JLanguageMultilang::isEnabled())
			{
				continue;
			}

			$active = null;

			// Check if the branch is in the filter.
			if (array_key_exists($bv->title, $query->filters))
			{
				// Get the request filters.
				$temp = JFactory::getApplication()->input->request->get('t', array(), 'array');

				// Search for active nodes in the branch and get the active node.
				$active = array_intersect($temp, $query->filters[$bv->title]);
				$active = count($active) === 1 ? array_shift($active) : null;
			}

			$html .= '<div class="filter-branch' . $classSuffix . ' control-group">';
			$html .= '<label for="tax-' . JFilterOutput::stringUrlSafe($bv->title) . '" class="control-label">';
			$html .= JText::sprintf('COM_FINDER_FILTER_BRANCH_LABEL', JText::_(FinderHelperLanguage::branchSingular($bv->title)));
			$html .= '</label>';
			$html .= '<div class="controls">';
			$html .= JHtml::_('select.genericlist', $branches[$bk]->nodes, 't[]', 'class="inputbox"', 'id', 'title', $active, 'tax-' . JFilterOutput::stringUrlSafe($bv->title));
			$html .= '</div>';
			$html .= '</div>';
		}

		// Close the widget.
		$html .= '</div>';

		// Load the CSS/JS resources.
		if ($loadMedia)
		{
			JHtml::stylesheet('com_finder/sliderfilter.css', false, true, false);
		}

		return $html;
	}

	/**
	 * Method to generate fields for filtering dates
	 *
	 * @param   FinderIndexerQuery  $query    A FinderIndexerQuery object.
	 * @param   array               $options  An array of options.
	 *
	 * @return  mixed  A rendered HTML widget on success, null otherwise.
	 *
	 * @since   2.5
	 */
	public static function dates($query, $options)
	{
		$html = '';

		// Get the configuration options.
		$classSuffix = $options->get('class_suffix', null);
		$loadMedia = $options->get('load_media', true);
		$showDates = $options->get('show_date_filters', false);

		if (!empty($showDates))
		{
			// Build the date operators options.
			$operators = array();
			$operators[] = JHtml::_('select.option', 'before', JText::_('COM_FINDER_FILTER_DATE_BEFORE'));
			$operators[] = JHtml::_('select.option', 'exact', JText::_('COM_FINDER_FILTER_DATE_EXACTLY'));
			$operators[] = JHtml::_('select.option', 'after', JText::_('COM_FINDER_FILTER_DATE_AFTER'));

			// Load the CSS/JS resources.
			if ($loadMedia)
			{
				JHtml::stylesheet('com_finder/dates.css', false, true, false);
			}

			// Open the widget.
			$html .= '<ul id="finder-filter-select-dates">';

			// Start date filter.
			$html .= '<li class="filter-date' . $classSuffix . '">';
			$html .= '<label for="filter_date1">';
			$html .= JText::_('COM_FINDER_FILTER_DATE1');
			$html .= '</label>';
			$html .= '<br />';
			$html .= JHtml::_('select.genericlist', $operators, 'w1', 'class="inputbox filter-date-operator"', 'value', 'text', $query->when1, 'finder-filter-w1');
			$html .= JHtml::calendar($query->date1, 'd1', 'filter_date1', '%Y-%m-%d', 'title="' . JText::_('COM_FINDER_FILTER_DATE1_DESC') . '"');
			$html .= '</li>';

			// End date filter.
			$html .= '<li class="filter-date' . $classSuffix . '">';
			$html .= '<label for="filter_date2">';
			$html .= JText::_('COM_FINDER_FILTER_DATE2');
			$html .= '</label>';
			$html .= '<br />';
			$html .= JHtml::_('select.genericlist', $operators, 'w2', 'class="inputbox filter-date-operator"', 'value', 'text', $query->when2, 'finder-filter-w2');
			$html .= JHtml::calendar($query->date2, 'd2', 'filter_date2', '%Y-%m-%d', 'title="' . JText::_('COM_FINDER_FILTER_DATE2_DESC') . '"');
			$html .= '</li>';

			// Close the widget.
			$html .= '</ul>';
		}

		return $html;
	}
}
