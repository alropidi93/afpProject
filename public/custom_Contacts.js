

$(document).ready(function(){

  $('#successContacts').on("click", function(event){
    var name = $('#nameContacts').val();
    var email = $('#emailContacts').val();
    var phone = $('#phoneContacts').val();
    var bodyMessage = $('#messageContacts').val();

    var url = baseUrl + "/contacts/sendMessage" + "/" + name +"/" + email + "/" + phone + "/" + bodyMessage;
    console.log(url);
    $.getJSON( url, {} )
    .done(function( data, textStatus, jqXHR ) {
      if ( console && console.log ) {
        console.log(data);
      }
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {
      if ( console && console.log ) {
        console.log( "errorThrown: " + errorThrown + "Algo ha fallado: " +  textStatus);
      }
    });
  });


});
