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

    // Get a pData instance
    $graph_data = new pData;

    /* Create the pChart object */
    $myPicture = new pImage(700, 230, $graph_data);

    // Get the id from the model state
    $id = $this->getState('stats.id');

    // Get the property views and what not
    $data = $this->getData($id, '#__property_views');

    // Gets an array of the last X months in reverse order
    for ($x = 0; $x < 12; $x++) {
      $months[] = date('mY', mktime(0, 0, 0, date('m') - $x, 1));
    }

    // Sort and then reverse the array
    ksort($months, SORT_DESC);
    $months = array_reverse($months);

    // Set up an array to hold the series data
    $count = array();

    // Based on each month, we loop over the property data and if data exists for that month add it to the array
    foreach ($months as $key => $value) {

      if (array_key_exists($value, $data)) {
        $count[] = $data[$value];
      } else {
        $count[] = 0;
      }
    }


    // Gets an array of the last X months in reverse order
    for ($x = 0; $x < 12; $x++) {
      $formatted_months[] = date('M', mktime(0, 0, 0, date('m') - $x, 1));
    }

    // Sort and then reverse the array
    ksort($formatted_months, SORT_DESC);

    $formatted_months = array_reverse($formatted_months);

    $graph_data->addPoints($count, 'Page views');

    $graph_data->setSerieOnAxis("Page views", 0);

    $graph_data->setSerieWeight("Page views",1);

    $graph_data->setAxisName(0, "Total");

    /* Bind a data serie to the X axis */
    $graph_data->addPoints($formatted_months, "Labels");
    $graph_data->setSerieDescription("Labels", "Months");
    $graph_data->setAbscissa("Labels");

    /* Choose a nice font */
    $myPicture->setFontProperties(array("FontName" => JPATH_SITE . "/libraries/pchart/fonts/verdana.ttf", "FontSize" => 11));

    /* Define the boundaries of the graph area */
    $myPicture->setGraphArea(60, 40, 670, 190);
    $myPicture->drawText(0, 0, JTEXT::_('Property Statistics for ' . (int) $id), array("FontSize" => 20, "Align" => TEXT_ALIGN_TOPLEFT));

    /* Draw the scale, keep everything automatic */
    $myPicture->drawScale(array("GridR" => 180, "GridG" => 180, "GridB" => 180, "DrawSubTicks" => true));

    /* Write a legend box */
    $myPicture->setFontProperties(array("FontName" => JPATH_SITE . "/libraries/pchart/fonts/verdana.ttf", "FontSize" => 9, "R" => 80, "G" => 80, "B" => 80));
    $myPicture->drawLegend(250, 50, array("Style" => LEGEND_BOX, "BoxSize" => 4, "R" => 200, "G" => 200, "B" => 200, "Surrounding" => 20, "Alpha" => 30));

    /* Draw the scale, keep everything automatic */
    $myPicture->drawLineChart();

    $filename = "_stats" . (int) $id . '.png';

    /* Render the picture */
    $myPicture->Render(JPATH_SITE . "/cache/" . $filename);

    echo '<img src = "' . JURI::root() . '/cache/' . $filename . '" />';

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
       concat(month(date_created), year(date_created)) as month
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
      $data['0' . $value['month']] = $value['count'];
    }

    return $data;
  }

}
