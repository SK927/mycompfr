function toggleStatus( info, state )
{
  if( confirm( 'Confirmer ?' ) )
  {
    let bar = $( "#status-bar" );

    setStatusBar();

    info = info.split( '_' );

    $.ajax( {
      type: 'POST',
      url: 'src/admin_ajax-update-registration-status.php',
      data: { competition_id: info[0], user_id: info[1], new_state: state },
      success: function( response )
      {      
        console.log(response);
        let result = JSON.parse( response );
        
        setStatusBar( result.text_to_display, result.error );

        window.location.reload();
      },
      error: function( xhr, status, error ) 
      {
        setStatusBar( xhr, error );
      }
    } );
  }

} 

//-------------------------------------

$( document ).on( 'click', '.going', function( e )
{
  let info = e.target.closest( 'button' ).value;
  toggleStatus( info, 'YES' );   
} ); 

//-------------------------------------

$( document ).on( 'click', '.not-going', function( e )
{
  let info = e.target.closest( 'button' ).value;
  toggleStatus( info, 'NO' );   
} ); 