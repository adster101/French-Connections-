
jQuery(document).ready(function(){

 jQuery('.map').maphilight();	


  jQuery('a[data-toggle="tab"]').on('shown', function (e) {
    
    // Store the selected tab #ref in local storage, IE8+ 
    localStorage['selectedTab']=jQuery(e.target).attr('href');
    
    // Get the selected tab from the local storage
    var selectedTab = localStorage['selectedTab'];

    if (selectedTab == '#mapsearch') {
      
      initmap();
      
      // Get jiggy with the Google Maps, innit!
      
      // Do an ajax call to populate the list of nearest towns...
      jQuery.getJSON("/administrator/index.php?option=com_fcsearch&task=mapsearch.getresults&format=json",
          
          {
            
            alias:'south-of-france',
            format:"json"
          },
          function(data){
            var options = '';
            for (var i = 0; i < data.length; i++) {
              options += '<option value="' + data[i].id + '">' + data[i].title + '</option>';
            }
            jQuery("select#jform_city").html(options);        

          } 
        );  
      
      
     
      
    }
    
    
   
    
      
  });

  // Get the selected tab, if any and set the tab accordingly...
  var selectedTab = localStorage['selectedTab'];  
  jQuery('.nav li a[href="'+selectedTab+'"]').tab('show');
  
  
  
  
  
  
  

  

  jQuery('#property-search-button').click(function(event){
    
    // val is the 'active' suggestion populated by typeahead
    // e.g. the option chosen should be the last active one
    var val = jQuery(".typeahead.dropdown-menu").find('.active').attr('data-value');
   
    // The value contained in the typeahead field
    var chosen = jQuery(".typeahead").attr('value');
    
    // Double check that the typeahead has any elements, if not then it means it's already populated, e.g. when you land on a search results page
    var count = jQuery(".typeahead.dropdown-menu li").length;


    // If chosen not empty and count not zero, chances are we have a auto suggestion choice
    if (chosen !== '' && count !== 0) {
      if ( val !== chosen) {
        jQuery('#myModal').modal();
        return false;
      }
    } else if (chosen == '') { // otherwise, just check that the chosen field isn't empty...check the q var on the server side
      jQuery('#myModal').modal();
      return false;      
    }
    
    // Form checks out, looks like the user chose something from the suggestions
    // Strip the string to make it like classifications table alias
    var query = stripVowelAccent(chosen);   

    // The path of the search, e.g. /search or /fr/search
    var path = jQuery('form#property-search').attr('action');  

    // Set the value of s_kwds to the search string alias
    jQuery('#s_kwds').attr('value',query);
    
    // Get the number of bedrooms chosen in the search
    bedrooms = jQuery('#search_bedrooms').attr('value');
    
    
    // Amend the path that the form is submitted to
    jQuery('form#property-search').attr('action', path+'/'+query+'/bedrooms_'+bedrooms);

    // Submit the form
    jQuery('form#property-search').submit();
    
    event.preventDefault();  
    return false;
    
  })

 

  
  jQuery(".typeahead").typeahead({
     
    source: function (query, process) {
      jQuery.get( 'index.php?option=com_fcsearch&task=suggestions.display&format=json&tmpl=component&lang=en', 
      { 
        q: query,
        items: 10
      }, 
      function (data) {
        process(data);
      }
      )
    }
  })
})

function initmap() {
  
    jQuery('#map').css('width','100%');
    jQuery('#map').css('height','500px');

    var myLatLng = new google.maps.LatLng(46.2,2.8);
    var myOptions = {
      center: myLatLng,
      zoom: 6,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      disableDefaultUI: true,
      zoomControl:true
    }
    var map = new google.maps.Map(document.getElementById("map"), myOptions);

  }

// Function removes and replaces all French accented characters with non accented characters
// as well as removing spurious characters and replacing spaces with dashes...innit!
function stripVowelAccent(str) {

  var s=str;
  var rExps=[ 
  /[\xC0-\xC2]/g, 
  /[\xE0-\xE2]/g,
  /[\xC8-\xCA]/g,
  /[\xE8-\xEB]/g,
  /[\xCC-\xCE]/g,
  /[\xEC-\xEE]/g,
  /[\xD2-\xD4]/g,
  /[\xF2-\xF4]/g,
  /[\xD9-\xDB]/g,
  /[\xF9-\xFB]/g
  ];

  var repChar=['A','a','E','e','I','i','O','o','U','u'];

  for(var i=0; i<rExps.length; i++) {
    s=s.replace(rExps[i],repChar[i]);
  }

  // Replace braces with spaces
  s=s.replace(/[()]/g,' ');

  // Replace apostrophe with space
  s=s.replace(/[\']/g,' ');

  if(typeof String.prototype.trim !== 'function') {
    String.prototype.trim = function() {
      return this.replace(/^\s+|\s+$/g, ''); 
    }
  }

  // Trim any trailing space
  s=s.trim();

  // Remove any duplicate whitespace, and ensure all characters are alphanumeric
  s=s.replace(/(\s|[^A-Za-z0-9\-])+/g,'-');



  s=s.toLowerCase();

  return s;
}