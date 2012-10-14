/*!
 * jQuery lightweight plugin boilerplate
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
		this.element = jQuery(element);

		this.options = $.extend( {}, defaults, options) ;

		this._defaults = defaults;
		this._name = pluginName;

		this.init();
	}

	Plugin.prototype.init = function () {
		// Create new row after the last one is used
		this.element.find("input, select, textarea").live("keyup", function(){
			if(jQuery(this).val() == undefined || jQuery(this).val() == "") return;
			var container = jQuery(this).parents(".t3-expose-inline");
			var row = jQuery(this).parents(".t3-expose-inline-item").first();
			if(row.next(".t3-expose-inline-item").length == 0 && container.attr("data-mode") == "multiple"){
				addItem(container);
			}
			if(row.hasClass("t3-expose-inline-item-unused")){
				row.removeClass("t3-expose-inline-item-unused");
				//row.find(".close").show();
			}
		});

		// Show close buttons
		//this.element.find(".close").show();
		//this.element.find(".t3-expose-inline-item-unused .close").hide();

		// Remove the item and create a new one if it was the last one
		jQuery(".t3-expose-inline-item .close").live("click", function(){
			var e = jQuery(this);
			var container = e.parents(".t3-expose-inline");
			e.parents(".t3-expose-inline-item").remove();
			if(container.find(".t3-expose-inline-item").length < 1){
				addItem(container);
			}
		});
	};

	function addItem(container){
		var counter = Number(container.attr("data-counter"));
		var tpl = container.find(".t3-expose-inline-item-template");
		var newRow = tpl.clone().removeClass("t3-expose-inline-item-template").addClass("t3-expose-inline-item");

		newRow.find("input:hidden, select:hidden, textarea:hidden").removeAttr("disabled");
		newRow.html(newRow.html().replace(/000/g, counter).replace(/\[_template\]/g, ""));
		newRow.addClass("t3-expose-inline-item-unused").hide();
		jQuery(tpl).parent().append(newRow);
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

})( jQuery, window, document );