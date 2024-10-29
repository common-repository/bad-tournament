<?php
/**
 * Created by PhpStorm.
 * User: ldorier
 * Date: 13.11.2017
 * Time: 09:12
 */

/* Selector */
$html = '';

//$html .= '<div class="admin_block_label">Spiele</div>';
$html .= '<div class="bad_tournament_block" id="tournament_selector">';
    $html .= '<form method="post" id="tournament_selector_form" >';
        $html .= '<select name="tournament_selector_id" id="tournament_selector_id">';
            $html .= '<option value="0" >'.__( 'Choose...' , 'bad-tournament' ).'</option>';
            foreach( $tournaments as $k => $tournament_option ){
                $selected = '';
                if( isset( $_SESSION['tournament_to_display'] ) && $_SESSION['tournament_to_display'] == $tournament_option->id ){
                    $selected = 'selected="selected" ';
                }
                $html .= '<option value="'.$tournament_option->id.'" '.$selected.'>'.$tournament_option->name.'</option>';
            }
        $html .= '</select>';
    $html .= '</form>';
$html .= '</div>';

$html .= '<script>
    jQuery( \'#tournament_selector_id\' ).on( \'change\', function(){
        if( jQuery( this ).val() > 0 ){
            jQuery( \'#tournament_selector_form\').submit();
        }
    });
</script>';

//var_dump( $tournament );

