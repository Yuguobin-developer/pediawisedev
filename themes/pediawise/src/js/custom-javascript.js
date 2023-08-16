// Add your custom JS here.

(function($){
  $(document).ready(function(){

    // Home page slider part
    if ($('#about-us .feed-wrp').length) {
      $('#about-us .feed-wrp').slick({
        slidesToShow: 2,
        slidesToScroll: 1,
        autoplay: false,
        autoplaySpeed: 3000,
        infinite: true,
        arrows: true,
        dots: false,
        responsive: [
          {
            breakpoint: 1200,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]
      });
    }

    // Pediafeed page slider part
    if ($('.feed-page .posts-section .post-slider-wrap').length) {
      $('.feed-page .posts-section .post-slider-wrap').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: false,
        autoplaySpeed: 3000,
        infinite: true,
        arrows: true,
        dots: false
      });
    }

    /**
     * Baby Page sliders
     */
    // Hot Topics Post silder
    if ($('.baby-page .hot-topics-section .post-slider-wrap').length) {
      $('.baby-page .hot-topics-section .post-slider-wrap').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: false,
        autoplaySpeed: 3000,
        infinite: true,
        arrows: true,
        dots: false,
        responsive: [
          {
            breakpoint: 768,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1
            }
          },
          {
            breakpoint: 552,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]
      });
    }

    // Freebies Post silder
    if ($('.baby-page .freebies-section .post-slider-wrap').length) {
      $('.baby-page .freebies-section .post-slider-wrap').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: false,
        autoplaySpeed: 3000,
        infinite: true,
        arrows: true,
        dots: false
      });
    }

    // top 10s section
    if ($('.baby-page .top-10s-section .post-slider-wrap').length) {
      $('.baby-page .top-10s-section .post-slider-wrap').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: false,
        autoplaySpeed: 3000,
        infinite: true,
        arrows: true,
        dots: false,
        responsive: [
          {
            breakpoint: 768,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1
            }
          },
          {
            breakpoint: 552,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]
      });
    }

    // top 10s section
    if ($('.baby-page .question-of-the-week-section .post-slider-wrap').length) {
      $('.baby-page .question-of-the-week-section .post-slider-wrap').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: false,
        autoplaySpeed: 3000,
        infinite: true,
        arrows: true,
        dots: false,
        responsive: [
          {
            breakpoint: 768,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1
            }
          },
          {
            breakpoint: 552,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]
      });
    }

    /**
     * Topics page slider
     */
     // top 10s section
    if ($('.topics-page .pediafeed-section .posts').length) {
      $('.topics-page .pediafeed-section .posts').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: false,
        autoplaySpeed: 3000,
        infinite: true,
        arrows: true,
        dots: false,
        responsive: [
          {
            breakpoint: 768,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1
            }
          },
          {
            breakpoint: 552,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]
      });
    }

    /**
     * Single Post Page Slider
     */
    // Related Posts Section
    if ($('.single-post-page .related-posts-section .related-posts-wrp').length) {
      $('.single-post-page .related-posts-section .related-posts-wrp').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: false,
        autoplaySpeed: 3000,
        infinite: true,
        arrows: true,
        dots: false,
        responsive: [
          {
            breakpoint: 768,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1
            }
          },
          {
            breakpoint: 552,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]
      });
    }

    /**
     * Source collapse event 
     */
    $('.sources-section .section-title').click(function() {
      if ($(this).hasClass('source-collapse')) {
        $(this).removeClass('source-collapse');
        $(this).parent().parent().find('.source-content').eq(0).show();
      } else {
        $(this).addClass('source-collapse');
        $(this).parent().parent().find('.source-content').eq(0).hide();
      }
    });

  }); 

})(jQuery);
