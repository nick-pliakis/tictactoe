<?php

if (isset($_POST['human_or_com'])) {
    if ($_POST['human_or_com'] == 0) {
        $against_human = 1;
        $ply_one_name = isset($_POST['ply_one_name_hum']) && $_POST['ply_one_name_hum'] !== '' 
                ? $_POST['ply_one_name_hum'] : 'Anon_1';
        $ply_two_name = isset($_POST['ply_two_name_hum']) && $_POST['ply_two_name_hum'] !== '' 
                ? $_POST['ply_two_name_hum'] : 'Anon_2';
    } else if ($_POST['human_or_com'] == 1) {
        $against_human = 0;
        $ply_one_name = isset($_POST['ply_one_name_com']) && $_POST['ply_one_name_com'] !== '' 
                ? $_POST['ply_one_name_com'] : 'Anon_1';
        $ply_two_name = isset($_POST['ply_two_name_com']) && $_POST['ply_two_name_com'] !== '' 
                ? $_POST['ply_two_name_com'] : 'Computer';
    }
} else {
    $against_human = 1;
    $ply_one_name = 'Anon_1';
    $ply_two_name = 'Anon_2';
}
?>
<div class="container body-container">
    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="col-xs-4 col-md-4 play-box">
                    <input type="hidden" name="x_coord" value="0">
                    <input type="hidden" name="y_coord" value="0">
                </div>
                <div class="col-xs-4 col-md-4 play-box">
                    <input type="hidden" name="x_coord" value="0">
                    <input type="hidden" name="y_coord" value="1">
                </div>
                <div class="col-xs-4 col-md-4 play-box rightmost">
                    <input type="hidden" name="x_coord" value="0">
                    <input type="hidden" name="y_coord" value="2">
                </div>
            </div>
            <div class="row">
                <div class="col-xs-4 col-md-4 play-box">
                    <input type="hidden" name="x_coord" value="1">
                    <input type="hidden" name="y_coord" value="0">
                </div>
                <div class="col-xs-4 col-md-4 play-box">
                    <input type="hidden" name="x_coord" value="1">
                    <input type="hidden" name="y_coord" value="1">
                </div>
                <div class="col-xs-4 col-md-4 play-box rightmost">
                    <input type="hidden" name="x_coord" value="1">
                    <input type="hidden" name="y_coord" value="2">
                </div>
            </div>
            <div class="row">
                <div class="col-xs-4 col-md-4 play-box bottommost">
                    <input type="hidden" name="x_coord" value="2">
                    <input type="hidden" name="y_coord" value="0">
                </div>
                <div class="col-xs-4 col-md-4 play-box bottommost">
                    <input type="hidden" name="x_coord" value="2">
                    <input type="hidden" name="y_coord" value="1">
                </div>
                <div class="col-xs-4 col-md-4 play-box rightmost bottommost">
                    <input type="hidden" name="x_coord" value="2">
                    <input type="hidden" name="y_coord" value="2">
                </div>
            </div>
        </div>
        <div class="col-md-4 text-center hidden-xs">
            <h3>Game board</h3>
            <p class="text-center"><?= $ply_one_name . '<br/>VS<br/>' . $ply_two_name; ?></p>
            <hr/>
            <h3>Recent games</h3>
            <?php
            $counter = 0;
            if (count($latest_history) === 0) {
                echo '<p>No previous games available.</p><hr/>';
            } else {
                foreach ($latest_history as $latest_row) {
                    echo '<p><strong>' . ++$counter . '.</strong> ' . $latest_row['first_player_name'] . ' VS ' . $latest_row['second_player_name'] . '<br/>';
                    if ($latest_row['player_won'] == 1) {
                        echo $latest_row['first_player_name'] . ' won the game!<br/>';
                    } else {
                        echo $latest_row['second_player_name'] . ' won the game!<br/>';
                    }
                    $date_temp = explode(' ', $latest_row['game_date']);
                    $date_pieces = explode('-', $date_temp[0]);
                    echo 'The game took place on ' . $date_pieces[2] . '-' . $date_pieces[1] . '-' . $date_pieces[0];
                    echo '<hr/>';
                }
            }
            ?>
        </div>
    </div>
</div>
<input type="hidden" value="<?= $against_human; ?>" id="against_human">
<input type="hidden" value="<?= $ply_one_name; ?>" id="ply_one_name">
<input type="hidden" value="<?= $ply_two_name; ?>" id="ply_two_name">

<div class="modal fade" id="victory_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Victory!</h4>
            </div>
            <div class="modal-body">
                <div id="player_one_won"><?= $ply_one_name; ?> won!</div>
                <div id="player_two_won"><?= $ply_two_name; ?> won!</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <form method="post" action="play">
                    <button type="submit" class="btn btn-primary" name="reset_game">Reset game</button>                    
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="defeat_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Defeat...</h4>
            </div>
            <div class="modal-body">
                Unfortunately, no one won this match. You can reset the game and try your luck again!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <form method="post" action="play">
                    <button type="submit" class="btn btn-primary" name="reset_game">Reset game</button>                    
                </form>
            </div>
        </div>
    </div>
</div>