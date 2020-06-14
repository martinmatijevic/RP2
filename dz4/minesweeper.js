$(document).ready(function(){
    nRows = 9;
    nCols = 9;
    nMines = 10;
    id = 0;
    movesCounter = 0;
    $("#gameStart").on("click", initialize);
    $("#canvas").on("contextmenu", function() { return false; });
    $("#canvas").mousedown(function(event){
        if(event.which == 1){
            button="left";
            check(event);
            movesCounter++;
        }
        if(event.which == 3){
            button="right";
            check(event);
            movesCounter++;
        }
    });
});

function initialize(){
    var tmpnRows = $("#nRows").val();
    var tmpnCols = $("#nCols").val();
    var tmpnMines = parseInt($("#nMines").val());
    var maxMines = tmpnRows*tmpnCols;
    if(tmpnRows<1||tmpnCols<1||tmpnMines<0||tmpnRows>20||tmpnCols>20||tmpnMines>maxMines){
        $("#error").html("Nedopušteni parametri!");
        gameEnded = true;
        return;
    }
    else {
        nRows = tmpnRows;
        nCols = tmpnCols;
        nMines = tmpnMines;
        movesCounter = 0;
        w = 50*nCols;
        h = 50*nRows;
        gameEnded = false;
        questionMarkCounter = 0;
        nonHiddenTiles = 0;
        tile=[];
        $("#error").html("");
        $("#gameStatus1").html("");
        $("#gameStatus2").html("");
        var canvasContext = $("#canvas").get(0).getContext("2d");
        canvasContext.canvas.width = w;
        canvasContext.canvas.height = h;
    }
    for(var i = 0; i < nCols; i++)
        for(var j = 0; j < nRows; j++)
            tile.push({x:i,y:j, status:"hidden"})
    $.ajax({
        url:"https://rp2.studenti.math.hr/~zbujanov/dz4/id.php",
        type: "GET",
        data:{
            nRows:nRows,
            nCols:nCols,
            nMines:nMines
        },
        dataType: "json",
        success: function(data){
            if(typeof(data.error) !== "undefined")
                console.log("cekajPoruku :: success :: server javio grešku " + data.error);
            else{
                id = data.id;
                var canvasContext = $("#canvas").get(0).getContext("2d");
                for(var i = 0; i < nCols; ++i)
                    for(var j = 0; j < nRows; ++j)
                    {
                        canvasContext.fillStyle = "#36f";
                        canvasContext.fillRect(i*50, j*50, 50, 50);
                        canvasContext.strokeRect(i*50, j*50, 50, 50);
                    }
            }
        },
        error: function(xhr, status){
            console.log("initialize :: error :: status = " + status);
        }
    });
}

function check(event)
{
    if(gameEnded === true) return;
    if(nonHiddenTiles === (nRows*nCols-nMines) && questionMarkCounter === nMines) return;
    var canvas = $("#canvas").get(0);
    var canvasContext = canvas.getContext("2d");
    var rectangle = canvas.getBoundingClientRect();
    var x = event.clientX - rectangle.left;
    var y = event.clientY - rectangle.top;
    var row = Math.floor(y/50);
    var col = Math.floor(x/50);
    if(tile[row*nCols+col].status === "visible") return;
    $.ajax({
        url:"https://rp2.studenti.math.hr/~zbujanov/dz4/cell.php",
        type: "GET",
        data:{
            row:row,
            col:col,
            id:id
        },
        dataType: "json",
        success: function(data){
            if(typeof(data.error) !== "undefined")
                console.log("cekajPoruku :: success :: server javio grešku " + data.error);
            else{
                if (button==="left"){
                    hitMine = data.boom;
                    if(hitMine === true){
                        $("#gameStatus2").html("Izgubili ste.");
                        gameEnded = true;
                        return;
                    }
                    for(var i = 0; i < data.cells.length; i++){
                        canvasRow = data.cells[i].row;
                        canvasColumn = data.cells[i].col;
                        mineNumber = data.cells[i].mines;
                        if(tile[canvasRow*nCols+canvasColumn].status !== "visible") nonHiddenTiles++;
                        tile[canvasRow*nCols+canvasColumn].status = "visible";
                        if(hitMine === false){
                            canvasContext.fillStyle = "#ffffff";
                            canvasContext.fillRect(canvasColumn*50,canvasRow*50, 50, 50);
                            canvasContext.strokeRect(canvasColumn*50, canvasRow*50, 50, 50);
                            if(mineNumber !== 0){
                                canvasContext.font = "40px Comic Sans MS";
                                if(mineNumber === 1)
                                    canvasContext.fillStyle = "#0066ff";
                                else if(mineNumber === 2)
                                    canvasContext.fillStyle = "#009933";
                                else if(mineNumber === 3)
                                    canvasContext.fillStyle = "#ff3300";
                                else if(mineNumber === 4)
                                    canvasContext.fillStyle = "#0000cc";
                                else if(mineNumber === 5)
                                    canvasContext.fillStyle = "#800000";
                                else if(mineNumber === 6)
                                    canvasContext.fillStyle = "#33cccc";
                                else if(mineNumber === 7)
                                    canvasContext.fillStyle = "#000000";
                                else if(mineNumber === 8)
                                    canvasContext.fillStyle = "#808080";
                                canvasContext.fillText(mineNumber, canvasColumn*50 + 14, canvasRow*50 + 40);
                            }
                        }
                    }
                    gameStatus();
                };
                if (button === "right"){
                    mineGuessed = data.boom;
                    if(mineGuessed === true && tile[row*nCols+col].status === "flagged"){
                        questionMarkCounter--;
                        tile[row*nCols+col].status = "hidden";
                        canvasContext.fillStyle = "#3366ff";
                        canvasContext.fillRect(col*50,row*50, 50, 50);
                        canvasContext.strokeRect(col*50, row*50, 50, 50);
                        return;
                    }
                    else if(mineGuessed === false && tile[row*nCols+col].status === "flagged"){
                        questionMarkCounter++;
                        tile[row*nCols+col].status = "hidden";
                        canvasContext.fillStyle = "#3366ff";
                        canvasContext.fillRect(col*50,row*50, 50, 50);
                        canvasContext.strokeRect(col*50, row*50, 50, 50);
                        return;
                    }
                    else if(mineGuessed === true && tile[row*nCols+col].status === "hidden"){
                        questionMarkCounter++;
                        tile[row*nCols+col].status = "flagged";
                    }
                    else if(mineGuessed === false && tile[row*nCols+col].status === "hidden"){
                        questionMarkCounter--;
                        tile[row*nCols+col].status = "flagged";
                    }
                    canvasContext.font = "40px Comic Sans MS";
                    canvasContext.fillStyle = "#000000";
                    canvasContext.fillText("?", col*50 + 14, row*50 + 40);
                    gameStatus();
                };
            }
        },
        error: function(xhr, status){
            console.log("check :: error :: status = " + status);
        }
    });
}

function gameStatus(){
    if (gameEnded === true) return;
    if (nonHiddenTiles === (nRows*nCols-nMines) && questionMarkCounter===nMines){
        $("#gameStatus2").html("Pobjedili ste.");
        gameEnded=true;
    }
    else($("#gameStatus1").html("Broj poteza: " + movesCounter));
}
