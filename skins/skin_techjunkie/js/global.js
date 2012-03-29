(function($) {
    $(function(){
        
        // Search Form
        
        $('input, textarea').each(function () {
            if ($(this).val() == '') {
                $(this).val($(this).attr('name'));
            }
        }).focus(function () {
            $(this).removeClass('inputerror');
            if ($(this).val() == $(this).attr('name')) {
                $(this).val('');
            }
        }).blur(function () {
            if ($(this).val() == '') {
                $(this).val($(this).attr('name'));
            }
        });
        
        // Portfolio Page - Adds an individual class to each item... i.e. the first list item = 1 the second = 2 the third = 3 etc...
        
        $("#portfolio-content li").each(function(i) {
            i = i+1;
            $(this).addClass("portfolio-item"+i);
        });
        
        // Portfolio Page - When you hover, the description pops up
    
        $('.portfolio-description').css("display", "none");
        
        $('.portfolio-description').animate({
            "opacity" : "0.7"
        });
        
        $('.portfolio-item').hover(function(){
            $(this).children('.portfolio-description').slideDown();
        }, function(){
            $(this).children('.portfolio-description').slideUp();
        });
        
        $(".cufon").append("<span></span>");
        
        Cufon.replace('.title, .f-title, .heading-lg, .heading-sm');

        Cufon.replace('h1,h2,h3,h4,h5,h6');

        Cufon.replace('#menu li a');
        Cufon.replace("#tab-nav a");
        
        override_google_styles();
        $("#search-input").focus(override_google_styles);
        $("#search-input").blur(function() { 
            override_google_styles(); 
            $("#search-input").val(""); 
        });
        $("#search-input").change(override_google_styles);
        $("#search-input").mousedown(override_google_styles);
        $("#search-input").mouseup(override_google_styles);
        $("#search-input").val("");
    });
    
    function override_google_styles() {
        $("#search-input").attr("style", "");
    };
})(jQuery);