<?php
/**
 * Created by PhpStorm.
 * User: ldorier
 * Date: 30.10.2017
 * Time: 15:32
 */

if( !isset( $_POST[ 'generate_matchs_now_noround' ] )){
    $_SESSION['round']++;

    $data = array(
        'round' => $_SESSION['round']
    );
    $where = array( 'id' => $_SESSION['t_id'] );
    $wpdb->update( $wpdb->prefix . 'bvg_tournaments', $data, $where );

    $bvg_admin_msg .= 'Neuer Round (( '.$_SESSION['round'].' )) !!!';
}

