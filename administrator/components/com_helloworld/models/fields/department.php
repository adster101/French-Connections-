<?php
// No direct access to this file
defined('_JEXEC') or die;
 
// import the list field type
jimport('joomla.form.helper');


JFormHelper::loadFieldClass('list');
/**
 * HelloWorld Form Field class for the HelloWorld component
 */
class JFormFielddepartment extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'Department';
 
	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions() 
	{
		// Add include path to table classes
		//JTable::addIncludePath( JPATH_COMPONENT.'/tables' );
		// get an instance of the nested sets 
		//$table = JTable::getInstance( 'nestedsets', 'Table' );
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select
		(
			'*'
		);
		$query->from('#__categories');
		//$query->leftJoin('#__categories on catid=#__categories.id');
		$db->setQuery((string)$query);
		$messages = $db->loadObjectList();
		$options = array();
		if ($messages)
		{
			foreach($messages as $message) 
			{
				$options[] = JHtml::_('select.option', $message->id, $message->greeting . ($message->catid ? ' (' . $message->category . ')' : ''));
			}
		}
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
