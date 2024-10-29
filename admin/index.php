<?php
/**
 * Created by PhpStorm.
 * User: ldorier
 * Date: 30.10.2017
 * Time: 14:14
 */

if ( !defined( 'ABSPATH' ) ) die();

require_once plugin_dir_path(__FILE__). 'badt_functions.php';

if( defined('BADT_DEBUG_MODE') && BADT_DEBUG_MODE > 0 ){
    /* DEBUG ACTIVATED
    */
    global $badt_debug_obj;
    $badt_debug_obj = new badt_Bad_Debug;

}

$t_system = array(
    0 => '-',
    1 => __('Swiss System', 'bad-tournament'),
    2 => __('League', 'bad-tournament'),
    3 => __('KO System', 'bad-tournament'),
    4 => __('Grinding tournament', 'bad-tournament')
);

$ADMIN_VIEW = false;
if( isset( $_GET['admin_view'] ) ){
    $ADMIN_VIEW = $_GET['admin_view'];
}


global $wpdb;


/* GET/SET CONTENT */
require_once plugin_dir_path(__FILE__). 'db-get-content.php';
/* Variables */


/* ACTIONS */

if( isset($_POST['form_action']) ){
    include plugin_dir_path(__FILE__). 'action/'.$_POST['form_action'].'.php';
}else if( isset($_GET['t_select_id']) && is_numeric( $_GET['t_select_id'] ) ){
    $_POST['tournament_select_button'] = true;
    $_POST['tournament_select'] = $_GET['t_select_id'];
    include plugin_dir_path(__FILE__). 'action/tournament-select.php';
}
if( !$ADMIN_VIEW ){
    $ADMIN_VIEW = 'tournament';
}


//echo 'XXX'.$_SESSION['round'];
//unset( $_SESSION['t_id'] );
if( !isset( $_SESSION['t_id'] ) ){

    $last_tournament = badt_db_get_tournaments( false , true );

    if( isset($last_tournament[0]->id) && is_numeric($last_tournament[0]->id) ){
        $_SESSION['t_id'] = $last_tournament[0]->id;
        $_SESSION['t_system'] = $last_tournament[0]->system;
        $_SESSION['t_name'] = $last_tournament[0]->name;
        $_SESSION['current_tournament'] = get_object_vars( $last_tournament[0] );
        if( $_SESSION[ 'current_tournament' ][ 'club_restriction' ] > 0 ){
            $_SESSION[ 'current_tournament' ][ 'club_restriction_name' ] = badt_db_get_clubs( $_SESSION[ 'current_tournament' ][ 'club_restriction' ] )[0]->name;
        }
        $_SESSION[ 'current_tournament' ][ 'tournament_double' ] = false;
        if( $_SESSION['current_tournament']['tournament_typ'] == 3 || $_SESSION['current_tournament']['tournament_typ'] == 4 || $_SESSION['current_tournament']['tournament_typ'] == 5 || $_SESSION['current_tournament']['tournament_typ'] == 7 ){
            $_SESSION[ 'current_tournament' ][ 'tournament_double' ] = true;
        }
    }else{
        $_SESSION['t_id'] = 1;
        $_SESSION['t_system'] = 1;
        $_SESSION['t_name'] = __( 'No tournament yet', 'bad-tournament' );
        $_SESSION['current_tournament'] = false;
    }

}
if( !isset( $_SESSION['round'] ) ){
    $round = $wpdb->get_results( "SELECT round FROM ".$wpdb->prefix."bvg_tournaments WHERE id=".$_SESSION['t_id']." LIMIT 0,1" );
    $_SESSION['round'] = $round[0]->round;
}
$club_restriction = false;
if( $_SESSION[ 'current_tournament' ][ 'club_restriction' ] > 0 ){
    $club_restriction = $_SESSION[ 'current_tournament' ][ 'club_restriction' ];
}

/* GET DB CONTENT */
$tournaments = badt_db_get_tournaments();
$clubs = badt_db_get_clubs();
$cl_default_id = get_option( 'cl_default_id' );
$all_players = badt_db_get_all_players( $club_restriction );
$players = badt_db_get_players();
if( isset($_POST['view_round']) && is_numeric( $_POST[ 'view_round' ] ) ){
    $matches = badt_db_get_matches( $_SESSION['t_id'], $_POST['view_round'] );
}else{
    $matches = badt_db_get_matches( $_SESSION['t_id'], $_SESSION['round'] );
}

$nb_matchs = badt_db_nb_matches( $_SESSION['t_id'], false, true );
$couples = badt_db_get_couples();



//echo '<pre>';
//var_dump( $matches );

/* Generate matches if required */
if( empty($matches) || isset( $_POST['regenerate_matchs_now'] ) || isset( $_POST['generate_matchs_now_noround'] ) ){
    //echo $query;
    //var_dump( $players );
    include_once plugin_dir_path(__FILE__). 'action/generate-matches.php';
}

/* HTML */
$html = '';

/* Header */
include plugin_dir_path(__FILE__). 'index_html/header.php';

/* Tournament */
include plugin_dir_path(__FILE__). 'index_html/tournament-config.php';

/* Clubs */
include plugin_dir_path(__FILE__). 'index_html/clubs.php';

/* Players */
include plugin_dir_path(__FILE__). 'index_html/player-config.php';

/* Table */
include plugin_dir_path(__FILE__). 'index_html/tournament-table.php';

/* Matches */
include plugin_dir_path(__FILE__). 'index_html/matches.php';

/* Footer */
include plugin_dir_path(__FILE__). 'index_html/footer.php';

echo $html;


if( defined('BADT_DEBUG_MODE') && BADT_DEBUG_MODE > 0 ){
    /* DEBUG DISPLAYED
    */
    if( BADT_DEBUG_MODE == 1 ){
        $badt_debug_obj -> display_debug();
    }

}