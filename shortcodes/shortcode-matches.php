<?php
/**
 * Created by PhpStorm.
 * User: ldorier
 * Date: 17.11.2017
 * Time: 13:04
 */

$html_shortcode = '';


// Attributes
$atts = shortcode_atts(
    array(
        't_id' => '1', // Tournament ID: numeric
        'round' => false, // Round: numeric (0 for ALL) or ! for current round
        'mode' => false, // Mode: string "live" for Live Viewing => autorefresh
        'm_id' => false, // Match ID: numeric
    ),
    $atts
);
$round = $atts[ 'round' ];
$mode = $atts[ 'mode' ];
$m_id = $atts[ 'm_id' ];


include_once plugin_dir_path(__FILE__). '../admin/db-get-content.php';

if( $m_id ){
    /* Display defined matches
    */
    if( is_numeric( $m_id ) ){
        /* Display unique match
        */
        $matches = badt_db_get_matches( false, false, $m_id );
        $players = badt_db_get_players( $matches[0]->tournament_id );

    }else{
        /* Display more matches
        */
        $matches = badt_db_get_matches( false, false, $m_id );
        //var_dump( $matches );
        $ts_id = array();
        foreach( $matches as $m ){
            $ts_id[] = $m->tournament_id;
        }
        $t_id = implode( ',' , $ts_id );
        //echo 'XXXX: ' . $t_id;
        $players = badt_db_get_players( $t_id );
        //echo 'XXXX: ' . $t_id;
    }
}else{
    /* Display matches for a defined tournament/round
    */
    if( isset( $_SESSION['tournament_to_display'] ) && is_numeric( $_SESSION['tournament_to_display'] ) ){
        $tournament = badt_db_get_tournaments( $_SESSION['tournament_to_display'] );
        $players = badt_db_get_players( $_SESSION['tournament_to_display'] );
    }else{
        $tournament = badt_db_get_tournaments( $atts['t_id'] );
        $players = badt_db_get_players( $atts['t_id'] );
    }


    if( $round === 0 ){
        $round = false;
    }elseif( $round == '!' && isset( $tournament[0] ) ){
        $round = $tournament[0]->round;
    }


    if( isset( $tournament[0] ) ){
        if( isset( $_SESSION['tournament_to_display'] ) && is_numeric( $_SESSION['tournament_to_display'] ) ){
            $matches = badt_db_get_matches( $_SESSION['tournament_to_display'], $round );
            echo 'ROUND: '.$round. ' '.__FILE__.':'.__LINE__;
        }else{
            $matches = badt_db_get_matches( $atts['t_id'], $round );
        }


        $html_shortcode .= '<h3>Matches '.$tournament[0]->name.' / ' .'Round: '.( $round ? $round.' / ' : '' ).$tournament[0]->round.'</h3>';

        $html = '';
        $ROUND = $round;
        $ROUND_MAX = $tournament[0]->round;

    }
}

//var_dump( $players );

include plugin_dir_path(__FILE__). 'sc_html/matches-view.php';

$html_shortcode .= $html;
