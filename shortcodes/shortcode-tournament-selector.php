<?php
/**
 * Created by PhpStorm.
 * User: ldorier
 * Date: 17.11.2017
 * Time: 13:04
 */


/*
if( !isset( $_SESSION ) ){
    session_start();
}
*/

if( isset( $_POST['tournament_selector_id'] ) && is_numeric( $_POST['tournament_selector_id'] ) ){
    $_SESSION['tournament_to_display'] = $_POST['tournament_selector_id'];
}

$html_shortcode = '';


// Attributes
$atts = shortcode_atts(
    array(
        't_parent_id' => false,
        //'t_year' => false,
    ),
    $atts
);

include_once plugin_dir_path(__FILE__). '../admin/db-get-content.php';
if( isset($atts['t_parent_id']) && is_numeric( $atts['t_parent_id'] ) ){
    $tournaments = badt_db_get_tournaments( false, false, $atts['t_parent_id'] );
}else{
    $tournaments = badt_db_get_tournaments( );
}

if( !empty( $tournaments ) ){

    //$t_year = $atts[ 't_year' ];

    $html = '';

    include plugin_dir_path(__FILE__). 'sc_html/tournament-selector.php';

    $html_shortcode .= $html;
}
