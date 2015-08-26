<?php

/**
 * Function to add a record in the history table. The function receives a PDO object,
 * creates the insert query via prepared statements, to ensure data validity, and 
 * registers it in the database.
 * @param type $pdo     PDO object to use for the insertion
 * @param type $data    Data array containing the information to be entered in the 
 *                      database
 * @return boolean      For error checking purposes, return true on success and 
 *                      false on error
 */
function store_game_in_db($pdo, $data) {
    if (!isset($data['first_player_name']) || !isset($data['second_player_name']) || 
            !isset($data['player_won']) || !isset($data['game_date'])) {
        return false;
    }
    try {
        $stmt = $pdo->prepare('insert into history (first_player_name, second_player_name, player_won, game_date) '
                . 'values (:first_player_name, :second_player_name, :player_won, :game_date)');
        $stmt->bindValue(":first_player_name", $data['first_player_name']);
        $stmt->bindValue(":second_player_name", $data['second_player_name']);
        $stmt->bindValue(":player_won", $data['player_won']);
        $stmt->bindValue(":game_date", $data['game_date']);

        $stmt->execute();
    } catch (PDOException $e) {
        return false;
    }
    return true;
}

?>