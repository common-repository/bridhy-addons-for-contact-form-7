;(function ($) {
    'use strict';
    
    if (_wpcf7 == null) {
        var _wpcf7 = wpcf7;
    }

    var cf7vb_compose = _wpcf7.taggen.compose;

    _wpcf7.taggen.compose = function ( tagType, $form ) {

        var cf7vb_tag_close = cf7vb_compose.apply( this, arguments );

        if (tagType == 'conditional') cf7vb_tag_close += "[/conditional]";

        return cf7vb_tag_close;
    };
    
    var cfList = document.getElementsByClassName("cf7vb-cf").length;

    var index = cfList;

    //Triggering the add new rule button
    jQuery('#cf7vb-new-cf').on('click', function () {

        cf7vb_add_conditional_rule();
        cf7vb_cf_count();
        cf7vb_remove();
        cf7vb_replace_name_attr();

    });

    //Adding conditions in a rule
    function cf7vb_add_condition(){
        jQuery(document).on( 'click', '.cf7vb-add-condition', function (e) {
            
            e.preventDefault();
            
            var $this = jQuery(this);
			
            var $indexId = $this.attr('data-rule-id');

            var cf7vb_new_entry_html = jQuery('#cf7vb-new-entry .cf7vb-condition-group .cf7vb-conditions-wraper').html();

            $this.parent('.cf7vb-cf').find('.cf7vb-condition-group .cf7vb-conditions-wraper').append(cf7vb_new_entry_html.replace(/cf7vbid/g, $indexId));
            
            //cf7vb_replace_conditions_name();
            
            /*
            * Trigger the remove function
            */
            cf7vb_remove();
            
            /*
            * Count conditions
            */
            var $total_contition = $this.parent('.cf7vb-cf').find('.cf7vb-conditions-wraper .cf7vb-condition-wrap').length;
            
            $this.parent('.cf7vb-cf').find('input#cf7vb-conditions-count-ruleid').val($total_contition);

        });
    }
    cf7vb_add_condition();

    //Count length of conditions
    function cf7vb_cf_count() {

        var cfList = document.getElementsByClassName("cf7vb-cf").length;
        jQuery('#cf7vb-cf-count').val(cfList);

    }

    //Adding conditional rule
    function cf7vb_add_conditional_rule() {

        var cf7vb_new_entry_html = jQuery('#cf7vb-new-entry').html();

        jQuery('<div id="cf7vb-cf-' + index + '" class="cf7vb-cf">' + (cf7vb_new_entry_html.replace(/cf7vbid/g, index)) + '</div>').appendTo('.cf7vb-conditional-fields');

        index++;
    }
    
    //Replace rules conditions name with an uinque id
    function cf7vb_replace_name_attr() {

        var $count = 0;
        jQuery('.cf7vb-cf').each(function () {

            jQuery(this).find('.cf7vb-field').each(function () {

                jQuery(this).attr('name', jQuery(this).attr('name').replace(/\b0\b/g, $count));
            });

            $count++;
        });
    }
    
    //Replace conditions name with an uinque id
    function cf7vb_replace_conditions_name() {

        jQuery('.cf7vb-cf .cf7vb-conditions-wraper').each(function () {

            var $count = 0;
            jQuery(this).find('.cf7vb-condition-wrap').each(function () {
                jQuery(this).find('.cf7vb-field').each(function () {

                    //jQuery(this).attr('name', jQuery(this).attr('name')+'_cf7vbchildid');
                    jQuery(this).attr('name', jQuery(this).attr('name').replace(/cf7vbchildid/g, $count));
                    
                });
                $count++;
            });

        });
    }

    //Remove function
    function cf7vb_remove() {

        jQuery('.cf7vb-remove').on('click', function (e) {
            e.preventDefault();

            var $this = jQuery(this);

            //Remove field
            $this.parent().remove();

            //Replace fields name
            cf7vb_replace_name_attr();
            //Count fields
            cf7vb_cf_count();
        });
 
        jQuery('.cf7vb-remove-group').on('click', function (e) {
            e.preventDefault();

            var $this = jQuery(this);

            //Remove field
            $this.parent().remove();
            
            //Replacing nammes
            //cf7vb_replace_conditions_name();
            
        });

    }
    cf7vb_remove();

    /*
    * Count conditions
    */
    jQuery('.cf7vb-conditions-wraper').on('click', function (e) {
        e.preventDefault();
        
        var $total_contition = jQuery('.cf7vb-condition-wrap', this).length;
        jQuery(this).parent('.cf7vb-condition-group').find('input#cf7vb-conditions-count-ruleid').val($total_contition);
    });
    
    
})(jQuery);