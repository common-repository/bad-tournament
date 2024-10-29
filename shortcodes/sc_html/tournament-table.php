<?php
/**
 * Created by PhpStorm.
 * User: ldorier
 * Date: 13.11.2017
 * Time: 09:12
 */


/* Table */

//$html .= '<div class="admin_block_label">Tabelle</div>';
$html .= '<div class="admin_block nav_table" id="block_table">';
$html .= '<form method="post">';
$html .= '<input type="hidden" name="form_action" value="next-round" />';

$html .= '<ul class="table">';
$html .= '<li class="table_header">';
$html .= '<span class="pl_name">';
$html .= __( 'Player', 'bad-tournament' );
$html .= "</span>";
$html .= '<span class="pl_played">';
$html .= __( 'Games', 'bad-tournament' );
$html .= "</span>";
$html .= '<span class="pl_victory">';
$html .= __( 'V', 'bad-tournament' );
$html .= "</span>";
$html .= '<span class="pl_draw">';
$html .= __( 'D', 'bad-tournament' );
$html .= "</span>";
$html .= '<span class="pl_loss">';
$html .= __( 'L', 'bad-tournament' );
$html .= "</span>";
$html .= '<span class="pl_points_major">';
$html .= __( 'Points', 'bad-tournament' );
$html .= "</span>";
$html .= '<span class="pl_sets">';
$html .= __( 'Sets', 'bad-tournament' );
$html .= "</span>";
$html .= '<span class="pl_points">';
$html .= __( 'Total Points', 'bad-tournament' );
$html .= "</span>";
$html .= '</li>';

if( !is_numeric($table_view) || $table_view == 0 ){
    $table_view = 10000;
}
$nb_row = 0;
foreach( $players as $k => $player ){
    if( $player->status != 2 ) {

        $nb_row++;
        if ($nb_row > $table_view) {
            break;
        }

        $html .= '<li class="table_row"><span class="pl_rang">' . $nb_row . '.</span>';
        $html .= '<span class="pl_name">';
        $html .= $player->player_firstname . ' ' . $player->player_lastname;
        $html .= "</span>";

        $html .= '<span class="pl_played">';
        $html .= $player->played;
        $html .= "</span>";

        $html .= '<span class="pl_victory">';
        $html .= $player->victory;
        $html .= "</span>";

        $html .= '<span class="pl_draw">';
        $html .= $player->draw;
        $html .= "</span>";

        $html .= '<span class="pl_loss">';
        $html .= $player->loss;
        $html .= "</span>";

        $html .= '<span class="pl_points_major">';
        $html .= $player->points_major;
        $html .= "</span>";

        $html .= '<span class="pl_sets">';
        $html .= $player->sets;
        $html .= ' - ';
        $html .= $player->sets_against;
        $html .= "</span>";

        $html .= '<span class="pl_points">';
        $html .= $player->points;
        $html .= ' - ';
        $html .= $player->points_against;
        $html .= "</span>";
        $html .= '</li>';
    }
}
$html .= '</ul>';


$html .= '</form>';
$html .= '</div>';

