<?php
/**
 * Created by PhpStorm.
 * User: ldorier
 * Date: 30.10.2017
 * Time: 15:32
 * Actions: Remove all players for the current tournament
 *          Unactivate player for the current tournament
 *          Add (all) existing players to the current tournament
 */


/*
echo '<pre>';
var_dump( $all_players );
echo '</pre>';
*/
$wpdb->show_errors();
if( isset( $_POST['player_tournament_remove'] ) ){
    // Remove all players for the current tournament

    $query = "DELETE FROM
    ".$wpdb->prefix."bvg_matches

    WHERE
    tournament_id=".$_SESSION['t_id'];
    $wpdb->query( $query );

    $query = "DELETE FROM
    ".$wpdb->prefix."bvg_players_tournament

    WHERE
    tournament_id=".$_SESSION['t_id'];
    $wpdb->query( $query );


    $bvg_admin_msg .= 'Alle Spieler für das Turnier gelöscht...';

}else if( isset( $_POST['player_down'] ) ){
    // Unactivate player
    //$html_ajax .= __( 'Unactivate player now...', 'bad-tournament' );
    foreach( $_POST['player_select'] as $pl_id ){
        if( is_numeric( $pl_id )){
            //$html_ajax .= __( ' is_numeric ', 'bad-tournament' );
            if( !isset( $player_keep_status ) || $player_keep_status === false ){
                $query = "UPDATE
                ".$wpdb->prefix."bvg_players

                SET
                status=2

                WHERE
                id=".$pl_id;
                $wpdb->query( $query );
            }

            if( isset( $player_current_tournament_only) && $player_current_tournament_only ){
                $query = "UPDATE
                ".$wpdb->prefix."bvg_players_tournament

                SET
                status=2

                WHERE
                players_id=".$pl_id."
                AND
                tournament_id=".$_SESSION['t_id'];
                $wpdb->query( $query );

                $query = "SELECT
                pl_t.id

                FROM
                ".$wpdb->prefix."bvg_players_tournament as pl_t

                WHERE
                players_id=".$pl_id."
                AND
                tournament_id=".$_SESSION['t_id'];
            }else{
                $query = "UPDATE
                ".$wpdb->prefix."bvg_players_tournament

                SET
                status=2

                WHERE
                players_id=".$pl_id;
                $wpdb->query( $query );

                // Get player ID for this tournament if existing
                /* Get all players for the current tournament */
                $query = "SELECT
                pl_t.id

                FROM
                ".$wpdb->prefix."bvg_players_tournament as pl_t

                WHERE
                players_id=".$pl_id;

            }
            $player_tournaments = $wpdb->get_results( $query, OBJECT_K  );
            foreach( $player_tournaments as $pl_t ){
                $pl_t_ids[] = $pl_t->id;
            }
            if( is_array( $pl_t_ids ) ){
                $pl_t_ids_sql = implode( $pl_t_ids, ',' );
            }else{
                $pl_t_ids_sql = -25;
            }

            //var_dump( $pl_t_ids_sql );

            $query = $query = "UPDATE
            ".$wpdb->prefix."bvg_matches
    
            SET
            winner=player2_id
    
            WHERE
            winner=0
            AND
            (
                player1_id IN (".$pl_t_ids_sql.")
                OR
                player1_id_bis IN (".$pl_t_ids_sql.")
            )";
            //echo $query;
            $all_players = $wpdb->query( $query );

            $query = $query = "UPDATE
            ".$wpdb->prefix."bvg_matches
    
            SET
            winner=player1_id
    
            WHERE
            winner=0
            AND
            (
                player2_id IN (".$pl_t_ids_sql.")
                OR
                player2_id_bis IN (".$pl_t_ids_sql.")
            )";
            $all_players = $wpdb->query( $query );
        }

    }


    $bvg_admin_msg .= __('Player(s) now inactive.', 'bad-tournament');

}else if( isset( $_POST['all_players'] ) ){

    $where = '';
    if( $_SESSION['current_tournament']['tournament_typ'] == 1 || $_SESSION['current_tournament']['tournament_typ'] == 3 ){
        $where = '
        AND
        pl.sex = 1';
    }else if( $_SESSION['current_tournament']['tournament_typ'] == 2 || $_SESSION['current_tournament']['tournament_typ'] ==4 ){
        $where = '
        AND
        pl.sex = 2';
    }
    $query = "SELECT
    pl.id as player_id,
    pl.player_level as player_level_init
    
    FROM
    ".$wpdb->prefix."bvg_players as pl

    WHERE
    pl.status=1
    ".$where;

    if( $_SESSION[ 'current_tournament' ][ 'club_restriction' ] > 0 ){
        $query .= '
        AND club_id='.$_SESSION[ 'current_tournament' ][ 'club_restriction' ];
    }

    $all_players = $wpdb->get_results( $query, OBJECT_K  );

    foreach( $all_players as $pl ){
        $data = array(
            'tournament_id' => $_SESSION['t_id'],
            'players_id' => $pl->player_id,
            'player_level_init' => $pl->player_level_init,
            'status' => 1
        );
        $wpdb->insert( $wpdb->prefix . 'bvg_players_tournament', $data );
    }
    unset( $all_players );


    $bvg_admin_msg .= 'Alle Spieler für das Turnier gespeichert...';

}else if( !empty( $_POST['player_select'] ) ){

    // Add player(s) to the tournament
    include_once plugin_dir_path(__FILE__). '../db-get-content.php';
    $players = badt_db_get_all_players();
    $players_in_tournament = badt_db_get_players();
    $nb_new_players = 0;

    //var_dump( $players );
    foreach( $_POST['player_select'] as $pl_id ){
        if( !is_numeric( $pl_id ) ){
            break;
        }


        $player_already_in_tournament = false;
        foreach( $players_in_tournament as $player_in_tournament){
/*
            echo $pl_id.'<br />';
            echo $player_in_tournament->player_id.'<br />';
            echo $player_in_tournament->status.'<br />';
*/
            if( $player_in_tournament->player_id == $pl_id && $player_in_tournament->status == 2 ){
                $data = array(
                    'status' => 1
                );
                $wpdb->update( $wpdb->prefix . 'bvg_players_tournament',
                    $data,
                    array( 'id' => $player_in_tournament->id ) );
                $player_already_in_tournament = true;
                $bvg_admin_msg .= sprintf( __( "Player %1$d %2$d restored for the current tournament...", 'bad-tournament' ), $player_in_tournament->player_firstname, $player_in_tournament->player_lastname );
                break;
            }
        }
        if( !$player_already_in_tournament ){
            $nb_new_players++;
            $data = array(
                'tournament_id' => $_SESSION['t_id'],
                'players_id' => $pl_id,
                'player_level_init' => $players[ $pl_id ]->player_level,
                'status' => 1
            );
            $wpdb->insert( $wpdb->prefix . 'bvg_players_tournament', $data );
        }

    }

    if( $nb_new_players > 0 ) {
        $bvg_admin_msg .= __('Player(s) added to the current tournament...', 'bad-tournament');
    }
}

