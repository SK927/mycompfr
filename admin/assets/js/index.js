$( document ).on( 'submit', '#form-credentials', function( e )
{ 
  e.preventDefault();

  document.querySelectorAll( '.is-invalid' ).forEach( function( elem, i ){
    elem.classList.remove( 'is-invalid' );
  } );

  let target = $( this );

  $.ajax( {
    type: 'POST',
    url: target.attr( 'action' ),
    data: target.serialize(),
    success: function( response )
    { 
      let result = JSON.parse( response );  
      let error = result.error_login || result.error_credentials || result.error;

      if ( error )
      {
        if ( result.error_login )
        { 
          $( '#login' ).addClass( 'is-invalid' );
        }
        else if( result.error_credentials ) 
        {
          $( '#login' ).addClass( 'is-invalid' );
          $( '#password' ).addClass( 'is-invalid' );
        }
        setStatusBar( result.text_to_display, error );
      }
      else 
      {
        location.reload();
      }
    },
    error: function( xhr, status, error ) 
    {
      setStatusBar( xhr, error );
    }
  } );
} );
