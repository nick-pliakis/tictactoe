/** Initialising the global variables we need.
 *  currentPlayer: The player who is about to make a move, switches after every move
 *  gridArray: a 2d matrix containing the values of the cells, according to the moves made up to now
 *             The gridArray is initialized to 0 when the game starts
 *  clicksPerformed: number of valid clicks/moves up until now
*/
var currentPlayer = 0, gridArray = new Array(3), clicksPerformed = 0;
gridArray[0] = new Array(3), gridArray[1] = new Array(3), gridArray[2] = new Array(3);
for (var i = 0; i < 3; i++) {
    for (var j = 0; j < 3; j++) {
        gridArray[i][j] = 0;
    }
}
$(".play-box").on("click", function () {
    //Clicking on the box gives us its coordinates, so we can find where the symbol must be placed
    var clickCoords = getCellValue($(this)), against_human = $("#against_human").val(), 
            ply_one_name = $("#ply_one_name").val(), ply_two_name = $("#ply_two_name").val();
    //The action is performed only if the box is empty
    if (gridArray[clickCoords.x_coord][clickCoords.y_coord] === 0) {
        //Add another move to the counter
        clicksPerformed++;
        //Every player adds his own symbol to the box he clicks, with the cross player modifying 
        //the relevant grid value in gridArray to 1, while the circle player modifies the relevant 
        //grid value to -1.
        if (currentPlayer === 0) {
            $(this).addClass("crossed");
            gridArray[clickCoords.x_coord][clickCoords.y_coord] = 1;
        } else {
            $(this).addClass("circled");
            gridArray[clickCoords.x_coord][clickCoords.y_coord] = -1;
        }
        //Remove the pulsate effect
        $(this).stop(true, true);
        $(this).css({opacity: '1'});
        //If it's human vs human, switch players
        if (against_human == 1) {
            toggleCurrentPlayer();
        }
        //Call the relevant function to determine if the game needs to end
        determineWinCondition(clickCoords.x_coord, clickCoords.y_coord, ply_one_name, ply_two_name, 
                                    against_human, clicksPerformed);
    }
});

$(".play-box").hover(function () {
    //Hovering over the box creates a pulsating symbol effect, to show where the symbol will be inserted
    var clickCoords = getCellValue($(this));
    //Only create the effect if the box is empty
    if (gridArray[clickCoords.x_coord][clickCoords.y_coord] === 0) {
        //Create relevant effect depending on the active player
        if (currentPlayer === 0) {
            $(this).addClass("crossed");
        } else {
            $(this).addClass("circled");
        }
        //Call the effect function
        pulsate($(this));
    }
},
function () {
    //After the mouse leaving the area, the box is returned to its normal state
    var clickCoords = getCellValue($(this));
    if (gridArray[clickCoords.x_coord][clickCoords.y_coord] === 0) {
        $(this).removeClass("crossed");
        $(this).removeClass("circled");
    }
    //The effect is removed
    $(this).stop(true, true);
    $(this).css({opacity: '1'});
});

function toggleCurrentPlayer() {
    //Toggle player number from 0/1 to 1/0
    currentPlayer = Math.abs((currentPlayer - 1));
}

function pulsate(element) {
    //Create a pulsating effect by animating the box opacity to 0.3 and back to 1
    //After the end of the function, utilize a callback to call the function again
    //This creates a pulsating effect while the player hovers above the box
    element.animate({opacity: "0.3"}, 500);
    element.animate({opacity: '1'}, 500, function () {
        pulsate(element)
    });
}

function getCellValue(element) {
    //Get the element's coordinates in (x, y) format
    return new Object({
        x_coord: element.find("input[name=x_coord]").val(),
        y_coord: element.find("input[name=y_coord]").val()
    });
}

function determineWinCondition(x_coord, y_coord, ply_one_name, ply_two_name, against_human, moves_number) {
    //Perform an AJAX call to determine if the last move created victory conditions
    var script_append;
    //Call a different script depending on if the player is playing against a human or the computer
    if (against_human == 1) {
        script_append = "human";
    } else {
        script_append = "computer";
    } 
    //AJAX call
    $.ajax({
        type: "post",
        url: "../application/models/ajax/win_condition_" + script_append + ".php",
        data: {
            gridArray: gridArray,
            x_coord: x_coord,
            y_coord: y_coord,
            ply_one_name: ply_one_name,
            ply_two_name: ply_two_name,
            moves_number: moves_number
        }
    }).done(function(data) {
        if (against_human == 1) {
            //If playing against a human, we only need the number showing who, if any, won the game
            //The Endgame Screen is a modal window stating who the victor is
            if (parseInt(data) === 1) {
                showEndgameScreen(true, 1);
            } else if (parseInt(data) === 0) {
                showEndgameScreen(true, 0);                
            } else if (parseInt(data) === -1) {
                showEndgameScreen(false, -1);
            }
        } else {
            //If playing against the computer, the script checks for victory conditions, then performs
            //the computer's move, and then rechecks
            if (parseInt(data) === 1) {
                //The human has won (only a number is returned in this case)!
                showEndgameScreen(true, 1);
            } else {
                //Parse the result as JSON array
                var box = $.parseJSON(data);
                //Show the computer's symbol on the grid
                $("input[name=x_coord][value=" + box[0] + "]").siblings("input[name=y_coord][value=" + box[1] + "]").parent().addClass("circled");
                //If there is a victory condition
                if (typeof box[2] !== 'undefined') {
                    if (box[2] === 0) {
                        //The computer won!
                        showEndgameScreen(true, 0);
                    } else {
                        //Nobody won...
                        showEndgameScreen(false, -1);
                    }
                } else {
                    //If no victory conditions have been met, insert the computer's move to the gridArray (computer is now
                    //considered the player 2)
                    gridArray[box[0]][box[1]] = -1;
                }  
            }
        }
    }).fail(function(err) {
        console.log(err);
    });
}

function showEndgameScreen(someoneWon, whoWon) {
    //Show the endgame screen
    if (someoneWon) {
        //If someone won remove all events from the boxes (no more moves allowed)
        $(".play-box").off("click");
        $(".play-box").off("mouseenter");
        $(".play-box").off("mouseleave");
        //Display the winner's name
        if (whoWon == 1) {
            $("#player_two_won").hide();
        } else {
            $("#player_one_won").hide();            
        }
        //And show the modal
        $('#victory_modal').modal({backdrop:'static', keyboard:false});
    } else {
        //Nobody won, show the defeat modal...
        $('#defeat_modal').modal({backdrop:'static', keyboard:false});        
    }
}