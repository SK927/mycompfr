$( document ).on( 'change', '#competition-select', function( e )
{
  let target = document.getElementById( 'competition-select' );
  document.getElementById( 'other-competition' ).style.display = target.value == 'Other' ? 'flex' : 'none';
} );

// ========================================= //

$( document ).on( 'click', 'input[type=checkbox]', function( e )
{
  let selected = 0;

  document.querySelectorAll( '.form-check-input' ).forEach( elem => {
    selected += elem.checked;
  } );

  if( 2 <= selected )
  {
    document.getElementById( 'submit-button' ).disabled = false;

    if( selected == 3 )
    {
      e.preventDefault();
    }
  }
  else
  {
    document.getElementById( 'submit-button' ).disabled = true;
  }
  
} );



