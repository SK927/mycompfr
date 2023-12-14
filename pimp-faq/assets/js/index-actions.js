function copyToClipboard( textToDisplay )
{
	var textarea = document.getElementById( 'faq' );

	textarea.select();
	textarea.setSelectionRange( 0, 99999 ); // For mobile devices

	navigator.clipboard.writeText( textarea.value );
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
    	let result = JSON.parse( response ).resulting_string;
    	
    	if ( result != null )
    	{
	      document.getElementById( 'faq' ).value = result;
	      document.getElementById( 'new-content' ).style.display = 'block';
	      copyToClipboard();
	    }
    } 
  } );
} );