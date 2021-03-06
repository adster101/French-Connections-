<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class ClassificationModelClassification extends JModelAdmin
{

    /**
     * Method override to check if you can edit an existing record.
     *
     * @param	array	$data	An array of input data.
     * @param	string	$key	The name of the key for the primary key.
     *
     * @return	boolean
     * @since	1.6
     */
    protected function allowEdit($data = array(), $key = 'id')
    {
        // Check specific edit permission then general edit permission.
        return JFactory::getUser()->authorise('core.edit', 'com_classification.classification.' . ((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
    }

    public function getItem()
    {
        if ($item = parent::getItem($pk))
        {

            $registry = new JRegistry;
            $registry->loadString($item->property_type_info);
            $item->type = $registry->toArray();
            
            foreach ($item->type as $key => $value)
            {
               $item->$key = $value; 
            }
            
        }

        return $item;
    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Classification', $prefix = 'ClassificationTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param	array	$data		Data for the form.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	mixed	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true)
    {

        // Get the form.
        $form = $this->loadForm('com_classification.classification', 'classification', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_classification.edit.classification.data', array());

        if (empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method rebuild the entire nested set tree.
     *
     * @return  boolean  False on failure or error, true otherwise.
     *
     * @since   1.6
     */
    public function rebuild()
    {
        // Get an instance of the table object.
        $table = $this->getTable();

        if (!$table->rebuild())
        {
            $this->setError($table->getError());
            return false;
        }

        // Clear the cache
        $this->cleanCache();

        return true;
    }

    public function preprocessForm(\JForm $form, $data, $group = 'content')
    {

        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_attributes/models');
        $model = JModelLegacy::getInstance('Attributes', 'AttributesModel', $config = array('ignore_request' => true));

        $model->setState('filter.attribute_type_id', 1);
        $model->setState('list.limit', '');

        $property_types = $model->getItems();

        $addform = new SimpleXMLElement('<form />');


        foreach ($property_types as $type)
        {  // Replace spaces with dashes as per the search component
            $name = JStringNormalise::toDashSeparated(JApplication::stringURLSafe($type->title));

            $fields = $addform->addChild('fields');
            $fields->addAttribute('name', strtolower($name));



            $content = $fields->addChild('field');
            $content->addAttribute('name', 'content');
            $content->addAttribute('type', 'editor');
            $content->addAttribute('filter', 'safehtml');
            $content->addAttribute('label', JText::sprintf('COM_CLASSIFICATION_PROPERTY_TYPE_CONTENT', $type->title));
            $content->addAttribute('multiple', true);
            $content->addAttribute('default', '');

            $meta_title = $fields->addChild('field');
            $meta_title->addAttribute('name', 'meta_title');
            $meta_title->addAttribute('type', 'text');
            $meta_title->addAttribute('filter', 'string');
            $meta_title->addAttribute('label', JText::sprintf('COM_CLASSIFICATION_PROPERTY_TYPE_META_TITLE', $type->title));
            $meta_title->addAttribute('multiple', true);
            $meta_title->addAttribute('default', '');

            $meta_description = $fields->addChild('field');
            $meta_description->addAttribute('name', 'meta_description');
            $meta_description->addAttribute('type', 'text');
            $meta_description->addAttribute('filter', 'string');
            $meta_description->addAttribute('label', JText::sprintf('COM_CLASSIFICATION_PROPERTY_TYPE_META_DESCRIPTION', $type->title));
            $meta_description->addAttribute('multiple', true);
            $meta_description->addAttribute('default', '');
        }

        $form->load($addform);
    }

    public function save($data)
    {

        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_attributes/models');
        $model = JModelLegacy::getInstance('Attributes', 'AttributesModel', $config = array('ignore_request' => true));

        $model->setState('filter.attribute_type_id', 1);
        $model->setState('list.limit', '');

        $property_types = $model->getItems();

        $property_type_data = array();

        foreach ($property_types as $type)
        {

            $name = JStringNormalise::toDashSeparated(JApplication::stringURLSafe($type->title));
            $property_type_data[$name] = $data[$name];
        }
        
        // Wrap up the property type data present and save 'em
        if (count($property_type_data) > 0)
        {
            $registry = new JRegistry;
            $registry->loadArray($property_type_data);
            $data['property_type_info'] = (string) $registry;
        }

        return parent::save($data);
    }

}
