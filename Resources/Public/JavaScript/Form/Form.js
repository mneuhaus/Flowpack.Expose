jQuery(document).ready(function(){
	jQuery(".t3-expose-inline[data-mode='multiple']").inlineHelper();
	jQuery(".t3-expose-inline[data-mode='multiple']").parents("form").submit(function(){
		var form = jQuery(this);
		form.find(".t3-expose-inline-item-unused").remove();
	});
});