<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// TO DO: tidy this up either using the addscriptdeclaration or by putting this into a helper
// etc or at least a better formatted string
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


<?php foreach ($this->data['enquiries'] as $date => $data) : ?>
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

<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart2);

  function drawChart2() {
    // Create the data table.
    var data2 = new google.visualization.DataTable();
    data2.addColumn('date', 'Month');

    data2.addColumn('number', 'Property enquiries');
    data2.addColumn('number', 'Website click throughs');
    data2.addRows([


<?php foreach ($this->data['enquiries'] as $date => $data) : ?>
  <?php
  $date_parts = explode('-', $date);
  echo '[';
  echo 'new Date(' . $date_parts[1] . ',' . ($date_parts[0] - 1) . ',1)' . ', ' . $data['enquiries'] . ',' . $data['clicks'];
  echo '],';
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
    var chart = new google.visualization.LineChart(document.getElementById('chart_div2'));
    chart.draw(data2, options);
  }
</script>

<div id="chart_div"></div>
<div id="chart_div2"></div>

