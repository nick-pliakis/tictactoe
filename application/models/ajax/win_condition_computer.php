<?php
/*
 * This script is called when the player plays a game against the computer.
 * The grid values are passed as a 2d matrix, along with the coordinates of the 
 * last clicked box. Initially, a check is performed to see if the user's move 
 * has created win conditions. As per our implementation, if and only if a row's
 * or column's element values add up to 3, the human player has won (because this 
 * happens only if a row's elements all have values of 1). We don't need to check 
 * every row or column, only the row/column which includes the last clicked element.
 * Any other rows or columns will have been checked on previous calls of the script.
 * We also check the main and secondary diagonals on the same clause, since they can
 * also include a victory condition.
 * 
 * If no victory condition has been discovered, we move on to the computer selecting
 * a move. We have implemented a random selection for the computer, due to time 
 * constraints, but a smarter variant could be considered if more time was available. 
 * The computer chooses an empty cell to place its symbol by checking 
 * the grid sequentially on a random direction. We avoided a random generation of 
 * numbers and subsequent checkings, because they could conceivably make the decision
 * process hang up while trying to determine an empty position and failing.
 * 
 * After selecting a position, the computer checks if its choice leads to a victory 
 * condition. If it does, it returns the relevant signals back to the application
 * and the game ends. Otherwise, control is returned to the player and the game
 * resumes.
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
$moves_number = intval($_POST["moves_number"]);

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
    //The player has won, store the game in the database and return the state 
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
}
//Add the values on the same column as the last clicked box
for ($i = 0; $i < 3; $i++) {
    $grid_sum_col += $grid_array[$i][$last_y_coord];
}
if ($grid_sum_col === 3) {
    //The player has won, store the game in the database and return the state 
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
}
//Add the values on the main and inverse matrix diagonal (only if the clicked 
//box belongs to either)
if (abs($last_x_coord - $last_y_coord) == 2 || $last_x_coord == $last_y_coord) {
    for ($i = 0; $i < 3; $i++) {
        $grid_sum_diag_main += $grid_array[$i][$i];
        $grid_sum_diag_sec += $grid_array[$i][2 - $i];
    }
    if ($grid_sum_diag_main === 3 || $grid_sum_diag_sec === 3) {
        //The player has won, either by completing the main or the secondary diagonal, 
        //store the game in the database and return the state to the main application.
        $game_data = array(
            "first_player_name" => $ply_one_name,
            "second_player_name" => $ply_two_name,
            "player_won" => 1,
            "game_date" => date("Y-m-d H:i:s")
        );
        store_game_in_db($pdo, $game_data);
        echo 1; 
        exit;
    }
}

//Select a traversal method at random
$row_or_col = rand(0, 3);
$found_cell = false;
if ($row_or_col === 0) {
    //Check matrix row by row
    for ($i = 0; $i < 3; $i++) {
        for ($j = 0; $j < 3; $j++) {
            if ($grid_array[$i][$j] == 0) {
                $grid_array[$i][$j] = -1;
                $last_x_coord = $i;
                $last_y_coord = $j;
                $found_cell = true;
                break;
            }
        }
        if ($found_cell) {
            break;
        }
    }
} else if ($row_or_col === 1) {
    //Check matrix column by column
    for ($j = 0; $j < 3; $j++) {
        for ($i = 0; $i < 3; $i++) {
            if ($grid_array[$i][$j] == 0) {
                $grid_array[$i][$j] = -1;
                $last_x_coord = $i;
                $last_y_coord = $j;
                $found_cell = true;
                break;
            }
        }
        if ($found_cell) {
            break;
        }
    }
} else if ($row_or_col === 2) {
    //Check matrix row by row, in reverse
    for ($i = 2; $i >= 0; $i--) {
        for ($j = 2; $j >= 0; $j--) {
            if ($grid_array[$i][$j] == 0) {
                $grid_array[$i][$j] = -1;
                $last_x_coord = $i;
                $last_y_coord = $j;
                $found_cell = true;
                break;
            }
        }
        if ($found_cell) {
            break;
        }
    }
} else if ($row_or_col === 3) {
    //Check matrix column by column, in reverse
    for ($j = 2; $j >= 0; $j--) {
        for ($i = 2; $i >= 0; $i--) {
            if ($grid_array[$i][$j] == 0) {
                $grid_array[$i][$j] = -1;
                $last_x_coord = $i;
                $last_y_coord = $j;
                $found_cell = true;
                break;
            }
        }
        if ($found_cell) {
            break;
        }
    }
}

$game_state_array = array($last_x_coord, $last_y_coord);

$grid_sum_row = 0;
$grid_sum_col = 0;
$grid_sum_diag_main = 0;
$grid_sum_diag_sec = 0;
//Check if the computer's move creates victory conditions, exactly as before
//Only difference is that, instead of 1, when the computer wins, it returns 0
for ($i = 0; $i < 3; $i++) {
    $grid_sum_row += $grid_array[$last_x_coord][$i];
}
if ($grid_sum_row === -3) {
    $game_data = array(
        "first_player_name" => $ply_one_name,
        "second_player_name" => $ply_two_name,
        "player_won" => 1,
        "game_date" => date("Y-m-d H:i:s")
    );
    //If the game ended, store the game in the database and return the state to the 
    //main application
    store_game_in_db($pdo, $game_data);
    array_push($game_state_array, 0);
    echo json_encode($game_state_array); 
    exit;
}
//Column
for ($i = 0; $i < 3; $i++) {
    $grid_sum_col += $grid_array[$i][$last_y_coord];
}
if ($grid_sum_col === -3) {
    $game_data = array(
        "first_player_name" => $ply_one_name,
        "second_player_name" => $ply_two_name,
        "player_won" => 1,
        "game_date" => date("Y-m-d H:i:s")
    );
    //If the game ended, store the game in the database and return the state to the 
    //main application
    store_game_in_db($pdo, $game_data);
    array_push($game_state_array, 0);
    echo json_encode($game_state_array);
    exit;
}
//Diagonals (only if the last click was done there)
if (abs($last_x_coord - $last_y_coord) == 2 || $last_x_coord == $last_y_coord) {
    for ($i = 0; $i < 3; $i++) {
        $grid_sum_diag_main += $grid_array[$i][$i];
        $grid_sum_diag_sec += $grid_array[$i][2 - $i];
    }
    if ($grid_sum_diag_main === -3 || $grid_sum_diag_sec === -3) {
        $game_data = array(
            "first_player_name" => $ply_one_name,
            "second_player_name" => $ply_two_name,
            "player_won" => 1,
            "game_date" => date("Y-m-d H:i:s")
        );
        //If the game ended, store the game in the database and return the state to the 
        //main application
        store_game_in_db($pdo, $game_data);
        array_push($game_state_array, 0);
        echo json_encode($game_state_array);
        exit;
    }
}

//If none of the above conditions has happened and the move count is 9, then no victor
if ($moves_number > 4) {
    $game_data = array(
        "first_player_name" => $ply_one_name,
        "second_player_name" => $ply_two_name,
        "player_won" => -1,
        "game_date" => date("Y-m-d H:i:s")
    );
    //If the game ended, store the game in the database and return the state to the 
    //main application
    store_game_in_db($pdo, $game_data);
    array_push($game_state_array, -1);
    echo json_encode($game_state_array);
    exit;
}
//Return the game state to the main application in JSON form
echo json_encode($game_state_array);

?>