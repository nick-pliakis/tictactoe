<?php

/**
 * This script is called when the player plays a game against another human player.
 * The game receives the grid array and the last clicked gridbox. It checks the row
 * and column sums, and if and only if any row or column totals 3, then the first
 * player is victorious (since that would require every cell to have the value 1,
 * meaning that it would only contain crosses). If and only if any row or column
 * totals -3, then the second player is victorious (by the same logic, every cell
 * will have the value -1). Only the row and column on which the last clicked cell
 * belongs to is checked, to avoid unnecessary computations.
 * 
 * The main and secondary diagonals are also checked (only if the last clicked box 
 * is on either of them), also by the same logic.
 * 
 * We opted for a longer code, which checks every condition by itself, instead of 
 * checking for all victory conditions at the same time. By sacrificing some elegance,
 * we can now prune the states that don't interest us, since it would be useless to 
 * check, i.e., the columns and diagonals if the current row creates a victory 
 * condition. By providing exit clauses on each check, we effectively reduce the 
 * computations necessary.
 */

//Require the custom database connector and the AJAX functions
require_once 'custom_db_connect.php';
require_once 'ajax_functions.php';

//Register all the posted variables to avoid using the $_POST superglobal
$grid_array = $_POST["gridArray"];
$last_x_coord = $_POST["x_coord"];
$last_y_coord = $_POST["y_coord"];
$ply_one_name = $_POST["ply_one_name"];
$ply_two_name = $_POST["ply_two_name"];
$moves_number = $_POST["moves_number"];

//Initialize the grid sums
$grid_sum_row = 0;
$grid_sum_col = 0;
$grid_sum_diag_main = 0;
$grid_sum_diag_sec = 0;

//Add the values on the same row as the last clicked box
for ($i = 0; $i < 3; $i++) {
    $grid_sum_row += $grid_array[$last_x_coord][$i];
}
if ($grid_sum_row === 3) {
    //The first player has won, store the game in the database and return the state 
    //to the main application.
    $game_data = array(
        "first_player_name" => $ply_one_name,
        "second_player_name" => $ply_two_name,
        "player_won" => 1,
        "game_date" => date("Y-m-d H:i:s")
    );
    store_game_in_db($pdo, $game_data);
    echo 1; 
    exit;
} else if ($grid_sum_row === -3) {
    //The second player has won, store the game in the database and return the state 
    //to the main application.
    $game_data = array(
        "first_player_name" => $ply_one_name,
        "second_player_name" => $ply_two_name,
        "player_won" => 0,
        "game_date" => date("Y-m-d H:i:s")
    );
    store_game_in_db($pdo, $game_data);
    echo 0; 
    exit;
}
//Add the values on the same column as the last clicked box
for ($i = 0; $i < 3; $i++) {
    $grid_sum_col += $grid_array[$i][$last_y_coord];
}
if ($grid_sum_col === 3) {
    //The first player has won, store the game in the database and return the state 
    //to the main application.
    $game_data = array(
        "first_player_name" => $ply_one_name,
        "second_player_name" => $ply_two_name,
        "player_won" => 1,
        "game_date" => date("Y-m-d H:i:s")
    );
    store_game_in_db($pdo, $game_data);
    echo 1; 
    exit;
} else if ($grid_sum_col === -3) {
    //The second player has won, store the game in the database and return the state 
    //to the main application.
    $game_data = array(
        "first_player_name" => $ply_one_name,
        "second_player_name" => $ply_two_name,
        "player_won" => 0,
        "game_date" => date("Y-m-d H:i:s")
    );
    store_game_in_db($pdo, $game_data);
    echo 0; 
    exit;
}
//Add the values on the main and inverse matrix diagonal (only if the clicked 
//box belongs to either)
if (abs($last_x_coord - $last_y_coord) == 2 || $last_x_coord == $last_y_coord) {
    for ($i = 0; $i < 3; $i++) {
        $grid_sum_diag_main += $grid_array[$i][$i];
        $grid_sum_diag_sec += $grid_array[$i][2 - $i];
    }
    if ($grid_sum_diag_main === 3 || $grid_sum_diag_sec === 3) {
        //The first player has won, store the game in the database and return the state 
        //to the main application.
        $game_data = array(
            "first_player_name" => $ply_one_name,
            "second_player_name" => $ply_two_name,
            "player_won" => 1,
            "game_date" => date("Y-m-d H:i:s")
        );
        store_game_in_db($pdo, $game_data);
        echo 1; 
        exit;
    } else if ($grid_sum_diag_main === -3 || $grid_sum_diag_sec === -3) {
        //The second player has won, store the game in the database and return the state 
        //to the main application.
        $game_data = array(
            "first_player_name" => $ply_one_name,
            "second_player_name" => $ply_two_name,
            "player_won" => 0,
            "game_date" => date("Y-m-d H:i:s")
        );
        store_game_in_db($pdo, $game_data);
        echo 0; 
        exit;
    }
}
//If none of the above conditions has happened and the move count is 9, then no victor.
//The grid is full and no additional moves can be performed
if ($moves_number > 8) {
    $game_data = array(
        "first_player_name" => $ply_one_name,
        "second_player_name" => $ply_two_name,
        "player_won" => -1,
        "game_date" => date("Y-m-d H:i:s")
    );
    store_game_in_db($pdo, $game_data);
    echo -1;
    exit;
}

?>