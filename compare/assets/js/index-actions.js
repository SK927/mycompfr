$( document ).on( 'change', '.form-select', function( e )
{
  let occurence = this.id.split('-')[2];
  let target = document.getElementById( 'competition-select-' + occurence );

  document.getElementById( 'other-competition-' + occurence ).style.display = target.value == 'Other' ? 'flex' : 'none';
} );

