(function() {
	var T3 = window.T3 || {};
	window.T3 = T3;
	T3.Expose = T3.Expose || {};
	T3.Expose.RecordList = T3.Expose.RecordList || {};
	var $ = window.jQuery;

	T3.Expose.RecordList.init = function($recordList) {
		var resultListing = ResultListing.create({
			$container: $recordList
		});
		resultListing.bootstrap();
	};

	var ResultListing = Ember.Object.extend({
		$container: null,
		_$actionBar: null,

		currentSelection: Ember.Set.create(),

		numberOfSelectedElementsBinding: 'currentSelection.length',

		actionBarVisible: function() {
			return this.get('numberOfSelectedElements') > 0;
		}.property('numberOfSelectedElements').cacheable(),

		multipleSelectionActive: function() {
			return this.get('numberOfSelectedElements') > 1;
		}.property('numberOfSelectedElements').cacheable(),

		toggleActionBar: function() {
			if (this.get('actionBarVisible')) {
				if (this._$actionBar.hasClass('hiddenActionBar')){
					this._$actionBar.removeClass('hiddenActionBar');
					
					this.$container.find('[data-area=main]').width("auto");
					var width = this.$container.find('[data-area=main]').width() - this._$actionBar.outerWidth();
					//var actionBarWidth = this._$actionBar.width();
					//this._$actionBar.width(0).animate({width: actionBarWidth + "px"}, 5000);
					this.$container.find('[data-area=main]').width(width);
					
					this._$actionBar.height(this.$container.find('[data-area=main]').height());
				}
			} else {
				this._$actionBar.addClass('hiddenActionBar');
				var that = this;
				window.setTimeout(function() {
					that.$container.find('[data-area=main]').width("auto");
				}, 230);

			}
		}.observes('actionBarVisible'),

		/**
		 * INITIALIZATION
		 */
		init: function() {
			this._$actionBar = this.$container.find('[data-area=actionBar]');
		},

		bootstrap: function() {
			var that = this;

			this.$container.on('click', '[data-area=records] > *', function() {
				var $element = $(this);
				$element = $element.closest('[data-identifier]');
				if ($element.hasClass('typo3-expose-active')) {
					$element.removeClass('typo3-expose-active');
					that.currentSelection.remove($element.attr('data-identifier'));
				} else {
					$element.addClass('typo3-expose-active');
					that.currentSelection.add($element.attr('data-identifier'));
				}
			});

			this._initializeActionBar();
		},

		_initializeActionBar: function() {
			var that = this;
			var actionBarView = Ember.View.create({
				template: Ember.Handlebars.compile(this._$actionBar.html()),
				templateContext: this,
				// we also need to set this private member, to supposedly work around an ember bug.
				_templateContext: this,
				didInsertElement: function() {
					this.$().find('a').click(function(e) {
						that.showExposeController($(this).attr('href'), $(this).text());
						e.preventDefault();
					});
				}
			});
			actionBarView.replaceIn(this._$actionBar);
		},

		showExposeController: function(uri, title) {
			// Open an expose controller based on URI and currentSelection

			this.get('currentSelection').forEach(function(identifier) {
				uri += encodeURI('&moduleArguments[--exposeRuntime][objects][]=') + encodeURIComponent(identifier);
			});

			window.location.href = uri;
			/*uri += encodeURI('&moduleArguments[hideModuleDecoration]=1');

			var $dialog = $('<div></div>')
				.html('<iframe style="border: 0px; " src="' + uri + '" width="100%" height="100%"></iframe>')
				.dialog({
					autoOpen: true,
					modal: true,
					height: 525,
					width: 800,
					title: title,
					zIndex: 10050
				});
			*/
		}
	});

})();
