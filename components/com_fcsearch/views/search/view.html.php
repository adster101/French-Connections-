<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Search HTML view class for the Finder package.
 *
 * @package     Joomla.Site
 * @subpackage  com_finder
 * @since       2.5
 */
class FcSearchViewSearch extends JViewLegacy
{
	protected $query;

	protected $params;

	protected $state;

	protected $user;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  mixed  JError object on failure, void on success.
	 *
	 * @since   2.5
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Get view data.
		$state = $this->get('State');

    $results = $this->get('Results');
    
		$total = $this->get('Total');

    JDEBUG ? $GLOBALS['_PROFILER']->mark('afterFinderResults') : null;
		//$total = $this->get('Total');
		JDEBUG ? $GLOBALS['_PROFILER']->mark('afterFinderTotal') : null;
    
		$pagination = $this->get('Pagination');
    
    
    
		JDEBUG ? $GLOBALS['_PROFILER']->mark('afterFinderPagination') : null;

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Configure the pathway.
		if (!empty($query->input))
		{
			$app->getPathWay()->addItem($this->escape($query->input));
		}

		// Push out the view data.
		$this->state = &$state;
		$this->results = &$results;
		$this->total = &$total;
		$this->pagination = &$pagination;
		
    $this->prepareDocument();

    $this->sidebar = JHtmlSidebar::render();

	

		// Log the search
		JSearchHelper::logSearch('Log some useful search information...', 'com_fcsearch');



		// Check for layout override only if this is not the active menu item
		// If it is the active menu item, then the view and category id will match
		$active = $app->getMenu()->getActive();
		if (isset($active->query['layout']))
		{
			// We need to set the layout in case this is an alternative menu item (with an alternative layout)
			$this->setLayout($active->query['layout']);
		}

    // Need to set valid meta data for the page here, load any JS, CSS Etc
    
		JDEBUG ? $GLOBALS['_PROFILER']->mark('beforeFinderLayout') : null;

		parent::display($tpl);

		JDEBUG ? $GLOBALS['_PROFILER']->mark('afterFinderLayout') : null;
	}

	/**
	 * Method to get hidden input fields for a get form so that control variables
	 * are not lost upon form submission
	 *
	 * @return  string  A string of hidden input form fields
	 *
	 * @since   2.5
	 */
	protected function getFields()
	{
		$fields = null;

		// Get the URI.
		$uri = JURI::getInstance(JRoute::_($this->query->toURI()));
		$uri->delVar('q');
		$uri->delVar('o');
		$uri->delVar('t');
		$uri->delVar('d1');
		$uri->delVar('d2');
		$uri->delVar('w1');
		$uri->delVar('w2');

		// Create hidden input elements for each part of the URI.
		foreach ($uri->getQuery(true) as $n => $v)
		{
			if (is_scalar($v))
			{
				$fields .= '<input type="hidden" name="' . $n . '" value="' . $v . '" />';
			}
		}

		return $fields;
	}

	/**
	 * Method to get the layout file for a search result object.
	 *
	 * @param   string  $layout  The layout file to check. [optional]
	 *
	 * @return  string  The layout file to use.
	 *
	 * @since   2.5
	 */
	protected function getLayoutFile($layout = null)
	{
		// Create and sanitize the file name.
		$file = $this->_layout . '_' . preg_replace('/[^A-Z0-9_\.-]/i', '', $layout);

		// Check if the file exists.
		jimport('joomla.filesystem.path');
		$filetofind = $this->_createFileName('template', array('name' => $file));
		$exists = JPath::find($this->_path['template'], $filetofind);

		return ($exists ? $layout : 'result');
	}

	/**
	 * Prepares the document
	 *
	 * @param   FinderIndexerQuery  $query  The search query
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	protected function prepareDocument()
	{
    
		$app = JFactory::getApplication();
    $document = JFactory::getDocument();

		$title = null;

    $title = JStringNormalise::toSpaceSeparated($this->state->get('list.searchterm'));
	
    $title = UCFirst($title);
    
    $title = JText::sprintf('COM_FCSEARCH_TITLE', $title);


		$this->document->setTitle($title);

	
		// Configure the document meta-description.
		if (!empty($this->explained))
		{
			$explained = $this->escape(html_entity_decode(strip_tags($this->explained), ENT_QUOTES, 'UTF-8'));
			$this->document->setDescription($explained);
		}

		// Configure the document meta-keywords.
		if (!empty($query->highlight))
		{
			$this->document->setMetadata('keywords', implode(', ', $query->highlight));
		}

    $document->addScript(JURI::root() . 'media/jui/js/cookies.jquery.min.js','text/javascript', true);
    $document->addScript(JURI::root() . 'media/fc/js/search.js','text/javascript', true);
    $document->addScript(JURI::root() . 'media/fc/js/jquery.maphilight.min.js','text/javascript', true);
    $document->addScript(JURI::root() . 'media/fc/js/jquery-ui-1.8.23.custom.min.js', 'text/javascript', true);
    $document->addScript(JURI::root() . 'media/fc/js/date-range.js', 'text/javascript', true);
    $document->addStyleSheet(JURI::root() . 'media/fc/css/jquery-ui-1.8.23.custom.css');
		$document->addScript("https://maps.googleapis.com/maps/api/js?key=AIzaSyAwnosMJfizqEmuQs-WsJRyHKqEsU9G-DI&sensor=true");

    JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'sort_by',
			JHtml::_('select.options', array('beds'=>'Bedrooms'), 'value', 'text', $this->state->get('filter.published'), true)
    );
		
	}
}
