console.log( 'bad-tournament.js included...' );

/*
jQuery('.admin_block_label').on( 'click', function(){
    jQuery('.admin_block').hide();
    jQuery( this ).next().slideDown();
});
*/

jQuery('.table_row').on( 'click', function(){
    player_name_tosearch = jQuery( this ).find( '.pl_name').html();
    if( jQuery( '.nav_match' ).length > 0 ){
        jQuery( '.nav_match .match_pl1_name').each( function(){
            var text = jQuery(this).html();
            jQuery(this).html(text.replace( '<span style="color: #ff0; background-color: rgba( 0, 0, 0, 0.4); padding-right: 4px; padding-left: 4px;">(.*)</span>' , '$1'));
            text = jQuery(this).text();
            jQuery(this).html(text.replace( player_name_tosearch , '<span style="color: #ff0; background-color: rgba( 0, 0, 0, 0.4); padding-right: 4px; padding-left: 4px;">'+player_name_tosearch+'</span>'));
        });
        jQuery( '.nav_match .match_pl2_name').each( function(){
            var text = jQuery(this).html();
            jQuery(this).html(text.replace( '<span style="color: #ff0; background-color: rgba( 0, 0, 0, 0.4); padding-right: 4px; padding-left: 4px;">(.*)</span>' , '$1'));
            text = jQuery(this).text();
            jQuery(this).html(text.replace( player_name_tosearch , '<span style="color: #ff0; background-color: rgba( 0, 0, 0, 0.4); padding-right: 4px; padding-left: 4px;">'+player_name_tosearch+'</span>'));
        });

    }
});

jQuery('#round_select').on( 'change', function(){
    //console.log('Change round...');
    round_display = jQuery( this ).val();
    round_available = jQuery( this).attr( 'data-round' );
    jRound_available = '#block_game' + round_available;
    if( round_display == 0 ){
        jQuery(jRound_available + ' .match_round_header').fadeIn();
        jQuery(jRound_available + ' .match_row').fadeIn();
    }else{
        //console.log( 'jRound_available:' + jRound_available );

        jQuery( jRound_available + ' .match_round_header' ).fadeOut();
        jQuery( '.match_row' ).fadeOut();

        jQuery(jRound_available + ' .match_round_header').each(function(){
            if( jQuery( this).attr( 'data-round' ) == round_display ){
                jQuery( this ).slideDown();
                return false;
            }
        });

        jQuery(jRound_available + ' .match_row').each(function(){
            if( jQuery( this).attr( 'data-round' ) == round_display ){
                jQuery( this ).slideDown();
            }
        });
    }

});
