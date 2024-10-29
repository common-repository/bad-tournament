<?php
/**
 * Created by PhpStorm.
 * User: ldorier
 * Date: 30.10.2017
 * Time: 15:32
 */

$debugs = array(
    'Game Result',
    'File loaded'
);
if( defined('BADT_DEBUG_MODE') && BADT_DEBUG_MODE > 0 ) {
    $badt_debug_obj->store_debug($debugs);
}
//$wpdb->show_errors();

if( !is_numeric( $_POST['match_id'] ) ){
    if( $_POST['form_subaction'] == 'allgames-result' ){
        /* Save all matches */

        $json_data = $_POST[ 'json_data' ];
        $matches = json_decode( stripslashes( $json_data ) );

        foreach( $matches as $match ){
            $bvg_admin_msg .= badt_update_match( $match );
        }

    }else{
        $bvg_admin_msg .= 'Fehler: Match ID ist falsch...';
    }

}else{

    $bvg_admin_msg .= badt_update_match( $_POST );

    $bvg_admin_msg .= '<br />';
}

function badt_update_match( $match_vals ){
    global $wpdb, $badt_debug_obj;
    $bvg_admin_msg = '';

    if( !isset( $match_vals['match_id'] ) ){
        $match_vals_stocked = $match_vals;
        $match_vals = array();
        foreach( $match_vals_stocked as $param ){
            $match_vals[ $param->name ] = $param->value;
        }
    }

    /* Get match values before update */
    $match_before_update = badt_db_get_matches( false, false, $match_vals['match_id'] )[0];
    //var_dump( $match_before_update );

    $winner = 0;

    $m_id = $match_vals['match_id'];

    $pl1_id = $match_vals['pl1_id'];
    $pl2_id = $match_vals['pl2_id'];
    $pl1_id_bis = $match_vals['pl1_id_bis'];
    $pl2_id_bis = $match_vals['pl2_id_bis'];

    $pl1_set1 = $match_vals['pl1_m'.$m_id.'_set1'];
    $pl2_set1 = $match_vals['pl2_m'.$m_id.'_set1'];
    $pl1_set2 = $match_vals['pl1_m'.$m_id.'_set2'];
    $pl2_set2 = $match_vals['pl2_m'.$m_id.'_set2'];
    $pl1_set3 = $match_vals['pl1_m'.$m_id.'_set3'];
    $pl2_set3 = $match_vals['pl2_m'.$m_id.'_set3'];
    $pl1_set4 = $match_vals['pl1_m'.$m_id.'_set4'];
    $pl2_set4 = $match_vals['pl2_m'.$m_id.'_set4'];
    $pl1_set5 = $match_vals['pl1_m'.$m_id.'_set5'];
    $pl2_set5 = $match_vals['pl2_m'.$m_id.'_set5'];

    $s1 = 0;
    $s2 = 0;
    if( $pl1_set1 > $pl2_set1 ){
        $s1++;
    }elseif( $pl1_set1 < $pl2_set1 ){
        $s2++;
    }
    if( $pl1_set2 > $pl2_set2 ){
        $s1++;
    }elseif( $pl1_set2 < $pl2_set2 ){
        $s2++;
    }
    if( $pl1_set3 > $pl2_set3 ){
        $s1++;
    }elseif( $pl1_set3 < $pl2_set3 ){
        $s2++;
    }
    if( $pl1_set4 > $pl2_set4 ){
        $s1++;
    }elseif( $pl1_set4 < $pl2_set4 ){
        $s2++;
    }
    if( $pl1_set5 > $pl2_set5 ){
        $s1++;
    }elseif( $pl1_set5 < $pl2_set5 ){
        $s2++;
    }

    if( is_numeric( $match_vals['match_winner_'.$m_id] ) && $match_vals['match_winner_'.$m_id] > 0 ){
        $winner = $match_vals['match_winner_'.$m_id];
    }else{
        /* Try to set winner */
        $bvg_admin_msg .= 'Try to set winner...<br />';
        $nb_sets_completed = 0;
        $nb_sets_pl1 = 0;
        $nb_sets_pl2 = 0;

        if( $pl1_set1 > ($_SESSION['t_points_set']-1) || $pl2_set1 > ($_SESSION['t_points_set']-1) ){
            # First set
            $nb_sets_completed++;
            if( $pl1_set1 > $pl2_set1 ){
                $nb_sets_pl1++;
            }else{
                $nb_sets_pl2++;
            }
        }
        if( $pl1_set2 > ($_SESSION['t_points_set']-1) || $pl2_set2 > ($_SESSION['t_points_set']-1) ){
            # First set
            $nb_sets_completed++;
            if( $pl1_set2 > $pl2_set2 ){
                $nb_sets_pl1++;
            }else{
                $nb_sets_pl2++;
            }
        }
        if( $pl1_set3 > ($_SESSION['t_points_set']-1) || $pl2_set3 > ($_SESSION['t_points_set']-1) ){
            # First set
            $nb_sets_completed++;
            if( $pl1_set3 > $pl2_set3 ){
                $nb_sets_pl1++;
            }else{
                $nb_sets_pl2++;
            }
        }
        if( $pl1_set4 > ($_SESSION['t_points_set']-1) || $pl2_set4 > ($_SESSION['t_points_set']-1) ){
            # First set
            $nb_sets_completed++;
            if( $pl1_set4 > $pl2_set4 ){
                $nb_sets_pl1++;
            }else{
                $nb_sets_pl2++;
            }
        }
        if( $pl1_set5 > ($_SESSION['t_points_set']-1) || $pl2_set5 > ($_SESSION['t_points_set']-1) ){
            # First set
            $nb_sets_completed++;
            if( $pl1_set5 > $pl2_set5 ){
                $nb_sets_pl1++;
            }else{
                $nb_sets_pl2++;
            }
        }

        //echo 'NB Sets: '.$_SESSION['t_nb_sets'];
        if( $nb_sets_completed >= $_SESSION['t_nb_sets'] ){
            $winner = $pl1_id;
            if( $nb_sets_pl2 > $nb_sets_pl1 ){
                $winner = $pl2_id;
            }
        }
    }

    $bvg_admin_msg .= __( 'Winner: ' , 'bad-tournament' ).$winner.'<br />';

    if( $winner > 0 ){
        $pl1_level_current_change = 0;
        $pl2_level_current_change = 0;

        /* Update tournament table */
        if( $match_before_update->winner > 0 ){
            // Match was already updated...
            $played = 0;
            if( $match_before_update->winner == $pl1_id ){
                $pl1_level_current_change = ( $winner == $pl1_id ? 0 : -1 );
                $pl2_level_current_change = ( $winner == $pl1_id ? 0 : 1 );
                $p = ( $winner == $pl1_id ? 0 : -1 );
                $w = ( $winner == $pl1_id ? 0 : -1 );
                $l = ( $winner == $pl2_id ? 1 : 0 );
            }else{
                $pl1_level_current_change = ( $winner == $pl1_id ? 1 : 0 );
                $pl2_level_current_change = ( $winner == $pl1_id ? -1 : 0 );
                $p = ( $winner == $pl1_id ? 1 : 0 );
                $w = ( $winner == $pl1_id ? 1 : 0 );
                $l = ( $winner == $pl2_id ? 0 : -1 );
            }

            $d = 0;

            $s = $s1
                - ( $match_before_update->pl1_set1 > $match_before_update->pl2_set1 ? 1 : 0 )
                - ( $match_before_update->pl1_set2 > $match_before_update->pl2_set2 ? 1 : 0 )
                - ( $match_before_update->pl1_set3 > $match_before_update->pl2_set3 ? 1 : 0 )
                - ( $match_before_update->pl1_set4 > $match_before_update->pl2_set4 ? 1 : 0 )
                - ( $match_before_update->pl1_set5 > $match_before_update->pl2_set5 ? 1 : 0 );

            $s_opp = $s2
                - ( $match_before_update->pl2_set1 > $match_before_update->pl1_set1 ? 1 : 0 )
                - ( $match_before_update->pl2_set2 > $match_before_update->pl1_set2 ? 1 : 0 )
                - ( $match_before_update->pl2_set3 > $match_before_update->pl1_set3 ? 1 : 0 )
                - ( $match_before_update->pl2_set4 > $match_before_update->pl1_set4 ? 1 : 0 )
                - ( $match_before_update->pl2_set5 > $match_before_update->pl1_set5 ? 1 : 0 );

            $pt = ( $pl1_set1+ $pl1_set2 +$pl1_set3 + $pl1_set4 + $pl1_set5 ) - ( $match_before_update->pl1_set1 + $match_before_update->pl1_set2 + $match_before_update->pl1_set3 + $match_before_update->pl1_set4 + $match_before_update->pl1_set5 );
            $pt_opp = ( $pl2_set1+ $pl2_set2 +$pl2_set3 + $pl2_set4 + $pl2_set5 ) - ( $match_before_update->pl2_set1 + $match_before_update->pl2_set2 + $match_before_update->pl2_set3 + $match_before_update->pl2_set4 + $match_before_update->pl2_set5 );
        }else{
            $played = 1;
            $pl1_level_current_change = ( $winner == $pl1_id ? 1 : 0 );
            $pl2_level_current_change = ( $winner == $pl2_id ? 1 : 0 );
            $p = ( $winner == $pl1_id ? 1 : 0 );
            $w = ( $winner == $pl1_id ? 1 : 0 );
            $d = 0;
            $l = ( $winner == $pl2_id ? 1 : 0 );
            $s = $s1;
            $s_opp = $s2;
            $pt = ( $pl1_set1+ $pl1_set2 +$pl1_set3 + $pl1_set4 + $pl1_set5 );
            $pt_opp = ( $pl2_set1+ $pl2_set2 +$pl2_set3 + $pl2_set4 + $pl2_set5 );
        }

        /* Update for simple matches
        */
        $wpdb->query( $wpdb->prepare(
            "UPDATE
            ".$wpdb->prefix . 'bvg_players_tournament'."

            SET
            player_level_current=player_level_current+".$pl1_level_current_change.",
            played=played+%d,
            victory=victory+%d,
            draw=draw+%d,
            loss=loss+%d,
            points_major=points_major+%d,
            sets=sets+%d,
            sets_against=sets_against+%d,
            points=points+%d,
            points_against=points_against+%d

            WHERE
            id=".$pl1_id,

                $played, $w, $d, $l, $p, $s, $s_opp, $pt, $pt_opp
            )
        );
        $debugs = array(
            'Update for simple matches',
            'Update bvg_players_tournament table for player ID: '.$pl1_id
        );
        if( defined('BADT_DEBUG_MODE') && BADT_DEBUG_MODE > 0 ) {
            $badt_debug_obj->store_debug($debugs);
        }
        /* Update Grinding Tournament Double => Partner
        */
        if( $_SESSION['t_system'] == 4 && ( $_SESSION['current_tournament']['tournament_typ'] == 3 || $_SESSION['current_tournament']['tournament_typ'] == 4 || $_SESSION['current_tournament']['tournament_typ'] == 5 || $_SESSION['current_tournament']['tournament_typ'] == 7 ) ){

            $wpdb->query( $wpdb->prepare(
                "UPDATE
                ".$wpdb->prefix . 'bvg_players_tournament'."

                SET
                player_level_current=player_level_current+".$pl1_level_current_change.",
                played=played+%d,
                victory=victory+%d,
                draw=draw+%d,
                loss=loss+%d,
                points_major=points_major+%d,
                sets=sets+%d,
                sets_against=sets_against+%d,
                points=points+%d,
                points_against=points_against+%d

                WHERE
                id=".$pl1_id_bis,

                $played, $w, $d, $l, $p, $s, $s_opp, $pt, $pt_opp
            )
            );
        }
        /* Update Double (if not grinding)
            */
        else if( $_SESSION['current_tournament']['tournament_typ'] == 3 || $_SESSION['current_tournament']['tournament_typ'] == 4 || $_SESSION['current_tournament']['tournament_typ'] == 5 || $_SESSION['current_tournament']['tournament_typ'] == 7 ){

            $wpdb->query( $wpdb->prepare(
                "UPDATE
                ".$wpdb->prefix . 'bvg_players_double'."

                SET
                player_level_current=player_level_current+".$pl1_level_current_change.",
                played=played+%d,
                victory=victory+%d,
                draw=draw+%d,
                loss=loss+%d,
                points_major=points_major+%d,
                sets=sets+%d,
                sets_against=sets_against+%d,
                points=points+%d,
                points_against=points_against+%d

                WHERE
                tournament_id=".$_SESSION[ 't_id' ]."
                AND
                player1_id=".$pl1_id,

                $played, $w, $d, $l, $p, $s, $s_opp, $pt, $pt_opp
                )
            );

        }

        if( $match_before_update->winner > 0 ) {
            // Match was already updated...
            $played = 0;
            if( $match_before_update->winner == $pl2_id ){
                $p = ( $winner == $pl2_id ? 0 : -1 );
                $w = ( $winner == $pl2_id ? 0 : -1 );
                $l = ( $winner == $pl1_id ? 1 : 0 );
            }else{
                $p = ( $winner == $pl2_id ? 1 : 0 );
                $w = ( $winner == $pl2_id ? 1 : 0 );
                $l = ( $winner == $pl1_id ? 0 : -1 );
            }

            $d = 0;

            $s_opp = $s1
                - ( $match_before_update->pl1_set1 > $match_before_update->pl2_set1 ? 1 : 0 )
                - ( $match_before_update->pl1_set2 > $match_before_update->pl2_set2 ? 1 : 0 )
                - ( $match_before_update->pl1_set3 > $match_before_update->pl2_set3 ? 1 : 0 )
                - ( $match_before_update->pl1_set4 > $match_before_update->pl2_set4 ? 1 : 0 )
                - ( $match_before_update->pl1_set5 > $match_before_update->pl2_set5 ? 1 : 0 );

            $s = $s2
                - ( $match_before_update->pl2_set1 > $match_before_update->pl1_set1 ? 1 : 0 )
                - ( $match_before_update->pl2_set2 > $match_before_update->pl1_set2 ? 1 : 0 )
                - ( $match_before_update->pl2_set3 > $match_before_update->pl1_set3 ? 1 : 0 )
                - ( $match_before_update->pl2_set4 > $match_before_update->pl1_set4 ? 1 : 0 )
                - ( $match_before_update->pl2_set5 > $match_before_update->pl1_set5 ? 1 : 0 );

            $pt_opp = ( $pl1_set1+ $pl1_set2 +$pl1_set3 + $pl1_set4 + $pl1_set5 ) - ( $match_before_update->pl1_set1 + $match_before_update->pl1_set2 + $match_before_update->pl1_set3 + $match_before_update->pl1_set4 + $match_before_update->pl1_set5 );
            $pt = ( $pl2_set1+ $pl2_set2 +$pl2_set3 + $pl2_set4 + $pl2_set5 ) - ( $match_before_update->pl2_set1 + $match_before_update->pl2_set2 + $match_before_update->pl2_set3 + $match_before_update->pl2_set4 + $match_before_update->pl2_set5 );
            //echo 'XX: '.$pt.' ('.( $pl2_set1+ $pl2_set2 +$pl2_set3 + $pl2_set4 + $pl2_set5 ).' / '.( $match_before_update->pl2_set1 + $match_before_update->pl2_set2 + $match_before_update->pl2_set3 + $match_before_update->pl2_set4 + $match_before_update->pl2_set5 ).')<br />';
            //echo 'YY: '.$pt_opp.' ('.( $pl1_set1+ $pl1_set2 +$pl1_set3 + $pl1_set4 + $pl1_set5 ).' / '.( $match_before_update->pl1_set1 + $match_before_update->pl1_set2 + $match_before_update->pl1_set3 + $match_before_update->pl1_set4 + $match_before_update->pl1_set5 ).')<br />';
        }else{
            $played = 1;
            $p = ( $winner == $pl2_id ? 1 : 0 );
            $w = ( $winner == $pl2_id ? 1 : 0 );
            $d = 0;
            $l = ( $winner == $pl1_id ? 1 : 0 );
            $s_opp = $s1;
            $s = $s2;
            $pt_opp = ( $pl1_set1+ $pl1_set2 +$pl1_set3 + $pl1_set4 + $pl1_set5 );
            $pt = ( $pl2_set1+ $pl2_set2 +$pl2_set3 + $pl2_set4 + $pl2_set5 );
        }

        /*echo $wpdb->prepare(
            "UPDATE
            ".$wpdb->prefix . 'bvg_players_tournament'."

            SET
            player_level_current=player_level_current+".$pl2_level_current_change.",
            played=played+%d,
            victory=victory+%d,
            draw=draw+%d,
            loss=loss+%d,
            points_major=points_major+%d,
            sets=sets+%d,
            sets_against=sets_against+%d,
            points=points+%d,
            points_against=points_against+%d

            WHERE
            id=".$pl2_id,

            $played, $w, $d, $l, $p, $s, $s_opp, $pt, $pt_opp
        );*/

        /* Update for simple matches
        */

        $wpdb->query($wpdb->prepare(
            "UPDATE
            " . $wpdb->prefix . 'bvg_players_tournament' . "

            SET
            player_level_current=player_level_current+" . $pl2_level_current_change . ",
            played=played+%d,
            victory=victory+%d,
            draw=draw+%d,
            loss=loss+%d,
            points_major=points_major+%d,
            sets=sets+%d,
            sets_against=sets_against+%d,
            points=points+%d,
            points_against=points_against+%d

            WHERE
            id=" . $pl2_id,

            $played, $w, $d, $l, $p, $s, $s_opp, $pt, $pt_opp
            )
        );


        /* Update Grinding Tournament Double => Partner
        */
        if( $_SESSION['t_system'] == 4 && ( $_SESSION['current_tournament']['tournament_typ'] == 3 || $_SESSION['current_tournament']['tournament_typ'] == 4 || $_SESSION['current_tournament']['tournament_typ'] == 5 || $_SESSION['current_tournament']['tournament_typ'] == 7 ) ){
            $wpdb->query( $wpdb->prepare(
                "UPDATE
                ".$wpdb->prefix . 'bvg_players_tournament'."

                SET
                player_level_current=player_level_current+".$pl2_level_current_change.",
                played=played+%d,
                victory=victory+%d,
                draw=draw+%d,
                loss=loss+%d,
                points_major=points_major+%d,
                sets=sets+%d,
                sets_against=sets_against+%d,
                points=points+%d,
                points_against=points_against+%d

                WHERE
                id=".$pl2_id_bis,

                $played, $w, $d, $l, $p, $s, $s_opp, $pt, $pt_opp
                )
            );
        }
        /* Update Double (if not grinding)
        */
        else if( $_SESSION['current_tournament']['tournament_typ'] == 3 || $_SESSION['current_tournament']['tournament_typ'] == 4 || $_SESSION['current_tournament']['tournament_typ'] == 5 || $_SESSION['current_tournament']['tournament_typ'] == 7 ){

            $wpdb->query( $wpdb->prepare(
                "UPDATE
                ".$wpdb->prefix . 'bvg_players_double'."

                SET
                player_level_current=player_level_current+".$pl2_level_current_change.",
                played=played+%d,
                victory=victory+%d,
                draw=draw+%d,
                loss=loss+%d,
                points_major=points_major+%d,
                sets=sets+%d,
                sets_against=sets_against+%d,
                points=points+%d,
                points_against=points_against+%d

                WHERE
                tournament_id=".$_SESSION[ 't_id' ]."
                AND
                player1_id=".$pl2_id,

                $played, $w, $d, $l, $p, $s, $s_opp, $pt, $pt_opp
            )
            );


        }

    }

    $data = array(
        'pl1_set1' => ( is_numeric( $match_vals['pl1_m'.$m_id.'_set1'] ) ? $match_vals['pl1_m'.$m_id.'_set1'] : 0 ),
        'pl2_set1' => ( is_numeric( $match_vals['pl2_m'.$m_id.'_set1'] ) ? $match_vals['pl2_m'.$m_id.'_set1'] : 0 ),
        'pl1_set2' => ( is_numeric( $match_vals['pl1_m'.$m_id.'_set2'] ) ? $match_vals['pl1_m'.$m_id.'_set2'] : 0 ),
        'pl2_set2' => ( is_numeric( $match_vals['pl2_m'.$m_id.'_set2'] ) ? $match_vals['pl2_m'.$m_id.'_set2'] : 0 ),
        'pl1_set3' => ( is_numeric( $match_vals['pl1_m'.$m_id.'_set3'] ) ? $match_vals['pl1_m'.$m_id.'_set3'] : 0 ),
        'pl2_set3' => ( is_numeric( $match_vals['pl2_m'.$m_id.'_set3'] ) ? $match_vals['pl2_m'.$m_id.'_set3'] : 0 ),
        'pl1_set4' => ( is_numeric( $match_vals['pl1_m'.$m_id.'_set4'] ) ? $match_vals['pl1_m'.$m_id.'_set4'] : 0 ),
        'pl2_set4' => ( is_numeric( $match_vals['pl2_m'.$m_id.'_set4'] ) ? $match_vals['pl2_m'.$m_id.'_set4'] : 0 ),
        'pl1_set5' => ( is_numeric( $match_vals['pl1_m'.$m_id.'_set5'] ) ? $match_vals['pl1_m'.$m_id.'_set5'] : 0 ),
        'pl2_set5' => ( is_numeric( $match_vals['pl2_m'.$m_id.'_set5'] ) ? $match_vals['pl2_m'.$m_id.'_set5'] : 0 ),
        'winner' => $winner
    );
    $wpdb->update( $wpdb->prefix . 'bvg_matches',
        $data,
        array( 'id' => $match_vals['match_id'] ) );

    $bvg_admin_msg .= __( 'Match successfully updated.' , 'bad-tournament' ).'<br />';
    return $bvg_admin_msg;
}
