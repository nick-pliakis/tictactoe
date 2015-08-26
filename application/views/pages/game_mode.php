<div class="container body-container">
    <div class="row text-center">
        <p>Welcome to the game. Please select one of the two following options to begin playing!
    </div>
    <div class="row text-center">
        <div class="col-md-6 col-xs-12 text-center">
            <div class="col-md-12 col-xs-12 with-border-full">
                <h3>Play with another human!</h3>
                <form method="post" action="game">
                    <div class="form-group">
                        <label for="ply_one_name_hum">First player's name:</label>
                        <input type="text" class="form-control" id="ply_one_name_hum" 
                               name="ply_one_name_hum" placeholder="First player's name">
                    </div>    
                    <div class="form-group">
                        <label for="ply_two_name_hum">Second player's name:</label>
                        <input type="text" class="form-control" id="ply_two_name_hum" 
                               name="ply_two_name_hum" placeholder="Second player's name">
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="human_or_com" value="0">
                        <button type="submit" class="btn btn-success btn-block" 
                                id="human_button">Play!</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-6 col-xs-12 text-center">
            <div class="col-md-12 col-xs-12 with-border-full">
                <h3>Play against the computer!</h3>
                <form method="post" action="game">
                    <div class="form-group">
                        <label for="ply_one_name_com">Player's name:</label>
                        <input type="text" class="form-control" id="ply_one_name_com" 
                               name="ply_one_name_com" placeholder="Player's name">
                    </div>
                    <div class="form-group">
                        <label for="ply_two_name_com">The computer will be your opponent!</label>
                        <input type="text" class="form-control" id="ply_two_name_hum" 
                               name="ply_two_name_com" placeholder="Computer player's name (optional)">
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="human_or_com" value="1">
                        <button type="submit" class="btn btn-success btn-block" 
                                id="com_button">Play!</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>