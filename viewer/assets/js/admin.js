$( document ).on( 'click', '.btn', function( e )
{
  let competitionId = document.getElementById( 'competition-id' ).innerHTML;
  let current = this;
  let next = current.nextSibling.nextSibling;
  let link = document.getElementById( 'live-link' ).value;

  try
  {
    next = next.value;
  }
  catch
  {
    next = '';
  }

  $.ajax( {
    type: 'POST',
    url: 'src/admin_ajax-set-current.php',
    data:{id: encodeURI( competitionId ), current:this.value, next:next, live:link},
    success: function( response )
    { 
      let error = JSON.parse(response);
      if ( ! error )
      {
        $(current).parent().find('.btn-success').toggleClass("btn-success btn-light");
        $(current).toggleClass("btn-success btn-light");
      }
    },
    error: async function( xhr, status, error ) 
    {

    } 
  } );

} ); 

//-------------------------------------

$( document ).on( 'focusout', '#live-link', function( e )
{
  let competitionId = document.getElementById( 'competition-id' ).innerHTML;
  let link = document.getElementById( 'live-link' ).value;

  $.ajax( {
    type: 'POST',
    url: 'src/admin_ajax-update-live.php',
    data:{id: encodeURI( competitionId ), live:link},
    success: function( response ) {},
    error: async function( xhr, status, error ) {} 
  } );

} ); 