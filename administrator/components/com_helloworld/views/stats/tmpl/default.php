<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart);
  function drawChart() {
    // Create the data table.
    var data = new google.visualization.DataTable();
    data.addColumn('date', 'Month');

    data.addColumn('number', 'Property view count');
    data.addRows([

<?php foreach ($this->data as $date => $data) : ?>
  <?php
  $date_parts = explode('-', $date);
  echo '[new Date(' . $date_parts[1] . ',' . ($date_parts[0] - 1) . ',1)' . ', ' . $data['views'] . '],'
  ?>

<?php endforeach; ?>
    ]);

    var options = {
      title: 'Property statistics',
      width:'100%',
      height:'100%',
      chartArea:{left:30},
      theme:'maximised',
      focusTarget:'category'
    };
    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
    chart.draw(data, options);
  }
</script>

<div id="chart_div"></div>

