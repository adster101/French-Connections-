<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modellegacy');

// Import the graph gubbins
jimport('pchart.class.pDraw');
jimport('pchart.class.pImage');

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
    // Get a pData instance
    $graph = new pDraw();

    $id = $this->getState('stats.id');

    // Get the property views and what not
    $data = $this->getData($id, '#__property_views');

    foreach ($data as $key => $value) {
      /* Save the data in the pData array */
      $graph->addPoints($value->year, "Year");
      $graph->addPoints($value->month, "Month");
      $graph->addPoints($value - count, "Count");
    }

    $myData->setAbscissa("Month");

    /* Associate the "Humidity" data serie to the second axis */
    $myData->setSerieOnAxis("Count", 1);

    /* Name this axis "Time" */
    $myData->setXAxisName("Count");

    /* Specify that this axis will display time values */
    $myData->setXAxisDisplay(AXIS_FORMAT_TIME, "H:i");


    return $data;
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
       month(date_created) as month,
       year(date_created) as year,
       count(id) as count
     ');
    $query->from($table);
    $query->where('property_id = ' . (int) $id);
    $query->where('date_created > ' . $db->quote(date('Y-m-d', $last_year)));
    $query->group('YEAR(date_created)');
    $query->group('month(date_created)');

    $db->setQuery($query);

    $rows = $db->loadObjectList();

    return $rows;
  }

}
