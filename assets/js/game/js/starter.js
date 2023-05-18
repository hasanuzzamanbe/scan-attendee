


let gameScreen = null;
let startScreen = null;
let gameOverScreen = null;
let bg = null;
let score = 0

function setup() {
    canvasWidth = window.screen.width > 400 ? 400 : window.screen.width;
    canvasHeight = canvasWidth * 1.77;

    this.canvas = createCanvas(canvasWidth, canvasHeight);
    this.canvas.parent('game-container');
    canvasPosition = this.canvas.elt.getBoundingClientRect();



    //load Product Logos
    for (let i = 1; i < 8; i++) {
        productImages.push(loadImage(window.scanAttendeeGameUrl+'/assets/js/game/images/product/' + i + '.png'))
    }
    //Load cart animation
    cartSpriteSheet = loadImage(window.scanAttendeeGameUrl+'/assets/js/game/sprite/cart.png');
    cartSpriteData = loadJSON(window.scanAttendeeGameUrl+'/assets/js/game/sprite/cart.json');

    bombSpriteSheet = loadImage(window.scanAttendeeGameUrl+'/assets/js/game/sprite/bomb.png');
    bombSpriteData = loadJSON(window.scanAttendeeGameUrl+'/assets/js/game/sprite/bomb.json');


    bg = loadImage(window.scanAttendeeGameUrl+'/assets/js/game/images/test.png');

}

function draw() {
    if(isEmailSubmitted){
        background(bg)
    }
    
    
    if (gameState === 'initial') {
        if (startScreen === null) {
            startScreen = new StartScreen();

        }
        startScreen.draw();
    } else if (gameState === 'started') {
        if (gameScreen === null) {
           gameScreen = new GameScreen(onGameOver);
        }
        gameScreen.draw();
    }else if(gameState === 'ended'){
        console.log(score)
        if (gameOverScreen === null) {
            gameOverScreen = new GameOverScreen(restartGame,score);
        }
        gameOverScreen.draw();

    }

}

function windowResized() {
    if (windowHeight < canvasHeight) {
        canvasHeight = windowHeight;
        canvasWidth = canvasHeight / 1.77;
    } else if (windowWidth < canvasWidth) {
        canvasWidth = windowWidth;
        canvasHeight = canvasWidth * 1.77;
    } else {
        canvasWidth = windowWidth;
        canvasHeight = canvasWidth * 1.77;
    }

    if (windowHeight < canvasHeight) {
        canvasHeight = windowHeight;
        canvasWidth = canvasHeight / 1.77;
    }

    if(canvasWidth> 400){
        canvasWidth = 400
        canvasHeight = canvasWidth * 1.77;
    }
    resizeCanvas(canvasWidth, canvasHeight);
    canvasPosition = this.canvas.elt.getBoundingClientRect();
}

function restartGame(){
    score = 0;
    isPlaying = true;
    isGameStarted =true;
    gameState = 'started'
    gameOverScreen = null
    loop();
    resetScreen();
}
function onGameOver(newScore){

    score = newScore
    isGameOver = true
    isGameStarted = false
    isPlaying = false
    gameState = 'ended'
    gameScreen = null
    noLoop()
}


function mousePressed() {

    if(!isClickedInsideRect()) return;

    if(gameState === 'ended')
        return;

    if (gameState === 'initial') {
        this.restartGame()
    }else if(gameState === 'started' && gameScreen!= null){
        gameScreen.handelTap()
    }
}

function isClickedInsideRect(){
    let insideXAxis = false
    let insideYAxis = false
    if(mouseX >=0 &&  mouseX <= canvasPosition.width ){
        insideXAxis = true
    }
    if(mouseY >=0 &&  mouseY <= canvasPosition.height  ){
        insideYAxis = true
    }
    return (insideXAxis && insideYAxis && isEmailSubmitted)
}

function resetScreen() {
    gameScreen = null;
    startScreen = null;
    gameOverScreen = null
}