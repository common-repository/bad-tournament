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

    $_POST['player_down'] = true;
    $_POST['player_select'] = array( $_POST['pl_id'] );
    $player_keep_status = true;
    $player_current_tournament_only = true;

    include plugin_dir_path(__FILE__).'add-existing-players.php';
    // Can only change a game not yet started
    /* Get match infos to be sure match is not yet started/completed */
    $html_ajax .= __('Player removed of this tournament...', 'bad-tournament' );

}