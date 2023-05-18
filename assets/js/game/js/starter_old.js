let gameScreen = null;
let startScreen = null;
let gameOverScreen = null;
let bg = null;

function setup() {

    canvasWidth = window.screen.width > 400 ? 400 : window.screen.width;
    canvasHeight = canvasWidth * 1.77;

    this.canvas = createCanvas(canvasWidth, canvasHeight);
    this.canvas.parent('game-container');

    canvasPosition = this.canvas.elt.getBoundingClientRect();



    //load Product Logos
    for (let i = 1; i < 8; i++) {
        productImages.push(loadImage('images/product/' + i + '.png'))
    }
    //Load cart animation
    cartSpriteSheet = loadImage('sprite/cart.png');
    cartSpriteData = loadJSON('sprite/cart.json');
    //initialy we don't need loop

    bg = loadImage('images/bg-mobile.jpg');

    bombSpriteSheet = loadImage('sprite/bomb.png');
    bombSpriteData = loadJSON('sprite/bomb.json');

}

function draw() {
    background(bg);
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
        if (gameOverScreen === null) {
            gameOverScreen = new GameOverScreen(restartGame);
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
    isPlaying = true;
    isGameStarted =true;
    gameState = 'started'
    loop();
    resetScreen();
}
function onGameOver(){
    isGameOver = true
    isGameStarted = false
    isPlaying = false
    gameState = 'ended'
    gameScreen = null

}


function mousePressed() {

    if(!isClickedInsideRect()) return;

    if(gameState === 'ended')
        return;

    if (gameState === 'initial') {
        isPlaying = true;
        isGameStarted =true;
        gameState = 'started'
        //enable loop as our game started
        loop();
        resetScreen();
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
    return (insideXAxis && insideYAxis)
}

function resetScreen() {
    gameScreen = null;
    startScreen = null;
    gameOverScreen = null
}