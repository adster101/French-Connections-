Joomla.submitbutton = function(task)
{
	if (task == '')
	{
		return false;
	}
	else
	{
		var isValid=true;
		var action = task.split('.');
		if (action[1] != 'cancel' && action[1] != 'close')
		{
			var forms = $$('form.form-validate');
			for (var i=0;i<forms.length;i++)
			{
				if (!document.formvalidator.isValid(forms[i]))
				{
					isValid = false;
					break;
				}
			}
		}

		if (isValid)
		{
			Joomla.submitform(task);
			return true;
		}
		else
		{
			alert(Joomla.JText._('COM_HELLOWORLD_HELLOWORLD_UNSAVED_CHANGES','Are you sure?'));
			return false;
		}
	}
}



  // un-Binds the beforeunload event from the window object.
  // We don't want this show if e.g. user is trying to save
	// Only required on sign up forms
	function unbindBeforeUnload() {
		window.onbeforeunload = '';
	}

  // Binds the beforeunload event to the window object.
	// Should only be called if a specific form has been changed.
	function bindBeforeUnload(event) {

	}

// When the DOM is ready add the change handler to the form.
window.addEvent('domready', function() {

  $$('form.form-validate')[0].addEvent("change", function(){
    //window.onbeforeunload = function() {
			//return "Bye";
		//};
  })

  // Manually trigger the change event if a calendar is clicked...
  $$('.calendar').addEvent("click", function(event){
    event.stop();
    $$('form.form-validate')[0].fireEvent('change');
  })

});


