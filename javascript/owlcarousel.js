/**
 * response carousel with previous and nextr button
 * see also owlcarousel.css
 */
$(document).ready
(
  function()
  {
    var owl = $('.owl-carousel');
     $('.owl-manual').owlCarousel
     (
       {
        loop:true,
         margin:10,
         nav:false,
        responsive:
        {
           0:    { items:1 },
           600:  { items:2 },
           1000: { items:4 }
        }
       }
    );
    // Custom Button
    $('.customNextBtn').click(function() 
    {
       owl.trigger('next.owl.carousel');
    });
    $('.customPreviousBtn').click(function() {
     owl.trigger('prev.owl.carousel');
    });
  }
);

/**
 * autoplay
 */
$(document).ready
(
  function()
  {
    var owl = $('.owl-carousel');
    $('.owl-carousel').owlCarousel
    (
       {
          loop: true,
          center: true,
          items: 3,
          margin: 30,
          autoplay: true,
          dots:true,
          nav:false,
          autoplayTimeout: 1500,
          smartSpeed: 500,
          navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
          responsive:
          {
              0:    { items:1 },
              600:  { items:2 },
              1000: { items:4 }
          }
       }
    );
  }
);
/*
jQuery(document).ready(function($) {
  "use strict";
  $('#customers-testimonials').owlCarousel( {
      loop: true,
      center: true,
      items: 3,
      margin: 30,
      autoplay: true,
      dots:true,
      nav:true,
      autoplayTimeout: 8500,
      smartSpeed: 450,
      navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
      responsive: {
        0: {
          items: 1
        },
        768: {
          items: 2
        },
        1170: {
          items: 3
        }
      }
    });
  });
  */