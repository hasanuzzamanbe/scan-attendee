class GameOverScreen {
    constructor(onRestart,score) {
        clear()
        this.score = score
        this.onRestart = onRestart
        this.onSubmit = false;
    }

   draw (){
       fill(255);
       textAlign(CENTER);
       textSize(25);
       text("Game Over", (canvasWidth/2), canvasHeight / 2.3);
       text(this.score, (canvasWidth/2), (canvasHeight / 2.3) + 25);

       $('#game-end-button-container').empty()
       let restartButton = createButton('Restart');
       restartButton.parent('game-end-button-container')
       restartButton.mousePressed(()=>{
           if(this.onSubmit) return
           this.restartGame()
       });


       let submitButton = createButton('Submit');
       submitButton.parent('game-end-button-container')
       submitButton.mousePressed(()=>{
           if(this.onSubmit) return

           this.onSubmit = true;
           let email = localStorage.getItem("game-email");
           let id = localStorage.getItem("game-attendee-id");
            let ref = this;
           jQuery.post( window.scanAttendeeGameAjaxUrl,
               {
                   action:'add_game_score',
                   email: email,
                   score: this.score,
                   attendee_id: id
               },
               function( resp ) {
                   ref.onSubmit = false;
                   if(resp.success){
                        let data =  resp.data

                       Swal.fire({
                           title: 'Success!',
                           text: data.message,
                           icon: 'success',
                       })
                       //window.location.reload();
                   }else{
                       Swal.fire({
                           title: 'Error!',
                           text: 'Something went wrong',
                           icon: 'error',
                       })
                   }

           });


       });
    }

    restartGame (){
        $('#game-end-button-container').empty()
        if(typeof this.onRestart != "undefined" && this.onRestart != null){
            this.onRestart()
        }
    }
}