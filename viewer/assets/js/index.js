$( document ).on( 'change', '#competition-select', function( e )
{
  let target = document.getElementById( 'competition-select' );

  document.getElementById( 'other-competition' ).style.display = target.value == 'Other' ? 'flex' : 'none';
} );

//-------------------------------------

$( document ).on( 'click', '.btn', function( e )
{
  e.preventDefault();

  let competitionId = '';

  if ( document.getElementById( 'other-competition' ).style.display != 'none' )
  {
    competitionId = document.getElementById( 'other-competition' ).value;
  }
  else
  {
    competitionId = document.getElementById( 'competition-select' ).value;
  }

  if ( this.innerHTML == 'Admin page' )
  {
    window.open( 'admin?id=' + competitionId, '_blank' );
  }
  else if ( this.innerHTML == 'Viewer' )
  {
    window.open( 'viewer?id=' + competitionId, '_blank' );
  }
} );

