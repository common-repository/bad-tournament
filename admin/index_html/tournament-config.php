<?php
/**
 * Created by PhpStorm.
 * User: ldorier
 * Date: 13.11.2017
 * Time: 09:12
 */


/* Tournament */
//$html .= '<div class="admin_block_label">Tournament</div>';
$html .= '<div class="admin_block nav_tournament" id="block_tournament_select" '.( $ADMIN_VIEW == 'tournament' ? 'style="display: block;"' : '' ).'>';
$html .= '<form method="post" class="validate" novalidate="novalidate" >';
$html .= '<input type="hidden" name="form_action" value="tournament-select" />';
$html .= '<input type="hidden" name="tournament_select_id" value="'.$_SESSION['t_id'].'" />';

    $html .= '<table class="form-table">';
    if( !empty( $tournaments ) ){
        $html .= '<tr class="form-field form-required">';
            $html .= '<th scope="row">';
                $html .= '<label>'.__('Choose tournament:', 'bad-tournament').'</label>';
            $html .= '</th>';
            $html .= '<td>';
                $html .= '<select type="text" value="" name="tournament_select" id="tournament_select">';
                $html .= '<option value="0">'.__('Choose', 'bad-tournament').'</option>';
                foreach( $tournaments as $tournament ){
                    $html .= '<option value="'.$tournament->id.'" >'.$tournament->name.'</option>';
                    if( $tournament->id == $_SESSION['t_id'] ){
                        $_SESSION['t_name'] = $tournament->name;
                        $_SESSION['t_round'] = $tournament->round;
                        $_SESSION['t_system'] = $tournament->system;
                        $_SESSION['t_nb_sets'] = $tournament->nb_sets;
                        $_SESSION['t_points_set'] = $tournament->points_set;
                        $_SESSION['t_max_points_set'] = $tournament->max_points_set;
                    }
                }
                $html .= '</select>';
            $html .= '</td>';
        $html .= '</tr>';

        $html .= '<tr class="form-field form-required">';
            $html .= '<td colspan="2" >';
                $html .= '<input type="submit" class="button button-primary" value="'.__('Choose tournament', 'bad-tournament').'" id="tournament_select_button" name="tournament_select_button" title="'.__( 'Will activate this tournament in the admin area...' , 'bad-tournament' ).'" />';
                $html .= '<input type="submit" class="submit2 button-primary button" value="'.__('Remove tournament', 'bad-tournament').'" id="tournament_remove_button" name="tournament_remove_button" title="'.__( 'This tournament will be removed and all matches and players will be deleted for this tournament as well in the database' , 'bad-tournament' ).'" />';
                $html .= '<input type="submit" class="submit2 button-primary button" value="'.__('Restart tournament', 'bad-tournament').'" id="tournament_restart_button" name="tournament_restart_button" title="'.__( 'All matches for this tournament will be deleted, players stats reinitialized and current round set to 1...' , 'bad-tournament' ).'" />';
            $html .= '</td>';
        $html .= '</tr>';
    }


    $html .= '<tr class="form-field form-required">';
        $html .= '<th scope="row">';
            $html .= '<label>'.__('Parent tournament:', 'bad-tournament').'</label>';
        $html .= '</th>';
        $html .= '<td>';
            $html .= '<select type="text" value="" name="tournament_parent_select" id="tournament_parent_select">';
            $html .= '<option value="0" selected="selected">'.__('Choose', 'bad-tournament').'</option>';
            $html .= '<option value="0">'.__('No parent', 'bad-tournament').'</option>';
            foreach( $tournaments as $tournament ){
                $html .= '<option value="'.$tournament->id.'" '.( $tournament->id == $_SESSION['t_id'] ? 'selected="selected"' : '' ).'>'.$tournament->name.'</option>';
            }
            $html .= '</select>';
        $html .= '<td>';
    $html .= '</tr>';

    $html .= '<tr class="form-field form-required">';
        $html .= '<th scope="row">';
            $html .= '<label>'.__('Tournament name:', 'bad-tournament').'</label>';
        $html .= '</th>';
        $html .= '<td>';
            $html .= '<input type="text" value="" placeholder="'.__('Tournament Name', 'bad-tournament').'" name="tournament_name" id="tournament_name" />';
        $html .= '</td>';
    $html .= '</tr>';


    wp_enqueue_media();


    $logo_src = '';
    $logo_src_id = '';

    /*
    $logo = $tournament->logo;
    if( is_numeric( $logo ) && $logo > 0 ){
        $logo_src_id = $logo;
        $logo_src = wp_get_attachment_url( $logo );
    }else if( !empty( $logo ) ){
        $logo_src = $logo;
    }
    */

    $html .= '<tr class="form-field form-required">';
        $html .= '<th scope="row">';
            $html .= '<label>'.__('Tournament logo:', 'bad-tournament').'</label>';
        $html .= '</th>';
        $html .= '<td>';
            $html .= '<div class="image-preview-wrapper">';
                $html .= '<img id="image-preview" src="'.$logo_src.'" height="100">';
            $html .= '</div>';
            $html .= '<input id="upload_image_button" type="button" class="button" value="'.__( 'Choose image' , 'bad-tournament' ).'">';
            $html .= '<input type="hidden" name="image_attachment_id" id="image_attachment_id" value="'.$logo_src_id.'">';

            $html .= '<label class="new_line">'.__('or use external URL:', 'bad-tournament').'</label>';
            $html .= '<input type="text" value="'.$logo_src.'" placeholder="http://" name="tournament_logo_url" id="tournament_logo_url" />';
        $html .= '</td>';
    $html .= '</tr>';

    $html .= '<tr class="form-field form-required">';
        $html .= '<th scope="row">';
            $html .= '<label>'.__('Localization:', 'bad-tournament').'</label>';
        $html .= '</th>';
        $html .= '<td>';
            $html .= '<input type="text" value="" placeholder="'.__('Localization', 'bad-tournament').'" name="tournament_localization" id="tournament_localization" />';
        $html .= '</td>';
    $html .= '</tr>';

$html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
        $html .= '<label>'.__('Date:', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
        $html .= '<input type="text" value="" placeholder="'.__('from...', 'bad-tournament').'" name="tournament_date_start" id="tournament_date_start" class="datetime2picker" />';
        $html .= '<input type="text" value="" placeholder="'.__('...to', 'bad-tournament').'" name="tournament_date_end" id="tournament_date_end" class="datetime2picker" />';
    $html .= '</td>';
$html .= '</tr>';


$html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
        $html .= '<label>'.__('Tournament Typ   :', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
        $html .= '<div class="radio_block">';
            $html .= '<span><input type="radio" id="tournament_typ1" name="tournament_typ" value="1" checked="checked" /> <label for="tournament_typ1" class="radio">'.__('Simple Men', 'bad-tournament').'</label></span>';
            $html .= '<span><input type="radio" id="tournament_typ2" name="tournament_typ" value="2" /> <label for="tournament_typ2" class="radio">'.__('Simple Women', 'bad-tournament').'</label></span>';
            $html .= '<span><input type="radio" id="tournament_typ3" name="tournament_typ" value="3" /> <label for="tournament_typ3" class="radio">'.__('Double Men', 'bad-tournament').'</label></span>';
            $html .= '<span><input type="radio" id="tournament_typ4" name="tournament_typ" value="4" /> <label for="tournament_typ4" class="radio">'.__('Double Women', 'bad-tournament').'</label></span>';
            $html .= '<span><input type="radio" id="tournament_typ5" name="tournament_typ" value="5" /> <label for="tournament_typ5" class="radio">'.__('Mixte', 'bad-tournament').'</label></span>';
            $html .= '<span><input type="radio" id="tournament_typ6" name="tournament_typ" value="6" /> <label for="tournament_typ5" class="radio">'.__('Simple Free', 'bad-tournament').'</label></span>';
            $html .= '<span><input type="radio" id="tournament_typ7" name="tournament_typ" value="7" /> <label for="tournament_typ5" class="radio">'.__('Double Free', 'bad-tournament').'</label></span>';
        $html .= '</div>';
    $html .= '</td>';
$html .= '</tr>';

$html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
        $html .= '<label>'.__('Other tournaments :', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
        $html .= '<div class="radio_block">';
            $html .= '<span><input type="checkbox" id="subtournament_typ1" name="subtournament_typ[]" value="1" /> <label for="subtournament_typ1" class="radio">'.__('Simple Men', 'bad-tournament').'</label></span>';
            $html .= '<span><input type="checkbox" id="subtournament_typ2" name="subtournament_typ[]" value="2" /> <label for="subtournament_typ2" class="radio">'.__('Simple Women', 'bad-tournament').'</label></span>';
            $html .= '<span><input type="checkbox" id="subtournament_typ3" name="subtournament_typ[]" value="3" /> <label for="subtournament_typ3" class="radio">'.__('Double Men', 'bad-tournament').'</label></span>';
            $html .= '<span><input type="checkbox" id="subtournament_typ4" name="subtournament_typ[]" value="4" /> <label for="subtournament_typ4" class="radio">'.__('Double Women', 'bad-tournament').'</label></span>';
            $html .= '<span><input type="checkbox" id="subtournament_typ5" name="subtournament_typ[]" value="5" /> <label for="subtournament_typ5" class="radio">'.__('Mixte', 'bad-tournament').'</label></span>';
            $html .= '<span><input type="checkbox" id="subtournament_typ6" name="subtournament_typ[]" value="6" /> <label for="subtournament_typ5" class="radio">'.__('Simple Free', 'bad-tournament').'</label></span>';
            $html .= '<span><input type="checkbox" id="subtournament_typ7" name="subtournament_typ[]" value="7" /> <label for="subtournament_typ5" class="radio">'.__('Double Free', 'bad-tournament').'</label></span>';
        $html .= '</div>';
    $html .= '</td>';
$html .= '</tr>';

$html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
        $html .= '<label>'.__('Tournament System:', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
        $html .= '<div class="radio_block">';
        $html .= '<span><input type="radio" id="tournament_system1" name="tournament_system" value="1" checked="checked" /> <label for="tournament_system1" class="radio">'.__('Swiss System', 'bad-tournament').'</label></span>';
        //$html .= '<span><input type="radio" id="tournament_system2" name="tournament_system" value="2" disabled="disabled" /> <label for="tournament_system2" class="radio">'.__('League', 'bad-tournament').'</label></span>';
        //$html .= '<span><input type="radio" id="tournament_system3" name="tournament_system" value="3" disabled="disabled" /> <label for="tournament_system3" class="radio">'.__('KO System', 'bad-tournament').'</label></span>';
        $html .= '<span><input type="radio" id="tournament_system4" name="tournament_system" value="4" /> <label for="tournament_system4" class="radio">'.__('Grinding Tournament', 'bad-tournament').'</label></span>';
        $html .= '</div>';
    $html .= '</td>';
$html .= '</tr>';

$html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
        $html .= '<label>'.__('Nb sets to win:', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
        $html .= '<input type="number" value="" name="tournament_nb_sets" id="tournament_nb_sets" min="1" max="3" />';
    $html .= '</td>';
$html .= '</tr>';

$html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
        $html .= '<label>'.__('Points/Set:', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
        $html .= '<input type="number" value="" name="tournament_points_set" id="tournament_points_set" min="11" max="21" />';
    $html .= '</td>';
$html .= '</tr>';

$html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
        $html .= '<label>'.__('Max points per set:', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
        $html .= '<input type="number" value="" name="tournament_max_points_set" id="tournament_max_points_set" min="11" max="30" />';
    $html .= '</td>';
$html .= '</tr>';

$html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
        $html .= '<label>'.__('Current round for this tournament:', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
        $html .= '<input type="number" value="" name="round" id="round" min="1" max="64" />';
    $html .= '</td>';
$html .= '</tr>';

$html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
        $html .= '<label>'.__('Max rounds for the full tournament:', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
        $html .= '<input type="number" value="" name="round_max" id="round_max" min="1" max="64" />';
    $html .= '</td>';
$html .= '</tr>';

$html .= '<tr class="form-field form-required">';
    $html .= '<th scope="row">';
        $html .= '<label>'.__('Club restriction:', 'bad-tournament').'</label>';
    $html .= '</th>';
    $html .= '<td>';
        $html .= '<select name="club_restriction" id="club_restriction">';
        $html .= '<option value="0" >'.__( 'Open' , 'bad-tournament' ).'</option>';
        foreach( $clubs as $club ){
            $html .= '<option value="'.$club->id.'" >'.$club->name.'</option>';
        }
        $html .= '</select>';
    $html .= '</td>';
$html .= '</tr>';

$html .= '<tr class="form-field form-required">';
    $html .= '<td colspan="2" >';
        $html .= '<input type="submit" value="'.__('Create tournament', 'bad-tournament').'" class="button button-primary" />';
        $html .= '<input type="submit" value="'.__('Edit tournament', 'bad-tournament').'" name="tournament_edit" id="tournament_edit" class="submit2 button button-primary" />';
    $html .= '</td>';
$html .= '</tr>';

        $html .= '</table>';
    $html .= '</form>';

    $html .= '<h1 class="topspace">'.__('Shortcodes', 'bad-tournament').'</h1>';
    $html .= '<div class="shortcode_bvg"><h2>'.__('Tournaments selector for all tournaments', 'bad-tournament').'</h2><input type="text" class="wp_style" value="[bad_tournament_selector t_parent_id=false]" /></div>';
    $html .= '<div class="shortcode_bvg"><h2>'.__('Tournaments selector for a defined group', 'bad-tournament').'</h2><input type="text" class="wp_style" value="[bad_tournament_selector t_parent_id='.$_SESSION['t_id'].']" /></div>';
    $html .= '<div class="shortcode_bvg"><h2>'.__('Tournament summary', 'bad-tournament').'</h2><input type="text" class="wp_style" value="[bad_tournament_summary t_id='.$_SESSION['t_id'].' t_view=full]" /></div>';


$html .= '</div>';

$html .= '<script>
tournament = [];
';
    foreach( $tournaments as $tournament ){

        $datetime_start_arr = explode( ' ', $tournament->date_start );
        $date_start_arr = explode( '-', $datetime_start_arr[0] );
        $tournament->date_start = $date_start_arr[2].'/'.$date_start_arr[1].'/'.$date_start_arr[0].' '.$datetime_start_arr[1];

        $datetime_end_arr = explode( ' ', $tournament->date_end );
        $date_end_arr = explode( '-', $datetime_end_arr[0] );
        $tournament->date_end = $date_end_arr[2].'/'.$date_end_arr[1].'/'.$date_end_arr[0].' '.$datetime_end_arr[1];

        $tournament->logo_url = $tournament->logo;
        if( is_numeric( $tournament->logo ) ){
            $tournament->logo_url = wp_get_attachment_url( $tournament->logo );
        }

        $html .= 'tournament['.$tournament->id.'] = \''.json_encode( $tournament ).'\';';
    };
$html .= "
    jQuery(document).ready(function() {
        var startDateTextBox = jQuery('#tournament_date_start');
        var endDateTextBox = jQuery('#tournament_date_end');

        startDateTextBox.datetimepicker({
            dateFormat: 'dd/mm/yy',
            timeFormat: 'hh:mm',
            onClose: function(dateText, inst) {
                if (endDateTextBox.val() != '') {
                    var testStartDate = startDateTextBox.datetimepicker('getDate');
                    var testEndDate = endDateTextBox.datetimepicker('getDate');
                    if (testStartDate > testEndDate)
                        endDateTextBox.datetimepicker('setDate', testStartDate);
                }
                else {
                    endDateTextBox.val(dateText);
                }
            },
                onSelect: function (selectedDateTime){
                endDateTextBox.datetimepicker('option', 'minDate', startDateTextBox.datetimepicker('getDate') );
            }
        });
        endDateTextBox.datetimepicker({
            dateFormat: 'dd/mm/yy',
            timeFormat: 'hh:mm',
            onClose: function(dateText, inst) {
                if (startDateTextBox.val() != '') {
                    var testStartDate = startDateTextBox.datetimepicker('getDate');
                    var testEndDate = endDateTextBox.datetimepicker('getDate');
                    if (testStartDate > testEndDate)
                        startDateTextBox.datetimepicker('setDate', testEndDate);
                }
                else {
                    startDateTextBox.val(dateText);
                }
            },
            onSelect: function (selectedDateTime){
            startDateTextBox.datetimepicker('option', 'maxDate', endDateTextBox.datetimepicker('getDate') );
        }
        });
    });";
$html .= '</script>';

