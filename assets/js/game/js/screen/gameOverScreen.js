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
       submitButton.mousePressed(async () => {
           if (this.onSubmit) return

           this.onSubmit = true;
           let email = localStorage.getItem("game-email");
           let id = localStorage.getItem("game-attendee-id");
           let name = localStorage.getItem("game-attendee-name");
           let ref = this;

           const score = await this.encryptValue(this.score, 'AuthLabAuthLab12')
           jQuery.post(window.scanAttendeeGameAjaxUrl,
               {
                   action: 'add_game_score',
                   email: email,
                   score: score,
                   attendee_id: id,
                   name:name
               },
               function (resp) {
                   ref.onSubmit = false;
                   if (resp.success) {
                       let data = resp.data
                       Swal.fire({
                           title: 'Success',
                           text: data.message,
                           icon: 'success',
                           showDenyButton: true,
                           showCancelButton: false,
                           confirmButtonText: 'Finish',
                           denyButtonText: `Reply`,
                       }).then((result) => {
                           /* Read more about isConfirmed, isDenied below */
                           if (result.isConfirmed) {
                               window.location.reload();
                           } else if (result.isDenied) {
                               ref.restartGame()
                           }
                       })

                   } else {
                       Swal.fire({
                           title: 'Error!',
                           text: 'Something went wrong',
                           icon: 'error',
                       })
                   }

               });


       });
    }

    async encryptValue(value, secretKey) {
        // Convert the value and secret key to ArrayBuffer objects
        const encoder = new TextEncoder();
        const valueData = encoder.encode(value);
        const keyData = encoder.encode(secretKey);

        // Import the secret key
        const cryptoKey = await crypto.subtle.importKey('raw', keyData, 'AES-CBC', false, ['encrypt']);

        // Generate an initialization vector (IV)
        const iv = crypto.getRandomValues(new Uint8Array(16));

        // Encrypt the value using AES-CBC algorithm with the secret key and IV
        const encryptedData = await crypto.subtle.encrypt({ name: 'AES-CBC', iv }, cryptoKey, valueData);

        // Concatenate the IV and encrypted data
        const encryptedBytes = new Uint8Array(iv.byteLength + encryptedData.byteLength);
        encryptedBytes.set(iv, 0);
        encryptedBytes.set(new Uint8Array(encryptedData), iv.byteLength);

        // Convert the encrypted value to a hex-encoded string
        return Array.prototype.map.call(encryptedBytes, byte => ('00' + byte.toString(16)).slice(-2)).join('');
    }

    restartGame (){
        $('#game-end-button-container').empty()
        if(typeof this.onRestart != "undefined" && this.onRestart != null){
            this.onRestart()
        }
    }
}