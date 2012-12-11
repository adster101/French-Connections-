
jQuery(document).ready(function(){

  jQuery('#property-search-button').click(function(event){
    
    // val is the 'active' suggestion populated by typeahead
    // e.g. the option chosen should be the last active one
    var val = jQuery(".typeahead.dropdown-menu").find('.active').attr('data-value');
   
    // The value contained in the typeahead field
    var chosen = jQuery(".typeahead").attr('value');
    
    // Double check that the typeahead has any elements, if not then it means it's already populated, e.g. when you land on a search results page
    var count = jQuery(".typeahead.dropdown-menu").length;


    // The two should match as we want to ensure user enters destination    
    if (chosen !== '' && count !== 0) {
      if ( val !== chosen) {
        jQuery('#myModal').modal();
        return false;
      }
    } 
    
    
    // Form checks out, looks like the user chose something from the suggestions
    // Strip the string to make it like classifications table alias
    var query = stripVowelAccent(chosen);   
    
    //var path = '<?php echo JRoute::_(JURI::base() . 'search/') ?>';
    

    //jQuery('form#property-search').attr('action', path+query);

    console.log(query);
    jQuery('#s_kwds').attr('value',query);


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
    
    // Trim any trailing space
    s=s.trim();
    
    // Remove any duplicate whitespace, and ensure all characters are alphanumeric
    s=s.replace(/(\s|[^A-Za-z0-9\-])+/g,'-');

  
    
    s=s.toLowerCase();

    return s;
  }
})