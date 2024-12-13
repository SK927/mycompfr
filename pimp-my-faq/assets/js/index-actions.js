function copyToClipboard( id )
{
  var textarea = document.getElementById( id );

  $('.selected').removeClass('selected');

	textarea.select();
	textarea.setSelectionRange( 0, 99999 ); // For mobile devices
        
	navigator.clipboard.writeText( textarea.value );

  textarea.classList.add( "selected" );
} 

//-------------------------------------

$( document ).on( 'submit', 'form', function( e )
{
  e.preventDefault();

  let target = $( this );

  $.ajax( {
    type: 'POST',
    url: target.attr( 'action' ),
    data: target.serialize(),
    success: function( response )
    { 
      let result = JSON.parse( response );
      
      if ( result != null )
      {
        document.getElementById( 'content-faq' ).value = result.resulting_string_faq;
        document.getElementById( 'content-wl' ).value = result.resulting_string_wl;
        document.getElementById( 'new-content' ).style.display = 'flex';
        copyToClipboard( 'content-faq' );
      }
    } 
  } );
} );

//-------------------------------------

$( document ).on( 'click', '#accept-ots', function( e )
{
  let target = document.getElementById( 'accept-ots' );

  document.getElementById( 'contact' ).style.display = target.checked ? 'flex' : 'none';

} );

//-------------------------------------

$( document ).on( 'change', '#competition-select', function( e )
{
  let target = document.getElementById( 'competition-select' );

  document.getElementById( 'other-competition' ).style.display = target.value == 'Other' ? 'flex' : 'none';
} );

//-------------------------------------

$( document ).ready(function()
{
  let inputs = document.querySelectorAll("input[type='checkbox']");
  let colors = [ "#dc5643", "#fcff22", "#47c76c", "#0061ff" ];

  for( let i = 0; i < inputs.length; i++ )
  {
    inputs[i].style.accentColor = colors[ i % colors.length ];   
  }
});

