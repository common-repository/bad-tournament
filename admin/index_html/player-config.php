<?php
/**
 * Created by PhpStorm.
 * User: ldorier
 * Date: 13.11.2017
 * Time: 09:12
 */

$nb_players_matches = 4;
if( $_SESSION['current_tournament']['tournament_typ'] == 1 || $_SESSION['current_tournament']['tournament_typ'] == 2 || $_SESSION['current_tournament']['tournament_typ'] == 6 ){
    $nb_players_matches = 2;
}

/*
echo '<pre>';
var_dump( $_SESSION['current_tournament'] );
var_dump( $tournaments );
var_dump( $all_players );
echo '</pre>';
*/

/* Players */
//$html .= '<div class="admin_block_label">Spieler</div>';
$html .= '<div class="admin_block nav_player" id="block_add_players" '.( $ADMIN_VIEW == 'players' ? 'style="display: block;"' : '' ).'>';

$nb_player_unactivated_for_this_tournament = 0;
foreach( $players as $pl ){
    if( $pl->status == 2 ){
        $nb_player_unactivated_for_this_tournament++;
    }
}

/* Remove players depending of sex if required */
foreach( $all_players as $k => $all_player ){
    if( ( $_SESSION['current_tournament']['tournament_typ'] == 1 || $_SESSION['current_tournament']['tournament_typ'] == 3 ) && $all_player->player_sex == 2 ){
        unset( $all_players[$k] );
    }
    if( ( $_SESSION['current_tournament']['tournament_typ'] == 2 || $_SESSION['current_tournament']['tournament_typ'] ==4 ) && $all_player->player_sex == 1 ){
        unset( $all_players[$k] );
    }
}

if( count( $all_players )  != count( $players ) - $nb_player_unactivated_for_this_tournament && count( $all_players ) > count( $players ) ){
    $html .= '<form method="post">';
    $html .= '<input type="hidden" name="form_action" value="add-existing-players" />';
    //$html .= '<input type="hidden" id="swiss_system_point" name="schweizer_system_punkte" value="0" />';

    $html .= '<table class="form-table">';

    $html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
    $html .= '<label>'.__('Player(s):', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
    $html .= '<select type="text" value="" name="player_select[]" id="player_select" multiple="multiple" >';
    //$html .= '<option value="0">'.__('Choose', 'bad-tournament').'</option>';

    /*
    echo '<pre>';
    var_dump( $all_players );
    var_dump( $players );
    echo '</pre>';
    */

    foreach( $all_players as $k => $all_player ){

        /*
        if( ( $_SESSION['current_tournament']['tournament_typ'] == 1 || $_SESSION['current_tournament']['tournament_typ'] == 3 ) && $all_player->player_sex == 2 ){
            continue;
        }
        if( ( $_SESSION['current_tournament']['tournament_typ'] == 2 || $_SESSION['current_tournament']['tournament_typ'] ==4 ) && $all_player->player_sex == 1 ){
            continue;
        }
        */

        foreach( $players as $player ){
            if( $player->players_id == $k && $player->status != 2 ){
                continue 2;
            }
        }

        $html .= '<option value="'.$all_player->player_id.'" data-sex="'.$all_player->player_sex.'" data_level="'.$all_player->player_level.'">'.$all_player->player_firstname.' '.$all_player->player_lastname.'</option>';
    }
    $html .= '</select>';
    $html .= '</td>';
    $html .= '</tr>';

    $html .= '<tr class="form-field form-required">';
        $html .= '<td colspan="2" >';
            $html .= '<input type="submit" class="button-primary button" value="'.__('Add player(s) to the tournament', 'bad-tournament').'" />';
            $html .= '<input type="submit" class="button-primary button submit2" value="'.__('Add all players', 'bad-tournament').'" id="all_players" name="all_players" />';
            $html .= '<input type="submit" class="button-primary button submit2" value="'.__('Remove all players for this Tournament', 'bad-tournament').'" id="player_tournament_remove" name="player_tournament_remove" />';
            $html .= '<input type="submit" class="button-primary button submit2" value="'.__('Set player(s) as inactive', 'bad-tournament').'" id="player_down" name="player_down" />';
        $html .= '</td>';
    $html .= '</tr>';
    $html .= '</table>';

    $html .= '</form>';
    $html .= '<hr />';
}else if( $nb_players_matches == 4 && $_SESSION[ 'current_tournament' ][ 'system' ] != 4 ){
    /* Need to set couple of players */
    $html .= '<form method="post">';
    $html .= '<input type="hidden" name="form_action" value="set-players-couple" />';
    $html .= '<input type="hidden" id="tournament_type" value="'.$_SESSION['current_tournament']['tournament_typ'].'" />';
    //$html .= '<input type="hidden" id="swiss_system_point" name="schweizer_system_punkte" value="0" />';

    $html .= '<table class="form-table">';

    $html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
    $html .= '<label>'.__('Set couples:', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';

    $html .= '<select type="text" value="" name="players_list_select_couple[]" id="players_list_select_couple" multiple="multiple" >';

    $doubles = array();
    foreach( $couples as $couple ){
        $doubles[] = $couple->player1_id;
        $doubles[] = $couple->player2_id;
    }

    //var_dump($players);
    foreach( $players as $player ){
        if( !in_array( $player->id , $doubles ) ){
            $html .= '<option value="'.$player->id.'" data-sex="'.$player->player_sex.'">'.$player->player_firstname.' '.$player->player_lastname.'</option>';
        }
    }
    $html .= '</select>';

    $html .= '</td>';
    $html .= '</tr>';
    $html .= '</table>';

    $html .= '<div id="players_couple_list">';
        $html .= '<ul>';

        $html .= '</ul>';
    $html .= '</div>';

    $html .= '</form>';
    $html .= '<hr />';
}




$html .= '<form method="post">';
$html .= '<h3>'.__('Add or edit player', 'bad-tournament').'</h3>';
$html .= '<input type="hidden" name="form_action" value="add-players" />';

$html .= '<table class="form-table">';

$html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
        $html .= '<label>'.__('Firstname:', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
        $html .= '<input type="text" value="" placeholder="'.__('Firstname', 'bad-tournament').'" name="firstname" />';
    $html .= '</td>';
$html .= '</tr>';

$html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
        $html .= '<label>'.__('Lastname:', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
        $html .= '<input type="text" value="" placeholder="'.__('Lastname', 'bad-tournament').'" name="lastname" />';
    $html .= '</td>';
$html .= '</tr>';

$html .= '</table>';

if( !isset( $_SESSION[ 'player_form_extended' ] ) || $_SESSION[ 'player_form_extended' ] != 1 ){
    $html .= '<div class="plus_icon"></div><br />';
    $html .= '<div id="player_infos_extended" class="admin_block_extended">';
}else{
    //$html .= '<div id="player_infos_extended" class="admin_block_extended" style="display: block;">';
}

$html .= '<table class="form-table">';

$html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
        $html .= '<label>'.__('Club:', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
        $html .= '<select name="club_id" id="club_player">';
        $html .= '<option value="0" >'.__( 'Choose...' , 'bad-tournament' ).'</option>';
        foreach( $clubs as $club ){
            $selected = '';
            if( $cl_default_id == $club->id ){
                $selected = 'selected="selected"';
            }
            $html .= '<option value="'.$club->id.'" '.$selected.'>'.$club->name.'</option>';
        }
        $html .= '</select>';
    $html .= '</td>';
$html .= '</tr>';

$html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
        $html .= '<label>'.__('Set as club contact:', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
        $html .= '<input type="checkbox" value="1" name="club_contact" />';
    $html .= '</td>';
$html .= '</tr>';

$html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
        $html .= '<label>'.__('Player ID:', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
        $html .= '<input type="text" value="" placeholder="'.__('XXXXXXXXX', 'bad-tournament').'" name="player_id" />';
    $html .= '</td>';
$html .= '</tr>';

$html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
        $html .= '<label>'.__('Birthdate:', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
        $html .= '<input type="text" value="" placeholder="'.__('DD/MM/YYYY', 'bad-tournament').'" name="birthdate" class="datepicker" />';
    $html .= '</td>';
$html .= '</tr>';

$selected = 0;
if( $_SESSION['current_tournament']['tournament_typ'] == 1 || $_SESSION['current_tournament']['tournament_typ'] == 3 ){
    $selected = 1;
}else if( $_SESSION['current_tournament']['tournament_typ'] == 2 || $_SESSION['current_tournament']['tournament_typ'] ==4 ){
    $selected = 2;
}
$html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
        $html .= '<label>'.__('Sex:', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
        $html .= '<div class="radio_block">';
        $html .= '<span><input type="radio" value="1" placeholder="'.__('Man', 'bad-tournament').'" name="sex" id="sex1" '.( $selected == 1 ? 'checked="checked" ' : '' ).'/><label for="sex1" class="radio">'.__('Male', 'bad-tournament').'</label></span>';
        $html .= '<span><input type="radio" value="2" placeholder="'.__('Woman', 'bad-tournament').'" name="sex" id="sex2" '.( $selected == 2 ? 'checked="checked" ' : '' ).'/><label for="sex2" class="radio">'.__('Female', 'bad-tournament').'</label></span>';
        $html .= '</div>';
    $html .= '</td>';
$html .= '</tr>';


wp_enqueue_media();

$pic_src = '';
$pic_src_id = '';

$html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
        $html .= '<label>'.__('Profile picture:', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
        $html .= '<div class="pic-preview-wrapper">';
            $html .= '<img id="pic-preview" src="'.$pic_src.'" height="100">';
        $html .= '</div>';
        $html .= '<input id="upload_pic_button" type="button" class="button" value="'.__( 'Choose image' , 'bad-tournament' ).'">';
        $html .= '<input type="hidden" name="profile_attachment_id" id="profile_attachment_id" value="'.$pic_src_id.'">';

        $html .= '<label class="new_line">'.__('or use external URL:', 'bad-tournament').'</label>';
        $html .= '<input type="text" value="'.$pic_src.'" placeholder="http://" name="profile_pic_url" id="profile_pic_url" />';
    $html .= '</td>';
$html .= '</tr>';


$html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
        $html .= '<label>'.__('Value (for swiss system):', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
        $html .= '<input type="text" value="" placeholder="'.__('Points', 'bad-tournament').'" name="swiss_system_point" />';
    $html .= '</td>';
$html .= '</tr>';

$html .= '</table>';

if( !isset( $_SESSION[ 'player_form_extended' ] ) || $_SESSION[ 'player_form_extended' ] != 1 ) {
    $html .= '</div>';
}



$html .= '<input type="submit" value="'.__('Add player to the tournament', 'bad-tournament').'" class="button-primary button" />';


$html .= '</form>';

if( count( $all_players ) > 0 ){
    //var_dump($all_players);
    $p_id_forShortcode = key($all_players);
    $html .= '<h1 class="topspace">'.__('Shortcodes', 'bad-tournament').'</h1>';
    $html .= '<div class="shortcode_bvg"><h2>'.__('Player view', 'bad-tournament').'</h2><input type="text" class="wp_style" value="[bad_tournament_player p_id='.$p_id_forShortcode.']" /></div>';
}


$html .= '</div>';
