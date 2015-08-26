<div class="container body-container">
    <div class="row">
        <table class="table table-centered table-striped">
            <thead>
                <tr>
                    <th>Game ID</th>
                    <th>First player name</th>
                    <th>Second player name</th>
                    <th>Victor</th>
                    <th>Date played</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($history) === 0) {
                    echo '<tr><td colspan="5">No previous game data exists!</td></tr>';
                } else {
                    foreach ($history as $hist_row) {
                        if ($hist_row['player_won'] == 1) {
                            $who_won = $hist_row['first_player_name'];
                        } else if ($hist_row['player_won'] == 0) {
                            $who_won = $hist_row['second_player_name'];                            
                        } else {
                            $who_won = 'No winner';
                        }
                        $date_temp = explode(' ', $hist_row['game_date']);
                        $date_pieces = explode('-', $date_temp[0]);
                        
                        echo '<tr>';
                        echo '<th>' . $hist_row['game_id'] . '</th>';
                        echo '<td>' . $hist_row['first_player_name'] . '</td>';
                        echo '<td>' . $hist_row['second_player_name'] . '</td>';
                        echo '<td>' . $who_won . '</td>';
                        echo '<td>' . $date_pieces[2] . '-' . $date_pieces[1] . ' ' . $date_pieces[0] . ', ' . $date_temp[1] . '</td>';
                        echo '</tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>