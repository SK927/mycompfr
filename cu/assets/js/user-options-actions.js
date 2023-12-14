$( document ).on( 'click', '.button-back', function( e )
{
  e.preventDefault();

  sessionStorage.setItem( 'isBack', true );
   
  history.back();
} );