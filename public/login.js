$('a[href*=\\#whoarewe]').on('click', function(event){
      event.preventDefault();
      $('html,body').animate({scrollTop:$(this.hash).offset().top}, 500);
});
   //document.getElementById("login").onclick=function(){
  function signIn(){
     var vemail=$("#email-log").val();
     var vpassword=$("#password-log").val();
     var vcheckbox=$("#checkbox-log").val();

     console.log(vemail);
     $.ajax({
       url: "/login", //nueva url
       
       headers: {'X-CSRF-TOKEN': $('[name="_token"]').val()},
       //header: {'X-CRSF-TOKEN': _token},
       type:"POST",
       datatype:"json",
       data: {

         email:vemail,
         password:vpassword,
         remember:vcheckbox

       },
       success: function(){
         console.log("Success");
         location.reload(true);
         //window.location.href = "https://absortio.herokuapp.com/home";
       },

       error: function(response) {
          //var responseText=response['responseText'];

           //var json = JSON.parse(response);

          //console.log(response);
          document.getElementById("badcredentials").innerHTML = "";
          document.getElementById("email-required").innerHTML = "";
          document.getElementById("password-required").innerHTML ="";

          console.log(response);
          var obj = JSON.parse(response['responseText']);




          var count = Object.keys(obj).length;

          if (count==1){
            if (obj['email']){
              if (obj['email']=="The email field is required.")
                document.getElementById("email-required").innerHTML = obj['email'];
              else {
                document.getElementById("badcredentials").innerHTML = obj['email'];
              }

            }
            else if(obj['password'])
              document.getElementById("password-required").innerHTML = obj['password'];

          }


          else if (count==2){
            document.getElementById("email-required").innerHTML = obj['email'];
            document.getElementById("password-required").innerHTML = obj['password'];
          }

       }
     });
   };

   function reset_password(){
     //console.log("Estoy dentro de la funcion reset");
     //$('#login-modal').modal('hide');
     //$('#reset-modal').modal('show');


     var vemail=$("#email").val();
    document.getElementById("reset-email-required").innerHTML ="";
     //console.log("Success");

     $.ajax({
       url: "/password/email", //nueva url
       //url: "http://absortio.herokuapp.com/password/email",
       headers: {'X-CSRF-TOKEN': $('[name="_token"]').val()},
       //header: {'X-CRSF-TOKEN': _token},
       type:"POST",
       datatype:"json",
       data: {

         email:vemail

       },
       success: function(){

         console.log("Good!");
         document.getElementById("email-sended").innerHTML = "<div class='alert alert-success'>The email was sent correctly.</div>";
         //location.reload(true);
         //window.location.href = "https://absortio.herokuapp.com/home";
       },

       error: function(response) {


          console.log("Error");
          var key=response['statusText'];
          //var obj = JSON.parse(response);
          //console.log(response['statusText']);
          //document.getElementById("reset-email-required").innerHTML = "The email field is required.";
          if (key=="Unprocessable Entity")
                document.getElementById("reset-email-required").innerHTML = "The email field is required.";
          else if (key=="Internal Server Error")
                document.getElementById("reset-email-required").innerHTML = "We can't find a user with that e-mail address.";

          }
       });

     }



     $(document).ready(function() {
       $('#login-modal').on('hidden.bs.modal', function () {
         console.log('Se cerro el modal de login');
         document.getElementById("login-form").reset();
         document.getElementById("badcredentials").innerHTML = "";
         document.getElementById("email-required").innerHTML = "";
         document.getElementById("password-required").innerHTML ="";
       });
         //$('#register-form').modal('show');


     });
$(document).ready(function() {
  $('#reset-modal').on('hidden.bs.modal', function () {
    console.log('Se cerro el modal de reset');
    document.getElementById("reset-form").reset();
    document.getElementById("reset-email-required").innerHTML = "";
    document.getElementById("email-sended").innerHTML = "";

  });



   });
