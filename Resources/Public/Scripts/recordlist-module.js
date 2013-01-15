$(document).ready(function() {
	$('body').addClass('js');

	var $recordListSelector = $('.typo3-expose-recordlist');
	if ($recordListSelector.length > 0) {
		T3.Expose.RecordList.init($recordListSelector);
	}
});