jQuery.fn.extend({
  scrollTo : function(speed, easing) {
    return this.each(function() {
      var targetOffset = $(this).offset().top;
      $("html,body").animate({scrollTop: targetOffset}, speed, easing);
    });
  }
});

window.heights = [];

function toggleButtonOptions(type) {
    if (type == "file") {
    	hideBlock(".validations_block");
    	$(".validation_option").each(function() {
    	    $(this).attr("checked", false);
    	});
    }
	else if (type != "button") {
		$("textarea[@name='button_onclick']").val("");
		showBlock(".validations_block");
	}
	else {
		hideBlock(".validations_block");
		$(".validations_block").find("input[@type='checkbox']").each(function() {
			$(this).attr("checked", false);
		});
		$(".validations_block").find("input[@type='text']").each(function() {
			$(this).val("");
		});
		$(".validations_block").find("textarea").each(function() {
			$(this).val("");
		});
	}
};

function hideBlock(selector) {
    $(selector).fadeOut(1000);
};

function showBlock(selector) {
    $(selector).fadeIn(1000);
};

function toggleCheckedBoxes(selectors, value) {
    for (var i=0; i<selectors.length; i++) {
        $(selectors[i]).attr("checked", value);
    }
};

$(function() {
    $(".scrollTo").bind("click", function() {
        $($(this).attr("href")).scrollTo(500);
    });
	$("select[@name='fieldtype']").bind("change", function() {
		var type = $(this).val();
		hideBlock("." + window.fieldtype);
		window.fieldtype = type;
		showBlock("." + type);
		toggleButtonOptions(type);
	});
	$("select[@name='fieldtype']").find("option").each(function() {
		if ($(this).attr("selected")) {
			var type = $(this).val();
			window.fieldtype = type;
			try {
				showBlock("." + type);
			}
			catch(e) {/* fail silently */}
			toggleButtonOptions(type);
		}
	});
	
	$(".validation_option").bind("click", function() {
		var type = $(this).val();
		if ($(this).attr("checked")) {
			showBlock("." + type);
			if (type == "email") {
				toggleCheckedBoxes([
					"input[@value='url']",
					"input[@value='number']",
					"input[@value='regex']"
				], false);
				hideBlock(".regex");
			}
			else if (type == "url") {
				toggleCheckedBoxes([
					"input[@value='email']",
					"input[@value='number']",
					"input[@value='regex']"
				], false);
				hideBlock(".regex");
			}
			else if (type == "number") {
				toggleCheckedBoxes([
					"input[@value='url']",
					"input[@value='email']",
					"input[@value='regex']"
				], false);
				hideBlock(".regex");
			}
			if (type == "regex") {
				toggleCheckedBoxes([
					"input[@value='url']",
					"input[@value='number']",
					"input[@value='email']"
				], false);
				// hideBlock(".regex");
			}
		}
		else {
			$("input[@name='" + type + "']").val("");
			hideBlock("."+type);
		}
		var optionSelected = false;
		$(".validation_option").each(function() {
			if ($(this).attr("checked")) {
				$("#validation_error_message").show();
				optionSelected = true;
			}
		});
		if (!optionSelected) {
			hideBlock("#validation_error_message");
		}
	});
	$(".validation_option").each(function() {
		if ($(this).attr("checked")) {
			var type = $(this).val();
			try {
				showBlock("."+type);
			}
			catch(e) {/* fail silently */}
			showBlock("#validation_error_message");
		}
	});
	$("input[@value='Save Field']").bind("click", function(e) {
		var isFieldError = false;
		var type = "";
		var cssClass = $("input[@name='class']").val();
		if ($.trim(cssClass) != "" && !/^[a-zA-Z]+[a-zA-Z0-9_-]*/.test(cssClass)) {
			isFieldError = true;
			errorMessage = "That does not appear to be a valid CSS selector for CSS Class";
		}
		$(".validation_option").each(function() {
			if ($(this).attr("checked")) {
				type = $(this).val();
				if ($("input[@name='" + type + "']").val() == "") {
					isFieldError = true;
					errorMessage = "Please enter a value for '" + type + "'";
				}
			}
		});
		if (isFieldError) {
			e.preventDefault();
			alert(errorMessage);
		}
	});
});