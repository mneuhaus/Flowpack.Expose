// @codekit-prepend "Form/jquery.inlinehelper.js"
// @codekit-prepend "Form/Form.js"
// @codekit-prepend "../Components/chosen/chosen/chosen.jquery.min.js"
// @codekit-prepend "../Components/datepicker/js/bootstrap-datepicker.js"
// @codekit-prepend "../Components/jquery.hotkeys.js"
// @codekit-prepend "Form/jquery.selectbox.js"

$(document).ready(function(){
	// $("input[type='text']:first").focus();

	// Table select all behavior
	$(".select-all").change(function(){
		var table = $(this).parents("table");
        var row = table.find(".select-row");
		if(row.prop('checked'))
            row.prop('checked', false);
		else
            row.prop('checked', true);
	});

	// $(".inline[data-mode='multiple']").inlineHelper();

	// $('.t3-expose-form-field-selectbox').selectbox();

	$('a.method-post').click(function() {
        var p = $(this).attr('href').split('?');
        var action = p[0];
        var params = p[1].split('&');
        var form = $(document.createElement('form')).attr('action', action).attr('method','post');
        $('body').append(form);
        for (var i in params) {
            var tmp= params[i].split('=');
            var key = tmp[0], value = tmp[1];
            $(document.createElement('input')).attr('type', 'hidden').attr('name', key).attr('value', value).appendTo(form);
        }
        $(form).submit();
        return false;
    });
});