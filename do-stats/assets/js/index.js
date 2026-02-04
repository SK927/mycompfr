$( document ).ready( function() {
  $.ajax( {
    type: 'POST',
    url: 'src/ajax_update-user-competitions.php',
    contentType: false,
    cache: false,
    success: function( response )
    {  
      let result = JSON.parse( response );

      if ( result.captive != "" )
      {
        let div = document.getElementById( 'splash-screen' );
        div.innerHTML = result.captive;

        if ( result.error != true )
        {
          window.setTimeout(function () {
            location.href = "statistics.php";
          }, 5000);
        }
      }
      else
      {
        window.location.href = "statistics.php";
      }
    },
    error: function( xhr, status, error ) 
    {
      
    }
  });
});