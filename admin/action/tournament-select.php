<?php
/**
 * Created by PhpStorm.
 * User: ldorier
 * Date: 30.10.2017
 * Time: 15:32
 */

/* SET VARIABLES */
$new_t_id = 0;
$new_t_name = 'Undefined tournament';
$parent_id = 0;
if( $_POST['tournament_select_id'] > 0 && ( !isset( $_POST['tournament_parent_select'] ) || $_POST['tournament_parent_select'] == 0 ) ){
    $parent_id = $_POST['tournament_select_id'];
}else if( isset( $_POST['tournament_parent_select'] ) && $_POST['tournament_parent_select'] > 0 && is_numeric( $_POST['tournament_parent_select'] ) ){
    $parent_id = $_POST['tournament_parent_select'];
}

$nb_sets = 2;
if( isset( $_POST['tournament_nb_sets'] ) && is_numeric( $_POST['tournament_nb_sets'] ) ){
    $nb_sets = $_POST['tournament_nb_sets'];
}

$points_sets = 21;
if( isset( $_POST['tournament_points_set'] ) && is_numeric( $_POST['tournament_points_set'] ) ){
    $points_sets = $_POST['tournament_points_set'];
}

$tournament_max_points_set = 30;
if( isset( $_POST['tournament_max_points_set'] ) && is_numeric( $_POST['tournament_max_points_set'] ) ){
    $tournament_max_points_set = $_POST['tournament_max_points_set'];
}

$round_max = 0;
if( isset( $_POST['round_max'] ) && is_numeric( $_POST['round_max'] ) ){
    $round_max = $_POST['round_max'];
}


if( isset( $_POST['tournament_date_start'] ) && !empty( $_POST['tournament_date_start'] ) && $_POST['tournament_date_start'] != '00/00/0000 00:00:00' ){
    $date_start = $_POST['tournament_date_start'];
    if( !isset( $_POST['tournament_date_end'] ) || $_POST['tournament_date_end'] < $_POST['tournament_date_start'] ){
        $_POST['tournament_date_end'] = $_POST['tournament_date_start'];
    }
    $date_end = $_POST['tournament_date_end'];

    $datetimestart = explode( ' ', $date_start );
    $datestart = explode( '/', $datetimestart[0] );
    $date_start = $datestart[2].'-'.$datestart[1].'-'.$datestart[0].' '.$datetimestart[1].':00';

    $datetimeend = explode( ' ', $date_end );
    $dateend = explode( '/', $datetimeend[0] );
    $date_end = $dateend[2].'-'.$dateend[1].'-'.$dateend[0].' '.$datetimeend[1].':00';
}else{
    $date_start = date('Y-m-d H:i:00');
    $date_end = $date_start;
}


/* ACTIONS */
/* REMOVE TOURNAMENT */
if( isset( $_POST['tournament_remove_button'] ) && is_numeric( $_POST['tournament_select'] ) ){
    if( $_POST['tournament_select'] == $_SESSION['t_id'] ){
        unset( $_SESSION['t_id'], $_SESSION['t_name'], $_SESSION['t_system'], $_SESSION['current_tournament'] );
        $ADMIN_VIEW = 'tournament';
    }

    /* Remove matches */
    $query = "DELETE FROM
    ".$wpdb->prefix."bvg_matches

    WHERE
    tournament_id=".$_POST['tournament_select'];
    $wpdb->query( $query );
    //echo $query.'<br />';

    // Remove doubles
    $query = "DELETE FROM
    ".$wpdb->prefix."bvg_players_double

    WHERE
    tournament_id = ".$_POST['tournament_select'];

    $wpdb->query( $query );

    /* Remove players */
    $query = "DELETE FROM
    ".$wpdb->prefix."bvg_players_tournament

    WHERE
    tournament_id=".$_POST['tournament_select'];
    $wpdb->query( $query );
    //echo $query.'<br />';

    /* Remove tournament */
    $query = "DELETE FROM
    ".$wpdb->prefix."bvg_tournaments

    WHERE
    id=".$_POST['tournament_select'];
    $wpdb->query( $query );
    //echo $query.'<br />';

    $bvg_admin_msg .= __( 'Tournament removed !' , 'bad-tournament' ).'<br />';
}
/* RESTART TOURNAMENT */
else if( isset( $_POST['tournament_restart_button'] ) && is_numeric( $_POST['tournament_select'] ) ){
    $ADMIN_VIEW = 'tournament';
    /* Edit players */

    /* Remove matches */
    $query = "DELETE FROM
    ".$wpdb->prefix."bvg_matches

    WHERE
    tournament_id=". $_POST['tournament_select'];
    $wpdb->query( $query );
    //echo $query.'<br />';

    /* Reinitialize players */
    $query = "UPDATE
    ".$wpdb->prefix."bvg_players_tournament

    SET
    played=0,
    player_level_current=0,
    victory=0,
    draw=0,
    loss=0,
    points_major=0,
    sets=0,
    sets_against=0,
    points=0,
    points_against=0,
    opponents=''

    WHERE
    tournament_id=". $_POST['tournament_select'] ;
    $wpdb->query( $query );
    //echo $query.'<br />';

    /* Restart tournament */
    $query = "UPDATE
    ".$wpdb->prefix."bvg_tournaments

    SET
    round=1,

    WHERE
    id=".$_POST['tournament_select'];
    $wpdb->query( $query );
    //echo $query.'<br />';

    /* Reinitialize doubles if required */
    if( $_SESSION['current_tournament']['tournament_typ'] == 3 || $_SESSION['current_tournament']['tournament_typ'] == 4 || $_SESSION['current_tournament']['tournament_typ'] == 5 || $_SESSION['current_tournament']['tournament_typ'] == 7 ){
        $query = "UPDATE
        ".$wpdb->prefix."bvg_players_double

        SET
        played=0,
        player_level_current=0,
        victory=0,
        draw=0,
        loss=0,
        points_major=0,
        sets=0,
        sets_against=0,
        points=0,
        points_against=0,
        opponents=''

        WHERE
        tournament_id=". $_POST['tournament_select'] ;
        $wpdb->query( $query );
    }

    $_SESSION['round'] = 1;

    $bvg_admin_msg .= __( 'Restart tournament !' , 'bad-tournament' ).'<br />';
}
/* EDIT TOURNAMENT */
else if( isset( $_POST['tournament_edit'] ) && is_numeric( $_POST['tournament_select'] ) ){
    /* EDIT EXISTING TOURNAMENT */

    $logo = $_POST['image_attachment_id'];
    if( isset( $_POST['tournament_logo_url'] ) && !empty( $_POST['tournament_logo_url'] ) ){
        $logo = $_POST['tournament_logo_url'];
    }

    $data = array(
        'parent_id' => $parent_id,
        'name' => $_POST['tournament_name'],
        'round' => $_POST['round'],
        'system' => $_POST['tournament_system'],
        'nb_sets' => $nb_sets,
        'points_set' => $points_sets,
        'max_points_set' => $tournament_max_points_set,
        'club_restriction' => $_POST['club_restriction'],
        'tournament_typ' => $_POST['tournament_typ'],
        'localization' => $_POST['tournament_localization'],
        'round_max' => $round_max,
        'date_start' => $date_start,
        'date_end' => $date_end,
        'logo' => $logo
    );

    $_SESSION['current_tournament'] = $data;

    $where = array( 'id' => $_POST['tournament_select'] );
    $wpdb->update( $wpdb->prefix . 'bvg_tournaments', $data, $where );
    $_SESSION['t_id'] = $_POST['tournament_select'];
    $_SESSION['t_name'] = $_POST['tournament_name'];
    $_SESSION['t_system'] = $_POST['tournament_system'];
    $_SESSION[ 'current_tournament' ][ 'tournament_double' ] = false;
    if( $_SESSION['current_tournament']['tournament_typ'] == 3 || $_SESSION['current_tournament']['tournament_typ'] == 4 || $_SESSION['current_tournament']['tournament_typ'] == 5 || $_SESSION['current_tournament']['tournament_typ'] == 7 ){
        $_SESSION[ 'current_tournament' ][ 'tournament_double' ] = true;
    }
    $bvg_admin_msg .= __( 'Tournament edited !' , 'bad-tournament' ).'<br />';
}
/* SELECT TOURNAMENT */
else if( isset( $_POST['tournament_select_button'] ) && is_numeric( $_POST['tournament_select'] ) ){
    // SELECT EXISTING TOURNAMENT

    $new_t_id = $_POST['tournament_select'];

    $query = "SELECT
    *
    
    FROM
    ".$wpdb->prefix."bvg_tournaments
    
    WHERE
    id=".$new_t_id."
    
    LIMIT
    0,1";
    $tournaments = $wpdb->get_results( $query );
    $new_t_name = $tournaments[0]->name;
    $new_t_system = $tournaments[0]->system;
    $_SESSION['round'] = $tournaments[0]->round;
    $_SESSION['current_tournament'] = get_object_vars( $tournaments[0] );
    $_SESSION['t_id'] = $new_t_id;
    $_SESSION['t_name'] = $new_t_name;
    $_SESSION['t_system'] = $new_t_system;
    if( $_SESSION[ 'current_tournament' ][ 'club_restriction' ] > 0 ){
        $_SESSION[ 'current_tournament' ][ 'club_restriction_name' ] = badt_db_get_clubs( $_SESSION[ 'current_tournament' ][ 'club_restriction' ] )[0]->name;
    }
    $_SESSION[ 'current_tournament' ][ 'tournament_double' ] = false;
    if( $_SESSION['current_tournament']['tournament_typ'] == 3 || $_SESSION['current_tournament']['tournament_typ'] == 4 || $_SESSION['current_tournament']['tournament_typ'] == 5 || $_SESSION['current_tournament']['tournament_typ'] == 7 ){
        $_SESSION[ 'current_tournament' ][ 'tournament_double' ] = true;
    }
}
/* CREATE TOURNAMENT */
else{

    // CREATE NEW TOURNAMENT
    $sub_names = array(
        1 => __( 'Simple Men', 'bad-tournament' ),
        2 => __( 'Simple Women', 'bad-tournament' ),
        3 => __( 'Double Men', 'bad-tournament' ),
        4 => __( 'Double women', 'bad-tournament' ),
        5 => __( 'Mixte', 'bad-tournament' ),
        6 => __( 'Simple Free', 'bad-tournament' ),
        7 => __( 'Double Free', 'bad-tournament' )
    );

    $logo = $_POST['image_attachment_id'];
    if( isset( $_POST['tournament_logo_url'] ) && !empty( $_POST['tournament_logo_url'] ) ){
        $logo = $_POST['tournament_logo_url'];
    }

    $new_t_name = $_POST['tournament_name'];
    if( isset( $_POST['subtournament_typ'] ) && !empty( $_POST['subtournament_typ'] ) ){
        $new_t_name = $_POST['tournament_name'].__( ' (' , 'bad-tournament' ).$sub_names[ $_POST['tournament_typ'] ].__( ')' , 'bad-tournament' );
    }
    $data = array(
        'parent_id' => $parent_id,
        'name' => $_POST['tournament_name'],
        'round' => 1,
        'system' => $_POST['tournament_system'],
        'nb_sets' => $nb_sets,
        'points_set' => $points_sets,
        'max_points_set' => $tournament_max_points_set,
        'club_restriction' => $_POST['club_restriction'],
        'tournament_typ' => $_POST['tournament_typ'],
        'localization' => $_POST['tournament_localization'],
        'round_max' => $round_max,
        'date_start' => $date_start,
        'date_end' => $date_end,
        'logo' => $logo
    );
    //$wpdb->show_errors();
    $wpdb->insert( $wpdb->prefix . 'bvg_tournaments', $data );

    $new_t_id = $wpdb->insert_id;
    $data['id'] = $new_t_id;
    $_SESSION['current_tournament'] = $data;
    $new_t_system = $_POST['tournament_system'];
    $_SESSION['round'] = 1;

    if( $_SESSION[ 'current_tournament' ][ 'club_restriction' ] > 0 ){
        $_SESSION[ 'current_tournament' ][ 'club_restriction_name' ] = badt_db_get_clubs( $_SESSION[ 'current_tournament' ][ 'club_restriction' ] )[0]->name;
    }

    $_SESSION['t_id'] = $new_t_id;
    $_SESSION['t_name'] = $new_t_name;
    $_SESSION['t_system'] = $new_t_system;
    $_SESSION[ 'current_tournament' ][ 'tournament_double' ] = false;
    if( $_SESSION['current_tournament']['tournament_typ'] == 3 || $_SESSION['current_tournament']['tournament_typ'] == 4 || $_SESSION['current_tournament']['tournament_typ'] == 5 || $_SESSION['current_tournament']['tournament_typ'] == 7 ){
        $_SESSION[ 'current_tournament' ][ 'tournament_double' ] = true;
    }

    $bvg_admin_msg .= __( 'New tournament: ' , 'bad-tournament' ).$new_t_name.'<br />';

    /* Create subtournaments */

    if( isset( $_POST['subtournament_typ'] ) && !empty( $_POST['subtournament_typ'] ) ){
        foreach( $_POST['subtournament_typ'] as $sub_typ ){
            if( $sub_typ != $_POST['tournament_typ'] ){
                $new_t_name = $_POST['tournament_name'].__( ' (' , 'bad-tournament' ).$sub_names[ $sub_typ ].__( ')' , 'bad-tournament' );

                $data = array(
                    'parent_id' => $new_t_id,
                    'name' => $new_t_name,
                    'round' => 1,
                    'system' => $_POST['tournament_system'],
                    'nb_sets' => $nb_sets,
                    'points_set' => $points_sets,
                    'max_points_set' => $tournament_max_points_set,
                    'club_restriction' => $_POST['club_restriction'],
                    'tournament_typ' => $sub_typ,
                    'localization' => $_POST['tournament_localization'],
                    'date_start' => $date_start,
                    'date_end' => $date_end,
                    'logo' => $logo
                );
                //$wpdb->show_errors();
                $wpdb->insert( $wpdb->prefix . 'bvg_tournaments', $data );
                $bvg_admin_msg .= __( 'New subtournament: ' , 'bad-tournament' ).$new_t_name.'<br />';
            }
        }
    }


}

/*
$wpdb->show_errors();
$wpdb->print_error();
*/
if( isset( $_SESSION['t_id'] ) ){
    $bvg_admin_msg .= __( 'Selected tournament: ' , 'bad-tournament' ).$_SESSION['t_name'].' !!!';
}
