(function($){
  $(document).ready(function(){
   if ($('.div-description-imgs').length > 0) {
  	 $('.div-description-imgs').slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: true,
        arrows: false,
        adaptiveHeight: true
     });
     $('.deslick_left').on('click',function(){
        $('.div-description-imgs').slick('slickPrev');
     });
     $('.deslick_right').on('click',function(){
        $('.div-description-imgs').slick('slickNext');
     });
   }
  });
})(jQuery);

jQuery(document).ready(function ($) {
    var $section = $('#de_refe_section');
    var $image = $('.colonne_itineraires_map img');
    var $wrapper = $('.itineraires-wrapper');
    if ($image.length>0) {
        var imageHeight = $image.outerHeight();
        var wrapperHeight = $wrapper.outerHeight();
        var $imageleft = $image.offset().left;
        var $imagewidth = $image.width();
        if ($(window).width() > 768) {
            $(window).scroll(function () {
                var scrollTop = $(window).scrollTop();
                var sectionOffset = $section.offset().top;
                var triggerPoint = sectionOffset - 200;
    
                if (wrapperHeight > imageHeight) {
                    if (scrollTop >= triggerPoint && scrollTop <= (sectionOffset + wrapperHeight - imageHeight)) {
                        $image.css({
                            'position': 'fixed',
                            'top': '200px',
                            'left': '' + $imageleft + 'px'
                        });
                    } else {
                        $image.css('position', 'static');
                    }
                }
            });
        }
    }
    if($('#formulaire_dedevis').length > 0){
        $('.bouton_circuits').on('click',function(e){
            e.preventDefault();
            $('#formulaire_dedevis').fadeIn('slow');
            $('body').css('overflow','hidden');
        });
        $('#formulaire_dedevis .et-pb-icon').on('click',function(){
            $('#formulaire_dedevis').fadeOut('slow');
            $('body').css('overflow','visible');
        });
    }
});