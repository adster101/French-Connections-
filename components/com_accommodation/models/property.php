<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
 
/**
 * HelloWorld Model
 */
class AccommodationModelProperty extends JModelItem
{
	/**
	 * @var object item
	 */
	protected $item;
 
	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState() 
	{
		$app = JFactory::getApplication();
		// Get the message id
		$id = JRequest::getInt('id');
		$this->setState('property.id', $id);
 
		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		parent::populateState();
	}
 

 
	/**
	 * Get the message
	 * @return object The message to be displayed to the user
	 */
	public function getItem() 
	{    
		if (!isset($this->item)) 
		{
			// Get the language for this request 
			$lang = & JFactory::getLanguage()->getTag();
			// Get the state for this property ID
			$id = $this->getState('property.id');
      
      // Language logic - should be more generic than this, in case we add more languages...
			if ($lang === 'fr-FR') {
				$select = '
          trans.greeting,
          bathrooms,
          toilets,
          catid,
          hel.id,
          params,
          trans.greeting,
          trans.description,
          occupancy,
          swimming,
          latitude,
          longitude,
          nearest_town';
			} else {
				$select = '
          catid,
          toilets,
          bathrooms,
          hel.id,
          params,
          hel.greeting,
          hel.description,
          occupancy,
          swimming,
          latitude,
          longitude,
          nearest_town';
			}

			$this->_db->setQuery($this->_db->getQuery(true)
				->from('#__helloworld as hel')
				->select($select)
				->leftJoin('#__helloworld_translations AS trans ON hel.id = trans.property_id')
				->where('hel.id='. (int)$id));

			if (!$this->item = $this->_db->loadObject()) 
			{
				$this->setError($this->_db->getError());
			}
		}
		return $this->item;
	}
}
