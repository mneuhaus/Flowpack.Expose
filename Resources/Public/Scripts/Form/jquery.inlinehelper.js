/*!
 * $ lightweight plugin boilerplate
 * Original author: @ajpiano
 * Further changes, comments: @addyosmani
 * Licensed under the MIT license
 */

// the semi-colon before the function invocation is a safety
// net against concatenated scripts and/or other plugins
// that are not closed properly.
;(function ( $, window, document, undefined ) {
	// Create the defaults once
	var pluginName = 'inlineHelper';
	var defaults = {
			container: ".t3-expose-inline[data-mode='multiple']",
			template: ".t3-expose-inline-item-template",
			item: ".t3-expose-inline-item"
		};

	// The actual plugin constructor
	function Plugin( element, options ) {
		this.element = $(element);

		this.options = $.extend( {}, defaults, options) ;

		this._defaults = defaults;
		this._name = pluginName;

		this.init();
	}

	Plugin.prototype.init = function () {
		// Create new row after the last one is used
		this.element.find("input, select, textarea").live("keyup", handleChange);
		this.element.find("input, select, textarea").live("change", handleChange);

		this.element.find('[data-property="__identity"] input').each(function(){
			var identity = $(this);
			identity.parents("tr").append(identity);
		});
		this.element.find('[data-property="__identity"]').remove();

		// Show close buttons
		//this.element.find(".close").show();
		//this.element.find(".t3-expose-inline-item-unused .close").hide();

		// Remove the item and create a new one if it was the last one
		$(".t3-expose-inline-item .close").live("click", function(){
			var e = $(this);
			var container = e.parents(".t3-expose-inline");
			removeItem(e.parents(".t3-expose-inline-item"));
			if(container.find(".t3-expose-inline-item").length < 1){
				addItem(container);
			}
		});

		this.element.parents("form").submit(function(){
			var form = $(this);
			form.find(".t3-expose-inline-item-unused").each(function(){
				var item = jQuery(this);
				var hiddenInputs = item.parents('form').find('div').first();
				item.find('[name]').each(function(){
					var e = jQuery(this);
					var hiddenInput = item.parents('form').find('[name="' + e.attr('name') + '"]');
					hiddenInput.removeAttr('name');
				});
			});
		});
	};

	function removeItem(item) {
		var hiddenInputs = item.parents('form').find('div').first();
		item.find('input[name]').each(function(){
			var e = jQuery(this);
			var hiddenInput = item.parents('form').find('[name="' + e.attr('name') + '"]');
			hiddenInput.remove();
		});
		item.remove();
	}

	function handleChange () {
		if($(this).val() == undefined || $(this).val() == "") return;
		var container = $(this).parents(".t3-expose-inline");
		var row = $(this).parents(".t3-expose-inline-item").first();
		if(row.next(".t3-expose-inline-item").length == 0 && container.attr("data-mode") == "multiple"){
			addItem(container);
		}
		if(row.hasClass("t3-expose-inline-item-unused")){
			row.removeClass("t3-expose-inline-item-unused");
			//row.find(".close").show();
		}
	}

	function addItem(container){
		var counter = Number(container.attr("data-counter"));
		var tpl = container.find(".t3-expose-inline-item-template");
		var newRow = tpl.clone().removeClass("t3-expose-inline-item-template").addClass("t3-expose-inline-item");

		newRow.find("input:hidden, select:hidden, textarea:hidden").removeAttr("disabled");
		newRow.html(newRow.html().replace(/000/g, counter).replace(/\[_template\]/g, ""));
		newRow.addClass("t3-expose-inline-item-unused").hide();
		$(tpl).parent().append(newRow);
		newRow.fadeIn("slow");
		container.attr("data-counter", counter+1);
	}

	// A really lightweight plugin wrapper around the constructor,
	// preventing against multiple instantiations
	$.fn[pluginName] = function ( options ) {
		return this.each(function () {
			if (!$.data(this, 'plugin_' + pluginName)) {
				$.data(this, 'plugin_' + pluginName,
				new Plugin( this, options ));
			}
		});
	}

})( $, window, document );