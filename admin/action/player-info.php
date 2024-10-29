<?php
/**
 * Created by PhpStorm.
 * User: ldorier
 * Date: 30.10.2017
 * Time: 15:32
 */

if( !is_numeric( $_POST['pl_id'] ) ){

    $html_ajax .= 'Fehler: etwas stimmt hier nicht...';
}else{
    global $wpdb;
    // Can only change a game not yet started
    /* Get match infos to be sure match is not yet started/completed */
    $query = "SELECT
    *
    
    FROM
    ".$wpdb->prefix."bvg_players
    
    WHERE
    id = ".$_POST['pl_id'];
    $player = $wpdb->get_results( $query );

    /*
    $html_ajax .= '<div class="pl_tooltip_name">';
        $html_ajax .= '<label class="pl_tooltip_label">';
            $html_ajax .= __('Name: ', 'bad-tournament');
        $html_ajax .= '</label>';
        $html_ajax .= '<span class="label_row">'.$player[0]->firstname.' '.$player[0]->lastname.'</span>';
    $html_ajax .= '</div>';
    */

    if( is_numeric( $player[0]->profile_attachment_id ) ){
        $player[0]->profile_attachment_id = wp_get_attachment_url( $player[0]->profile_attachment_id );
    }
    $html_ajax .= '<div class="bad_tournament_logo" id="tournament_summary_logo">';
    $html_ajax .= '<img src="'.$player[0]->profile_attachment_id.'" class="bad_tournament_logo" />';
    $html_ajax .= '</div>';

    $html_ajax .= '<div class="pl_tooltip_player_name">';
        $html_ajax .= '<label class="pl_tooltip_label">';
            $html_ajax .= __('Name: ', 'bad-tournament');
        $html_ajax .= '</label>';
        $html_ajax .= '<span class="label_row"><span class="player_current_value">'. $player[0]->firstname .' '. $player[0]->lastname .'</span><input class="pl_edit_field" type="text" id="firstname1" name="firstname" value="'.$player[0]->firstname.'" /> <input class="pl_edit_field" type="text" id="lastname1" name="lastname" value="'.$player[0]->lastname.'" /></span>';
    $html_ajax .= '</div>';
    $html_ajax .= '<div class="pl_tooltip_player_id">';
        $html_ajax .= '<label class="pl_tooltip_label">';
            $html_ajax .= __('ID: ', 'bad-tournament');
        $html_ajax .= '</label>';
        $html_ajax .= '<span class="label_row"><span class="player_current_value">'.( $player[0]->player_id == '' ? ' - ' : $player[0]->player_id ).'</span><input class="pl_edit_field" type="text" id="player_id" name="player_id" value="'.$player[0]->player_id.'" /></span>';
    $html_ajax .= '</div>';
    $html_ajax .= '<div class="pl_tooltip_sex">';
        $html_ajax .= '<label class="pl_tooltip_label">';
            $html_ajax .= __('Sex: ', 'bad-tournament');
        $html_ajax .= '</label>';
        $html_ajax .= '<span class="label_row"><span class="player_current_value">'.( $player[0]->sex == 2 ? __( 'Female' , 'bad-tournament' ) : __( 'Male' , 'bad-tournament' ) ).'</span><select class="pl_edit_field" id="sex" name="sex" ><option value="0" class="no_edit">'.__( 'Choose...' , 'bad-tournament' ).'</option><option value="1" '.( $player[0]->sex == 1 ? 'selected="selected"' : '' ).'>'.__( 'Male' , 'bad-tournament' ).'</option><option value="2" '.( $player[0]->sex == 2 ? 'selected="selected"' : '' ).'>'.__( 'Female' , 'bad-tournament' ).'</option></select></span>';
    $html_ajax .= '</div>';
    $html_ajax .= '<div class="pl_tooltip_birthdate">';
        $html_ajax .= '<label class="pl_tooltip_label">';
            $html_ajax .= __('Birthdate: ', 'bad-tournament');
        $html_ajax .= '</label>';
            $date = new DateTime( $player[0]->birthdate );
            $html_ajax .= '<span class="label_row"><span class="player_current_value">'.$date->format('d/m/Y').'</span><input class="pl_edit_field datepicker" type="text" id="birthdate" name="birthdate" value="'.$date->format('d/m/Y').'" /></span>';
    $html_ajax .= '</div>';
    if( $_SESSION['t_system'] == 1 ){
        $html_ajax .= '<div class="pl_tooltip_level_init">';
            $html_ajax .= '<label class="pl_tooltip_label">';
                $html_ajax .= __('Level: ', 'bad-tournament');
            $html_ajax .= '</label>';
            $html_ajax .= '<span class="label_row"><span class="player_current_value" id="player1_current_value">'.$player[0]->player_level.'</span><input class="pl_edit_field" type="text" id="player_level" name="player_level" value="'.$player[0]->player_level.'" /></span>';
        $html_ajax .= '</div>';
    }

    if( $_SESSION[ 'current_tournament' ][ 'tournament_double' ] === true ){
        /* Double => 2nd player infos
        */
        $query = "SELECT
        *

        FROM
        ".$wpdb->prefix."bvg_players

        WHERE
        id = ".$_POST['pl2_id'];
        $player = $wpdb->get_results( $query );

        $html_ajax .= '<hr />';
        //$html_ajax .= $query;

        if( is_numeric( $player[0]->profile_attachment_id ) ){
            $player[0]->profile_attachment_id = wp_get_attachment_url( $player[0]->profile_attachment_id );
        }
        $html_ajax .= '<div class="bad_tournament_logo" id="tournament_summary_logo">';
        $html_ajax .= '<img src="'.$player[0]->profile_attachment_id.'" class="bad_tournament_logo" />';
        $html_ajax .= '</div>';

        $html_ajax .= '<div class="pl_tooltip_player_name">';
        $html_ajax .= '<label class="pl_tooltip_label">';
        $html_ajax .= __('Name: ', 'bad-tournament');
        $html_ajax .= '</label>';
        $html_ajax .= '<span class="label_row"><span class="player_current_value">'. $player[0]->firstname .' '. $player[0]->lastname .'</span><input class="pl_edit_field pl_edit_field2" type="text" id="firstname2" name="firstname" value="'.$player[0]->firstname.'" /> <input class="pl_edit_field pl_edit_field2" type="text" id="lastname2" name="lastname" value="'.$player[0]->lastname.'" /></span>';
        $html_ajax .= '</div>';
        $html_ajax .= '<div class="pl_tooltip_player_id">';
        $html_ajax .= '<label class="pl_tooltip_label">';
        $html_ajax .= __('ID: ', 'bad-tournament');
        $html_ajax .= '</label>';
        $html_ajax .= '<span class="label_row"><span class="player_current_value">'.( $player[0]->player_id == '' ? ' - ' : $player[0]->player_id ).'</span><input class="pl_edit_field pl_edit_field2" type="text" id="player_id" name="player_id" value="'.$player[0]->player_id.'" /></span>';
        $html_ajax .= '</div>';
        $html_ajax .= '<div class="pl_tooltip_sex">';
        $html_ajax .= '<label class="pl_tooltip_label">';
        $html_ajax .= __('Sex: ', 'bad-tournament');
        $html_ajax .= '</label>';
        $html_ajax .= '<span class="label_row"><span class="player_current_value">'.( $player[0]->sex == 2 ? __( 'Female' , 'bad-tournament' ) : __( 'Male' , 'bad-tournament' ) ).'</span><select class="pl_edit_field pl_edit_field2" id="sex" name="sex" ><option value="0" class="no_edit">'.__( 'Choose...' , 'bad-tournament' ).'</option><option value="1" '.( $player[0]->sex == 1 ? 'selected="selected"' : '' ).'>'.__( 'Male' , 'bad-tournament' ).'</option><option value="2" '.( $player[0]->sex == 2 ? 'selected="selected"' : '' ).'>'.__( 'Female' , 'bad-tournament' ).'</option></select></span>';
        $html_ajax .= '</div>';
        $html_ajax .= '<div class="pl_tooltip_birthdate">';
        $html_ajax .= '<label class="pl_tooltip_label">';
        $html_ajax .= __('Birthdate: ', 'bad-tournament');
        $html_ajax .= '</label>';
        $date = new DateTime( $player[0]->birthdate );
        $html_ajax .= '<span class="label_row"><span class="player_current_value">'.$date->format('d/m/Y').'</span><input class="pl_edit_field pl_edit_field2 datepicker" type="text" id="birthdate" name="birthdate" value="'.$date->format('d/m/Y').'" /></span>';
        $html_ajax .= '</div>';
        if( $_SESSION['t_system'] == 1 ){
            $html_ajax .= '<div class="pl_tooltip_level_init">';
            $html_ajax .= '<label class="pl_tooltip_label">';
            $html_ajax .= __('Level: ', 'bad-tournament');
            $html_ajax .= '</label>';
            $html_ajax .= '<span class="label_row"><span class="player_current_value" id="player2_current_value">'.$player[0]->player_level.'</span><input class="pl_edit_field pl_edit_field2" type="text" id="player_level" name="player_level" value="'.$player[0]->player_level.'" /></span>';
            $html_ajax .= '</div>';
        }

    }

    /* Matches
    */

    $query = "SELECT
    id

    FROM
    ".$wpdb->prefix."bvg_players_tournament

    WHERE
    tournament_id= ".$_SESSION[ 't_id' ]."
    AND
    players_id = ".$_POST['pl_id'];
    $player = $wpdb->get_results( $query );

    include_once plugin_dir_path(__FILE__). '../db-get-content.php';
    $matches = badt_db_get_matches( $_SESSION['t_id'], false, false, $player[0]->id );
    $players = badt_db_get_players( $_SESSION['t_id'] );


    $html = '';
    $ROUND = false;
    $ROUND_MAX = 1000;
    $matchs_view_type = 'list_player_view';
    include_once plugin_dir_path(__FILE__). '../../shortcodes/sc_html/matches-view.php';
    $html_ajax .= '<div id="matches_list_player">';
    $html_ajax .= '<h3>'.__( 'Matches' , 'bad-tournament' ).'</h3>';
    $html_ajax .= $html;
    $html_ajax .= '</div>';
    /*
    echo '<pre>';
    var_dump( $player_matches );
    echo '</pre>';
    */
}