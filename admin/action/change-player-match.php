<?php
/**
 * Created by PhpStorm.
 * User: ldorier
 * Date: 30.10.2017
 * Time: 15:32
 */

if( !is_numeric( $_POST['match_id'] ) ){

    $html_ajax .= 'Fehler: etwas stimmt hier nicht...';
}else{

    $nb_players_matches = 4;
    if( $_SESSION['current_tournament']['tournament_typ'] == 1 || $_SESSION['current_tournament']['tournament_typ'] == 2 || $_SESSION['current_tournament']['tournament_typ'] == 6 ){
        $nb_players_matches = 2;
    }

    global $wpdb;
    // Can only change a game not yet started
    /* Get match infos to be sure match is not yet started/completed */
    $query = "SELECT
    *
    
    FROM
    ".$wpdb->prefix."bvg_matches
    
    WHERE
    id = ".$_POST['match_id'];
    $matches = $wpdb->get_results( $query );

    if( $matches[0]->pl1_set1 > 0 || $matches[0]->pl2_set1 > 0 ){
        $html_ajax .= 'Match schon gestartet. Änderung unmöglich...';
    }else{

        $pl1_id_bis = 0;
        $pl2_id_bis = 0;
        if( $nb_players_matches == 4 ){
            $pl1_id = $_POST['players_id'][0];
            $pl2_id = $_POST['players_id'][2];
            if( isset( $_POST['players_id'][1] ) ){
                $pl1_id_bis = $_POST['players_id'][1];
                $pl2_id_bis = $_POST['players_id'][3];
            }
        }else{
            $pl1_id = $_POST['players_id'][0];
            $pl2_id = $_POST['players_id'][1];
        }


        if( !is_numeric( $pl1_id ) ||  !is_numeric( $pl2_id ) ||  !is_numeric( $pl1_id_bis ) ||  !is_numeric( $pl2_id_bis ) ){
            $html_ajax .= 'Fehler: etwas stimmt hier nicht !..'.$pl1_id.$pl2_id.$pl1_id_bis.$pl2_id_bis;
        }else{
            $data = array(
                'player1_id' => $pl1_id,
                'player2_id' => $pl2_id,
                'player1_id_bis' => $pl1_id_bis,
                'player2_id_bis' => $pl2_id_bis
            );
            $wpdb->query( 'SET foreign_key_checks = 0;' );
            $wpdb->update( $wpdb->prefix . 'bvg_matches',
                $data,
                array( 'id' => $_POST['match_id'] ) );
            $wpdb->query( 'SET foreign_key_checks = 1;' );

            $html_ajax .= 'Match geändert !';
        }


    }


}