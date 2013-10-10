<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * HelloWorldList Model
 */

class FeaturedPropertiesModelFeaturedPropertyTypes extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'e.id',
				'state', 'e.state',
				'created', 'e.date_created',
        'title','hw.title',
			);
    }

		parent::__construct($config);
	}

  /**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
   *
	 */
	protected function getListQuery()
	{

    // Get the user to authorise
    $user	= JFactory::getUser();

		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('
      *
    ');

		// From the hello table
		$query->from('#__featured_property_types a');
    $query->where('published = 1');

    return $query;
	}
}

