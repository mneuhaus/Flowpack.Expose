$(document).ready(function() {
	$('body').addClass('js');

	var $recordListSelector = $('.typo3-admin-recordlist');
	if ($recordListSelector.length > 0) {
		T3.Admin.RecordList.init($recordListSelector);
	}
});