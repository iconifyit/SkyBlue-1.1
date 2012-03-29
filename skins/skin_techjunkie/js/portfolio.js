// Portfolio Page - Adds an individual class to each item... i.e. the first list item = 1 the second = 2 the third = 3 etc...
$(function() {	
	$("#portfolio-content li").each(function (i) {
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
	
	Cufon.replace('#sub-menu li a');
	Cufon.replace('.portfolio-description h1');
});