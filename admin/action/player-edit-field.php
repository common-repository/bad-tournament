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

    if( isset( $_POST['pl_id2'] ) && $_POST['pl_id2'] > 0 ){
        $_POST['pl_id'] = $_POST['pl_id2'];
    }

    if( strpos( $_POST['pl_field_name'] , 'stname') > 0 ){
        $_POST['pl_field_name'] = substr( $_POST['pl_field_name'], 0, -1 );
    }
    if( $_POST['pl_field_name'] == 'birthdate' ){
        $dates = explode( '/', $_POST['pl_field_value'] );
        $_POST['pl_field_value'] = $dates[2].'-'.$dates[1].'-'.$dates[0];
    }

    $data = array(
        $_POST['pl_field_name'] => $_POST['pl_field_value']
    );
    $wpdb->update( $wpdb->prefix . 'bvg_players',
        $data,
        array( 'id' => $_POST['pl_id'] )
    );

    if( $_POST['pl_field_name'] == 'player_level' ){
        $data = array(
            'player_level_init' => $_POST['pl_field_value']
        );
        $wpdb->update( $wpdb->prefix . 'bvg_players_tournament',
            $data,
            array( 'players_id' => $_POST['pl_id'],
                'tournament_id' => $_SESSION['t_id']),
            array(
                '%d',	// value1
                '%d'	// value2
            ),
            array( '%d' )
        );
        //$html_ajax .= 'OK'.$_POST['pl_id'].'/'.$_SESSION['t_id'];
    }


    $html_ajax .= __('Player value "'. $_POST['pl_field_name'] .'" updated with '. $_POST['pl_field_value'], 'bad-tournament' );

}