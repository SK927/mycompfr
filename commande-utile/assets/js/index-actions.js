$( document ).on( 'click', '.import-link', function( e )
{
  e.preventDefault();

  if ( confirm( 'Confirmer l\'importation ?' ) )
  {
    let href = $( this ).attr( 'href' );

    document.location.href = href;
  }
});