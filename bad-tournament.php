<?php

/*
Plugin Name: Bad Tournament
Plugin URI: http://etalkers.org/what/wordpress-development/bad-tournament-wp-plugin
Description: Badminton / Tennis / Table tennis Tournament Plugin
Author: Laurent Dorier
Version: 1.4
Author URI: http://etalkers.org
Text Domain: bad-tournament
Domain Path: /languages
*/

if ( !defined( 'ABSPATH' ) ) die();

/*
add_action('init', 'myStartSession', 1);
function myStartSession() {
    if(!session_id()) {
        session_start();
    }
}
*/

class badt_Bad_Tournament
{
    function __construct(){
        load_plugin_textdomain( 'bad-tournament', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        add_action( 'admin_init', array( $this, 'bad_tournament_start_session' ) );
        add_action( 'admin_menu', array( $this, 'bad_tournament_menu' ) );

        // Ajax
        add_action( 'wp_ajax_change_players_match', array( $this, 'change_players_match' ) );
        add_action( 'wp_ajax_player_tooltip', array( $this, 'player_tooltip' ) );
        add_action( 'wp_ajax_player_remove', array( $this, 'player_remove_from_tournament' ) );
        add_action( 'wp_ajax_player_edit_field', array( $this, 'player_edit_field' ) );
        add_action( 'wp_ajax_set_club_default', array( $this, 'set_club_default' ) );
        add_action( 'wp_ajax_set_player_form_default', array( $this, 'set_player_form_default' ) );
        add_action( 'wp_ajax_export_file', array( $this, 'export_file' ) );
        add_action( 'wp_ajax_save_players_couple', array( $this, 'save_players_couple' ) );

        add_action( 'admin_head', array( $this, 'bvg_head_javascript_object' ) );


        add_shortcode( 'bad_tournament_selector', array( $this, 'bad_tournament_selector_shortcode' ) );
        add_shortcode( 'bad_tournament_table', array( $this, 'bad_tournament_table_shortcode' ) );
        add_shortcode( 'bad_tournament_matches', array( $this, 'bad_tournament_matches_shortcode' ) );
        add_shortcode( 'bad_tournament_summary', array( $this, 'bad_tournament_summary_shortcode' ) );
        add_shortcode( 'bad_tournament_player', array( $this, 'bad_tournament_player_shortcode' ) );
    }

    function bvg_head_javascript_object(){
        include_once plugin_dir_path(__FILE__). 'admin/db-get-content.php';
        $tournaments = badt_db_get_tournaments();
        ?>
        <script>
            var bvg_tournament_constants = {
                "badTournamentURI": '<?php echo plugin_dir_url( __FILE__ ); ?>',
                "badTournamentMale": '<?php echo __( 'Male' , 'bad-tournament' ); ?>',
                "badTournamentFemale": '<?php echo __( 'Female' , 'bad-tournament' ); ?>',
                "confirmRemoveTournament": '<?php echo __( 'Are you sure you want to remove this tournament ?' , 'bad-tournament' ); ?>',
                "confirmRestartTournament": '<?php echo __( 'Are you sure you want to restart this tournament ?' , 'bad-tournament' ); ?>',
                "createMatchSubmitLabel": '<?php echo __( 'Create match now !' , 'bad-tournament' ); ?>',
            }

            var bvg_submenus = {
                <?php
                foreach( $tournaments as $t ){
                    echo '"'.$t->id.'" : "'.$t->name.'",';
                }
                ?>
            };

            console.log( bvg_tournament_constants );
        </script>
        <?php
    }

    function bad_tournament_menu(){
        add_menu_page(
            'Bad Tournament',
            __('Bad Tournament', 'bad-tournament'),
            'manage_options',
            'bad_tournament',
            array( $this, 'bad_tournament_admin' ),
            plugin_dir_url( __FILE__ ).'icons/bad-tournament-icon.png',
            20
        );

        add_submenu_page( 'bad_tournament', 'Clubs', __('Clubs', 'bad-tournament'), 'manage_options', 'admin.php?page=bad_tournament&admin_view=clubs');
        add_submenu_page( 'bad_tournament', 'Players', __('Players', 'bad-tournament'), 'manage_options', 'admin.php?page=bad_tournament&admin_view=players');
        add_submenu_page( 'bad_tournament', 'Table', __('Table', 'bad-tournament'), 'manage_options', 'admin.php?page=bad_tournament&admin_view=table');
        add_submenu_page( 'bad_tournament', 'Matches', __('Matches', 'bad-tournament'), 'manage_options', 'admin.php?page=bad_tournament&admin_view=matches');

        add_submenu_page( 'bad_tournament', 'Options', __('Options', 'bad-tournament'), 'manage_options', 'admin.php?page=bad_tournament&admin_view=export');
    }

    function bad_tournament_start_session(){
        if( !isset( $_SESSION ) ){
            session_start();
        }

        register_setting( 'badt', 'badt_debug');
    }

    function bad_tournament_admin(){

        $bvg_admin_msg = '';
        if( get_option( 'badt_debug' ) == 1 ){
            define( 'BADT_DEBUG_MODE', true );
        }else{
            define( 'BADT_DEBUG_MODE', false );
        }


        /* jQuery UI for datepicker
        wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css');
        wp_register_script('addons_script', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', array('jquery'), '');
        wp_enqueue_script('addons_script');
        */
        wp_enqueue_script('jquery-ui-slider');
        wp_enqueue_script('jquery-ui-datepicker');

        /* Datetime Picker Plugin */
        wp_enqueue_style( 'addons2_style', plugin_dir_url(__FILE__).'admin/jquery-ui-timepicker-addon.css');
        wp_register_script('addons2_script', plugins_url( 'admin/jquery-ui-timepicker-addon.js', __FILE__ ), array('jquery'), '');
        wp_enqueue_script('addons2_script');



        wp_enqueue_style( 'bad_tournament_admin_style', plugin_dir_url(__FILE__).'admin/bad-tournament-admin.css');
        wp_register_script( 'bad_tournament_admin', plugins_url( 'admin/bad-tournament-admin.js', __FILE__ ) );
        $translation_array = array(
            'save_now' => __( 'Save now ?..', 'bad-tournament' )
        );
        wp_localize_script( 'bad_tournament_admin', 'bad_tournament_admin', $translation_array );
        wp_enqueue_script( 'bad_tournament_admin'  );

        if ( current_user_can('edit_pages') ) {

            //echo 'DB USER: '.DB_USER.' '.DB_NAME.' '.DB_PASSWORD.' '.DB_HOST;
            include plugin_dir_path(__FILE__).'admin/install.php';

            $bad_tournament_current_version = get_option( 'bad_tournament_installed' );
            if( $bad_tournament_current_version !== $bad_tournament_version ){

                /* Not yet installed or need update */
                $bvg_admin_msg = badt_install( $bad_tournament_version, $bad_tournament_current_version );
                update_option('bad_tournament_installed', $bad_tournament_version  );

            }

            if( $_GET['admin_view'] == 'export' || $_GET['admin_view'] == 'import' || $_GET['admin_view'] == 'cleanup' ){
                include plugin_dir_path(__FILE__).'admin/index-export.php';
            }else{
                add_action( 'admin_footer', array( $this , 'media_selector_print_scripts' ) );
                include plugin_dir_path(__FILE__).'admin/index.php';
            }

        }else{
            die( __('You are not allowed to access here...', 'bad-tournament') );
        }


        return true;
    }


    /* AJAX */

    // Ajax: Return player infos for tootip
    function player_tooltip( $atts ){
        $html_ajax = '';

        include plugin_dir_path(__FILE__).'admin/action/player-info.php';

        echo $html_ajax;
        wp_die();
    }

    // Ajax: Remove player from tournament
    function player_remove_from_tournament( $atts ){
        $html_ajax = '';
        include plugin_dir_path(__FILE__).'admin/action/player-remove-from-tournament.php';

        echo $html_ajax;
        wp_die();
    }

    // Ajax: Edit player infos
    function player_edit_field(){
        $html_ajax = '';
        include plugin_dir_path(__FILE__).'admin/action/player-edit-field.php';

        echo $html_ajax;
        wp_die();
    }

    // Ajax: Set club as default
    function set_club_default(){
        $html_ajax = '';
        include plugin_dir_path(__FILE__).'admin/action/club-set-default.php';

        echo $html_ajax;
        wp_die();
    }

    // Ajax: Set player form view as default
    function set_player_form_default(){
        $html_ajax = '';
        include plugin_dir_path(__FILE__).'admin/action/player-form-set-default.php';

        echo $html_ajax;
        wp_die();
    }

    // Ajax: Change player in an existing match
    function change_players_match(){
        $html_ajax = '';
        include plugin_dir_path(__FILE__).'admin/action/change-player-match.php';
        //var_dump( $_POST );
        echo $html_ajax;
        wp_die();
    }

    // Ajax: Export as csv file
    function export_file(){
        $asFile = true;
        include plugin_dir_path(__FILE__).'admin/action/export-data.php';
        wp_die();
    }

    // Ajax: Save players couple
    function save_players_couple(){
        $html_ajax = '';
        include_once plugin_dir_path(__FILE__). 'admin/db-get-content.php';
        include plugin_dir_path(__FILE__).'admin/action/save-players-couple.php';
        echo $html_ajax;
        wp_die();
    }


    /* SHORTCODES */

    // Shortcode tournament selector
    function bad_tournament_selector_shortcode( $atts ){

        add_action('init', array( $this, 'bad_tournament_start_session' ) );

        /* Use css from theme if existing */
        $theme_uri = get_theme_file_path();
        if( file_exists( $theme_uri.'/bad-tournament.css' ) && !defined( 'BADT_THEME_CSS' ) ){
            wp_enqueue_style( 'bad_tournament', get_theme_file_uri() . '/bad-tournament.css' );
            define( 'BADT_THEME_CSS' , true );
        }else if( !defined( 'BADT_THEME_CSS' ) ){
            wp_enqueue_style( 'bad_tournament_admin_style', plugin_dir_url(__FILE__).'bad-tournament.css');
        }

        $html_shortcode = '';
        include plugin_dir_path( __FILE__ ).'shortcodes/shortcode-tournament-selector.php';

        return $html_shortcode;
    }

    // Shortcode table
    function bad_tournament_table_shortcode( $atts ) {

        /* Use css from theme if existing */
        $theme_uri = get_theme_file_path();
        if( file_exists( $theme_uri.'/bad-tournament.css' ) && !defined( 'BADT_THEME_CSS' ) ){
            wp_enqueue_style( 'bad_tournament', get_theme_file_uri() . '/bad-tournament.css' );
            define( 'BADT_THEME_CSS' , true );
        }else if( !defined( 'BADT_THEME_CSS' ) ){
            wp_enqueue_style( 'bad_tournament_admin_style', plugin_dir_url(__FILE__).'bad-tournament.css');
        }



        $html_shortcode = '';
        include plugin_dir_path( __FILE__ ).'shortcodes/shortcode-table.php';

        return $html_shortcode;
    }

    // Shortcode matches
    function bad_tournament_matches_shortcode( $atts ) {

        /* Use css from theme if existing */
        $theme_uri = get_theme_file_path();
        if( file_exists( $theme_uri.'/bad-tournament.css' ) && !defined( 'BADT_THEME_CSS' ) ){
            wp_enqueue_style( 'bad_tournament', get_theme_file_uri() . '/bad-tournament.css' );
            define( 'BADT_THEME_CSS' , true );
        }else if( !defined( 'BADT_THEME_CSS' ) ){
            wp_enqueue_style( 'bad_tournament_admin_style', plugin_dir_url(__FILE__).'bad-tournament.css');
        }

        wp_enqueue_script( 'bad_tournament', plugins_url( 'bad-tournament.js', __FILE__ ) );
        wp_enqueue_script('jquery-effects-core', '', '', array('jquery'));

        $html_shortcode = '';
        include plugin_dir_path( __FILE__ ).'shortcodes/shortcode-matches.php';

        return $html_shortcode;
    }

    // Shortcode tournament summary
    function bad_tournament_summary_shortcode( $atts ) {


        /* Use css from theme if existing */
        $theme_uri = get_theme_file_path();

        if( file_exists( $theme_uri.'/bad-tournament.css' ) && !defined( 'BADT_THEME_CSS' ) ){
            wp_enqueue_style( 'bad_tournament', get_theme_file_uri() . '/bad-tournament.css' );
            define( 'BADT_THEME_CSS' , true );
        }else if( !defined( 'BADT_THEME_CSS' ) ){
            wp_enqueue_style( 'bad_tournament_admin_style', plugin_dir_url(__FILE__).'bad-tournament.css');
        }
        $html_shortcode = '';
        include plugin_dir_path( __FILE__ ).'shortcodes/shortcode-summary.php';

        return $html_shortcode;
    }

    // Shortcode player
    function bad_tournament_player_shortcode( $atts ) {

        /* Use css from theme if existing */
        $theme_uri = get_theme_file_path();
        if( file_exists( $theme_uri.'/bad-tournament.css' ) && !defined( 'BADT_THEME_CSS' ) ){
            wp_enqueue_style( 'bad_tournament', get_theme_file_uri() . '/bad-tournament.css' );
            define( 'BADT_THEME_CSS' , true );
        }else if( !defined( 'BADT_THEME_CSS' ) ){
            wp_enqueue_style( 'bad_tournament_admin_style', plugin_dir_url(__FILE__).'bad-tournament.css');
        }

        wp_enqueue_script( 'bad_tournament', plugins_url( 'bad-tournament.js', __FILE__ ) );
        //wp_enqueue_script('jquery-effects-core', '', '', array('jquery'));

        $html_shortcode = '';
        include plugin_dir_path( __FILE__ ).'shortcodes/shortcode-player.php';

        return $html_shortcode;
    }


    /* Media Library */
    function media_selector_print_scripts() {

        $my_saved_attachment_post_id = get_option( 'media_selector_attachment_id', 0 );

        ?><script type='text/javascript'>
            jQuery( document ).ready( function( $ ) {
                // Uploading files
                var file_frame;
                var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
                var set_to_post_id = <?php echo $my_saved_attachment_post_id; ?>; // Set this
                var jPreview = $( '#image-preview' );
                var jAttachmentId = $( '#image_attachment_id' );
                jQuery('#upload_image_button, #upload_pic_button').on('click', function( event ){
                    if( jQuery( this).attr( 'id' ) == 'upload_pic_button' ){
                        jPreview = $( '#pic-preview' );
                        jAttachmentId = $( '#profile_attachment_id' );
                    }else{
                        jPreview = $( '#image-preview' );
                        jAttachmentId = $( '#image_attachment_id' );
                    }
                    event.preventDefault();
                    // If the media frame already exists, reopen it.
                    if ( file_frame ) {
                        // Set the post ID to what we want
                        file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
                        // Open frame
                        file_frame.open();
                        return;
                    } else {
                        // Set the wp.media post id so the uploader grabs the ID we want when initialised
                        wp.media.model.settings.post.id = set_to_post_id;
                    }
                    // Create the media frame.
                    file_frame = wp.media.frames.file_frame = wp.media({
                        title: 'Select a image to upload',
                        button: {
                            text: 'Use this image',
                        },
                        multiple: false	// Set to true to allow multiple files to be selected
                    });
                    // When an image is selected, run a callback.
                    file_frame.on( 'select', function() {
                        // We set multiple to false so only get one image from the uploader
                        attachment = file_frame.state().get('selection').first().toJSON();
                        // Do something with attachment.id and/or attachment.url here
                        jPreview.attr( 'src', attachment.url ).css( 'width', 'auto' );
                        jAttachmentId.val( attachment.id );
                        // Restore the main post ID
                        wp.media.model.settings.post.id = wp_media_post_id;
                    });
                    // Finally, open the modal
                    file_frame.open();
                });
                // Restore the main ID when the add media button is pressed
                jQuery( 'a.add_media' ).on( 'click', function() {
                    wp.media.model.settings.post.id = wp_media_post_id;
                });
            });
        </script><?php
    }

}

new badt_Bad_Tournament();