<?php
/**
 * Created by PhpStorm.
 * User: ldorier
 * Date: 30.10.2017
 * Time: 14:40
 */

if ( !defined( 'ABSPATH' ) ) die();

$bad_tournament_version = '1.4';

function badt_install( $bad_tournament_version, $bad_tournament_current_version = false ){

    $bvg_admin_msg = '';

    if( $bad_tournament_version == '1.0' ) {
        /* First version */

        badt_install_init();

        $bvg_admin_msg .= __( 'Plugin Bad Tournament installed !' , 'bad-tournament' );
    }else{
        /* Update */

        if( $bad_tournament_current_version == false || $bad_tournament_current_version < 1 ){
            badt_install_init();
        }

        $bvg_admin_msg .= badt_update( $bad_tournament_current_version );
    }

    return $bvg_admin_msg;
}

function badt_update( $bad_tournament_current_version ){
    $bvg_admin_msg = __( 'Plugin Bad Tournament updated with following versions:' , 'bad-tournament' ).'<br />';

    $existing_updates = array(
        '1.0.1',
        //'1.0.2',
        //'1.1',
        //'1.2',
        '1.3',
        '1.4'
    );

    foreach( $existing_updates as $version ){
        if( $version > $bad_tournament_current_version ){

            $func_name = 'badt_update_'.str_replace( '.', '_', $version );
            if( function_exists( $func_name ) && $msg = $func_name() ){
                $bvg_admin_msg .= ' '.$msg.'<br />';
            }else if( !function_exists( $func_name ) ){
                $bvg_admin_msg .= ' Version '.$version.__( ':no database update required' , 'bad-tournament' ).'<br />';
            }else{
                $bvg_admin_msg .= ' '.$version.' ('.__( 'ERROR: function '.$func_name.' is missing...' , 'bad-tournament' ).')<br />';
            }

        }
    }


    return $bvg_admin_msg;
}

function badt_install_init(){

    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $nom_table = $wpdb->prefix . 'bvg_clubs';
    $sql = "CREATE TABLE $nom_table (
                  id bigint(20) unsigned NOT NULL auto_increment,
                  name varchar(200) NOT NULL,
                  club_id varchar(50) NOT NULL,
                  url varchar(150) NOT NULL,
                  contact_id bigint(20) unsigned NOT NULL,
                  PRIMARY KEY (id)
                ) ENGINE=InnoDB;";
    dbDelta( $sql );

    $nom_table = $wpdb->prefix . 'bvg_matches';
    $sql = "CREATE TABLE $nom_table (
                  id bigint(20) unsigned NOT NULL auto_increment,
                  player1_id bigint(20) unsigned NOT NULL,
                  player2_id bigint(20) unsigned NOT NULL,
                  player1_id_bis bigint(20) unsigned NOT NULL,
                  player2_id_bis bigint(20) unsigned NOT NULL,
                  tournament_id bigint(20) unsigned NOT NULL,
                  round int(11) NOT NULL,
                  winner int(11) NOT NULL,
                  pl1_set1 int(11) NOT NULL,
                  pl2_set1 int(11) NOT NULL,
                  pl1_set2 int(11) NOT NULL,
                  pl2_set2 int(11) NOT NULL,
                  pl1_set3 int(11) NOT NULL,
                  pl2_set3 int(11) NOT NULL,
                  pl1_set4 int(11) NOT NULL,
                  pl2_set4 int(11) NOT NULL,
                  pl1_set5 int(11) NOT NULL,
                  pl2_set5 int(11) NOT NULL,
                  parent_id int(11) NOT NULL,
                  PRIMARY KEY (id),
                  INDEX (player1_id),
                  INDEX (player2_id),
                  INDEX (player1_id_bis),
                  INDEX (player2_id_bis),
                  INDEX (tournament_id)
                ) ENGINE=InnoDB;";
    dbDelta( $sql );

    $nom_table = $wpdb->prefix . 'bvg_players';
    $sql = "CREATE TABLE $nom_table (
                  id bigint(20) unsigned NOT NULL auto_increment,
                  firstname varchar(150) NOT NULL,
                  lastname varchar(150) NOT NULL,
                  player_level int(6) unsigned NOT NULL,
                  status int(6) unsigned NOT NULL,
                  club_id bigint(20) unsigned NOT NULL,
                  player_id varchar(50) NOT NULL,
                  birthdate date,
                  sex int(2) unsigned NOT NULL,
                  PRIMARY KEY (id)
                ) ENGINE=InnoDB;";
    dbDelta( $sql );

    $data = array(
        'firstname' => 'Laurent',
        'lastname' => 'Dorier',
        'player_level' => '500',
        'status' => 1,
        'club_id' => 1,
        'player_id' => 1,
        'birthdate' => '1973-02-02',
        'sex' => 1,
        'profile_attachment_id' => 'http://yopla.doo'
    );
    $wpdb->insert( $wpdb->prefix . 'bvg_players', $data );



    $nom_table = $wpdb->prefix . 'bvg_players_double';
    $sql = "CREATE TABLE $nom_table (
                  id bigint(20) unsigned NOT NULL auto_increment,
                  tournament_id bigint(20) unsigned NOT NULL,
                  players_id bigint(20) unsigned NOT NULL,
                  played int(3) unsigned NOT NULL,
                  player_level_init int(3) unsigned NOT NULL,
                  player_level_current int(3) unsigned NOT NULL,
                  victory int(3) unsigned NOT NULL,
                  draw int(3) unsigned NOT NULL,
                  loss int(3) unsigned NOT NULL,
                  points_major int(3) unsigned NOT NULL,
                  sets int(3) unsigned NOT NULL,
                  sets_against int(3) unsigned NOT NULL,
                  points int(6) unsigned NOT NULL,
                  points_against int(6) unsigned NOT NULL,
                  opponents varchar(250) NOT NULL,
                  status int(6) unsigned NOT NULL,
                  PRIMARY KEY (id),
                  INDEX (tournament_id),
                  INDEX (players_id)
                ) ENGINE=InnoDB;";
    dbDelta( $sql );

    $nom_table = $wpdb->prefix . 'bvg_players_tournament';
    $sql = "CREATE TABLE $nom_table (
                  id bigint(20) unsigned NOT NULL auto_increment,
                  tournament_id bigint(20) unsigned NOT NULL,
                  players_id bigint(20) unsigned NOT NULL,
                  played int(3) unsigned NOT NULL,
                  player_level_init int(3) unsigned NOT NULL,
                  player_level_current int(3) unsigned NOT NULL,
                  victory int(3) unsigned NOT NULL,
                  draw int(3) unsigned NOT NULL,
                  loss int(3) unsigned NOT NULL,
                  points_major int(3) unsigned NOT NULL,
                  sets int(3) unsigned NOT NULL,
                  sets_against int(3) unsigned NOT NULL,
                  points int(6) unsigned NOT NULL,
                  points_against int(6) unsigned NOT NULL,
                  opponents varchar(250) NOT NULL,
                  status int(6) unsigned NOT NULL,
                  PRIMARY KEY (id),
                  INDEX (tournament_id),
                  INDEX (players_id)
                ) ENGINE=InnoDB;";
    dbDelta( $sql );

    $nom_table = $wpdb->prefix . 'bvg_tournaments';
    $sql = "CREATE TABLE $nom_table (
                  id bigint(20) unsigned NOT NULL auto_increment,
                  parent_id int(11) unsigned NOT NULL,
                  name varchar(150) NOT NULL,
                  round int(11) NOT NULL,
                  system int(11) NOT NULL,
                  nb_sets int(11) NOT NULL,
                  points_set int(11) NOT NULL,
                  max_points_set int(11) NOT NULL,
                  club_restriction int(11) unsigned NOT NULL,
                  tournament_typ int(3) unsigned NOT NULL,
                  date_start datetime NOT NULL,
                  date_end datetime NOT NULL,
                  localization varchar(250) NOT NULL,
                  PRIMARY KEY (id),
                  INDEX (parent_id)
                ) ENGINE=InnoDB;";
    dbDelta( $sql );


    /* ADD FOREIGN KEYS */
    $nom_table = $wpdb->prefix . 'bvg_clubs';
    $sql = "ALTER TABLE ".$nom_table."
                    ADD FOREIGN KEY (contact_id)
                      REFERENCES ".$wpdb->prefix . "bvg_players(id)
                      ON UPDATE NO ACTION ON DELETE NO ACTION;";
    $wpdb->query( $sql );

    $nom_table = $wpdb->prefix . 'bvg_matches';
    $sql = "ALTER TABLE ".$nom_table."
                    ADD FOREIGN KEY (player1_id)
                      REFERENCES ".$wpdb->prefix . "bvg_players_tournament(id)
                      ON UPDATE NO ACTION ON DELETE NO ACTION,
                    ADD FOREIGN KEY (player2_id)
                      REFERENCES ".$wpdb->prefix . "bvg_players_tournament(id)
                      ON UPDATE NO ACTION ON DELETE NO ACTION,
                    ADD FOREIGN KEY (player1_id_bis)
                      REFERENCES ".$wpdb->prefix . "bvg_players_tournament(id)
                      ON UPDATE NO ACTION ON DELETE NO ACTION,
                    ADD FOREIGN KEY (player2_id_bis)
                      REFERENCES ".$wpdb->prefix . "bvg_players_tournament(id)
                      ON UPDATE NO ACTION ON DELETE NO ACTION,
                    ADD FOREIGN KEY (tournament_id)
                      REFERENCES ".$wpdb->prefix . "bvg_tournaments(id)
                      ON UPDATE NO ACTION ON DELETE NO ACTION;";
    $wpdb->query( $sql );


    $nom_table = $wpdb->prefix . 'bvg_players_tournament';
    $sql = "ALTER TABLE ".$nom_table."
                    ADD FOREIGN KEY (tournament_id)
                      REFERENCES ".$wpdb->prefix . "bvg_tournaments(id)
                      ON UPDATE NO ACTION ON DELETE NO ACTION,
                    ADD FOREIGN KEY (players_id)
                      REFERENCES ".$wpdb->prefix . "bvg_players(id)
                      ON UPDATE NO ACTION ON DELETE NO ACTION;";
    $wpdb->query( $sql );


    $nom_table = $wpdb->prefix . 'bvg_tournaments';
    $sql = "ALTER TABLE ".$nom_table."
                    ADD FOREIGN KEY (parent_id)
                      REFERENCES ".$wpdb->prefix . "bvg_tournaments(id)
                      ON UPDATE NO ACTION ON DELETE NO ACTION,
                    ADD FOREIGN KEY (club_restriction)
                      REFERENCES ".$wpdb->prefix . "bvg_clubs(id)
                      ON UPDATE NO ACTION ON DELETE NO ACTION;";
    $wpdb->query( $sql );

    return true;
}

function badt_update_1_0_1(){
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $nom_table = $wpdb->prefix . 'bvg_tournaments';
    $sql = "ALTER TABLE ".$nom_table."
                    ADD logo VARCHAR(255) NOT NULL AFTER localization;";

    $wpdb->query( $sql );

    $msg_update = __('Version 1.0.1: Add tournament logo', 'bad-tournament');

    return $msg_update;
}

/*
function badt_update_1_0_2(){
    $msg_update = __('Version 1.0.2: No database modification', 'bad-tournament');

    return $msg_update;
}

function badt_update_1_1(){
    $msg_update = __('Version 1.1: No database modification', 'bad-tournament');

    return $msg_update;
}

function badt_update_1_2(){
    $msg_update = __('Version 1.2: No database modification', 'bad-tournament');

    return $msg_update;
}
*/

function badt_update_1_3(){
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $nom_table = $wpdb->prefix . 'bvg_tournaments';
    $sql = "ALTER TABLE ".$nom_table."
                    ADD round_max INT(6) unsigned NOT NULL AFTER localization;";

    $wpdb->query( $sql );

    $msg_update = __('Version 1.3: Add tournament max round', 'bad-tournament');

    return $msg_update;
}

function badt_update_1_4(){
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $nom_table = $wpdb->prefix . 'bvg_players';
    $sql = "ALTER TABLE ".$nom_table."
                    ADD pic BIGINT(6) unsigned NOT NULL sex;";

    $wpdb->query( $sql );

    $msg_update = __('Version 1.4: Add player profile picture', 'bad-tournament');

    return $msg_update;
}