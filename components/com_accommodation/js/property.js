 jQuery(document).ready(function() {


  // The slider being synced must be initialized first
    jQuery('#carousel').flexslider({
      animation: "slide",
      controlNav: false,
      animationLoop: false,
      slideshow: false,
      itemWidth: 175,
      itemMargin: 5,
      asNavFor: '#slider'
    });
              
    jQuery('#slider').flexslider({
      animation: "slide",
      controlNav: false,
      animationLoop: false,
      slideshow: false,
      sync: "#carousel"
    });


  

  });

 

  