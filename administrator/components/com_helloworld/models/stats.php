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

  public function getGraph() {


    // Get the id from the model state
    $id = $this->getState('stats.id');

    // Get the property views and what not
    $data = $this->getData($id, '#__property_views');

    $months = array();

    // Gets an array of the last X months in reverse order
    for ($x = 0; $x < 12; $x++) {
      $months[] = date('m-Y', mktime(0, 0, 0, date('m') - $x, 1));
    }


    // Sort and then reverse the array
    ksort($months, SORT_DESC);
    $months = array_reverse($months);


    // Set up an array to hold the series data
    $count = array();

    // Based on each month, we loop over the property data and if data exists for that month add it to the array
    foreach ($months as $key => $value) {

      if (array_key_exists($value, $data)) {
        $count[$value]['views'] = $data[$value]['count'];
      } else {

        $count[$value]['views'] = 0;
      }
    }


    return $count;
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

}
