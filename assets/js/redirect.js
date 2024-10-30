;(function ($) {
    'use strict';

    jQuery(document).ready(function () {
         cf7vb_redirect_mailsent_handler() ;

        function cf7vb_redirect_mailsent_handler() {
            document.addEventListener('wpcf7mailsent', function (event) {
               
                var form = cf7vb_redirect_object[event.detail.contactFormId];
               
                var cr_enable = cf7vb_redirect_enable[event.detail.contactFormId];
                
                if( typeof  cf7vb_redirect_tag_support !== 'undefined' ){
                    var cf7vbTagSupport = cf7vb_redirect_tag_support[event.detail.contactFormId];
                }else {
                    var cf7vbTagSupport = '';
                }
                
                if(typeof cf7vb_redirect_type !== 'undefined') {
                
                    var cf7vbRedirectType = cf7vb_redirect_type[event.detail.contactFormId];
                
                }else {
                    
                    var cf7vbRedirectType = '';
                }
                
                if( cr_enable == 'yes' && cf7vbRedirectType != 'yes' ) {
                    // Set redirect URL
                    if (form.cf7vb_redirect_to_type == 'to_url' && form.external_url) {
    					
                        if (typeof cf7vb_global_tag_support === 'function' && cf7vbTagSupport == 'on') {

                            cf7vb_global_tag_support(event, form.external_url, form.target);
                        }else {
                            var redirect_url = form.external_url;
                        }

                    } else if(form.cf7vb_redirect_to_type == 'to_page') {
                        var redirect_url = form.thankyou_page_url;
                    }
    
                    // Redirect
                    if( cf7vbTagSupport == '' || cf7vbTagSupport != 'on' ){ //if tag support disabled
                        if (redirect_url) {
                            if (!form.target) {
                                location.href = redirect_url;
                            } else {
                                window.open(redirect_url , '_blank');
                            }
                        }
                    }
                
                }
                

            }, false);
        }
	
    });

})(jQuery);
