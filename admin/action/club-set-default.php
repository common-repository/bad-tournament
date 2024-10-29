<?php
/**
 * Created by PhpStorm.
 * User: ldorier
 * Date: 30.10.2017
 * Time: 15:32
 */

if( !is_numeric( $_POST['club_id'] ) ){

    $html_ajax .= 'Fehler: etwas stimmt hier nicht...';
}else{
    update_option( 'cl_default_id' , $_POST['club_id'] );

    $html_ajax .= __( 'Club set now as default', 'bad-tournament' );

}