class StartScreen {

    constructor() {
        clear()
    }

   draw (){

        if(!isEmailSubmitted){
            return
        }

       fill(255);
       textAlign(CENTER);
       textSize(25);
       text("Welcome To AuthLab Game!", canvasWidth / 2, canvasWidth / 2.3);
       textSize(20);
       text("Collect our product in your cart.", canvasWidth / 2, canvasWidth / 2);
       text("Tap To Start", canvasWidth / 2, canvasWidth / 2 + 30);
    }
}