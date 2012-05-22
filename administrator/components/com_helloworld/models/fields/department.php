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
		// Get a nested sets table instance for the categories table.
		// Note that here we are using the global Joomla categories not the component ones.
		$table = JTable::getInstance('HelloWorld_categories', 'HelloWorldTable');
		// Get the sub tree for node 11. I know this to be the root node I am interested in.
		// To do: Add method to table class to retrieve the node via alias. May be more robust in the long run
		$subTree = $table->getTree(11);
		// Set up an array to hold the oprions
		$options = array();
		if ($subTree)
		{
			// Loop over each subtree item
			foreach($subTree as $item) 
			{
				if($table->isLeaf( $item->id )) {
					$options[] = JHtml::_('select.option', $item->id, $item->title);
				}
			}
		}
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
