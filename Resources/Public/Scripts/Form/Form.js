$(document).ready(function(){
	$(".t3-expose-inline[data-mode='multiple']").inlineHelper();
	$(".t3-expose-inline[data-mode='multiple']").parents("form").submit(function(){
		var form = $(this);
		form.find(".t3-expose-inline-item-unused").remove();
		// TODO: Remove the name attr from all form elements beneath unused instead
	});
	$(".chosen").chosen();
});