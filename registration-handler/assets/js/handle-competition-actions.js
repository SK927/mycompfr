$( document ).on( 'change', '.done', function( e )
{
  e.preventDefault();
  
  let target = $( this );
  let userId = target.attr( 'id' );
  let competitionId = target.closest( '.competition-id' ).attr( 'id' );
  let checked = document.getElementById( userId + '_checkbox' ).checked;

  setStatusBar();
  
  $.ajax( {
    type: 'POST',
    url: 'src/admin_ajax-toggle-printed.php',
    data: { competition_id:competitionId, user_id:userId, printed:checked },
    success: function( response )
    {
      let result = JSON.parse( response );
      setStatusBar( result.text_to_display, result.error );
    },
    error: function( xhr, status, error ) 
    {
      setStatusBar( xhr, error );
    }
  } );
} );