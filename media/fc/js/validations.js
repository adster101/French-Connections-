/* 
 * These validation rules apply to any form that has these validations added to the field.
 * 
 * Need to determine when and where to load them.
 * 
 */

window.addEvent('domready', function() {

  document.formvalidator.setHandler('name', function(value) {
    regex = /^[a-zA-Z]+$/;
    console.log(regex.test(value));
    return regex.test(value);
  });
  document.formvalidator.setHandler('telephone', function(value) {
    // Only allow digits, spaces and pluses
    regex = /^[\d +]{11,25}$/;
    return regex.test(value);
  });
  document.formvalidator.setHandler('message', function(value) {
    regex = /^[\w-\/., !"'\n]+$/;
    return regex.test(value);
  });
  document.formvalidator.setHandler('date', function(value) {
    regex = /^(\d{4})-(\d{2})-(\d{2})$/;
    return regex.test(value);
  });
  document.formvalidator.setHandler('numeric', function(value) {
    regex = /^[0-9]{1,2}/;
    return regex.test(value);
  });

  document.formvalidator.setHandler('occupancy', function(value) {
    regex = /^[^a-z]+$/;
    return regex.test(value);
  });
  document.formvalidator.setHandler('swimming', function(value) {
    regex = /^[0-1]+$/;
    return regex.test(value);
  });
});
