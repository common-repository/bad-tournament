<?php
/**
 * Created by PhpStorm.
 * User: ldorier
 * Date: 30.10.2017
 * Time: 15:32
 * Actions: Add a new player profile
 *
 */

$dates = explode( '/', $_POST['birthdate'] );
$birthdate = $dates[2].'-'.$dates[1].'-'.$dates[0];


if( !isset( $_POST['swiss_system_point'] ) || empty( $_POST['swiss_system_point'] ) ){
    $_POST['swiss_system_point'] = 0;
}
if( !isset( $_POST['sex'] ) || empty( $_POST['sex'] ) ){
    $_POST['sex'] = 0;
}

$user_pic = $_POST['profile_attachment_id'];
if( isset( $_POST['profile_pic_url'] ) && !empty( $_POST['profile_pic_url'] ) ){
    $user_pic = $_POST['profile_pic_url'];
}

$data = array(
    'firstname' => $_POST['firstname'],
    'lastname' => $_POST['lastname'],
    'player_level' => $_POST['swiss_system_point'],
    'status' => 1,
    'club_id' => $_POST['club_id'],
    'player_id' => $_POST['player_id'],
    'birthdate' => $birthdate,
    'sex' => $_POST['sex'],
    'profile_attachment_id' => $user_pic
);
$wpdb->insert( $wpdb->prefix . 'bvg_players', $data );

$str   = htmlspecialchars( $wpdb->last_result, ENT_QUOTES );
$query = htmlspecialchars( $wpdb->last_query, ENT_QUOTES );

$bvg_admin_msg .= __( 'New player added: ', 'bad-tournament' ).$query;

$player_id = $wpdb->insert_id;

$data = array(
    'tournament_id' => $_SESSION['t_id'],
    'players_id' => $wpdb->insert_id,
    'player_level_init' => $_POST['swiss_system_point']
);
$wpdb->insert( $wpdb->prefix . 'bvg_players_tournament', $data );
$bvg_admin_msg .= '<br />'.__( 'Player added for the current tournament', 'bad-tournament' );

if( isset( $_POST['club_contact'] ) && $_POST['club_contact'] == 1 ){
    $data = array(
        'contact_id' => $player_id
    );
    $wpdb->update( $wpdb->prefix . 'bvg_clubs',
        $data,
        array( 'id' => $_POST['club_id'] ) );

    $bvg_admin_msg .= '<br />'.__( 'Player added as contact for this club', 'bad-tournament' );
}

