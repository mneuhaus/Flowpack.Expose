// @codekit-prepend "Form/jquery.inlinehelper.js"
// @codekit-prepend "Form/Form.js"
// @codekit-prepend "../components/chosen/chosen/chosen.jquery.min.js"

jQuery(document).ready(function(){
	jQuery("input[type='text']:first").focus();

	// Table select all behavior
	jQuery(".select-all").change(function(){
		var table = jQuery(this).parents("table");
		if(jQuery(this).attr('checked') == "checked")
			table.find(".select-row").attr('checked', "checked");
		else
			table.find(".select-row").removeAttr('checked');
	});

	jQuery(".inline[data-mode='multiple']").inlineHelper();
});