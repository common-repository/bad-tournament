console.log( 'bad-tournament-admin.js included...' );
console.log( bvg_tournament_constants );

/*
Switching admin page */
jQuery('.nav_item').on( 'click', function(){
    if( jQuery( '#main_nav' ).hasClass( 'refresh_required' ) ){
        console.log( 'Need to refresh page to reupdate everything after ajax requests' );
        window.location = window.location.hash;
        return true;
    }
    jQuery('.nav_item').removeClass( 'active' );
    jQuery( this ).addClass( 'active' );
    id_nav = jQuery( this ).attr( 'id' );
    id_block = '.admin_block.' + id_nav;
    jQuery('.admin_block').hide();
    jQuery( id_block ).slideDown( 'slow' );
});

/* View other round
*/
jQuery( '#view_round' ).on( 'change', function(){
    console.log( 'New round...' );
    //debugger;
    jQuery( this ).parent('form').submit();
});

/*
Allow to create match */
jQuery('#create_match_button').click(function(event){
    console.log('Display crete game form');
    event.preventDefault();
    jQuery('#create_match_form').slideDown('slow');
    jQuery(this).attr( 'id' , 'create_match_submit');
    jQuery(this).attr( 'value' , bvg_tournament_constants.createMatchSubmitLabel);
    jQuery(this).attr( 'type' , 'submit');

    jQuery('#create_match_submit').click(function(){
        jQuery( this).parent().submit();
    });
});

/*
Set couple of players */
jQuery('#players_list_select_couple').on('click', 'option', function( event ){
    unvalid_couple = false;
    tournament_type = jQuery( '#tournament_type' ).val();
    if( ( tournament_type == 3 && jQuery( this ).data( 'sex' ) == 2 ) || ( tournament_type == 4 && jQuery( this ).data( 'sex' ) == 1 ) ){
        console.log( 'Check sex couple...' );
        alert( 'This player can not play in this tournament' );
    }else{
        //console.log( 'Set couple...' );
        nb_total_options = jQuery( '#players_list_select_couple option').length;
        nb_sel_options = jQuery( '#players_list_select_couple option:selected').length;
        if( nb_sel_options == 2 ){
            // Check if this couple is allowed
            jQuery( '#players_list_select_couple option:selected').each( function( i ){
                console.log( 'Check sex couple for mixed...' );
                if( i == 0){
                    first_selected_player_sex = jQuery( this ).data( 'sex' );
                }else{
                    if( tournament_type == 5 && jQuery( this ).data( 'sex' ) == first_selected_player_sex ){
                        alert( 'Both players are male or female. Not allowed for mixed tournaments.' );
                        option_clicked.stop();
                        unvalid_couple = true;
                    }
                }
                console.log( tournament_type );
                console.log( first_selected_player_sex );
                console.log( jQuery( this ).data( 'sex' ) );
            });

            if( !unvalid_couple ){
                jQuery( '#players_list_select_couple option:selected').each( function( i ){
                    player_couple_class = 'player_inCouple_' + i;
                    html_str = '<li id="player_inCouple_' + jQuery( this ).val() + '" data_id="' + jQuery( this ).val() + '" class="' + player_couple_class + '">' + jQuery( this ).html() + '</li>';
                    jQuery( '#players_couple_list' ).append( html_str );
                    jQuery( this ).remove();
                    if( i == 0){
                        pl1_id = jQuery( this ).val();
                    }else{
                        pl2_id = jQuery( this ).val();
                    }

                });
                nb_total_options = nb_total_options -2;

                // Save
                jQuery('#ajax_spinner_layer').fadeIn();
                jQuery( '#main_nav' ).addClass( 'refresh_required' );
                data = {
                    action: 'save_players_couple',
                    pl1_id: pl1_id,
                    pl2_id: pl2_id
                };
                jQuery.ajax({
                    type: "POST",
                    data : data,
                    async: true,
                    cache: false,
                    url: ajaxurl,
                    success: function(data) {
                        console.log( 'Players couple now saved...' );
                        jQuery('#ajax_spinner_layer').fadeOut( 'slow' );
                    }
                });

                //console.log( 'Nb selected options: ' + nb_sel_options );
                // Remove select field if no more player available
                if( nb_total_options < 1 ){
                    jQuery('#players_list_select_couple').fadeOut( 'slow', function(){
                        jQuery('#players_list_select_couple').after( 'All couple of players are set... Probably correct, but check again the following list before to start the tournament.' );
                    });
                }

                /* remove line trough all players with same sex for mixed couples
                */
                if( tournament_type == 5 ){
                    $('#players_list_select_couple option').removeClass('notAllowed');
                }
            }

        }else if( nb_sel_options > 0 ){
            /* Line trough all players with same sex for mixed couples
            */
            if( tournament_type == 5 ){
                console.log('Strike !!!!');
                sex_selected = $( this ).data('sex');
                $('#players_list_select_couple option:not([data-sex!='+sex_selected+'])').not( this ).addClass('notAllowed');
            }
        }else{
            if( tournament_type == 5 ){
                $('#players_list_select_couple option').removeClass('notAllowed');
            }
        }

    }

});

/* Remove couple of players */
jQuery('#players_couple_list').on('dblclick', 'li', function(){
    console.log( 'Remove couple...' );
    // Display the players list if required
    if( !jQuery('#players_list_select_couple').is(':visible') ){
        jQuery('#players_list_select_couple').fadeIn();
    }
    html_str = '<option value="' + jQuery( this ).val() + '" >' + jQuery( this ).html() + '</option>';
    jQuery('#players_list_select_couple').append( html_str );
    if( jQuery( this ).hasClass( 'player_inCouple_0' ) ){
        html_str = '<option value="' + jQuery( this ).next().val() + '">' + jQuery( this ).next().html() + '</option>';
        jQuery( this ).next().remove();
    }else{
        html_str = '<option value="' + jQuery( this ).prev().val() + '">' + jQuery( this ).prev().html() + '</option>';
        jQuery( this).prev().remove();
    }
    jQuery( this ).remove();
    jQuery('#players_list_select_couple').append( html_str );
});

/*
Set match winner */
jQuery('.match_winner').on( 'click', function(){
    jMatchId = '#match_winner_' + jQuery( this ).attr('data_m_id');
    jFormId = '#form_match_' + jQuery().attr('data_m_id');
    jQuery( jMatchId ).val( jQuery( this ).attr( 'data' ) );
    jQuery( jFormId ).submit();
});

/*
Choose existing tournament */
jQuery('#tournament_select_button').on( 'click', function(){
    jQuery( '#tournament_name').val( '' );
    jQuery( '#tournament_select_id' ).val( jQuery( '#tournament_select' ).val() );
});

jQuery('#tournament_remove_button').on( 'click', function(){
    if( jQuery( '#tournament_select' ).val() > 0 ){
        if( !confirm( bvg_tournament_constants.confirmRemoveTournament ) ){
            return false;
        }
    }else{
        alert( 'Choose a tournament to remove' );
        return false;
    }

});

jQuery('#tournament_restart_button').on( 'click', function(){
    if( jQuery( '#tournament_select' ).val() > 0 ){
        if( !confirm( bvg_tournament_constants.confirmRestartTournament ) ){
            return false;
        }
    }else{
        alert( 'Choose a tournament to restart' );
        return false;
    }

});

/*
Fill tournament form with existing values */
jQuery('#tournament_select').on( 'change', function(){
    if( jQuery( this ).val() > 0 ){
        jQuery( '#tournament_edit').fadeIn();
        console.log( tournament[jQuery( '#tournament_select' ).val()] );
        selected_tournament = JSON.parse( tournament[jQuery( '#tournament_select' ).val()] );
        //console.log( selected_tournament );

        jQuery( '#tournament_parent_select option' ).each( function(){
            if( jQuery( this).val() == selected_tournament.parent_id ){
                jQuery( this).attr( 'selected', 'selected' );
            }
        } );
        jQuery( '#tournament_name').val( selected_tournament.name );

        jQuery( '#tournament_localization').val( selected_tournament.localization );
        jQuery( '#tournament_date_start').val( selected_tournament.date_start );
        jQuery( '#tournament_date_end').val( selected_tournament.date_end );

        jQuery( 'input[name=tournament_typ]' ).each( function(){
            if( jQuery( this).val() == selected_tournament.tournament_typ ){
                jQuery( this).attr( 'checked', 'checked' );
            }
        } );

        jQuery( 'input[name=tournament_system]' ).each( function(){
            if( jQuery( this).val() == selected_tournament.system ){
                jQuery( this).attr( 'checked', 'checked' );
            }
        } );
        jQuery( '#tournament_nb_sets').val( selected_tournament.nb_sets );
        jQuery( '#tournament_points_set').val( selected_tournament.points_set );
        jQuery( '#tournament_max_points_set').val( selected_tournament.max_points_set );
        jQuery( '#round_max').val( selected_tournament.round_max );
        jQuery( '#round').val( selected_tournament.round );
        jQuery( '#club_restriction option' ).each( function(){
            if( jQuery( this).val() == selected_tournament.club_restriction ){
                jQuery( this).attr( 'selected', 'selected' );
            }
        } );

        if( isNaN( selected_tournament.logo ) ){
            jQuery( '#tournament_logo_url').val( selected_tournament.logo );
            jQuery( '#image_attachment_id').val( '' );
            jQuery( '#image-preview').attr( 'src' , selected_tournament.logo_url );
        }else{
            jQuery( '#tournament_logo_url').val( '' );
            jQuery( '#image_attachment_id').val( selected_tournament.logo );
            jQuery( '#image-preview').attr( 'src' , selected_tournament.logo_url );
        }
    }


    //jQuery( '#tournament_select_id' ).val( jQuery( '#tournament_select' ).val() );
});

jQuery( '#upload_image_button').on( 'click', function(){
    jQuery( '#tournament_logo_url').val( '' );
});
jQuery( '#tournament_logo_url').on( 'blur', function(){
    if( jQuery( this ).val() != '' ){
        console.log( jQuery( this ).val() );
        jQuery( '#image-preview').attr( 'src' , jQuery( this ).val() );
    }
});

jQuery( '#upload_pic_button').on( 'click', function(){
    jQuery( '#profile_pic_url').val( '' );
});
jQuery( '#profile_pic_url').on( 'blur', function(){
    if( jQuery( this ).val() != '' ){
        console.log( jQuery( this ).val() );
        jQuery( '#pic-preview').attr( 'src' , jQuery( this ).val() );
    }
});

/*
Close admin message */
jQuery('#bvg_admin_msg_close').on( 'click', function(){
    console.log( 'Close admin msg...' );
    jQuery( '#bvg_admin_msg').animate({ height: 0, opacity: 0 }, 'slow');
});

/*
Expand players list */
jQuery('#player_select, #players_list_select_couple').on( 'focus mouseover' , function(){
    if( jQuery( this ).children().length > 25 ){
        jQuery( this ).height( 400 );
    }else if( jQuery( this ).children().length > 8 ){
        jQuery( this ).height( 250 );
    }
});
jQuery('#player_select, #players_list_select_couple').on( 'blur mouseout' , function(){
   jQuery( this ).height( 150 );
});

/*
Display nb players in the current tournament */
jQuery('#player_select').on( 'change' , function(){
    nb_players_init = jQuery( '#nb_players_tournament' ).data( 'init' );
    nb_players_added = jQuery("#player_select :selected").length;
    nb_players_total = nb_players_init + nb_players_added ;
    console.log( 'Nb players: ' + nb_players_init );
    console.log( 'Nb players added: ' + nb_players_added );
    jQuery('#nb_players_tournament').html( nb_players_init + ' (+' + nb_players_added + ') ' + nb_players_total );
});

/*
Expand new player form */
jQuery('.plus_icon').on( 'click', function(){
    jQuery( this).next().next().slideDown();

    jQuery('#ajax_spinner_layer').fadeIn();
    jQuery( '#main_nav' ).addClass( 'refresh_required' );
    data = {
        action: 'set_player_form_default'
    };
    jQuery.ajax({
        type: "POST",
        data : data,
        async: true,
        cache: false,
        url: ajaxurl,
        success: function(data) {
            console.log( 'Player form extended by default now...' );
            jQuery('#ajax_spinner_layer').fadeOut( 'slow' );
        }
    });
});

/*
Change player for a match */
jQuery( 'select.player_name' ).on('change', function() {
        if (confirm('Wollen Sie wirklich die Spieleinstellung ändern ? ')) {
            jQuery('#ajax_spinner_layer').fadeIn();
            jQuery( '#main_nav' ).addClass( 'refresh_required' );
            the_form = jQuery(this).closest('form');
            pl_select = the_form.find( '.player_name' );
            players_id = [];
            pl_select.each(function(i){
                players_id[i] = jQuery( this ).val();
                jSelector = 'input[name="'+jQuery( this).attr( 'data_pl_id' )+'"]';
                the_form.find( jSelector).val( jQuery( this ).val() );
            });

            match_id = the_form.find( '.match_id' ).val();
            console.log( players_id );
            console.log( match_id );
            var data = {
                action: 'change_players_match',
                match_id: match_id,
                players_id: players_id
            };

            jQuery.ajax({
                type: "POST",
                data : data,
                async: true,
                cache: false,
                url: ajaxurl,
                success: function(data) {
                    console.log(data);
                    jQuery('#ajax_spinner_layer').fadeOut();
                }
            });

        }
    } );

/*
Expand player profile on table view */
jQuery('.pl_infos').on( 'click', function(){
    console.log('Display player infos');

    row_parent = jQuery( this).closest( 'li.table_row' );

    if( row_parent.find( '.player_infos').length > 0 ){
        console.log( 'Remove player infos' );
        row_parent.find( '.player_infos').fadeOut().remove();
    }else{
        jQuery('#ajax_spinner_layer').fadeIn();
        jQuery( '#main_nav' ).addClass( 'refresh_required' );
        console.log( 'Display player infos' );
        pl_id = jQuery( this ).attr('data-pl_id');
        pl2_id = jQuery( this ).attr('data-pl2_id');

        var data = {
            action: 'player_tooltip',
            pl_id: pl_id,
            pl2_id: pl2_id
        };

        jQuery.ajax({
            type: "POST",
            data : data,
            async: true,
            cache: false,
            url: ajaxurl,
            success: function(data) {
                console.log( 'Attach infos now...' );
                console.log(data);
                row_parent.append( '<div class="player_infos"></div>' );
                row_parent.find( '.player_infos').append( data );
                jQuery('#ajax_spinner_layer').fadeOut( 'slow' );
                /* add datepicker to forms */
                jQuery('.datepicker').datepicker( {dateFormat: "dd/mm/yy"} );

                jQuery( '.pl_edit_field' ).on('change keypress', function(e){
                    pl_id2 = 0;
                    if( jQuery( this).hasClass( 'pl_edit_field2' ) ){
                        pl_id2 = pl2_id;
                    }
                    allow_to_edit = true;
                    if( jQuery('option:selected', this).hasClass( 'no_edit' ) ){
                        allow_to_edit = false;
                    }
                    if( jQuery( '#edit_field_valid' ).length < 1 && allow_to_edit === true ){
                        jQuery( this).after( '<img src="' + bvg_tournament_constants.badTournamentURI + 'icons/bad-tournament-ok-icon.png" id="edit_field_valid" class="edit_field_valid" />' );

                        jQuery( '#edit_field_valid' ).on( 'click', function(){
                            if( confirm( bad_tournament_admin.save_now )){
                                var pl_field = jQuery( this );
                                pl_field_name = jQuery( this ).prev().prop( 'id' );
                                pl_field_value = jQuery( this ).prev().val();
                                jQuery('#ajax_spinner_layer').fadeIn();
                                data = {
                                    action: 'player_edit_field',
                                    pl_id: pl_id,
                                    pl_id2: pl_id2,
                                    pl_field_name: pl_field_name,
                                    pl_field_value: pl_field_value
                                };
                                jQuery.ajax({
                                    type: "POST",
                                    data : data,
                                    async: true,
                                    cache: false,
                                    url: ajaxurl,
                                    success: function(data) {
                                        console.log('Field: ' + pl_field_name );
                                        if( pl_field_name == 'sex' ){
                                            if( pl_field_value == 1 ){
                                                pl_field_value = bvg_tournament_constants.badTournamentMale;
                                            }else{
                                                pl_field_value = bvg_tournament_constants.badTournamentFemale;
                                            }

                                        }
                                        pl_field.parent().find( 'span.player_current_value').html( pl_field_value );
                                        //console.log( pl_field.parent() );
                                        //console.log( pl_field.parent().find( 'span.player_current_value') );
                                        if( pl_field_name == 'player_level' ){
                                            if( pl_id2 > 0 ){
                                                pl_field.parent().closest( 'li').find( '.pl_level_init' ).html('(' + pl_field_value + ')');
                                            }else{
                                                console.log( 'Level Pl1: ' + jQuery( '#player1_current_value' ).html() );
                                                level_double = parseInt( jQuery( '#player1_current_value' ).html() ) + parseInt( jQuery( '#player2_current_value' ).html() );
                                                pl_field.parent().closest( 'li' ).find( '.pl_level_init').html( '(' + level_double + ')' );
                                            }
                                        }else if( pl_field_name == 'firstname1' || pl_field_name == 'lastname1' || pl_field_name == 'firstname2' || pl_field_name == 'lastname2' ){
                                            if( pl_id2 > 0 ){
                                                console.log( pl_field.parent().closest( 'li' ).find( '.pl_name_pl2') );
                                                pl_field.parent().closest( 'li' ).find( '.pl_name_pl2').html( jQuery( '#firstname2' ).val() + ' ' + jQuery( '#lastname2' ).val() );
                                            }else{
                                                pl_field.parent().closest( 'li' ).find( '.pl_name_pl1').html( jQuery( '#firstname1' ).val() + ' ' + jQuery( '#lastname1' ).val() );
                                            }

                                        }
                                        if( jQuery( '#bvg_admin_msg' ).length > 0 ){
                                            jQuery( '#bvg_admin_msg').append( data );
                                        }else{
                                            jQuery( '#bad_tournament_maintitle' ).before( '<div id="bvg_admin_msg"><span id="bvg_admin_msg_close"></span>' + data + '</div>' );
                                            jQuery('#bvg_admin_msg_close').on( 'click', function(){
                                                console.log( 'Close admin msg...' );
                                                jQuery( '#bvg_admin_msg').animate({ height: 0, opacity: 0 }, 'slow');
                                            });
                                        }
                                        console.log(data);
                                        jQuery('#ajax_spinner_layer').fadeOut( 'slow' );
                                        pl_field.fadeOut(function(){
                                            pl_field.remove();
                                        });
                                    }
                                });
                            }

                        })

                    }
                    var key = e.which;
                    if( key == 13 ){
                        jQuery('#edit_field_valid').click();
                        return false;
                    }
                });
            }
        });
    }

} );

/*
Remove player from tournament */
jQuery('.pl_remove').on( 'click', function(){
    jQuery('#ajax_spinner_layer').fadeIn();
    jQuery( '#main_nav' ).addClass( 'refresh_required' );

    console.log('Remove player');

    row_parent = jQuery( this).closest( 'li.table_row' );

    pl_id = jQuery( this ).attr('data-pl_id');
    pl2_id = jQuery( this ).attr('data-pl2_id');

    var data = {
        action: 'player_remove',
        pl_id: pl_id,
        pl2_id: pl2_id
    };

    jQuery.ajax({
        type: "POST",
        data : data,
        async: true,
        cache: false,
        url: ajaxurl,
        success: function(data) {
            row_parent.fadeOut();
            if( jQuery( '#bvg_admin_msg' ).length > 0 ){
                jQuery( '#bvg_admin_msg').append( data );
            }else{
                jQuery( '#bad_tournament_maintitle' ).before( '<div id="bvg_admin_msg"><span id="bvg_admin_msg_close"></span>' + data + '</div>' );
                jQuery('#bvg_admin_msg_close').on( 'click', function(){
                    console.log( 'Close admin msg...' );
                    jQuery( '#bvg_admin_msg').animate({ height: 0, opacity: 0 }, 'slow');
                });
            }
            console.log(data);
            jQuery('#ajax_spinner_layer').fadeOut( 'slow' );
        }
    });
} );

/*
Add datepicker to forms */
//jQuery('.datepicker').datepicker( {dateFormat: "dd/mm/yy"} );
jQuery('.datepicker').datepicker( {dateFormat: "dd/mm/yy"} );
jQuery('.datetimepicker').datetimepicker( {dateFormat: "dd/mm/yy", timeFormat: "hh:mm"} );

/*
Set club as default */
jQuery( '#club_player, .clubs_name' ).on('change keypress click', function(e){
    if( jQuery( this ).val() > 0 ){
        if( jQuery( '#edit_field_valid' ).length > 0 ){
            jQuery( '#edit_field_valid' ).remove();
        }

        jQuery( this ).after( '<img src="' + bvg_tournament_constants.badTournamentURI + 'icons/bad-tournament-ok-icon.png" id="edit_field_valid" class="edit_field_valid" />' );

        var club_id = jQuery( this ).val();
        jQuery( '#edit_field_valid' ).on( 'click', function(){

                jQuery('#ajax_spinner_layer').fadeIn();
                jQuery( '#main_nav' ).addClass( 'refresh_required' );
                data = {
                    action: 'set_club_default',
                    club_id: club_id
                };
                jQuery.ajax({
                    type: "POST",
                    data : data,
                    async: true,
                    cache: false,
                    url: ajaxurl,
                    success: function(data) {
                        console.log('Club ID: ' + club_id );

                        if( jQuery( '#bvg_admin_msg' ).length > 0 ){
                            jQuery( '#bvg_admin_msg').append( data );
                        }else{
                            jQuery( '#bad_tournament_maintitle' ).before( '<div id="bvg_admin_msg"><span id="bvg_admin_msg_close"></span>' + data + '</div>' );
                            jQuery('#bvg_admin_msg_close').on( 'click', function(){
                                console.log( 'Close admin msg...' );
                                jQuery( '#bvg_admin_msg').animate({ height: 0, opacity: 0 }, 'slow');
                            });
                        }
                        console.log(data);
                        jQuery('#ajax_spinner_layer').fadeOut( 'slow' );
                    }
                });


        })

    }else if( jQuery( this ).val() == 0 ){
        jQuery( '#edit_field_valid' ).remove();
    }
    var key = e.which;
    if( key == 13 ){
        jQuery('#edit_field_valid').click();
        return false;
    }
});

/*
Export */
jQuery('#export1').on( 'click' , function(){
    if( jQuery( this ).attr('checked') == 'checked' ){
        jQuery('.export_checkbox').attr('checked', 'checked').attr('disabled', 'disabled');
    }else{
        jQuery('.export_checkbox').attr('checked', false ).attr('disabled', false );
    }

    jQuery( this ).attr( 'disabled', false );
});

/* Export data as file */
jQuery('#export_file').on( 'click', function(){
    console.log( 'CSV file start...' );
    checkbox_types = new Array();

    jQuery('.export_checkbox:checked').each( function(){
        checkbox_types.push( jQuery( this ).val() );
    });
    if( checkbox_types.length ){
        //jQuery('#ajax_spinner_layer').fadeIn();

        type_data = checkbox_types.join( ',' );
        console.log(type_data);
        data = {
            action: 'export_file',
            type_data: type_data
        };
        window.location = 'admin-ajax.php?action=export_file&type_data=' + type_data;
        //console.log( window.location.hash );
        //jQuery('#ajax_spinner_layer').fadeOut( 'slow' );
        return false;
    }
    alert( 'You need to select something...' );
    return false;

});



/*
Menus */
var badt_submenus = '<ul class="wp-submenu wp-submenu-wrap badt_submenu" id="badt_submenu" style="top: -47px !important;"><li class="wp-submenu-head" aria-hidden="true">Bad Tournament</li>';
for( var key in bvg_submenus ){
    if (bvg_submenus.hasOwnProperty(key)) {
        badt_submenus += '<li class="badt-menu-open"><a href="admin.php?page=bad_tournament&t_select_id='+key+'" class="wp-first-item">'+bvg_submenus[key]+'</a></li>';
    }
}
    badt_submenus += '</ul>';
var badt_link_decoration = '<div class="wp-menu-arrow"><div></div></div>';

jQuery(document).ready(function() {

    jQuery('li.wp-first-item a.wp-first-item:contains(Bad Tournament)')
        .append( badt_link_decoration )
        .addClass('wp-has-submenu')
        .after( badt_submenus )
        .parent()
        .addClass('badt-menu-open')
        .addClass('opensub')
        .addClass('wp-has-submenu')
        .addClass('wp-has-current-submenu');

    /* Menu opacity effect */
    jQuery(function () {
        jQuery(window).scroll(function () {
            if( jQuery(window).scrollTop() >= 35 ) {
                jQuery( 'nav#main_nav' ).addClass( 'transparent' );
            } else {
                jQuery( 'nav#main_nav' ).removeClass( 'transparent' );

            }
        });
    });

    /* Display submenus */

    jQuery('li.wp-first-item a.wp-first-item:contains(Bad Tournament)').on( 'hover', function(){
        console.log( 'Display menu now...' );
        jQuery( this ).parent().addClass( 'opensub' );
        jSubmenu = jQuery( '#badt_submenu' );
        jSubmenu.show();
        jQuery('#wpcontent, .wp-menu-name').on( 'hover' , function(){
            jSubmenu.hide();
        });
    });


});

