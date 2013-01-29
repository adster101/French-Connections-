jQuery(document).ready(function() {
  
 
  // Iterate over all the form fields in the contact form 
  jQuery('#adminForm label').each(function() {
    // Get the id of the label element and split it - derived the input field id
    var id = this.id.split('-');
    // Get the title and content 
    var text = this.title.split('::');
    // Prime each element with a popover on focus
    popover = jQuery('#'+id[0]).popover({
      title:text[0],
      content:text[1],
      placement:'right',
      trigger:'focus'
    });
  });
});


window.addEvent('domready', function() {
  
  document.formvalidator.setHandler('name',
    function (value) {
      regex=/^[a-zA-Z]+$/;
      console.log(regex.test(value));
      return regex.test(value);
    });
  document.formvalidator.setHandler('telephone',
    function (value) {
      // Only allow digits, spaces and pluses
      regex=/^[\d +]{11,25}$/;
      return regex.test(value);
    });
  document.formvalidator.setHandler('message',
    function (value) {
      regex=/^[\w-\/., !"'\n]+$/;
      return regex.test(value);
    });
  document.formvalidator.setHandler('date',
    function (value) {
      regex=/^(\d{4})-(\d{2})-(\d{2})$/;
      return regex.test(value);
    });
 document.formvalidator.setHandler('numeric',
    function (value) {
      regex=/^[0-9]{1,2}/;
      return regex.test(value);
    });
    
   
}); 

  
  