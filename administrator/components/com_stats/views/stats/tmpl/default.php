<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');

$search 

?>
<form action="<?php echo JRoute::_('index.php?option=com_stats'); ?>" method="post" name="adminForm" id="adminForm">

  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>
      <div id="filter-bar" class="btn-toolbar">
        <div class="filter-search btn-group pull-left">
          <label class="element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
          <input type="text" name="filter_search" 
                 class="input-xlarge"
                 id="filter_search" 
                 value="<?php echo $this->escape($this->state->get('filter.search')); ?>" 
                 title="<?php echo JText::_('COM_STATS_ENTER_PRN_TO_VIEW_STATS'); ?>" 
                 placeholder="<?php echo JText::_('COM_STATS_ENTER_PRN_TO_VIEW_STATS'); ?>" />        
        </div>
        <div class="btn-group pull-left hidden-phone">
          <button class="btn tip hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
          <button class="btn tip hasTooltip" type="button" onclick="document.id('filter_search').value = '';
              this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
        
        </div>
        
      </div>
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

      <?php echo JHtml::_('form.token'); ?>

    </div>
</form>