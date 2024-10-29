<?php
/**
 * Created by PhpStorm.
 * User: ldorier
 * Date: 10.11.2017
 * Time: 11:42
 */

/* Get all tournaments */
function badt_db_get_tournaments( $tournament_id = false, $get_last = false, $get_parent = false ){

    global $wpdb;

    if( isset( $get_parent ) && is_numeric( $get_parent ) ){
        /* GET SUBTOURNAMENTS */
        $query = "SELECT
        *
        
        FROM
        ".$wpdb->prefix."bvg_tournaments
        
        WHERE
        parent_id=".$get_parent;
    }else if( !$tournament_id && !$get_last ){
        /* GET ALL TOURNAMENTS */
        $query = "SELECT
        *
        
        FROM
        ".$wpdb->prefix."bvg_tournaments";
    }else if( !$get_last ){
        /* GET TOURNAMENT ON ID */
        $query = "SELECT
        *
        
        FROM
        ".$wpdb->prefix."bvg_tournaments
        
        WHERE
        id=".$tournament_id;
    }else{
        /* GET LAST TOURNAMENT */
        $query = "SELECT
        *
        
        FROM
        ".$wpdb->prefix."bvg_tournaments
        
        ORDER BY
        id DESC
        
        LIMIT
        0,1";
    }

    $tournaments = $wpdb->get_results( $query  );

    return $tournaments;
}

/* Get all tournaments */
function badt_db_get_clubs( $club_id = false ){

    global $wpdb;

    if( !$club_id ){
        $query = "SELECT
        *

        FROM
        ".$wpdb->prefix."bvg_clubs

        ORDER BY
        name ASC";
    }else{
        $query = "SELECT
        *

        FROM
        ".$wpdb->prefix."bvg_clubs

        WHERE
        id=".$club_id;
    }


    $clubs = $wpdb->get_results( $query );

    return $clubs;
}

/* Get all players */
function badt_db_get_all_players( $club_restriction = false ){

    global $wpdb;

    $where = '';
    if( is_numeric( $club_restriction ) ){
        $where = 'AND
        club_id = '.$club_restriction;
    }
    $query = "SELECT
    pl.id as player_id,
    pl.firstname as player_firstname,
    pl.lastname as player_lastname,
    pl.player_level as player_level,
    pl.sex as player_sex,
    pl.status as status
    
    FROM
    ".$wpdb->prefix."bvg_players as pl
    
    WHERE
    pl.status=1
    ".$where."
    
    ORDER BY
    pl.lastname ASC, pl.firstname ASC
    ";
    $all_players = $wpdb->get_results( $query, OBJECT_K  );
    //echo $query;

    return $all_players;
}

/* Get all players for the current tournament */
function badt_db_get_players( $tournament_id = false, $players_list = array(), $players_view = array() ){

    global $wpdb;

    if( !$tournament_id ){
        $tournament_id = $_SESSION['t_id'];
    }

    $where = '';
    if( !empty( $players_list ) ){
        $players_id_IN = implode( ',' , $players_list );
        $where .= '
        AND
        pl_t.id IN ('.$players_id_IN.')';
    }

    if( is_numeric( $tournament_id ) ){
        $query = "SELECT
        pl_t.id as id,
        pl.id as player_id,
        pl.firstname as player_firstname,
        pl.lastname as player_lastname,
        pl.sex as player_sex,
        pl_t.*
        
        FROM
        ".$wpdb->prefix."bvg_players as pl
        JOIN
        ".$wpdb->prefix."bvg_players_tournament as pl_t
        ON
        pl.id = pl_t.players_id
        
        WHERE
        pl_t.tournament_id = ".$tournament_id.$where."
        
        ORDER BY
        pl_t.points_major DESC, pl_t.played ASC, pl_t.sets DESC, pl_t.sets_against ASC, pl_t.points DESC, pl_t.points_against ASC, pl_t.player_level_init DESC
        ";
    }else if( empty( $players_view ) ){
        $query = "SELECT
        pl_t.id as id,
        pl.id as player_id,
        pl.firstname as player_firstname,
        pl.lastname as player_lastname,
        pl.sex as player_sex,
        pl_t.*
        
        FROM
        ".$wpdb->prefix."bvg_players as pl
        JOIN
        ".$wpdb->prefix."bvg_players_tournament as pl_t
        ON
        pl.id = pl_t.players_id
        
        WHERE
        pl_t.tournament_id IN (".$tournament_id.")".$where."
        
        ORDER BY
        pl_t.points_major DESC, pl_t.played ASC, pl_t.sets DESC, pl_t.sets_against ASC, pl_t.points DESC, pl_t.points_against ASC, pl_t.player_level_init DESC
        ";

    }else if( !empty( $players_view ) ){
        $where = implode( ',' , $players_view );
        $query = "SELECT
        pl.id as player_id,
        pl.firstname as player_firstname,
        pl.lastname as player_lastname,
        pl.sex as player_sex
        
        FROM
        ".$wpdb->prefix."bvg_players as pl
        
        WHERE
        pl.id IN (".$where.")";
    }

    //$wpdb->show_errors();
    //echo $query.'<br />';
    $players = $wpdb->get_results( $query, OBJECT_K  );
    //var_dump( $players );
    return $players;
}

/* Count played matches for the current tournament */
function badt_db_nb_matches( $tournament_id = false, $round = false, $completed = false ){

    global $wpdb;

    if( $completed === true ){
        $query = "SELECT
        count(*) as nb

        FROM
        ".$wpdb->prefix."bvg_matches

        WHERE
        tournament_id = ".$tournament_id."
        AND
        winner != 0";
    }else{
        $query = "SELECT
        count(*) as nb

        FROM
        ".$wpdb->prefix."bvg_matches

        WHERE
        tournament_id = ".$tournament_id;
    }



    $nb_matches = $wpdb->get_results( $query );

    return $nb_matches[0]->nb;
}

/* Get matches */
function badt_db_get_matches( $tournament_id = false, $round = false, $match_id = false, $player_id = false, $double = false ){

    global $wpdb;

    if( $double ){
        $query = "SELECT
            *

            FROM
            ".$wpdb->prefix."bvg_matches

            WHERE
            id = ".$match_id."

            LIMIT
            0,1
            ";
    }else if( $player_id ){
        $query = "SELECT
            *

            FROM
            ".$wpdb->prefix."bvg_matches

            WHERE
            player1_id = ".$player_id."
            OR
            player2_id = ".$player_id."
            OR
            player1_id_bis = ".$player_id."
            OR
            player2_id_bis = ".$player_id."

            ORDER BY
            round ASC
            ";
    }else if( $match_id ){
        if( is_numeric( $match_id ) ){
            $query = "SELECT
            *
    
            FROM
            ".$wpdb->prefix."bvg_matches
    
            WHERE
            id = ".$match_id."
    
            LIMIT
            0,1
            ";
        }else{
            $query = "SELECT
            *
    
            FROM
            ".$wpdb->prefix."bvg_matches
    
            WHERE
            id IN (".$match_id.")
            ";
        }

    }else{

        if( !$tournament_id ){
            $tournament_id = $_SESSION['t_id'];
        }

        if( !$round ){
            $query = "SELECT
            *
    
            FROM
            ".$wpdb->prefix."bvg_matches
    
            WHERE
            tournament_id = ".$tournament_id."
    
            ORDER BY
            round, id ASC
            ";
        }else{
            $query = "SELECT
            *
    
            FROM
            ".$wpdb->prefix."bvg_matches
    
            WHERE
            tournament_id = ".$tournament_id."
            AND
            round = ".$round."
    
            ORDER BY
            id ASC
            ";

        }
    }

    //echo $query;

    $matches = $wpdb->get_results( $query );

return $matches;
}

/*  */
function badt_db_get_couples( $tournament_id = false ){
    global $wpdb;

    if( !$tournament_id ){
        $tournament_id = $_SESSION['t_id'];
    }

    $query = "SELECT
    pl_d.*

    FROM
    ".$wpdb->prefix."bvg_players_double as pl_d

    WHERE
    pl_d.tournament_id = ".$tournament_id."

    ORDER BY
    pl_d.points_major DESC, pl_d.played ASC, pl_d.sets DESC, pl_d.sets_against ASC, pl_d.points DESC, pl_d.points_against ASC, pl_d.player_level_init DESC
    ";
    //$wpdb->show_errors();
    //echo $query;
    $couples = $wpdb->get_results( $query, OBJECT_K  );
    //var_dump( $players );
    return $couples;
}

global $tournament_typ_array;
$tournament_typ_array = array(
    1 => __('Simple Men', 'bad-tournament'),
    2 => __('Simple Women', 'bad-tournament'),
    3 => __('Double Men', 'bad-tournament'),
    4 => __('Double Women', 'bad-tournament'),
    5 => __('Mixte', 'bad-tournament'),
    6 => __('Simple Free', 'bad-tournament'),
    7 => __('Double Free', 'bad-tournament')

);