$( document ).on( 'change', '.form-select', function( e )
{
  let occurence = this.id.split('-')[2];
  let target = document.getElementById( 'competition-select-' + occurence );

  document.getElementById( 'other-competition-' + occurence ).style.display = target.value == 'Other' ? 'flex' : 'none';
} );


$( document ).on( 'submit', 'form', function( e )
{
  e.preventDefault();
  
  let competitionId = [];

  for ( var i = 1; i <= 2; i++ )
  {
    if ( document.getElementById( 'other-competition-' + i ).style.display != 'none' )
    {
      competitionId.push( document.getElementById( 'other-competition-' + i ).value );
    }
    else
    {
      competitionId.push( document.getElementById( 'competition-select-' + i ).value );
    }
  }

  window.location.href = 'display-compared-lists?competition1=' + competitionId[0] + '&competition2=' + competitionId[1];
} );

