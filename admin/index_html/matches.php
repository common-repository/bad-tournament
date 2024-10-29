<?php
/**
 * Created by PhpStorm.
 * User: ldorier
 * Date: 13.11.2017
 * Time: 09:12
 */

$nb_players_matches = 4;
if( $_SESSION['current_tournament']['tournament_typ'] == 1 || $_SESSION['current_tournament']['tournament_typ'] == 2 || $_SESSION['current_tournament']['tournament_typ'] == 6 ){
    $nb_players_matches = 2;
}



/* Matches */


//$html .= '<div class="admin_block_label">Spiele</div>';
$html .= '<div class="admin_block nav_match" id="block_game" '.( $ADMIN_VIEW == 'matches' ? 'style="display: block;"' : '' ).'>';

    /* Create custom match */
    $html .= '<div style="clear: both; margin-bottom: 25px; display: inline-block;">';
        $html .= '<form method="post" id="match_form_custom" action="admin.php?page=bad_tournament&admin_view=matches" class="match_form">';
            $html .= '<input type="hidden" name="form_action" value="game-create" />';


            $html .= '<div id="create_match_form" style="float: left; display: none; margin-bottom: 15px;;">';
                $html .= '<div style="clear: both;">';

                    //$html .= '<table class="form-table">';

                    $html .= '<select name="pl1_m_name" id="pl1_m_name" class="player_name_create" />';
                    $html_options = '';
                    foreach( $players as $k => $player ){
                        $html_options .= '<option value="'.$player->id.'">'.$player->player_firstname.' '.$player->player_lastname.'</option>';
                    }
                    $html .= $html_options;
                    $html .= '</select>';
                    if( $nb_players_matches == 4 ){
                        $html .= '<select name="pl1_m_name_bis" id="pl1_m_name_bis" class="player_name_create" />';
                        $html .= $html_options;
                        $html .= '</select>';
                    }
                $html .= '</div>';
                $html .= '<div>VS</div>';
                $html .= '<div style="clear: both;">';

                    $html .= '<select name="pl2_m_name" id="pl2_m_name" class="player_name_create" />';
                    $html .= $html_options;
                    $html .= '</select>';
                    if( $nb_players_matches == 4 ){
                        $html .= '<select name="pl2_m_name_bis" id="pl2_m_name_bis" class="player_name_create" />';
                        $html .= $html_options;
                        $html .= '</select>';
                    }
                $html .= '</div>';
            $html .= '</div>';

            $html .= '<input id="create_match_button" type="button" value="'.__('Create custom match', 'bad-tournament').'" class="button button-primary" />';
        $html .= '</form>';
    $html .= '</div>';

    //var_dump( $matches );
    if( !empty( $matches ) ){


        if( $_SESSION['t_round'] > 1 ) {
            $html .= '<div style="clear: both; margin-bottom: 25px; display: block;"><form method="post" action="admin.php?page=bad_tournament&admin_view=matches" >
            '.__( 'Round: ' , 'bad-tournament').'<select name="view_round" id="view_round" style="vertical-align: baseline; float: none;">';
            for ($i = 0; $i < $_SESSION['t_round']; $i++) {
                $j = $i + 1;
                $html .= '<option value="' . $j . '" ' . (($i + 1) == $_SESSION['round'] ? 'selected="selected"' : '') . '>' . $j . '</option>';
            }
            $html .= '</select>';
            if( isset($_POST['view_round']) && is_numeric( $_POST[ 'view_round' ] ) ){
                $html .= '<h2 style="line-height: 1.8;    padding-left: 10px;    display: flex;">' . __( 'Matches for the round: ' , 'bad-tournament') . $_POST['view_round']. '</h2>';
            }
            $html .= '
</form></div>';
        }

        //echo '<pre>';
        //var_dump( $players );
        uasort($players, function($a, $b) {
            return strcmp( $a->player_lastname , $b->player_lastname);
        });
        //var_dump( $players );
        //echo '</pre>';

        /* Allow to regenerate all games ? */
        $winner_exists = false;
        $all_matches_id = array();
        $tabindex = 0;
        $nb_open_matches = 0;
        foreach( $matches as $match ){
            $m_id = 0;
            $winner = 0;
            $pl1_set1 = 0;
            $pl1_set2 = 0;
            $pl1_set3 = 0;
            $pl1_set4 = 0;
            $pl1_set5 = 0;
            $pl2_set1 = 0;
            $pl2_set2 = 0;
            $pl2_set3 = 0;
            $pl2_set4 = 0;
            $pl2_set5 = 0;

            if( !is_array( $match ) ){
                //var_dump($players[ $match->player1_id ]);
                $player1_name = $players[ $match->player1_id ]->player_firstname.' '.$players[ $match->player1_id ]->player_lastname;
                $player2_name = $players[ $match->player2_id ]->player_firstname.' '.$players[ $match->player2_id ]->player_lastname;
                if( $nb_players_matches == 4 ){
                    $player1_name_bis = $players[ $match->player1_id_bis ]->player_firstname.' '.$players[ $match->player1_id_bis ]->player_lastname;
                    $player2_name_bis = $players[ $match->player2_id_bis ]->player_firstname.' '.$players[ $match->player2_id_bis ]->player_lastname;
                    $pl1_id_bis = $match->player1_id_bis;
                    $pl2_id_bis = $match->player2_id_bis;
                }
                $m_id = $match->id;
                $pl1_id = $match->player1_id;
                $pl2_id = $match->player2_id;
                $winner = $match->winner;

                $pl1_set1 = $match->pl1_set1;
                $pl1_set2 = $match->pl1_set2;
                $pl1_set3 = $match->pl1_set3;
                $pl1_set4 = $match->pl1_set4;
                $pl1_set5 = $match->pl1_set5;
                $pl2_set1 = $match->pl2_set1;
                $pl2_set2 = $match->pl2_set2;
                $pl2_set3 = $match->pl2_set3;
                $pl2_set4 = $match->pl2_set4;
                $pl2_set5 = $match->pl2_set5;
            }else{
                $player1_name = $match['player1_name'];
                $player2_name = $match['player2_name'];
                $pl1_id = $match['player1_id'];
                $pl2_id = $match['player2_id'];
                if( $nb_players_matches == 4 ) {
                    $player1_name_bis = $match['player1_name_bis'];
                    $player2_name_bis = $match['player2_name_bis'];
                    $pl1_id_bis = $match['player1_id_bis'];
                    $pl2_id_bis = $match['player2_id_bis'];
                }
                $winner = $match['winner'];
                $m_id = $match['id'];
            }

            if( $m_id > 0 ){
                $all_matches_id[] = $m_id;

                if( $winner > 0 ){
                    $winner_exists = true;
                }else{
                    $nb_open_matches++;
                }

                if( !isset( $players[ $match->player1_id ] ) && $match->player1_id > 0 ){
                    $player1_name = 'Inaktiv';
                }
                if( !isset( $players[ $match->player2_id ] ) && $match->player2_id > 0 ){
                    $player2_name = 'Inaktiv';
                }
                if( !isset( $players[ $match->player1_id_bis ] ) && $match->player1_id_bis > 0 ){
                    $player1_name_bis = 'Inaktiv';
                }
                if( !isset( $players[ $match->player2_id_bis ] ) && $match->player2_id_bis > 0 ){
                    $player2_name_bis = 'Inaktiv';
                }


                //var_dump( $match );
                $html .= '<form method="post" id="match_form_'.$m_id.'" action="admin.php?page=bad_tournament&admin_view=matches" class="match_form'.( $winner != $pl1_id && $winner != $pl2_id ? '_open' : '' ).'">';
                $html .= '<input type="hidden" name="form_action" value="game-result" />';
                $html .= '<input type="hidden" name="pl1_id" value="'.$pl1_id.'" />';
                $html .= '<input type="hidden" name="pl2_id" value="'.$pl2_id.'" />';
                $html .= '<input type="hidden" name="pl1_id_bis" value="'.$pl1_id_bis.'" />';
                $html .= '<input type="hidden" name="pl2_id_bis" value="'.$pl2_id_bis.'" />';
                $html .= '<input type="hidden" id="match_winner_'.$m_id.'" name="match_winner_'.$m_id.'" value="" />';

                if( $_SESSION['current_tournament'][ 'tournament_typ' ] == 3 || $_SESSION['current_tournament'][ 'tournament_typ' ] == 4 || $_SESSION['current_tournament'][ 'tournament_typ' ] == 5 || $_SESSION['current_tournament'][ 'tournament_typ' ] == 7 ){
                    /* Double
                    */
                    $html .= '<div style="line-height: 26px;"><input type="checkbox" name="match_id" class="match_id" value="'.$m_id.'" '.( $winner == 0 ? 'checked="checked"' : 'disabled="disabled"' ).' />';
                    $html .= __( 'Match' , 'bad-tournament').' '.$m_id.': ';
                    $html .= $players[$pl1_id]->player_lastname;
                    $html .= ' / '.$players[$pl1_id_bis]->player_lastname;
                    $html .= ( $_SESSION['t_system'] == 1 ? ' ('.($players[$pl1_id]->player_level_init+$players[$pl1_id_bis]->player_level_init).')' : '' ).' - ';
                    $html .= $players[$pl2_id]->player_lastname;
                    $html .= ' / '.$players[$pl2_id_bis]->player_lastname;
                    $html .= ( $_SESSION['t_system'] == 1 ? ' ('.($players[$pl2_id]->player_level_init+$players[$pl2_id_bis]->player_level_init).')' : '' ).'</div>';
                }else{
                    /* Simple
                    */
                    $html .= '<div style="line-height: 26px;"><input type="checkbox" name="match_id" class="match_id" value="'.$m_id.'" '.( $winner == 0 ? 'checked="checked"' : 'disabled="disabled"' ).' />'.__( 'Match' , 'bad-tournament').' '.$m_id.': '.$players[$pl1_id]->player_firstname.' '.$players[$pl1_id]->player_lastname.( $_SESSION['t_system'] == 1 ? '('.$players[$pl1_id]->player_level_init.')' : '' ).' - '.$players[$pl2_id]->player_firstname.' '.$players[$pl2_id]->player_lastname.( $_SESSION['t_system'] == 1 ? '('.$players[$pl2_id]->player_level_init.')' : '' ).'</div>';
                }


                $html .= '<div style="clear: both;">';


                //$html .= '<table class="form-table">';

                $html .= '<select name="pl1_m'.$m_id.'_name" id="pl1_m'.$m_id.'_name" data_pl_id="pl1_id" class="player_name '.( $winner == $pl1_id ? 'winner' : '' ).' '.( $winner == $pl2_id ? 'loser' : '' ).'" />';
                if( $player1_name == 'Inaktiv' ){
                    $html .= '<option value="0" selected="selected">'.$player1_name.'</option>';
                }
                foreach( $players as $k => $player ){
                    $html .= '<option value="'.$player->id.'" '.( $k == $pl1_id ? 'selected="selected"' : '' ).'>'.$player->player_firstname.' '.$player->player_lastname.'</option>';
                }
                $html .= '</select>';

                //$html .= '<input type="text" value="'.$player1_name.'" name="pl1_m'.$m_id.'_name" class="player_name '.( $winner == $pl1_id ? 'winner' : '' ).' '.( $winner == $pl2_id ? 'loser' : '' ).'" />';
                if( !empty( $player1_name_bis ) ){
                    //$html .= '<input type="text" value="'.$player1_name_bis.'" name="pl1_m'.$m_id.'_name_bis" class="player_name '.( $winner == $pl1_id ? 'winner' : '' ).' '.( $winner == $pl2_id ? 'loser' : '' ).'" />';

                    $html .= '<select name="pl1_m'.$m_id.'_name_bis" id="pl1_m'.$m_id.'_name_bis" data_pl_id="pl1_id_bis" class="player_name '.( $winner == $pl1_id ? 'winner' : '' ).' '.( $winner == $pl2_id ? 'loser' : '' ).'" />';
                    if( $player1_name_bis == 'Inaktiv' ){
                        $html .= '<option value="0" selected="selected">'.$player1_name_bis.'</option>';
                    }
                    foreach( $players as $k => $player ){
                        $html .= '<option value="'.$player->id.'" '.( $k == $pl1_id_bis ? 'selected="selected"' : '' ).'>'.$player->player_firstname.' '.$player->player_lastname.'</option>';
                    }
                    $html .= '</select>';
                }
                $html .= '<input type="number" value="'.$pl1_set1.'" name="pl1_m'.$m_id.'_set1" class="set_score" min="0" max="'.$_SESSION['current_tournament']['max_points_set'].'" tabindex='.($tabindex+1).' />';
                $html .= '<input type="number" value="'.$pl1_set2.'" name="pl1_m'.$m_id.'_set2" class="set_score" min="0" max="'.$_SESSION['current_tournament']['max_points_set'].'" tabindex='.($tabindex+3).' />';
                $html .= '<input type="number" value="'.$pl1_set3.'" name="pl1_m'.$m_id.'_set3" class="set_score" min="0" max="'.$_SESSION['current_tournament']['max_points_set'].'" tabindex='.($tabindex+5).' />';
                $html .= '<input type="number" value="'.$pl1_set4.'" name="pl1_m'.$m_id.'_set4" class="set_score" '.( $_SESSION['current_tournament']['nb_sets'] < 3 ? 'disabled="disabled"' : '' ).'tabindex='.($tabindex+7).' />';
                $html .= '<input type="number" value="'.$pl1_set5.'" name="pl1_m'.$m_id.'_set5" class="set_score" '.( $_SESSION['current_tournament']['nb_sets'] < 3 ? 'disabled="disabled"' : '' ).'tabindex='.($tabindex+9).' />';
                $html .= '<input type="submit" value="'.__('Winner', 'bad-tournament').'" class="match_winner button" data="'.$pl1_id.'" data_m_id="'.$m_id.'" />';
                $html .= '</div>';




                $html .= '<div>';

                $html .= '<select name="pl2_m'.$m_id.'_name" id="pl2_m'.$m_id.'_name" data_pl_id="pl2_id" class="player_name '.( $winner == $pl2_id ? 'winner' : '' ).' '.( $winner == $pl1_id ? 'loser' : '' ).'" />';
                if( $player2_name == 'Inaktiv' ){
                    $html .= '<option value="0" selected="selected">'.$player2_name.'</option>';
                }
                foreach( $players as $k => $player ){
                    $html .= '<option value="'.$player->id.'" '.( $k == $pl2_id ? 'selected="selected"' : '' ).'>'.$player->player_firstname.' '.$player->player_lastname.'</option>';
                }
                $html .= '</select>';

                //$html .= '<input type="text" value="'.$player2_name.'" name="pl2_m'.$m_id.'_name" class="player_name '.( $winner == $pl2_id ? 'winner' : '' ).' '.( $winner == $pl1_id ? 'loser' : '' ).'" />';
                if( !empty( $player2_name_bis ) ){
                    //$html .= '<input type="text" value="'.$player2_name_bis.'" name="pl2_m'.$m_id.'_name_bis" class="player_name '.( $winner == $pl2_id ? 'winner' : '' ).' '.( $winner == $pl1_id ? 'loser' : '' ).'" />';

                    $html .= '<select name="pl2_m'.$m_id.'_name_bis" id="pl2_m'.$m_id.'_name_bis" data_pl_id="pl2_id_bis" class="player_name '.( $winner == $pl2_id ? 'winner' : '' ).' '.( $winner == $pl1_id ? 'loser' : '' ).'" />';
                    if( $player2_name_bis == 'Inaktiv' ){
                        $html .= '<option value="0" selected="selected">'.$player2_name_bis.'</option>';
                    }
                    foreach( $players as $k => $player ){
                        $html .= '<option value="'.$player->id.'" '.( $k == $pl2_id_bis ? 'selected="selected"' : '' ).'>'.$player->player_firstname.' '.$player->player_lastname.'</option>';
                    }
                    $html .= '</select>';
                }
                $html .= '<input type="number" value="'.$pl2_set1.'" name="pl2_m'.$m_id.'_set1" class="set_score" min="0" max="'.$_SESSION['current_tournament']['max_points_set'].'" tabindex='.($tabindex+2).' />';
                $html .= '<input type="number" value="'.$pl2_set2.'" name="pl2_m'.$m_id.'_set2" class="set_score" min="0" max="'.$_SESSION['current_tournament']['max_points_set'].'" tabindex='.($tabindex+4).' />';
                $html .= '<input type="number" value="'.$pl2_set3.'" name="pl2_m'.$m_id.'_set3" class="set_score" min="0" max="'.$_SESSION['current_tournament']['max_points_set'].'" tabindex='.($tabindex+6).' />';
                $html .= '<input type="number" value="'.$pl2_set4.'" name="pl2_m'.$m_id.'_set4" class="set_score" '.( $_SESSION['current_tournament']['nb_sets'] < 3 ? 'disabled="disabled"' : '' ).'tabindex='.($tabindex+8).' />';
                $html .= '<input type="number" value="'.$pl2_set5.'" name="pl2_m'.$m_id.'_set5" class="set_score" '.( $_SESSION['current_tournament']['nb_sets'] < 3 ? 'disabled="disabled"' : '' ).'tabindex='.($tabindex+10).' />';
                $html .= '<input type="submit" value="'.__('Winner', 'bad-tournament').'" class="match_winner button" data="'.$pl2_id.'" data_m_id="'.$m_id.'" />';
                $html .= '</div>';


                $html .= '<br />';

                $html .= '<input type="submit" value="'.__('Update match', 'bad-tournament').'" class="button button-primary" />';

                $html .= '<br /><br /><hr />';


                $html .= '</form>';


                $tabindex = $tabindex + 100;
            }

        }



        if( !$winner_exists ){
            $html .= '<form method="post" action="admin.php?page=bad_tournament&admin_view=matches">';
            $html .= '<input name="regenerate_matchs_now" type="submit" value="'.__('Regenerate matches', 'bad-tournament').'" class="button button-primary" />';
            $html .= '</form>';
        }

        if( $nb_open_matches > 0){
            $html .= '<form method="post" id="allmatches_form" action="admin.php?page=bad_tournament&admin_view=matches">';
            $html .= '<input type="hidden" name="form_action" id="form_action_updateable" value="game-result" />';
            $html .= '<input type="hidden" name="form_subaction" value="allgames-result" />';
            $html .= '<textarea name="json_data" id="json_data" style="display: none;" ></textarea>';
            $html .= '<input name="update_all_matches" id="update_all_matches" type="submit" value="'.__('Update all selected matches', 'bad-tournament').'" class="button button-primary '.( !$winner_exists ? 'submit2' : '' ).'" />';
            $html .= '<input name="delete_all_matches" id="delete_all_matches" type="button" value="'.__('Delete all selected matches', 'bad-tournament').'" class="button button-primary '.( !$winner_exists ? 'submit2' : '' ).'" />';
            $html .= '</form>';
            $html .= '<script>
            jQuery(\'#delete_all_matches\').on(\'click\', function(){
                if( confirm(\''.__('Are you sure ?', 'bad-tournament').'\') ){
                    jQuery(\'#form_action_updateable\').val(\'game-delete\');
                    forms_object = {};
                    forms_json = {};
                    jQuery(\'form.match_form_open\').each(function( index ){
                        forms_json[index] = jQuery( this ).serializeArray();
                    });
                    console.log( forms_json );
                    $(\'#json_data\').text( JSON.stringify( forms_json ) );
                    jQuery( this ).attr(\'type\', \'submit\');
                }

            });

            jQuery(\'#update_all_matches\').on(\'click\', function(){
                forms_object = {};
                forms_json = {};
                jQuery(\'form.match_form_open\').each(function( index ){
                    forms_json[index] = jQuery( this ).serializeArray();
                });
                console.log( forms_json );
                $(\'#json_data\').text( JSON.stringify( forms_json ) );
                //$(\'#allmatches_form\').submit();
            });
        </script>';
        }


        $html .= '<h1 class="topspace">'.__('Shortcodes', 'bad-tournament').'</h1>';
        $html .= '<div class="shortcode_bvg"><h2>'.__('Matches', 'bad-tournament').'</h2><input type="text" class="wp_style" value="[bad_tournament_matches t_id='.$_SESSION['t_id'].' round=0]" /></div>';
        $html .= '<div class="shortcode_bvg"><h2>'.__('Matches only for 3rd round', 'bad-tournament').'</h2><input type="text" class="wp_style" value="[bad_tournament_matches t_id='.$_SESSION['t_id'].' round=3]" /></div>';
        $html .= '<div class="shortcode_bvg"><h2>'.__('Matches only for the current round', 'bad-tournament').'</h2><input type="text" class="wp_style" value="[bad_tournament_matches t_id='.$_SESSION['t_id'].' round=!]" /></div>';
        $html .= '<div class="shortcode_bvg"><h2>'.__('Matches only for the current round (Live Viewing Mode)', 'bad-tournament').'</h2><input type="text" class="wp_style" value="[bad_tournament_matches t_id='.$_SESSION['t_id'].' round=! mode=live]" /></div>';
        $html .= '<div class="shortcode_bvg"><h2>'.__('Match unique in Live Viewing Mode', 'bad-tournament').'</h2><input type="text" class="wp_style" value="[bad_tournament_matches m_id='.$m_id.' mode=live]" /></div>';

    }else{

        $html .= '<form method="post" action="admin.php?page=bad_tournament&admin_view=matches">';
        $html .= '<input name="generate_matchs_now" type="submit" value="'.__('Create matches', 'bad-tournament').'" class="button button-primary" />';
        $html .= '</form>';
    }


$html .= '</div>';

