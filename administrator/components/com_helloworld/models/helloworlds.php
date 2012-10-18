<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * HelloWorldList Model
 */
class HelloWorldModelHelloWorlds extends JModelList
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
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'state', 'a.state',
				'access', 'a.access', 'access_level',
				'language', 'a.language',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'created_time', 'a.created_time',
				'created_user_id', 'a.created_user_id',
				'lft', 'a.lft',
				'rgt', 'a.rgt',
				'level', 'a.level',
				'path', 'a.path',
			);
		}
		parent::__construct($config);
	}
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param	string	An optional ordering field.
	 * @param	string	An optional direction (asc|desc).
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables
		$app		= JFactory::getApplication();
		$context	= $this->context;

		$extension = $app->getUserStateFromRequest('com_helloworlds.helloworlds.filter.extension', 'extension', 'com_helloworlds', 'cmd');

		$this->setState('filter.extension', $extension);
		$parts = explode('.', $extension);
		
    $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);
    
    
		// extract the component name
		$this->setState('filter.component', $parts[0]);

		$search = $this->getUserStateFromRequest($context.'.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		// List state information.
		parent::populateState('a.lft', 'asc');
	}
	
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.extension');
		$id	.= ':'.$this->getState('filter.published');

		return parent::getStoreId($id);
	}
	

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Get the user ID
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		
		// Get the list of user groups this user is assigned to		
		$groups = $user->getAuthorisedGroups();
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Select some fields
		$query->select('a.id,a.greeting, a.title, a.modified, a.expiry_date, a.alias, a.access, a.created_by, a.path, a.parent_id, a.level, a.lft, a.rgt, a.lang,ua.name AS author_name, a.published');
		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');
		// Check the user group this user belongs to.
		if (!(in_array(8, $groups) || in_array(11, $groups))) 
		{
			$query->where('created_by='.$userId);
		}
		$query->where('created_by !=0');
 
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '') {
			$query->where('(a.published = 0 OR a.published = 1)');
		}    
		
		// Filter by search in title
		// TODO - Try and tidy up this logic a bit.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$id = substr($search, 3);
				// In order to show all properties relating to the one being searched on we need to check whether this is a leaf node or not
				// Get an instance of the HelloWorldTable which is a nested set class
				$table = JTable::getInstance('HelloWorld', 'HelloWorldTable');			
				// Only proceed if the id is available to load and check further
				if ($table->load(array('id'=>$id))) {
					// Is this a leaf node (e.g. has no children)
					if($table->isLeaf($id)) {
						// This node has no children
						// Does it have a parent ID?
						if ($table->parent_id == 1) {
							// Is a leaf node and no parent so must be a single unit property
							$query->where('a.id = '.(int) $id);
						} elseif ($table->parent_id != 1) {	
							// This is a leaf node and has a parent so must be a unit
							// Need to get the parent ID
							$parent_id = $table->parent_id;
							// This pulls out the property with ID searched on, it's parent and any siblings. 
							$query->where('a.id = '.(int) $id.' OR a.id = '.(int) $parent_id .' OR a.parent_id = '.(int) $parent_id);
						}
					} else {						
						// Not a leaf node, but might have some units assigned
						$subtree = $table->getTree( $id );	
						// If the subtree has more than one element then most likely it has one or more units
						$property_ids = array();
						foreach ($subtree as $property) {
							$property_ids[] = $property->id;
						}
						$query->where('a.id in (' . implode(",",$property_ids) . ')');				

					}
				} else {
					// Try a search for this ID but most likely it doesn't exists
					$query->where('a.id = '.(int) substr($id, 3));
				}
			}
			elseif (stripos($search, 'account:') === 0) {
				$search = $db->Quote('%'.$db->escape(substr($search, 8), true).'%');
				$query->where('(ua.name LIKE '.$search.' OR ua.username LIKE '.$search.')');
			}
      elseif (stripos($search, 'accountid:') === 0) {
				$search = substr($search, 10);
				$query->where('(ua.id = '.$search . ')');        
      }
			else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(a.greeting LIKE '.$search.')');
			}
		}
		
		// From the hello table
		$query->from('#__helloworld as a');
		
		$listOrdering = $this->getState('list.ordering', 'a.lft');
		$listDirn = $db->escape($this->getState('list.direction', 'ASC'));

		$query->order($db->escape($listOrdering).' '.$listDirn);

		return $query;
	}

	function getLanguages()
	{
		$lang 	   =& JFactory::getLanguage();
		$languages = $lang->getKnownLanguages(JPATH_SITE);
		
		$return = array();
		foreach ($languages as $tag => $properties)
			$return[] = JHTML::_('select.option', $tag, $properties['name']);
		
		return $return;
	}
 
}
