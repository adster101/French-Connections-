window.addEvent('domready', function(){
  
  // Create an instances array to track all the instances we create
  var instances = {};
  
  new Picker.Date($('jform_tariffs_start_date_tariff_0'),{
    toggle: $('jform_tariffs_start_date_tariff_0_img'),
    onSelect: function(date){
            
      if (instances['jform_tariffs_end_date_tariff_0'] == null) {

       picker = new Picker.Date($('jform_tariffs_end_date_tariff_0'),{
          minDate:date
        });        
        instances['jform_tariffs_end_date_tariff_0'] = picker;      
      } else {
        instances['jform_tariffs_end_date_tariff_0'].destroy()
         picker = new Picker.Date($('jform_tariffs_end_date_tariff_0'),{
          minDate:date
        });        
        instances['jform_tariffs_end_date_tariff_0'] = picker;            

      }
        
       
        console.log(instances['jform_tariffs_end_date_tariff_0']);

    
    }
  })
})