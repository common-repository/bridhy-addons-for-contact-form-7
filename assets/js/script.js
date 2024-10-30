(function($) {
	$("#cf7vbcountry").select2({
		templateResult: formatState,
		templateSelection: formatState,
		width: '100%',
		closeOnSelect: true
	});

	function formatState(opt) {
		if (!opt.id) {
			return opt.text.toUpperCase();
		}

		var optimage = $(opt.element).attr('data-image');
		
		if (!optimage) {
			return opt.text.toUpperCase();
		} else {
			var $opt = $(
				'<span><img src="' + optimage + '" width="20px" "height=20px" /> ' + opt.text + '</span>'
			);
			return $opt;
		}
	}
	
})(jQuery);