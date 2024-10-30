jQuery(document).ready(function () {

	function cf7vb_redirect_enable() {
		var redirect_enable = jQuery('input.cf7vb_redirect_enable:checked').val();
		if ( redirect_enable  == 'yes' ) {
			jQuery('.cf7vb_default_redirect_wraper').show();
		} else {
			jQuery('.cf7vb_default_redirect_wraper').hide();
		}
	}
	cf7vb_redirect_enable();
	jQuery('input.cf7vb_redirect_enable').on('change', function(){
		cf7vb_redirect_enable();
		});
		
	//Conditional check
	function cf7vb_redirect_field() {
		var cf7vb_redirect_to_type = jQuery('input.cf7vb_redirect_to_type:checked').val();

		if (cf7vb_redirect_to_type == 'to_page') {
			jQuery('.cf7vb_redirect_to_page').show();
			jQuery('.cf7vb_redirect_to_url').hide();
		} else {
			jQuery('.cf7vb_redirect_to_url').show();
			jQuery('.cf7vb_redirect_to_page').hide();
		}
	}
	cf7vb_redirect_field();
	
	jQuery('input.cf7vb_redirect_to_type').on('change', function(){
		cf7vb_redirect_field();
	});

	jQuery('select.cf7vb-page-list').niceSelect();

	
});

/**
 * Nice Select added
 */


