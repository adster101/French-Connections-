<?php
/**
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
/**
 * Joomla User plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	User.joomla
 * @since		1.5
 */
class plgLettingTariffs extends JPlugin
{

  
	/** 
	 * @param	JForm	$form	The form to be altered.
	 * @param	array	$data	The associated data for the form.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	function onContentPrepareForm($form, $data)
	{
    // Check that this is an instance of a JForm and not some other object.
		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}
   
		// Check we are manipulating a valid form.
		$name = $form->getName();
    
    // Make sure we only update the tariffs form
    if (!in_array($name, array('com_helloworld.tariffs')))
		{
			return true;
		}		
        
    // Build an XML string to inject additional fields into the form
    $XmlStr = '<form>';
    $counter=0;
    $XmlStr.='<fieldset name="tariffs">';
    // Loop over the existing availability first
    foreach ($data->tariffs as $tariff) {
      
      // Ignore the first 'tariff' as it is an error counter added by the load db table instance
      if(count($tariff)) {
        
        $XmlStr.= '<fields name="tariffs_'.$counter.'">
        <field
          name="start_date"
          type="calendar"
          label="COM_HELLOWORLD_AVAILABILITY_FIELD_START_DATE_LABEL"
          description="COM_HELLOWORLD_AVAILABILITY_FIELD_START_DATE_DESC"
          size="20"
          class="inputbox"
          validate=""
          required="false"
          multiple="true"
          default="'.$tariff->start_date.'">
        </field>
        <field
          name="end_date"
          type="calendar"
          label="COM_HELLOWORLD_AVAILABILITY_FIELD_END_DATE_LABEL"
          description="COM_HELLOWORLD_AVAILABILITY_FIELD_END_DATE_DESC"
          size="20"
          class="inputbox"
          validate=""
          required="false"
          default="'.$tariff->end_date.'"
          multiple="true">
        </field>
        <field          
          name="tariff"
          type="text"
          label="COM_HELLOWORLD_TARIFFS_FIELD_TARIFF_LABEL"
          description="COM_HELLOWORLD_TARIFFS_FIELD_TARIFF_DESC"
          size="20"
          class="inputbox"
          validate=""
          required="false"
          default="'.$tariff->tariff.'"
          multiple="true"/></fields>';
        $counter++;
      }
      
      for($i=$counter;$i<=$counter+5;$i++) {
        $XmlStr.= '<fields name="tariffs_'.$i.'">
        <field
          name="start_date"
          type="calendar"
          label="COM_HELLOWORLD_AVAILABILITY_FIELD_START_DATE_LABEL"
          description="COM_HELLOWORLD_AVAILABILITY_FIELD_START_DATE_DESC"
          size="20"
          class="inputbox"
          validate=""
          required="false"
          multiple="true"
          default="">
        </field>
        <field
          name="end_date"
          type="calendar"
          label="COM_HELLOWORLD_AVAILABILITY_FIELD_END_DATE_LABEL"
          description="COM_HELLOWORLD_AVAILABILITY_FIELD_END_DATE_DESC"
          size="20"
          class="inputbox"
          validate=""
          required="false"
          default="">
          multiple="true">
        </field>
        <field          
          name="tariff"
          type="text"
          label="COM_HELLOWORLD_TARIFFS_FIELD_TARIFF_LABEL"
          description="COM_HELLOWORLD_TARIFFS_FIELD_TARIFF_DESC"
          size="20"
          class="inputbox"
          validate=""
          required="false"
          default=""
          multiple="true" 
          />
        </fields>';
      }
    }
    
    $XmlStr.='</fieldset></form>';
    $form->load($XmlStr);
    return true; 
	}
}


