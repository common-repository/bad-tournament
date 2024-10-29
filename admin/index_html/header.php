<?php
/**
 * Created by PhpStorm.
 * User: ldorier
 * Date: 13.11.2017
 * Time: 09:12
 */
global $tournament_typ_array;

/* MENU */
$html .= '<nav id="main_nav">';
$html .= '<ul>';
$html .= '<li class="nav_item'.( $ADMIN_VIEW == 'tournament' ? ' active"' : '' ).'" id="nav_tournament">';
$html .= __('Tournament', 'bad-tournament');
$html .= '</li>';

$html .= '<li class="nav_item'.( $ADMIN_VIEW == 'clubs' ? ' active"' : '' ).'" id="nav_club">';
$html .= __('Clubs', 'bad-tournament');
$html .= '</li>';

$html .= '<li class="nav_item'.( $ADMIN_VIEW == 'players' ? ' active"' : '' ).'" id="nav_player">';
$html .= __('Players', 'bad-tournament');
$html .= '</li>';

$html .= '<li class="nav_item'.( $ADMIN_VIEW == 'table' ? ' active"' : '' ).'" id="nav_table">';
$html .= __('Table', 'bad-tournament');
$html .= '</li>';

$html .= '<li class="nav_item'.( $ADMIN_VIEW == 'matches' ? ' active"' : '' ).'" id="nav_match">';
$html .= __('Matches', 'bad-tournament');
$html .= '</li>';

if( defined('BADT_DEBUG_MODE') && BADT_DEBUG_MODE > 0 ){
    $html .= '<li class="nav_item" id="display_debug_infos">';
    $html .= __('Debug infos', 'bad-tournament');
    $html .= '</li>';
}

$html .= '</ul>';
$html .= '</nav>';

/* MESSAGES */
if( !empty( $bvg_admin_msg ) ){
    $html .= '<div id="bvg_admin_msg"><span id="bvg_admin_msg_close"></span>'.$bvg_admin_msg.'</div>';
    /*
        echo '<div><pre>';
        var_dump( $_POST );
        echo '</pre></div>';
    */
}


if( $_SESSION['current_tournament'] !== false ){

    /* MAIN TITLE */
    $html .= '<h1 id="bad_tournament_maintitle" class="subtournament_name">'.$_SESSION['t_name'].' (ID:'.$_SESSION['current_tournament']['id'].')' .' ( '.__( 'Round', 'bad-tournament').': '.$_SESSION['round'].')</h1>';

    if( $_SESSION['current_tournament']['id'] != $_SESSION['current_tournament']['parent_id'] ){
        foreach( $tournaments as $k => $t ){
            if( $t->id == $_SESSION['current_tournament']['parent_id'] ){
                $tournament_parent = $tournaments[ $k ];
                $html .= '<h2 class="tournament_name">'.$tournament_parent->name.' (ID:'.$tournament_parent->id.')</h2>';
                break;
            }
        }

    }

    if( $_SESSION['current_tournament']['club_restriction'] > 0 ){
        $html .= '<h3>'.$_SESSION[ 'current_tournament' ][ 'club_restriction_name' ].'</h3>';
    }

    $html .= __('Tournament typ: ', 'bad-tournament').'<b>'.$tournament_typ_array[ $_SESSION['current_tournament'][ 'tournament_typ' ] ].'</b>';
    $html .= '<br />';
    $html .= __('Tournament system: ', 'bad-tournament').'<b>'.$t_system[ $_SESSION['current_tournament']['system'] ].'</b>';
    $html .= '<br />';
    $nb_players_tournament = count( $players );
    $html .= __('Players: ', 'bad-tournament').'<span id="nb_players_tournament" data-init="'.$nb_players_tournament.'">'.$nb_players_tournament.'</span>';
    $html .= '<br />';
    $html .= __('Matches played: ', 'bad-tournament').$nb_matchs;
    if( $_SESSION[ 'current_tournament' ][ 'round_max' ]  > 0){
        $html .= '<br />';
        $html .= __('Max rounds: ', 'bad-tournament').$_SESSION[ 'current_tournament' ][ 'round_max' ];
    }else{
        $html .= '<br />';
        $html .= __('Max rounds: ', 'bad-tournament').' '.__( 'No limit' , 'bad-tournament');
    }

    $html .= '<h4>'.__( 'Sets to win:', 'bad-tournament').' '.$_SESSION['current_tournament']['nb_sets'].' '.__( 'Points/Set:', 'bad-tournament').' '.$_SESSION['current_tournament']['points_set'].' '.__( 'Max. points per set:', 'bad-tournament').' '.$_SESSION['current_tournament']['max_points_set'].'</h4>';

}else{
    if( !empty( $tournaments ) ){
        $html .= '<h1 id="bad_tournament_maintitle" class="subtournament_name">'.__( 'You need to create a new tournament or select an existing tournament' , 'bad-tournament' ).'</h1>';
    }else{
        $html .= '<h1 id="bad_tournament_maintitle" class="subtournament_name">'.__( 'You need to create your first tournament' , 'bad-tournament' ).'</h1>';
    }
}




/*
echo '<pre>';
var_dump( $players );
echo '<hr />';
var_dump( $all_players );
echo '</pre>';
*/