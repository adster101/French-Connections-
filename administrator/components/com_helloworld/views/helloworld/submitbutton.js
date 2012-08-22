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
           var invalid = $$('fieldset.invalid');  

           // If there are invalid fieldsets 
           if(invalid.length) {
             // Find the active slide
             invalid.each(function(el){
               var panel = el.getParents('.pane-slider');
               var bar = panel.getSiblings('h3')[0];

               panel.setStyle('height','auto');
               panel.removeClass('pane-hide').addClass('pane-down');
               bar.removeClass('pane-toggler').addClass('pane-toggler-down');
             });
           }
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
			alert(Joomla.JText._('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE','Some values are unacceptable'));
			return false;
		}
	}
}


 // Helper function to retrieve the parent slide
    function getSlide(el)
    {
        while(!el.hasClass('slider_item')) {
            el = el.getParent();
        }

        var identifier = el.getProperty('id').replace('slider_item_', '');
        el.id = identifier;
        el.c_id = identifier.split('-')[0];
        el.nr = parseInt(identifier.split('-')[1]);

        return el;
    }