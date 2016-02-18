jQuery(document).ready(function () {

  /*
   * Availability period date picker
   * 
   * 1. When user clicks start date mark the calendar form as 'updated'
   * 2. When user clicks end date (e.g. when date is greater thatn start date) and calendar is edited
   *    2a. Show the update form prepopulated with chosen start and end dates and options to choose availability status
   * 3. User clicks update and ajax request is sent to update the calendar as appropriate
   * 4. Validation (JForm in controller) on server side for date and availability choice
   * 5. Update calendar to reflect chosen dates
   * 6. If user clicks off calendar reset it
   * 7. If user cancels from modal also reset chosen dates
   * 
   */


  jQuery(function () {

    bind_hover();

    jQuery('#availabilityModal').on('hidden', function () {
      // Clear the current selections...
      reset();
    })

    jQuery('#availabilityModal').on('shown', function () {

      // Position modal absolute and bump it down to the scrollPosition
      jQuery(this)
              .css({
                position: 'absolute',
                marginTop: jQuery(window).scrollTop() + 'px',
                bottom: 'auto',
                top: '0'
              });

    });



    // Prevent the default click behaviour on the calendar link elements
    jQuery('.avCalendar td a').on('click', function (e) {

      // Prevent the default click behaviour
      e.preventDefault();

      // Get the date that has been clicked...
      var date = jQuery(this).parent('td').attr('data-date');


      // Check if the form has already been updated        
      var form = jQuery('form#adminForm.updated');

      // Existing availability status
      var existing = jQuery(this).parent('td').hasClass('available');


      if (form.length > 0) {

        jQuery(this).addClass('end');


        // TO DO: Check that start and end dates are valid, e.g. end date is after start date



        // Update the start/end date depending on what's been chosen
        jQuery('#adminForm #jform_end_date').attr('value', date);


        // Should have a valid start and end date here...
        jQuery('#availabilityModal').modal('show');

        // TO DO: Disable the save button, show a working spinners (when the save button is pressed)

      } else {

        jQuery(this).addClass('start');

        jQuery('#adminForm #jform_start_date').attr('value', date);

        // Add a 'updated' class to the modal form
        jQuery('form#adminForm').addClass('updated');



        // Need some mechanism to indicate the users intent




      }

      // Rebind the hover event to update the tooltip that is displayed
      bind_hover();
    })
  });


})

/* 
 * Function which resets the current selections
 */
function reset() {

  var start_date = jQuery('#jform_start_date').val('');
  var end_date = jQuery('#jform_end_date').val('');
  var status = jQuery('#jform_availability').val('');
  jQuery('form#adminForm').removeClass('updated');
  jQuery('.avCalendar td a').removeClass('edited start end');
}

/* 
 * Function to bind a tooltip to the hover event on the calendar date picker
 * 
 * 
 */
function bind_hover() {

  // Remove any existing tooltips 
  jQuery('.avCalendar td a').tooltip('destroy');

  // And remove any existing hover events
  jQuery('.avCalendar td a').unbind('mouseenter mouseleave');


  // This adds a tooltip to the date the user is hovering over...
  jQuery('.avCalendar td a').hover(function () {

    // The availability form DOM element
    var form = jQuery('form#adminForm.updated');

    // The start date
    var start_date = jQuery('#jform_start_date').val();

    // The date being hovered over
    var hover_date = jQuery(this).parent().attr('data-date');

    if (form.length > 0) { // Form has already been updated, presumabely with a start date

      var title = Joomla.JText.strings.COM_HELLOWORLD_HELLOWORLD_AVAILABILITY_CHOOSE_END_DATE;

    } else {

      var title = Joomla.JText.strings.COM_HELLOWORLD_HELLOWORLD_AVAILABILITY_CHOOSE_START_DATE;
    }



    // Add the relevant tooltip
    jQuery(this).tooltip({
      placement: 'top',
      title: title,
      trigger: 'hover'
    }).tooltip('show');

    // After creating the tooltip, 

  }, function () { // mouse off call back function
    jQuery(this).tooltip('hide');
  })
}
