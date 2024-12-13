$( document ).on( 'change', '#competition-select', function( e )
{
  let target = document.getElementById( 'competition-select' );

  document.getElementById( 'other-competition' ).style.display = target.value == 'Other' ? 'flex' : 'none';
} );

