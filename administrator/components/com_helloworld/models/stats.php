<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modellegacy');

// Import the graph gubbins
jimport('pchart.class.pDraw');
jimport('pchart.class.pImage');
jimport('pchart.class.pData');

/**
 * HelloWorld Model
 */
class HelloWorldModelStats extends JModelLegacy {
  /*
   * The property id for the stats we are retrieving
   *
   */

  protected $id = '';

  public function getGraphData() {

    // Get the id from the model state
    $id = $this->getState('stats.id');
    
    // Set up an array to hold the series data
    $graph_data = array();
    
    // Get the various data to populate the report 
    $data = $this->getData($id, '#__property_views');
    $enquiry_data = $this->getData($id, '#__enquiries');
    $enquiry_data = $this->getData($id, '#__enquiries');
    $clickthrough_data = $this->getData($id, '#__website_views');

    // Gets an array of the previous twelve months
    $months = $this->getMonths();

    $view_data = $this->processData($months, $data, 'views');
    $enquiry_data = $this->processData($months, $enquiry_data, 'enquiries');
    $click_data = $this->processData($months, $clickthrough_data, 'clicks');
    
    $graph_data['enquiries'] = array_merge_recursive($enquiry_data, $click_data, $view_data);
    
    return $graph_data;
  }

  /*
   * This method adds data to a graph_data method which is then used to display property stats
   * 
   */
  public function processData($months = array(), $data = array(), $stat = '')
  {
    // Based on each month, we loop over the property data and if data exists for that month add it to the array
    foreach ($months as $key => $value) {

      if (array_key_exists($value, $data)) {
        $graph_data[$value][$stat] = $data[$value]['count'];
      } else {

        $graph_data[$value][$stat] = 0;
      }
    }
    
    return $graph_data;
    
  }  
  
  public function populateState() {
    $input = JFactory::getApplication()->input;
    $id = $input->getInt('id');

    $this->setState('stats.id', $id);
  }

  public function getData($id = '', $table = '') {

    // Add in the number of page view this property has had in the last twelve months...
    $now = date('Y-m-d');
    $last_year = strtotime("-1 year", strtotime($now));
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    $query->select('
       count(id) as count,
       concat(month(date_created), year(date_created)) as monthyear,
       month(date_created) as month,
       year(date_created) as year
     ');
    $query->from($table);
    $query->where('property_id = ' . (int) $id);
    $query->where('date_created > ' . $db->quote(date('Y-m-d', $last_year)));
    $query->group('YEAR(date_created)');
    $query->group('month(date_created)');
    $query->order('year(date_created) asc');
    $db->setQuery($query);

    $rows = $db->loadAssocList();



    $data = array();

    // Key the array on the months returned
    foreach ($rows as $key => $value) {
      $date = date('m-Y', mktime(0, 0, 0, $value['month'],1, $value['year']));
      $data[$date]['count'] = $value['count'];
      $data[$date]['month'] = $value['count'];
      $data[$date]['year'] = $value['count'];
    }

    return $data;
  }

  public function getMonths()
  {
    $months = array();

    // Gets an array of the last X months in reverse order
    for ($x = 0; $x < 12; $x++) {
      $months[] = date('m-Y', mktime(0, 0, 0, date('m') - $x, 1));
    }

    // Sort and then reverse the array
    ksort($months, SORT_DESC);
    $months = array_reverse($months);
    
    return $months;
    
  }
  
}
