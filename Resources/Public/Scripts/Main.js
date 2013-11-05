// @codekit-prepend "Form/jquery.inlinehelper.js"
// @codekit-prepend "Form/Form.js"
// @codekit-prepend "../components/chosen/chosen/chosen.jquery.min.js"
// @codekit-prepend "../components/datepicker/js/bootstrap-datepicker.js"
// @codekit-prepend "../components/jquery.hotkeys.js"
// @codekit-prepend "Form/jquery.selectbox.js"

$(document).ready(function(){
	$("input[type='text']:first").focus();

	// Table select all behavior
	$(".select-all").change(function(){
		var table = $(this).parents("table");
		if($(this).attr('checked') == "checked")
			table.find(".select-row").attr('checked', "checked");
		else
			table.find(".select-row").removeAttr('checked');
	});

	$(".inline[data-mode='multiple']").inlineHelper();

	$('.t3-expose-form-field-selectbox').selectbox();
});