
function updateOtherOne() {
  
  var start_date;
  var end_date;
  
  start_date = $('jform_tariffs_start_date_tariff_0').getProperty('value');
  
  $('jform_tariffs_end_date_tariff_0').setProperty('value', '');
 
var calendar = new Calendar(0, new Date('2013-01-01'), null, onSelect(calendar));
calendar.create();
calendar.show();

}

function onSelect(calendar, date) {
  var input_field = document.getElementById("jform_tariffs_end_date_tariff_0");
  input_field.value = date;
  calendar.dateClicked = true;
  if (calendar.dateClicked) {
    calendar.callCloseHandler(); // this calls "onClose" (see above)
  }
};

      