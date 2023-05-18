$ = jQuery
$(()=>{
    $('#mail-submit-button').click(()=>{

        let email = $('#mail-input').val()
        let emailValid = validateEmail(email)

        let id = $('#attendee-id-input').val()
        let isIdValid = typeof id !== 'undefined' && id != null && !isNaN(id) && id.length>0 && id.length<=4;

        let name = $('#attendee-name-input').val()
        let isNameValid = typeof name !== 'undefined' && name != null  && id.length>0 && id.length<=100;


        if(emailValid && isIdValid && isIdValid){
            isEmailSubmitted = true
            $('#game-container').addClass('allowed')
            $('#mail-validation').text('')
            $('#attendee-id-validation').text('')
            $('#attendee-name-validation').text('')
            localStorage.setItem("game-email", email);
            localStorage.setItem("game-attendee-id", id);
            localStorage.setItem("game-attendee-name", name);
        }



        if(!emailValid){
            $('#mail-validation').text('Your email is not valid')
        }else{
            $('#mail-validation').text('')
        }

        if(!isIdValid){
            $('#attendee-id-validation').text('Your Id is not valid')
        }
        else{
            $('#attendee-id-validation').text('')
        }

        if(!isNameValid){
            $('#attendee-name-validation').text('Your Name is not valid')
        }
        else{
            $('#attendee-name-validation').text('')
        }





    })

    const validateEmail = (email) => {
        return String(email)
            .toLowerCase()
            .match(
                /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            );
    };
})