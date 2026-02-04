function toggleStatus( info, state )
{

    info = info.split( '_' );
    console.log(state);
    console.log(info);
    $.ajax( {
      type: 'POST',
      url: 'src/ajax_update-registration-status.php',
      data: { competition_id: info[0], user_id: info[1], new_state: state },
      success: function( response )
      {      
        let result = JSON.parse( response );
        
        window.location.reload();
      },
      error: function( xhr, status, error ) 
      {
        setStatusBar( xhr, error );
      }
    } );
} 

//-------------------------------------

$( document ).on( 'click', '.going', function( e )
{
  let info = e.target.closest( 'button' ).value;
  toggleStatus( info, 'OK' );   
} ); 

//-------------------------------------

$( document ).on( 'click', '.maybe', function( e )
{
  let info = e.target.closest( 'button' ).value;
  toggleStatus( info, 'ND' );   
} ); 

//-------------------------------------

$( document ).on( 'click', '.not-going', function( e )
{
  let info = e.target.closest( 'button' ).value;
  toggleStatus( info, 'NO' );   
} ); 